<?php 

namespace wangrunxinyes\OAuth\models;

use yii\authclient\AuthAction;
use wangrunxinyes\OAuth\exceptions\WRXOAuthException;

class OAuthAction extends AuthAction{
// 	/**
// 	 * Runs the action.
// 	 */
// 	public function run()
// 	{
// 		try {
// 			return parent::run();
// 		} catch (Exception $e) {
// 			throw new WRXOAuthException('Exception', 1001, $e);
// 		} catch (\yii\authclient\InvalidResponseException $e){
// 			throw new WRXOAuthException('InvalidResponseException', 1002, $e);
// 		} catch (\yii\web\HttpException $e){
// 			throw new WRXOAuthException('HttpException', 1003, $e);
// 		}
// 	}
}

?>