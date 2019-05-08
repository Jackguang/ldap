ldap
====
ldap curd

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist vendor/yii2-ldap "*"
```

or add

```
"vendor/yii2-ldap": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
	//在params配置ldap信息
    'ldap' => [
          'host' => '*********',	//服务器地址
          'port' => 389,
          'password' => '*****', //密码
          'baseDN' => 'dc=test,dc=test', //根节点
      ],
      //在main.php配置如下
      'modules' => [
          'ldap' => [ 
                 'class' => 'wzg\ldap\Module', 
             ], 
       ],
      ```