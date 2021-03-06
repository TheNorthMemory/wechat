<?php

/**
 * AbstractMessage.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Message;

use EasyWeChat\Support\Arr;
use EasyWeChat\Support\Attribute;

/**
 * Class AbstractMessage.
 */
abstract class AbstractMessage extends Attribute
{
    /**
     * Message id.
     *
     * @var int
     */
    protected $id;

    /**
     * Message target user open id.
     *
     * @var string
     */
    protected $to;

    /**
     * Message sender open id.
     *
     * @var string
     */
    protected $from;

    /**
     * Message attributes.
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct(Arr::only($attributes, $this->properties));
    }

    /**
     * Return message type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Magic call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return AbstractMessage
     */
    public function __call($method, $args)
    {
        if (property_exists($this, $method)) {
            $method = 'set';
            array_unshift($args, $method);
        }

        return call_user_func_array([$this, 'set'], $args);
    }

    /**
     * Magic getter.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return parent::__get($property);
    }

    /**
     * Magic setter.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return AbstractMessage
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            parent::__set($property, $value);
        }

        return $this;
    }
}
