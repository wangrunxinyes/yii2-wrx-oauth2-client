<?php

namespace wangrunxinyes\OAuth;

/**
 * Class User.
 *
 * @property string $owner_id    Xenforo User ID
 * @property string $owner_type
 * @property string $access_token
 * @property int    $client_id
 * @property string $shotbow_id  Shotbow User Identifier
 * @property string $username    
 * @property array  $scopes      Scope information
 */
class User implements \ArrayAccess
{
    private $container;

    public function __construct(array $array)
    {
        $this->container = $array;
    }

    public function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    public function __set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    public function __isset($offset)
    {
        return $this->offsetExists($offset);
    }

    public function __unset($offset)
    {
        $this->offsetUnset($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}
