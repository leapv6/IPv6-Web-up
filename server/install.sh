set -o errexit
apt update
apt upgrade
./php.sh
./server.sh
if [ ! -d /var/www ]; then
  mkdir /var/www
fi
mv php /var/www/upsite
chown www-data:www-data -R /var/www
