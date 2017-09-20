<?php

namespace wangrunxinyes\OAuth;

use Yii;
use yii\authclient\OAuth2;

class Client extends OAuth2
{
    /** {@inheritdoc} */
    public $authUrl = 'https://wangrunxin.com/oauth/api/authorize';
    /** {@inheritdoc} */
    public $tokenUrl = 'https://wangrunxin.com/oauth/api/access_token';
    /** {@inheritdoc} */
    public $apiBaseUrl = 'https://wangrunxin.com/oauth/api/oauth2';

    /**
     * @throws \yii\base\Exception
     *
     * @return User
     */
    protected function initUserAttributes()
    {
        return new User($this->api('me'));
    }

    /**
     * {@inheritdoc}
     */
    public function buildAuthUrl(array $params = [])
    {
        return parent::buildAuthUrl(array_merge(['state' => 'ignored'], $params));
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnUrl()
    {
        return Yii::$app->urlManager->createAbsoluteUrl(['/api/oauth/call-back', 'authclient' => 'wrxauth']);
    }
}
