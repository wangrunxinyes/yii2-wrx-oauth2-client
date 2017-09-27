Overwrite OauthController
------------
Example:

```php
use wangrunxinyes\OAuth\controllers\OauthController as BaseController;

class OauthController extends BaseController {
    public function successCallback($client) {
        /* @var Client $client */
        $user = $client->getUserAttributes ();
        /* @var User $user */
        if (is_null ( $user->client_user_id )) {
            /* new oauth user */
            $config = [
                 'open_id' => $user->open_id,
                      'random' => security_utils::createNonceStr(32)
            ];
            $config = security_utils::createSignedStr ( $config, \Yii::$app->getModule('wrx')->mixKey);
            return $this->redirect(array_merge(['/wrx/oauth/login'], $config));
        }else{
            /* user exists */
            $client_user = frontUser::findOne(['id' => $user->client_user_id]);
            $model = \Yii::createObject ( FrontLoginForm::className () );
            $model->internalLogin($client_user);
            return $this->redirect ( [
                 '/sc/user/index'
            ] );
        }
   }
   
   public function actionLogin($open_id){
        if(!security_utils::checkSignedStr($_GET, \Yii::$app->getModule('wrx')->mixKey)){
            throw new UnauthorizedHttpException('Unauthorized access to this action.');
        }
        return $this->redirect(array_merge(['/security/oauth-login'], $_GET));
   }
}
```