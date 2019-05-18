if [ -f "/.bootstrap.done" ]; then
    service apache2 restart
    exit
fi

DEBIAN_FRONTEND=noninteractive add-apt-repository -y ppa:ondrej/php
DEBIAN_FRONTEND=noninteractive add-apt-repository -y ppa:ondrej/apache2
DEBIAN_FRONTEND=noninteractive add-apt-repository -y ppa:webupd8team/java
DEBIAN_FRONTEND=noninteractive apt-get -y update
DEBIAN_FRONTEND=noninteractive apt-get -y install php7.0
DEBIAN_FRONTEND=noninteractive apt-get -y install apache2
DEBIAN_FRONTEND=noninteractive apt-get -y install mysql-server
DEBIAN_FRONTEND=noninteractive apt-get -y install libapache2-mod-php7.0 php7.0-mysql php7.0-curl php7.0-json php7.0-dom\
 php7.0-gd php7.0-zip


rm /etc/apache2/sites-enabled/000-default.conf
ln -s /vagrant/vagrant_provision/app.conf /etc/apache2/sites-enabled/001-app.conf
ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load
service apache2 restart

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

php composer-setup.php --install-dir=/bin --filename=composer
php -r "unlink('composer-setup.php');"


touch /.bootstrap.done