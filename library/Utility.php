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
}
