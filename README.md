ldap
====
ldap curd

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require wzg/yii2-ldap
```

or add

```
"wzg/yii2-ldap": "dev-master"
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
      'ret' => [
          5 => '北京公司', //对应系统内的公司ID和公司名称
      ],  
      'ldaptablename' => '\backend\models\AdminUser', //用户表命名空间（系统表名不同）
      'company_field' => 'company',//用户公司ID（字段不同）
      //在main.php配置如下
      'modules' => [
          'ldap' => [ 
                 'class' => 'wzg\ldap\Module', 
             ], 
       ],
      ```