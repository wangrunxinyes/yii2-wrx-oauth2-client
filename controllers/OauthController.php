<?php
namespace wangrunxinyes\OAuth\controllers;

use wangrunxinyes\OAuth\models\Client;
use wangrunxinyes\OAuth\models\User;
use wangrunxinyes\OAuth\utils\security_utils;
use yii\web\UnauthorizedHttpException;

class OauthController extends \yii\web\Controller
{
	public function actions()
	{
		return [
				'auth' => [
						'class' => 'wangrunxinyes\OAuth\models\OAuthAction',
						'successCallback' => [$this, 'successCallback'],
				],
		];
	}
	
	public function successCallback($client) {
		/* @var Client $client */
		$user = $client->getUserAttributes ();
		/* @var User $user */
		if (is_null ( $user->client_user_id )) {
			$config = [
					'open_id' => $user->open_id,
					'random' => security_utils::createNonceStr(32)
			];
			$config = security_utils::createSignedStr ( $config, \Yii::$app->getModule('wrx')->mixKey);
			return $this->redirect(array_merge(['/wrx/oauth/login'], $config));
		}
	}
	
	public function actionLogin($open_id){
		if(!security_utils::checkSignedStr($config, \Yii::$app->getModule('wrx')->mixKey)){
			throw new UnauthorizedHttpException('Unauthorized access to this action.');
		}
		//please overwrite login method and bind local user;
	}
}

?>