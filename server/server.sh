apt install -y python3-mysql.connector python3-pip
python3 -m pip install redis
python3 -O -m py_compile py/py/*.py
rm py/py/*.py
mv py/py/__pycache__/* py/py/
NAME="upsite"
sed -i '4i# Provides:	  '$NAME py/smqtt.init
sed -i 12iNAME=$NAME py/smqtt.init
mv py/smqtt.init /etc/init.d/$NAME
systemctl enable $NAME
mv py /usr/local/$NAME
