set -o errexit
apt install -y gcc make automake openssl zlib1g-dev libpcre3 libpcre3-dev libssl-dev libxslt1-dev libxml2-dev libgeoip-dev
if [ -x /usr/sbin/nginx ]; then
	apt purge nginx
	rm /usr/sbin/nginx
	systemctl disable nginx
	rm /etc/init.d/nginx
fi
./configure --with-http_ssl_module --add-module=src/http/modules/ngx_http_substitutions --prefix=/usr/local/leapvalue
make && make install
#ln -s /usr/local/leapvalue/sbin/nginx /usr/sbin/leapvalue
