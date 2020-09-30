Chatroom
========================================================================
[![status](https://img.shields.io/badge/Project%20Status-work--in--progress-green.svg)](#)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=jesus_andrade45%40yahoo%2ecom&lc=US&item_name=GitHub%20Projects&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted)

A basic chatroom created with PHP, JavaScript, and MySQL.

**Current webpage:** [Click here](https://fancyjesse.com/projects/chatroom/)


Introduction
------------------------------------------------------------------------
This chatroom's messages are updated through polling.
Messages are sent to everyone regardless of log-in status, but the user must be registered and logged-in to send messages.


Prerequisites
------------------------------------------------------------------------
PHP

MySQL


Installation
------------------------------------------------------------------------
Install MySQL
```
$ apt-get install mysql-server --fix-missing
```

Clone the Chatrooom project
```
$ git clone https://github.com/idle-user/chatroom
```


Setup - Database
------------------------------------------------------------------------
In order to store and send messages to chatroom users, SQL tables are required.
These tables can be automatically created my running **InitTables.php**.
```
$ php scripts/InitTables.php 
```


Usage
------------------------------------------------------------------------
Visit the webpage (index.php) to join the chatroom.

Register and log-in to send messages.


License
------------------------------------------------------------------------
See the file "LICENSE" for license information.


Authors
------------------------------------------------------------------------
idle-user
