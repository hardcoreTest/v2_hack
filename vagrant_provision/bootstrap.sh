if [ -f "/.bootstrap.done" ]; then
    service apache2 restart
    service elasticsearch restart
    exit
fi

DEBIAN_FRONTEND=noninteractive apt-get -y update
DEBIAN_FRONTEND=noninteractive apt-get -y upgrade
DEBIAN_FRONTEND=noninteractive apt-get -y install software-properties-common python-software-properties
DEBIAN_FRONTEND=noninteractive add-apt-repository -y ppa:ondrej/php
DEBIAN_FRONTEND=noninteractive add-apt-repository -y ppa:ondrej/apache2
DEBIAN_FRONTEND=noninteractive add-apt-repository -y ppa:webupd8team/java
DEBIAN_FRONTEND=noninteractive apt-get -y update
DEBIAN_FRONTEND=noninteractive apt-get -y upgrade
DEBIAN_FRONTEND=noninteractive apt-get -y install php7.0
DEBIAN_FRONTEND=noninteractive apt-get -y install apache2
DEBIAN_FRONTEND=noninteractive apt-get -y install mysql-server
DEBIAN_FRONTEND=noninteractive apt-get -y install libapache2-mod-php7.0 php7.0-mysql php7.0-curl php7.0-json php7.0-dom\
 php7.0-gd php7.0-zip php7.0-dev unzip php7.0-mbstring php-xdebug memcached php7.0-memcached

echo "
zend_extension=xdebug.so
xdebug.remote_enable=1
xdebug.remote_handler=dbgp
xdebug.remote_host=127.0.0.1
xdebug.remote_port=9001
xdebug.remote_autostart=0
xdebug.remote_connect_back=0" > /etc/php/7.0/mods-available/xdebug.ini


curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
DEBIAN_FRONTEND=noninteractive apt-get -y install nodejs

rm /etc/apache2/sites-enabled/000-default.conf
ln -s /vagrant/vagrant_provision/app.conf /etc/apache2/sites-enabled/001-app.conf
ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load
service apache2 restart

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/bin --filename=composer
php -r "unlink('composer-setup.php');"



adduser --quiet --home /home/vagrant vagrant
usermod -a -G sudo vagrant
echo "vagrant:vagrant" | chpasswd

cat /etc/ssh/sshd_config | sed -e 's/PasswordAuthentication no/#PasswordAuthentication no/g' > /root/22.txt
mv /root/22.txt /etc/ssh/sshd_config
service sshd restart

touch /.bootstrap.done