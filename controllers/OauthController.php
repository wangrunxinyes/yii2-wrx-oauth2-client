<?php 
namespace wangrunxinyes\OAuth\controllers;

use yii\authclient\AuthAction;

class OauthController extends \yii\base\Controller
{
    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
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