<?php

/**
 * @link https://github.com/rogertiongdev/RTphp RTphp GitHub project
 * @license https://rogertiongdev.github.io/MIT-License/
 *
 * @reference https://github.com/php/php-src/tree/master/ext/standard
 * @reference https://github.com/php/php-src/blob/master/ext/standard/crypt_blowfish.c
 * @reference https://github.com/php/php-src/blob/master/ext/standard/password.c
 * @reference http://php.net/manual/en/ref.password.php
 */

namespace RTdev\RTphp;

/**
 * Similar as PHP 5.5 Password functions. But this class only apply Bcrypt.
 *
 * @version 0.1
 * @author Roger Tiong RTdev
 */
class RTpassword {

    CONST BCRYPT_LEN = 60;
    CONST BCRYPT_NAME = 'bcrypt';
    CONST BCRYPT_IDENTIFIER = '$2y$';
    CONST BCRYPT_DEFAULT_COST = 10;
    CONST BCRYPT_REQ_SALT_LEN = 22;
    CONST BCRYPT_RAW_SALT_LEN = 16;
    CONST BASE_64_STR = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    CONST BF_ITOA_64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    /**
     * Test current PHP environment is able to run this class or NOT.
     *
     * @param string $password
     * @return boolean
     */
    public function test($password = 'password4testing') {

        return ($this->isCryptLoaded() && ($hash = $this->bcryptHash($password))) ? $this->verify($password, $hash) : FALSE;
    }

    /**
     * Verifies that a password matches a hash.
     *
     * @param string $password
     * @param string $hash
     * @return boolean
     */
    public function verify($password, $hash) {

        if (!$this->isCryptLoaded()) {

            return FALSE;
        }

        $npassword = (string) $password;
        $nhash = (string) $hash;

        if (!$this->isCryptLoaded() || !is_string($ret = crypt($npassword, $nhash)) || $this->binStrLen($ret) != $this->binStrLen($nhash) || $this->binStrLen($ret) <= 13) {

            return FALSE;
        }

        $status = 0;

        for ($i = -1; ++$i < $this->binStrLen($ret);) {

            $status |= (ord($ret[$i]) ^ ord($nhash[$i]));
        }

        return $status === 0;
    }

    /**
     * Creates a password hash.
     *
     * @param string $password
     * @param string $cost
     * @return boolean
     */
    public function bcryptHash($password, $cost = self::BCRYPT_DEFAULT_COST) {

        if (!$this->isCryptLoaded()) {

            return FALSE;
        }

        $npassword = (string) $password;
        $ncost = (int) $cost;

        if ($ncost < 4 || $ncost > 31) {

            trigger_error(sprintf('%s Expects cost is between 4 and 31. %d given. Default cost (10) will be apply.', __METHOD__, $ncost));
            $ncost = self::BCRYPT_DEFAULT_COST;
        }

        $hash = sprintf(self::BCRYPT_IDENTIFIER . '%02d$', $ncost) . $this->subBinStr(strtr(rtrim(base64_encode($this->getSalt()), '='), self::BASE_64_STR, self::BF_ITOA_64), 0, self::BCRYPT_REQ_SALT_LEN);
        $ret = crypt($npassword, $hash);

        return (is_string($ret) && $this->binStrLen($ret) == self::BCRYPT_LEN) ? $ret : FALSE;
    }

    /**
     * Returns information about the given hash.
     *
     * @param string $hash
     * @return array
     */
    public function getHashInfo($hash) {

        $nhash = (string) $hash;

        if ($this->subBinStr($nhash, 0, 4) == self::BCRYPT_IDENTIFIER && $this->binStrLen($nhash) == self::BCRYPT_LEN) {

            list($cost) = sscanf($nhash, self::BCRYPT_IDENTIFIER . '%d$');
            return array('algoName' => self::BCRYPT_NAME, 'cost' => $cost);
        }

        return array('algoName' => 'unknown', 'cost' => 0);
    }

    /**
     * Checks if the given hash matches the given options.
     *
     * @param string $hash
     * @param integer $cost
     * @return boolean
     */
    public function isNeedReHash($hash, $cost = self::BCRYPT_DEFAULT_COST) {

        $info = $this->getHashInfo((string) $hash);

        return ((int) $cost !== $info['cost']);
    }

    /**
     * Get length of given binary string
     *
     * @param string $binStr
     * @return integer
     */
    protected function binStrLen($binStr) {

        return (function_exists('mb_strlen')) ? mb_strlen($binStr, '8bit') : strlen($binStr);
    }

    /**
     * Sub binary string
     *
     * @param string $binStr Binary string
     * @param integer $start
     * @param integer $len
     * @return string
     */
    protected function subBinStr($binStr, $start, $len) {

        return (function_exists('mb_substr')) ? mb_substr($binStr, $start, $len, '8bit') : substr($binStr, $start, $len);
    }

    /**
     * Get random salt
     *
     * @param integer $saltLen Salt length
     * @return string
     */
    protected function getSalt($saltLen = self::BCRYPT_RAW_SALT_LEN) {

        $nsalt = '';
        $valid = FALSE;
        $nsaltLen = (int) $saltLen;

        if (function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {

            $valid = ($nsalt = mcrypt_create_iv($nsaltLen, MCRYPT_DEV_URANDOM) == TRUE);
        }

        if (!$valid && function_exists('openssl_random_pseudo_bytes')) {

            $strong = FALSE;
            $valid = ($nsalt = (openssl_random_pseudo_bytes($nsaltLen, $strong) && $strong) == TRUE);
        }

        if (!$valid && @is_readable('/dev/urandom')) {

            $file = fopen('/dev/urandom', 'r');
            $read = 0;
            $localBuffer = '';

            while ($read < $nsaltLen) {

                $read = $this->binStrLen($localBuffer .= fread($file, $nsaltLen - $read));
            }

            fclose($file);

            $valid = ($read >= $nsaltLen);
            $nsalt = str_pad($nsalt, $nsaltLen, '\0') ^ str_pad($localBuffer, $nsaltLen, '\0');
        }

        if (!$valid || $this->binStrLen($nsalt) < $nsaltLen) {

            $max = $this->binStrLen($nsalt);

            for ($i = -1; ++$i < $nsaltLen;) {

                ($i < $max) ? @$nsalt[$i] = @$nsalt[$i] ^ chr(mt_rand(0, 255)) : $nsalt .= chr(mt_rand(0, 255));
            }
        }

        return $nsalt;
    }

    /**
     * Check is PHP function: crypt loaded
     *
     * @return boolean
     */
    protected function isCryptLoaded() {

        if (!function_exists('crypt')) {

            trigger_error(sprintf('%s Expects crypt loaded.', __METHOD__));
            return FALSE;
        }
        return TRUE;
    }

}
