#!/usr/bin/python3
# python 3.5+
import os,subprocess,signal
logFile=None
log='/var/log/upsite.log'
with open('/run/upsite.pid','w') as f:
    f.write(str(os.getpid()))
logFile=open(log,'a')

proList={}
path=os.path.dirname(os.path.realpath(__file__))
def start():
    for a,b,c in os.walk(path+'/py'):
        for name in c:
            if name not in proList and name.endswith('.pyc') and not name.startswith('_'):
                popen=subprocess.Popen(['python3',path+'/py/'+name],stderr=logFile,stdout=logFile)
                proList[name]=popen
        break
def term(a,b):
    for name in proList:
        proList[name].terminate()
    exit()
def reloadreq(a,b):
    for name in proList:
        proList[name].send_signal(signal.SIGHUP)
    start()
start()
signal.signal(signal.SIGTERM,term)
signal.signal(signal.SIGHUP,reloadreq)
while True:
    try:
        (pid,code)=os.wait()
    except OSError as e:
        exit()
    for name in proList:
        if proList[name].pid==pid:
            if code!=signal.SIGTERM and code!=signal.SIGKILL:
                proList[name]=subprocess.Popen(['python3','%s/%s'%(path+'/py',name)],stderr=logFile,stdout=logFile)
            else:
                proList.pop(name)
            break
