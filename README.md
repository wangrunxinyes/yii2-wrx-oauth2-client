WRX OAuth Client 3.1 For WANGRUNXIN.COM
==================

A wrapper for implementing an OAuth2 Server(https://www.wangrunxin.com)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist wangrunxinyes/yii2-wrx-oauth2-client "*"
```

or add

```json
"wangrunxinyes/yii2-wrx-oauth2-client": "*"
```

to the require section of your composer.json.

To use this extension,  simply add the following code in your application configuration as a new module:

```php
'modules'=>[
        //other modules .....
        'wrx'  => [
                    'class' => 'wangrunxinyes\OAuth\Module',
					'controllerMap' => [
							'oauth' => 'frontend\controllers\oauth\OauthController',
					],
					'mixKey' => 'Your random code',
			],
        ],
'components' => [ 
        //other components .....
        'authClientCollection' => [
					'class' => yii\authclient\Collection::class,
					'clients' => [
							'wrxauth' => [
									'class' => wangrunxinyes\OAuth\models\Client::class,
									'clientId' => 'Your client_id',
									'clientSecret' => 'Your client_secret',
									'scope' => '',
							],
					],
			],
		]
```

The next step your shold run migration

```php
yii migrate --migrationPath=@vendor/wangrunxinyes/yii2-wrx-oauth2-client/migrations
```

For more, see https://wangrunxin.com