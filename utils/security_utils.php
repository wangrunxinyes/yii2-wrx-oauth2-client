<?php 

namespace wangrunxinyes\OAuth\utils;

use yii\base\Model;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class security_utils extends Model{
	const public_key = 'def00000a4b2383cdab0f7768daff6fcb9fe08306f61ef5fbbc671b61245f37a5bfe54a616473a6833eed215cc8979dd121f2224052f6a5128115cb0dbc8e49531a56dda';
	
	public static function createSignedStr($config, $mixKey) {
		$code = '';
		
		// create config with signature;
		ksort ( $config );
		$keys = [ ];
		foreach ( $config as $key => $value ) {
			$keys [] = $key;
			$code .= $key . '=' . $value . '&';
		}
		$keys = json_encode ( $keys );
		$code = substr ( $code, 0, strlen ( $code ) - 1 );
		$signature = sha1 ( $code . $keys . $mixKey );
		$config ['signature'] = $signature;
		$config ['keys'] = self::safeEncrypt($keys);
		return $config;
	}
	
	public static function checkSignedStr($config, $mixKey){	
		$code = '';
		$json_key = self::safeDecrypt($config ['keys']);
	
		$passedSignature = $config ['signature'];
		unset ( $config ['signature'] );
	
		// create config with signature;
		ksort ( $config );
		$keys = json_decode ( $json_key, true );
		foreach ( $config as $key => $value ) {
			if (in_array ( $key, $keys ))
				$code .= $key . '=' . $value . '&';
		}
		$code = substr ( $code, 0, strlen ( $code ) - 1 );
		$signature = sha1 ( $code . $json_key. $mixKey );
		if ($signature === $passedSignature) {
			return true;
		}
	
		return false;
	}
	
	public static function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for($i = 0; $i < $length; $i ++) {
			$str .= substr ( $chars, mt_rand ( 0, strlen ( $chars ) - 1 ), 1 );
		}
		return $str;
	}
	
	/**
	 * Encrypt a message
	 *
	 * @param string $message - message to encrypt
	 * @param string $key - encryption key
	 * @return string
	 */
	public static function safeEncrypt($message)
	{
		return Crypto::encrypt($message, Key::loadFromAsciiSafeString(self::public_key));
	}
	
	/**
	 * Decrypt a message
	 *
	 * @param string $encrypted - message encrypted with safeEncrypt()
	 * @param string $key - encryption key
	 * @return string
	 */
	public static function safeDecrypt($encrypted)
	{
		return Crypto::decrypt($encrypted, Key::loadFromAsciiSafeString(self::public_key));
	}
}

?>