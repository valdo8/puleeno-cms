Puleeno CMS
=====

Puleeno CMS是一个基于PHP Laravel框架的免费开源内容管理系统。Puleeno CMS易于使用且灵活，是创建各种网站（如博客、Wiki和电子商务网站）的绝佳选择。.

# 特征

## Puleeno CMS的各种功能, such as:

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

## 使用步骤:

1.下载CMS安装包
2.把安装包部署到虚拟空间或服务器上
3.创建数据库
4.设置CMS参数
5.开始使用cms

## Web服务器设置

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

# 许可

Puleeno CMS is licensed under the MIT license.

# 贡献者

Puleeno CMS由开发者和志愿者维护.

# Credits

Puleeno CMS使用各种开源组件和软件库.

