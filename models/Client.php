<?php

namespace wangrunxinyes\OAuth\models;

use Yii;
use yii\authclient\OAuth2;
use wangrunxinyes\OAuth\utils\security_utils;

class Client extends OAuth2 {
	/**
	 *
	 * {@inheritdoc}
	 */
	public $authUrl = 'https://wangrunxin.com/oauth/api/authorize.js';
	/**
	 *
	 * {@inheritdoc}
	 */
	public $tokenUrl = 'https://wangrunxin.com/oauth/token/access-token.js';
	/**
	 *
	 * {@inheritdoc}
	 */
	public $apiBaseUrl = 'https://wangrunxin.com/oauth/api';
	
	public $client_name = 'Oauth Client';
	
	public $refresh_token;
	
	/**
	 * Composes user authorization URL.
	 * @param array $params additional auth GET params.
	 * @return string authorization URL.
	 */
	public function buildAuthUrl(array $params = [])
	{
		$defaultParams = [
				'client_id' => $this->clientId,
				'response_type' => 'code',
				'redirect_uri' => $this->getReturnUrl(),
				'wrxoauth_displayname' => $this->client_name,
		];
		if (!empty($this->scope)) {
			$defaultParams['scope'] = $this->scope;
		}
	
		if ($this->validateAuthState) {
			$authState = $this->generateAuthState();
			$this->setState('authState', $authState);
			$defaultParams['state'] = $authState;
		}
		
		$auth_config = security_utils::createSignedStr(array_merge($defaultParams, $params), $this->clientSecret);
	
		return $this->composeUrl($this->authUrl, $auth_config);
	}
	
	/**
	 *
	 * @throws \yii\base\Exception
	 *
	 * @return User
	 */
	protected function initUserAttributes() {
		$response = $this->api ( 'user.js' );
		$user = User::findOne(['open_id' => $response['open_id']]);
		if(is_null($user)){
			$user = new User();
		}
		$user->setAttributes($response, false);
		$user->access_token = $this->getAccessToken()->getParam('access_token');
		$user->refresh_token = $this->getAccessToken()->getParam('refresh_token');
		$user->save();
		return $user;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 */
	public function getReturnUrl() {
		return Yii::$app->urlManager->createAbsoluteUrl ( [ 
				'/wrx/oauth/auth',
				'authclient' => 'wrxauth' 
		] );
	}
}
