set -o errexit
. /etc/lsb-release
if [ "$DISTRIB_CODENAME" != "bionic" ]; then
  version="7.0"
else
  version="7.2"
fi
installed="php$version-fpm php$version-mysql php$version-gd php$version-curl php$version-xml php$version-zip php$version-mbstring php$version-opcache php$version-dev openssl redis-server mysql-server"
apt install -y $installed
pecl install igbinary
echo 'extension=igbinary.so' >/etc/php/$version/mods-available/igbinary.ini
ln -s /etc/php/$version/mods-available/igbinary.ini /etc/php/$version/cli/conf.d/20-igbinary.ini
ln -s /etc/php/$version/mods-available/igbinary.ini /etc/php/$version/fpm/conf.d/20-igbinary.ini
pecl install redis
echo 'extension=redis.so' >/etc/php/$version/mods-available/redis.ini
ln -s /etc/php/$version/mods-available/redis.ini /etc/php/$version/cli/conf.d/20-redis.ini
ln -s /etc/php/$version/mods-available/redis.ini /etc/php/$version/fpm/conf.d/20-redis.ini
systemctl restart "php$version-fpm.service"
sed -i "13i\	\	fastcgi_pass unix:/run/php/php$version-fpm.sock;" upsite
echo "请输入设置的数据库root密码："
mysql -uroot -p --default-character-set=utf8 < upsite.sql
