<?php
namespace wangrunxinyes\OAuth\models;

use yii\db\ActiveRecord;
use yii\base\UnknownPropertyException;

/**
 * Class User.
 *
 * @property integer id
 * @property string extensions
 * @property string username
 * @property string photo
 * @property integer sex
 * @property string open_id
 * @property string client_user_id
 * @property string access_token
 * @property integer expires_at
 */
class User extends ActiveRecord
{

    protected $ext_data = [];

    /**
     * Declares the name of the database table associated with this AR class.
     * By default this method returns the class name as the table name by calling [[Inflector::camel2id()]]
     * with prefix [[Connection::tablePrefix]]. For example if [[Connection::tablePrefix]] is `tbl_`,
     * `Customer` becomes `tbl_customer`, and `OrderItem` becomes `tbl_order_item`. You may override this method
     * if the table is not named after this convention.
     *
     * @return string the table name
     */
    public static function tableName()
    {
        return '{{%oauth_wrx_users}}';
    }

    /**
     * Sets the attribute values in a massive way.
     *
     * @param array $values
     *            attribute values (name => value) to be assigned to the model.
     * @param bool $safeOnly
     *            whether the assignments should only be done to the safe attributes.
     *            A safe attribute is one that is associated with a validation rule in the current [[scenario]].
     * @see safeAttributes()
     * @see attributes()
     */
    public function setAttributes($values, $safeOnly = true)
    {
        if (is_array($values)) {
            $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
            foreach ($values as $name => $value) {
                $this->$name = $value;
            }
        }
    }

    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $e) {
            $ext_data = $this->getExtData();
            if (! isset($ext_data[$name])) {
                throw $e;
            }
            
            return $ext_data[$name];
        }
    }

    public function getExtData($refresh = false)
    {
        if (count($this->ext_data) == 0 || $refresh) {
            $this->ext_data = json_decode($this->extensions, true);
        }
        
        return $this->ext_data;
    }

    public function __set($name, $value)
    {
        try {
            return parent::__set($name, $value);
        } catch (UnknownPropertyException $e) {
            $this->setToExtensions($name, $value);
        }
    }

    public function getAccesstoken()
    {
        return unserialize($this->access_token);
    }

    public function setToExtensions($name, $value)
    {
        $data = json_decode($this->extensions, true);
        if (! is_array($data)) {
            $data = [];
        }
        $data[$name] = $value;
        $this->extensions = json_encode($data);
    }
}
