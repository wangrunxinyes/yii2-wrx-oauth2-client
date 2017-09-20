<?php

namespace wangrunxinyes\OAuth\models;

use Yii;
use yii\authclient\OAuth2;

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
	public $tokenUrl = 'https://wangrunxin.com/oauth/api/access_token.js';
	/**
	 *
	 * {@inheritdoc}
	 */
	public $apiBaseUrl = 'https://wangrunxin.com/oauth/api/oauth2.js';
	
	/**
	 *
	 * @throws \yii\base\Exception
	 *
	 * @return User
	 */
	protected function initUserAttributes() {
		return new User ( $this->api ( 'me' ) );
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 */
	public function buildAuthUrl(array $params = []) {
		return parent::buildAuthUrl ( array_merge ( [ 
				'state' => 'ignored' 
		], $params ) );
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
