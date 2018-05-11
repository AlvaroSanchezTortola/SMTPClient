# SMTPClient
![PHP - Version](https://img.shields.io/badge/PHP-7.1.15-blue.svg) ![Apache - Version](https://img.shields.io/badge/server-apache%2F2.4.27%20(Ubuntu)-lightgrey.svg)

This proyect provides a simple web interface for sending mails, providing authentication. It is connected to [this server](https://github.com/AlvaroSanchezTortola/SMTPServer), [this inbox](https://github.com/AlvaroSanchezTortola/SMTPReader) and [this logger](https://github.com/AlvaroSanchezTortola/SMTPLogger). 

## Installation
### General Instructions
#### Apache Web Server and PHP Interpreter
First, install the web server:
``` sh
sudo apt-get install apache2
```
then, install all `apache` and `PHP` dependencies:
``` sh
sudo apt-get install php libapache2-mod-php php-mcrypt php-mysql
```
finally, locate the files in `/var/www/html`.
## Run
Access the client in
```http
http://localhost/login.php
```