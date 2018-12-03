set -o errexit
cd server
./install.sh
cd ../client
./install.sh
mv ../server/upsite /usr/local/leapvalue/conf/vhost/
