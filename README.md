Puleeno CMS
=====

Puleeno CMS is a free and open-source content management system (CMS) that is built on the PHP framework Laravel. Puleeno CMS is easy to use and flexible, making it a great choice for creating a wide variety of websites and digital content, such as blogs, wikis, and e-commerce websites.

# Features

## Puleeno CMS includes a variety of features, such as:

A user-friendly interface that makes it easy to create and manage content without having to know how to code.
A variety of built-in templates, themes, and plugins that make it easy to customize your website.
Support for multiple languages.
A variety of features to help improve the SEO of your website.
Support for user roles and permissions.
A variety of security features.
Benefits of using Puleeno CMS

## There are many benefits to using Puleeno CMS, including:

Ease of use: Puleeno CMS is easy to use, even for users who have no prior experience with web development.
Flexibility: Puleeno CMS is flexible and can be used to create a wide variety of websites and digital content.
Features: Puleeno CMS includes a variety of features that make it easy to create, manage, and publish digital content.
Security: Puleeno CMS can help to improve the security of your website by providing features such as user roles and permissions, and security patches and updates.
Support: Puleeno CMS has a large community of users and developers who can provide support and help with troubleshooting.
Getting started with Puleeno CMS

## To get started with Puleeno CMS, you can follow these steps:

Download the Puleeno CMS installation package from the Puleeno CMS website.
Install the Puleeno CMS installation package on your web server.
Create a database for Puleeno CMS.
Configure Puleeno CMS.
Start using Puleeno CMS to create and manage your website or digital content.
Support

If you need help with Puleeno CMS, you can visit the Puleeno CMS website or join the Puleeno CMS community forum.

## Webserver Configs

### NGINX

```
server {
    listen       80;
    server_name  domain_name.com;
    set          $based  /var/www;
    root         $based/public;
    index        index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ ^/(extensions|themes)/(.+)/assets/(.+)\.(eot|svg|ttf|woff|woff2|png|jpg|gif|css|js)$ {
      root $based;
    }
}

```

# License

Puleeno CMS is licensed under the MIT license.

# Contributors

Puleeno CMS is developed and maintained by a community of volunteers.

# Credits

Puleeno CMS uses a variety of open-source software libraries and components.

