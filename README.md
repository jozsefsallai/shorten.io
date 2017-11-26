# shorten.io
Simple, lightweight URL shortener in PHP.

---

## What's so special about this?
Nothing. It's just like any other PHP URL shortener out there. I tried to make this as lightweight as possible, so don't expect things like user accounts and custom aliases (might do that in the future). It's optimal for private organizations wanting their own URL shortener. The code is very easy to understand, so it can be used for learning purposes, and certain parts of the source code can easily be used for other purposes as well. 

This project is a remake of [austindebruyn/short.en](https://github.com/austindebruyn/short.en).

## Local Configuration

### Prerequisites

* An Apache or nginx web server
* PHP (>= 5.4.0)
* MySQL

Before you start the installation, you need to create a new MySQL user and database. You can use any name you want, as you will have to provide those details in the installer. 

### Installation

Clone this repository:

```
git clone git@github.com:bigblog/shorten.io
```

Create a new config in your Apache or nginx server and enable the site. Once you're done, visit http://your_site/install.php to run the installer. 

**IMPORTANT!** If you're using nginx as your web server, you will have to configure the URL rewrites manually in your site config:

```
location / {
  rewrite ^/([A-Za-z0-9-]+)$ /redirect.php?alias=$1 break;
  rewrite ^/([A-Za-z0-9-]+)/$ /redirect.php?alias=$1 break;
}
```

## Possible Upcoming Features

I made this project as a hobby, but I do consider improving it with new features, such as:
* User accounts
* Custom aliases
* Statistics
* Admin Panel

If you have any feature suggestion you can feel free to create an issue for it - or, if you would like to contribute to the project, feel free to do so!

## License

All rights reserved. https://sallai.me 
