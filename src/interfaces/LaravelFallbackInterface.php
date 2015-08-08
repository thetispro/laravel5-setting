<?php namespace Thetispro\Setting\interfaces;


/**
 * Class LaravelFallbackInterface
 * @package Thetispro\Setting\interfaces
 */
class LaravelFallbackInterface implements FallbackInterface {

    /**
     * @param $key
     * @return mixed
     */
    public function fallbackGet($key, $default = null)
    {
        return \App::make('config')->get($key, $default);
    }

    /**
     * @param $key
     * @return bool
     */
    public function fallbackHas($key)
    {
        $settingExists = \App::make('config')->has($key);

        $setting = \App::make('config')->get($key);
        if (is_array($setting) and count($setting) == 0) {
            return false;
        }

        return $settingExists;
    }
}
