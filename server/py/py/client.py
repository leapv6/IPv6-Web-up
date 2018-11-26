import json,os,subprocess,time
import socket,signal
SERVER=('127.0.0.1',6446)
SSLPATH='/usr/local/leapvalue/cert/'
CONFPATH='/usr/local/leapvalue/conf/vhost/'

s=None
conned=False
if not os.path.exists(SSLPATH):
	os.mkdir(SSLPATH)
def connect():
	global s,conned
	conned=False
	s=socket.socket(socket.AF_INET)
	s.setsockopt(socket.SOL_SOCKET, socket.SO_KEEPALIVE, 1)
	while True:
		try:
			s.connect(SERVER)
			conned=True
			ping()
			print('connected',flush=True)
			return
		except Exception as err:
			time.sleep(10)
def add(param):
	name=param['site']['v6url'].replace('.','-')
	with open(CONFPATH+name,'w',encoding='utf8') as f:
		f.write(param['site']['content'])
	if int(param['site']['isHttps'])==1:
		cert=param['site']['cert']
		subprocess.getstatusoutput('wget -O %s.pem %s && wget -O %s.key %s'%(param['site']['v6url'],SSLPATH+cert['cert'],param['site']['v6url'],SSLPATH+cert['key']))
	res=subprocess.getstatusoutput('/etc/init.d/leapvalue reload')
	if res[0]==0:
		send({"cmd":"succ","reqid":param['reqid']})
	else:
		subprocess.getoutput('mv %s /tmp/%s'%(CONFPATH+name,name))
		send({"cmd":"fail","reqid":param['reqid']})

def delete(param):
	name=param['site']['v6url'].replace('.','-')
	os.remove(CONFPATH+name)
	subprocess.getstatusoutput('/etc/init.d/leapvalue reload')
	send({"cmd":"succ","reqid":param['reqid']})

methods={'add':add,'del':delete}
def ping(a=0,b=0):
	send({'cmd':'ping'})
def send(data):
	if not conned:
		return
	try:
		data=json.dumps(data)+'\n'
		s.sendall(data.encode())
	except Exception as err:
		connect()
		time.sleep(10)
		send(data)
connect()
signal.setitimer(signal.ITIMER_REAL,600,600)
signal.signal(signal.SIGALRM,ping)
while True:
	try:
		rec=s.recv(40960)
		if len(rec)>0:
			rec=rec.decode()
			arr=rec.split('\n')
			for x in arr:
				if len(x)>5:
					rec=json.loads(x)
					print(rec,flush=True)
					if 'cmd' not in rec or rec['cmd']=='deny':
						exit()
					if rec['cmd'] not in methods:
						send({'cmd':'fail','code':'1','msg':'need update'})
					else:
						methods[rec['cmd']](rec)
		else:
			connect()
	except Exception as err:
		print(err,flush=True)
		time.sleep(10)
