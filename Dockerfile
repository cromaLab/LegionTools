#docker build -t legion .
#docker run -d -p 2345:80 legion /usr/sbin/apache2ctl -D FOREGROUND
#http://localhost:2345/legion/
FROM ubuntu:14.04

RUN apt update && apt install -y apache2 php5 libapache2-mod-php5 php5-mcrypt php5-sqlite php-soap curl

# For developement
#RUN apt install -y vim

# Move index.php to first position
#  Remove index.php entry
RUN sed -ie 's/index.php //g' /etc/apache2/mods-enabled/dir.conf
#  Insert it at beginning
RUN sed -ie 's/DirectoryIndex /DirectoryIndex index.php /g' /etc/apache2/mods-enabled/dir.conf

ADD . /var/www/html/legion
WORKDIR /var/www/html/legion
RUN mkdir -p db

# Adapt PHP URL to use current IP (note: this is for my workstation, if you have a URL, please use that instead)
RUN sed -ie "s/^    $baseURL.*$/    \$baseURL = \"https:\/\/$(curl -s http:\/\/whatismyip.akamai.com\/)\/legion\";/g" config.php
# Also adapt PHP URL in Retainer/scripts/vars.js
RUN sed -ie "s/^var getTimeWaitedURL.*$/var getTimeWaitedURL = \"https:\/\/$(curl -s http:\/\/whatismyip.akamai.com\/)\/legion\/Retainer\/php\/getTimeWaited.php\";/g" ./Retainer/scripts/vars.js

RUN a2enmod ssl
# Create keys
RUN mkdir -p keys && openssl req -x509 -nodes -days 365 -newkey rsa:4096 -keyout ./keys/apache.key -out ./keys/apache.crt -subj "/C=US/ST=MI/L=Ann Arbor/O=University of Michigan/CN=www.legionpowered.net"
RUN mkdir /etc/apache2/ssl && cp ./keys/* /etc/apache2/ssl/
# Modify Apache SSL configuration
RUN sed -ie 's/ServerAdmin.*$/ServerAdmin someone@umich.edu\n                ServerName diae2.eecs.umich.edu\n                ServerAlias www.diae12.eecs.umich.edu/g' /etc/apache2/sites-available/default-ssl.conf
RUN sed -ie 's/ssl\/\(certs\|private\)\/ssl-cert-snakeoil/apache2\/ssl\/apache/g' /etc/apache2/sites-available/default-ssl.conf
RUN sed -ie '0,/\/etc\/apache2\/ssl\/apache.pem/s//\/etc\/apache2\/ssl\/apache.crt/' /etc/apache2/sites-available/default-ssl.conf
RUN a2ensite default-ssl.conf && service apache2 restart

EXPOSE 80

CMD /usr/sbin/apache2ctl -D FOREGROUND
