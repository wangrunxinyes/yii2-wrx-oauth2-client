<?php 
namespace wangrunxinyes\OAuth\controllers;

class OauthController extends \yii\base\Controller
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

    public function successCallback($client)
    {
        $attributes = $client->getUserAttributes();
        // user login or signup comes here
    }
}

?>