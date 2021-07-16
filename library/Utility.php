<?php

class Utility
{
    /**
     * Generate alpha numeric secret.
     *
     * @param  int      $length     Secret length.
     * @return string   New secret.
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

    /**
     * Save uploaded file to disk and return the full path to the newly created file.
     *
     * @param  string       $fileName         Original file name.
     * @param  string       $content          File content.
     * @param  string       $directoryName    Subdirectory to save the file into.
     * @param  string       $username         Current username.
     * @return array|null   The array of full path to the saved file and file extension (if any).
     */
    public static function saveFile($fileName, $content, $directoryName, $username)
    {
        $fNameParts = explode('.', $fileName);

        if (count($fNameParts) > 1) {
            $fExt = '.' . array_pop($fNameParts);
        } else {
            $fExt = '';
        }

        $fName = implode('.', $fNameParts) . '_' . $username . '_' . date('Ymd_His');
        $dir = UPLOAD_PATH . '/' . $directoryName;

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $fullPath = $dir . '/' . $fName . $fExt;
        $writeSuccess = file_put_contents($fullPath, $content);

        if ($writeSuccess) {
            return array(
                'path' => $fullPath,
                'ext'  => $fExt
            );
        } else {
            return null;
        }
    }
}
