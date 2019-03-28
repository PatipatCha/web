<?php
/**
 * Created by PhpStorm.
 * User: kinkop
 * Date: 5/9/2017 AD
 * Time: 9:46 AM
 */

namespace App\Bootstrap\Helpers;


class AuthHelper
{
    protected static $rememberDays = 356;

    public static function user($updateData = true)
    {
        $user = null;
        $sessionUser = session()->get('makroclickMember', null);
        $mustUpdate = false;

        if (is_array($sessionUser)) {
            $user = $sessionUser;

            if ($updateData && empty($user['profile']['customer_channel'])) {
                $mustUpdate = true;
            }

            if ($updateData && ! isset($user['profile']['makro_card_id'])) {
                $mustUpdate = true;
            }
        }

        if (empty($user) && isset($_COOKIE['_makroclickMember'])) { // log in แบบ remember จะมี cookie ถ้า session หลุดแล้ว ก็ต้องมาดึงจาก cookie แทน
            try {
                $cookieUser = decrypt($_COOKIE['_makroclickMember']);
                $cookieUser = json_decode($cookieUser, true);

                if (is_array($cookieUser)) {
                    $user = $cookieUser;

                    if ($updateData && empty($user['profile']['customer_channel'])) {
                        $mustUpdate = true;
                    }

                    if ($updateData && ! isset($user['profile']['makro_card_id'])) {
                        $mustUpdate = true;
                    }
                }
            } catch (\Exception $e) {

            }
        }

        if (! empty($user) && $mustUpdate) {
            try {
                $makroSdk = app()->make('makroSdk');
                $profile = $makroSdk->member()->profile();
                $user = self::updateUserFromProfile($profile);
            } catch (\Exception $e) {

            }
        }

        return $user;
    }

    public static function getTokenId()
    {
        $user = static::user();
        if (! empty($user['token_id'])) {
            return $user['token_id'];
        }

        return '';
    }

    public static function tempMemberId()
    {
        if (!session()->has('makroclickTempMemberId')) {
            static::createTempMemberId();
        }

        return session()->get('makroclickTempMemberId');
    }

    public static function createTempMemberId()
    {
        $key = uniqid(md5('makroclick'));
        session()->put('makroclickTempMemberId', $key);
    }

    public static function getMemberId($useTempIfNotLogin = true)
    {
        $user = static::user();
        if (! $user) {
            if ($useTempIfNotLogin) {
                return static::tempMemberId();
            }

            return null;
        }

        return $user['id'];
    }

    public static function login($user, $remember = false)
    {
        static::storeInSession($user);

        if ($remember) {
            self::storeInCookie($user);
        }
    }

    public static function logout()
    {
        session()->forget('makroclickMember');
        setcookie('_makroclickMember', null, -1, '/', config('session.domain'), config('session.secure'));

        if (isset($_COOKIE['_makroclickMember'])) {
            unset($_COOKIE['_makroclickMember']);
        }
    }

    protected static function storeInSession($user)
    {
        session()->put('makroclickMember', $user);
    }

    public static function storeInCookie($user)
    {
        $makroCardId = '';
        if (! empty(array_get($user, 'profile.makro_member_card'))) {
            $makroCardId = array_get($user, 'profile.makro_member_card');
        } else if (! empty(array_get($user, 'profile.makro_card_id'))) {
            $makroCardId = array_get($user, 'profile.makro_card_id');
        }

        // ถ้าจะเพิ่ม data ก็เพิ่มได้
        // แต่ต้อง check ด้วยว่า cookie ถุก set มั้ย
        // เพราะที่ต้องมาทำแบบนี้เนื่องจาก encrypt แล้ว string ยาวเกินไป ทำให้ cookie ไม่ถูก set
        $cookieUser = [
            'id' => $user['id'],
            'username' => $user['username'],
            'token_id' => $user['token_id'],
            'access_token' => $user['access_token'],
            'profile' => [
                'first_name' => array_get($user, 'profile.first_name'),
                'last_name' => array_get($user, 'profile.last_name'),
                'accept_delivery_service' => array_get($user, 'profile.accept_delivery_service', 'n'),
                'makro_card_id' => $makroCardId,
                'customer_channel' => array_get($user, 'profile.customer_channel', array_get($user, 'customer_channel')),
            ]
        ];

        $day = 60 * 60 * 24;
        $data = encrypt(json_encode($cookieUser));
        $expire = time() + ($day * static::$rememberDays);
        setcookie('_makroclickMember', $data, $expire, '/', config('session.domain'), config('session.secure'));
    }

    // เพิ่ออัพเดตข้อมูลของ member ที่ login ให้เป็นข้อมูลใหม่
    // จะเรียกใช้เฉพาะเมื่อมีการ call sdk member profile ()
    public static function updateUserFromProfile($profile)
    {
        $user = self::user(false);

        if (! empty($user)) {
            $user['profile']['makro_card_id'] = $profile['makro_card_id'];
            $user['profile']['customer_channel'] = $profile['customer_channel'];

//        if (isset($_COOKIE['_makroclickMember'])) {
//            self::storeInCookie($user); // เขียนลง cookie แบบนี้ทำให้เกิดปัญหาที่ server 502 bad request จึงไม่ต้องไปอัพเดต
//        }

            static::storeInSession($user);
        }

        

        return $user;
    }
}