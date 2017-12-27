<?php

namespace wangrunxinyes\OAuth\models;

use Yii;
use yii\authclient\OAuth2;
use wangrunxinyes\OAuth\utils\security_utils;
use yii\base\UnknownPropertyException;
use yii\authclient\OAuthToken;

class Client extends OAuth2 {
    CONST SERVER_DOMAIN = 'https://global.wangrunxin.com';
    public $authUrl = CLIENT::SERVER_DOMAIN . '/oauth/api/authorize.js';
    public $tokenUrl = CLIENT::SERVER_DOMAIN . '/oauth/token/access-token.js';
    public $revokeTokenUrl = CLIENT::SERVER_DOMAIN . '/oauth/token/revoke-token.js';
    public $apiBaseUrl = CLIENT::SERVER_DOMAIN . '/oauth/source';
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
		$this->updateUserToken ( $user );
		return $user;
	}
	
	/**
	 * Revoke an OAuth2 access token or refresh token.
	 * This method will revoke the current access
	 * token, if a token isn't provided.
	 *
	 * @param string|null $token
	 *        	The token (access token or a refresh token) that should be revoked.
	 * @return boolean Returns True if the revocation was successful, otherwise False.
	 */
	public function revokeToken($token = null) {
		if (is_null ( $token )) {
			$token = $this->getAccessToken ();
		}
		
		if (get_class ( $token ) !== OAuthToken::className ()) {
			return;
		}
		
		$request = $this->createRequest ();
		$request->setMethod ( 'POST' );
		$request->setFullUrl ( $this->revokeTokenUrl );
		$request->setData ( [ 
				'token_type_hint' => 'access_token',
				'token' => serialize ( $token ) 
		] );
		
		$response = $this->sendRequest ( $request );
	}
	public function updateUserToken($user) {
		/* @var User $user */
		$user->access_token = serialize ( $this->getAccessToken () );
		$user->save ();
	}
	
	/**
	 * Handles [[Request::EVENT_BEFORE_SEND]] event.
	 * Applies [[accessToken]] to the request.
	 *
	 * @param \yii\httpclient\RequestEvent $event
	 *        	event instance.
	 * @throws Exception on invalid access token.
	 * @since 2.1
	 */
	public function beforeApiRequestSend($event) {
		$accessToken = $this->getAccessToken ();
		
		if (! $accessToken->getIsValid ()) {
			$this->revokeToken ( is_object ( $accessToken ) ? $accessToken : NULL );
		}
		
		if (! is_object ( $accessToken ) || ! $accessToken->getIsValid ()) {
			throw new \Exception ( 'Invalid access token.' );
		}
		
		$this->applyAccessTokenToRequest ( $event->request, $accessToken );
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
		$client = (new Client ( \Yii::$app->components ['authClientCollection'] ['clients'] ['wrxauth'] ));
		$client->setAccessToken ( $user->getAccesstoken () );
		$client->setUserAttributes ( $user );
		return $client;
	}
}
