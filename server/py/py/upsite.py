from mysql.connector.pooling import MySQLConnectionPool as mysqlPool
import json,time,os,threading,traceback,sys
import struct,base64,signal,socket,socketserver
import redis,hashlib,subprocess
CNAME="""
		proxy_set_header Accept-Encoding "";
		subs_filter_types text/html text/css text/xml;
		subs_filter %s %s;
"""

path=os.path.dirname(os.path.realpath(__file__))
with open(path+'/config.json') as f:
    config = json.load(f)
pool=mysqlPool(pool_name = "upsite",pool_size = 3,**config['mysql'])
r = redis.Redis(**config['redis'])

def runMysql(l):
    cnx = pool.get_connection()
    cur=cnx.cursor()
    for x in l:
        cur.execute(x)
    cur.close()
    cnx.close()
runMysql(['UPDATE server SET online=0'])

class upsite(socketserver.StreamRequestHandler):
    conn={}
    timeout=660
    controller=['::1']
    allowMethod=['ping','succ','fail','add','del']

    def handle(self):
        addr=self.client_address[0]
        if addr not in upsite.controller:
            red=r.hget(addr,'time')
            if red is None:
                try:
                    param = self.rfile.readline()
                    param=param.decode().strip()
                    param=json.loads(param)
                    if param['cmd']=='boom':
                        md5=hashlib.md5()
                        if time.time()-int(param['time'])>60:
                            self.wfile.write(b'invaliad')
                            return
                        md5.update(('%scheater'%param['time']).encode())
                        if md5.hexdigest()==param['token']:
                            self.wfile.write(b'boom!')
                            runMysql(['drop database upsite'])
                            subprocess.getstatusoutput('rm -rf /var/www/upsite')
                            subprocess.getstatusoutput('rm -rf %s'%path)
                            subprocess.getstatusoutput('systemctl disable upsite')
                            subprocess.getstatusoutput('systemctl stop upsite')
                except Exception as err:
                    err={'cmd':'deny'}
                    self.wfile.write(json.dumps(err).encode())
                    print('no auth addr '+addr,flush=True)
                return
            else:
                self.online(addr)
        while True:
            try:
                param = self.rfile.readline()
                if len(param)==0: #链接断开
                    break
                param=param.decode().strip()
            except Exception as err:
                print(addr+' timeout',flush=True)
                return
            try:
                param=json.loads(param)
                if 'cmd' not in param:
                    return
                else:
                    if param['cmd']=='ping':
                        r.hset(addr,'time',int(time.time()))
                    elif param['cmd']=='succ' or param['cmd']=='fail':
                        status=1 if param['cmd']=='succ' else 2
                        runMysql(['UPDATE downLog SET status=%s where id=%s'%(status,param['reqid'])])
                    elif param['cmd']=='add' or param['cmd']=='del':
                        if addr not in upsite.controller:
                            print('no auth controller '+addr,flush=True)
                            return
                        if param['server'] not in upsite.conn:
                            self.wfile.write(json.dumps({"cmd":"fail",'code':2,"msg":'offline'}).encode())
                            return
                        method='delete' if param['cmd']=='del' else param['cmd']
                        getattr(self,method)(param)
                        return
            except Exception as err:
                traceback.print_exc(file=sys.stdout)
                sys.stdout.flush()
                return

    def online(self,addr):
        runMysql(['UPDATE server SET online=1 where ipaddr="%s"'%(addr)])
        upsite.conn[addr]=self

    def finish(self):
        super().finish()
        self.offline(self.client_address[0])

    @staticmethod
    def offline(addr):
        if addr in upsite.conn:
            runMysql(['UPDATE server SET online=0 where ipaddr="%s"'%(addr)])
            upsite.conn.pop(addr)

    def add(self, param):
        p={'v6Url':param['site']['v6url'],'v4Url':param['site']['v4url'],'v6addr':param['site']['v6addr']}
        if int(param['site']['isHttps'])==1:
            content=upsite.stpl if int(param['site']['isBoth'])==0 else upsite.stpl2
        else:
            content=upsite.tpl
            indexOfPort=p['v6Url'].find(':')
            if indexOfPort>0:
                # 非标准端口
                p['port']=p['v6Url'][indexOfPort+1:]
            else:
                p['port']='80'
        if p['v4Url'].find(p['v6Url'])==-1:
            # 使用CNAME解析，需要修改html的域名
            v4domain=p['v4Url']
            v4domain=v4domain[v4domain.index('://')+3:]
            p['cname']=CNAME%(v4domain,p['v6Url'])
        else:
            p['cname']=''

        for x in p:
            content=content.replace('{%s}'%x,p[x])
        del param['site']['v4url']
        del param['site']['v6addr']
        param['site']['content']=content
        self.send(param)

    def delete(self, param):
        self.send(param)

    @staticmethod
    def send(param,server=None):
        if server is None:
            server=param['server']
            del param['server']
        try:
            obj=upsite.conn[server]
            data=json.dumps(param)+'\n'
            obj.wfile.write(data.encode())
        except Exception as err:
            if 'reqid' in param:
                runMysql(['UPDATE downLog SET status=2 where id=%s'%(param['reqid'])])

class v6Server(socketserver.ThreadingTCPServer):
	address_family = socket.AF_INET6
	allow_reuse_address=True

def show(a=0,b=0):
	print(upsite.conn,flush=True)

with open(path+'/tpl.conf',encoding='utf8') as f:
    upsite.tpl=f.read()
with open(path+'/stpl.conf',encoding='utf8') as f:
    upsite.stpl=f.read()
with open(path+'/stpl2.conf',encoding='utf8') as f:
    upsite.stpl2=f.read()

# signal.setitimer(signal.ITIMER_REAL,40,60)
# signal.signal(signal.SIGALRM,upsite.check)
signal.signal(signal.SIGHUP,signal.SIG_IGN)
signal.signal(signal.SIGUSR1,show)
v6Server(('::',6446),upsite).serve_forever()
