<?php

namespace MakroSdk;

use MakroSdk\Exceptions\SDKException;

class MakroSdk extends ClientAbstract
{
    static $instance;

    public function __construct(array $config = array())
    {
        parent::__construct($config);

        static::$instance = array();
    }

    public function __call($name, $arguments)
    {
        if (empty(static::$instance[$name])) {
            if ($name == 'product') {
                static::$instance[$name] = new Product($this);
            } elseif ($name == 'member') {
                static::$instance[$name] = new Member($this);
            } elseif ($name == 'brand') {
                static::$instance[$name] = new Brand($this);
            } elseif ($name == 'cart') {
                static::$instance[$name] = new Cart($this);
            } elseif ($name == 'favorite') {
                static::$instance[$name] = new Favorite($this);
            } elseif ($name == 'category') {
                static::$instance[$name] = new Category($this);
            } elseif ($name == 'store') {
                static::$instance[$name] = new Store($this);
            } elseif ($name == 'gateway') {
                static::$instance[$name] = new Gateway($this);
            } elseif ($name == 'content') {
                static::$instance[$name] = new Content($this);
            } elseif ($name == 'group') {
                static::$instance[$name] = new Group($this);
            } elseif ($name == 'campaign') {
                static::$instance[$name] = new Campaign($this);
            } elseif ($name == 'address') {
                static::$instance[$name] = new Address($this);
            } elseif ($name == 'order') {
                static::$instance[$name] = new Order($this);
            } elseif ($name == 'notification') {
                static::$instance[$name] = new Notification($this);
            } elseif ($name == 'subscribe') {
                static::$instance[$name] = new Subscribe($this);
            } elseif ($name == 'memberAddress') {
                static::$instance[$name] = new MemberAddress($this);
            } elseif ($name == 'resetPassword') {
                static::$instance[$name] = new ResetPassword($this);
            } elseif ($name == 'banner') {
                static::$instance[$name] = new Banner($this);
            } elseif ($name == 'config') {
                static::$instance[$name] = new Config($this);
            } elseif ($name == 'coupon') {
                static::$instance[$name] = new Coupon($this);
            } else {
                throw new SDKException("Method({$name}) does not exist.");
            }
        }

        return static::$instance[$name];
    }
}
