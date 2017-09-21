<?php

namespace wangrunxinyes\OAuth\models;

use Yii;
use yii\authclient\OAuth2;
use wangrunxinyes\OAuth\utils\security_utils;
use yii\base\UnknownPropertyException;

class Client extends OAuth2 {
	
	public $authUrl = 'https://wangrunxin.com/oauth/api/authorize.js';
	public $tokenUrl = 'https://wangrunxin.com/oauth/token/access-token.js';
	public $apiBaseUrl = 'https://wangrunxin.com/oauth/source/';
	public $client_name = 'Wrx Stu Oauth Client';
	public $refresh_token;
	public $wechat_msg_api = 'send-wechat-notification.js';
	
	/**
	 * Composes user authorization URL.
	 * 
	 * @param array $params
	 *        	additional auth GET params.
	 * @return string authorization URL.
	 */
	public function buildAuthUrl(array $params = []) {
		$defaultParams = [ 
				'client_id' => $this->clientId,
				'response_type' => 'code',
				'redirect_uri' => $this->getReturnUrl (),
				'wrxoauth_displayname' => $this->client_name 
		];
		if (! empty ( $this->scope )) {
			$defaultParams ['scope'] = $this->scope;
		}
		
		if ($this->validateAuthState) {
			$authState = $this->generateAuthState ();
			$this->setState ( 'authState', $authState );
			$defaultParams ['state'] = $authState;
		}
		
		$auth_config = security_utils::createSignedStr ( array_merge ( $defaultParams, $params ), $this->clientSecret );
		
		return $this->composeUrl ( $this->authUrl, $auth_config );
	}
	
	/**
	 *
	 * @throws \yii\base\Exception
	 *
	 * @return User
	 */
	protected function initUserAttributes() {
		$response = $this->api ( 'user.js' );
		$user = User::findOne ( [ 
				'open_id' => $response ['open_id'] 
		] );
		if (is_null ( $user )) {
			$user = new User ();
		}
		$user->setAttributes ( $response, false );
		$user->access_token = serialize ( $this->getAccessToken () );
		$user->save ();
		return $user;
	}
	
	/**
	 * Handles [[Request::EVENT_BEFORE_SEND]] event.
	 * Applies [[accessToken]] to the request.
	 * @param \yii\httpclient\RequestEvent $event event instance.
	 * @throws Exception on invalid access token.
	 * @since 2.1
	 */
	public function beforeApiRequestSend($event)
	{
		$accessToken = $this->getAccessToken();
		
		if($accessToken->isExpired){
			$accessToken = $this->refreshAccessToken($accessToken);
			$this->setAccessToken($accessToken);
			$this->initUserAttributes();
		}
		
		if (!is_object($accessToken) || !$accessToken->getIsValid()) {
			throw new Exception('Invalid access token.');
		}
		
		$this->applyAccessTokenToRequest($event->request, $accessToken);
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
	public function __set($name, $value) {
		try {
			return parent::__set ( $name, $value );
		} catch ( UnknownPropertyException $e ) {
		}
	}
	public static function getInstance(User $user) {
		$client = (new Client ( \Yii::$app->components ['authClientCollection']['clients']['wrxauth']));
		$client->setAccessToken($user->getAccesstoken());
		return $client;
	}
}
