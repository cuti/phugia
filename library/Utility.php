<?php

class Utility
{
    /**
     * Generate alpha numeric secret.
     *
     * @param  int $length    Secret length.
     * @return string
     */
    public static function generateSecret($length)
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($permitted_chars), 0, $length);
    }

    /**
     * Get cache object
     *
     * @return Zend_Cache_Core
     */
    public static function getCache()
    {
        $frontendOptions = array(
            'lifetime' => 7200, // cache lifetime of 2 hours
            'automatic_serialization' => true,
        );

        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH . '/cache/',
        );


        if (!file_exists(APPLICATION_PATH . '/cache')) {
            mkdir(APPLICATION_PATH . '/cache');
        }

        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

        return $cache;
    }
}
