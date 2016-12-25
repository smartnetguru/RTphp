<?php

/**
 * @link https://github.com/rogertiongdev/RTphp RTphp GitHub project
 * @license https://rogertiongdev.github.io/MIT-License/
 */

namespace RTdev\RTphp;

/**
 * Collection or library of helper functions for common tasks.
 *
 * @version 0.1
 * @author Roger Tiong RTdev
 */
class RTutil {

    /**
     * Convert address to latitude and longitude
     *
     * @param array|string $address Address
     * @return array
     */
    public static function addr2LatLng($address) {

        if (is_array($address)) {

            $address = implode(', ', $address);
        }

        if (!empty($address)) {

            $url = sprintf('http://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false', str_replace(' ', '+', (string) $address));
            $output = json_decode(file_get_contents($url));

            return array(
                'latitude' => $output->results[0]->geometry->location->lat,
                'longitude' => $output->results[0]->geometry->location->lng
            );
        }
        return array('latitude' => 0, 'longitude' => 0);
    }

    /**
     * Convert latitude and longitude to address
     *
     * @param string $lat Latitude
     * @param string $lng Longitude
     * @return string
     */
    public static function latLng2Addr($lat, $lng) {

        $nlat = (string) $lat;
        $nlng = (string) $lng;

        if (!empty($nlat) && !empty($nlng)) {

            $url = sprintf('http://maps.googleapis.com/maps/api/geocode/json?latlng=%s,%s&sensor=false', trim($nlat), trim($nlng));
            $output = json_decode(file_get_contents($url));
            return ($output->status == 'OK') ? $output->results[1]->formatted_address : '';
        }
        return '';
    }

    /**
     * Check value is alphanumeric
     *
     * @param string $v Value to check
     * @return boolean
     */
    public static function isAlphanumeric($v) {

        return (is_string($v) && preg_match('/^[a-zA-Z ]*$/', $v));
    }

    /**
     * Check is value an email
     *
     * @param string $v Value to check
     * @return boolean
     */
    public static function isEmail($v) {

        return (is_string($v) && !empty($v) && filter_var($v, FILTER_VALIDATE_EMAIL));
    }

    /**
     * Check is value 1 end with value 2
     *
     * @param string $v1 Value 1
     * @param string $v2 Value 2
     * @return boolean
     */
    public static function isEndsWith($v1, $v2) {

        return (!is_string($v1) || !is_string($v2)) ? FALSE : (substr($v1, - strlen($v2)) === $v2);
    }

    /**
     * Check value is a valid Geo-location string
     *
     * @param string $v Geo-location string
     * @return boolean
     */
    public static function isGeoStr($v) {

        return (is_string($v) && preg_match('/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/', $v));
    }

    /**
     * Checks to see if the page is being server over SSL or not
     *
     * @param boolean $trust_proxy_headers True = allow proxy headers
     * @return boolean
     */
    public static function isHttps($trust_proxy_headers = FALSE) {

        // Check standard HTTPS header
        if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {

            return isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
        }
        // Check proxy headers if allowed
        return (boolean) $trust_proxy_headers && isset($_SERVER['X-FORWARDED-PROTO']) && $_SERVER['X-FORWARDED-PROTO'] == 'https';
    }

    /**
     * Check is value 1 start with value 2
     *
     * @param string $v1 Value 1
     * @param string $v2 Value 2
     * @return boolean
     */
    public static function isStartsWith($v1, $v2) {

        return (!is_string($v1) || !is_string($v2)) ? FALSE : (strpos($v1, $v2) === 0);
    }

    /**
     * Check is value 2 contains in value 1
     *
     * @param string $v1 Value 1
     * @param string $v2 Value 2
     * @param boolean $ignoreCase True = case insensitive
     * @return boolean
     */
    public static function isStrContains($v1, $v2, $ignoreCase = TRUE) {

        return (!is_string($v1) || !is_string($v2)) ? FALSE : ((boolean) $ignoreCase ? stripos($v1, $v2) !== FALSE : strpos($v1, $v2) !== FALSE);
    }

    /**
     * Check is value a URL
     *
     * @param string $v Value to check
     * @return boolean
     */
    public static function isUrl($v) {

        return (is_string($v) && preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $v));
    }

    /**
     * Convert contents in CSV to PHP array.<br>
     * Memory limit is based on php.ini
     *
     * @param string $fullFpath Full file path
     * @return array
     */
    public static function csv2Arr($fullFpath) {

        return ($r = file_get_contents($fullFpath)) ? array_map('str_getcsv', preg_split('/\r*\n+|\r+/', $r)) : array();
    }

    /**
     * Remove all white space and replace with single space.<br>
     * It can use as minifier for HTML, JS and CSS code display on view source URL.<br>
     * Avoid use '//' when using this function.<br>
     *
     * @param array|string $v Values to format
     * @return array|string
     */
    public static function force1Spc($v) {

        if (is_array($v)) {

            foreach ($v as $key => $element) {

                $v[$key] = self::force1Spc($element);
            }
            return $v;
        }
        else {
            return preg_replace('/\s+/', ' ', (string) $v);
        }
    }

    /**
     * Convert hex value to normal string
     *
     * @param string $hex Hex value to convert
     * @return string
     */
    public static function hex2Str($hex) {

        if (is_string($hex) && ctype_xdigit($hex)) {

            $str = '';
            $max = strlen($hex) - 1;

            for ($i = 0; $i < $max;) {

                $str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
                $i += 2;
            }
            return $str;
        }
        return $hex;
    }

    /**
     * Run self::str2Hex multiple times
     *
     * @param string $str String to convert
     * @param integer $multiply Number of loop
     * @return string
     */
    public static function multiStr2Hex($str, $multiply = 0) {

        $nstr = (string) $str;
        $max = (int) $multiply;

        for ($i = 0; ++$i <= $max;) {

            $nstr = self::str2Hex($nstr);
        }
        return $nstr;
    }

    /**
     * Format number to ordinal version
     *
     * @param integer $num Number to convert
     * @return string
     */
    public static function ordinal($num) {

        $nnum = (int) $num;
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        return ((($nnum % 100) >= 11) && (($nnum % 100) <= 13)) ? $nnum . 'th' : (($nnum) ? $nnum . $ends[$nnum % 10] : $nnum);
    }

    /**
     * Convert string to boolean
     *
     * @param string $str Value to convert
     * @param boolean $default Default result if fail
     * @return boolean
     */
    public static function str2Bool($str, $default = FALSE) {

        $nstr = (string) $str;
        $y = 'true|t|1|yeah|yes|y|okay|ok|ya|sure|agree|on|open|right|correct|known|success|positive|win|like|good|safe|do|does|have|approve';
        $n = 'false|f|0|nope|no|n|never|none|non|not|disagree|off|close|wrong|incorrect|unknown|fail|negative|lose|unlike|bad|unsafe|don\'t|doesn\'t|haven\'t|reject';
        return (preg_match('/^(' . $y . ')$/i', $nstr)) ? TRUE : (preg_match('/^(' . $n . ')$/i', $nstr) ? FALSE : (boolean) $default);
    }

    /**
     * Convert string to hex value
     *
     * @param string $str String to convert
     * @return string
     */
    public static function str2Hex($str) {

        $nstr = (string) $str;
        $hex = '';
        $max = strlen($nstr);

        for ($i = -1; ++$i < $max;) {

            $hex .= dechex(ord($nstr[$i]));
        }
        return $hex;
    }

    /**
     * Truncate a string to a specified length
     *
     * @param string $v Text to truncate
     * @param integer $len Length to truncate
     * @param string $append Text to append IF truncated
     * @param boolean $safemode True = truncate without cutting a word off
     * @return string
     */
    public static function truncate($v, $len = 0, $append = '...', $safemode = FALSE) {

        $nv = (string) $v;
        $nlen = (int) $len;

        if (strlen($nv) > $nlen) {

            if ((boolean) $safemode) {

                $last = strrpos($ret = substr($nv, 0, $nlen), ' ');
                $nret = ($last !== FALSE && $nv != $ret) ? substr($ret, 0, $last) : $ret;
                return ($nret != strlen($nv)) ? $nret .= $append : $nret;
            }
            return substr($nv, 0, ($nlen - strlen($append))) . $append;
        }
        return $nv;
    }

    /**
     * Calculates percentage of numerator and denominator.
     *
     * @param integer|float $numerator Numerator
     * @param integer|float $denominator Denominator
     * @param integer|float $decimals Decimal place
     * @param string $dec_point Custom decimal point
     * @param string $thousands_sep Custom thousand separator
     * @return integer
     */
    public static function calPercentage($numerator, $denominator, $decimals = 2, $dec_point = '.', $thousands_sep = ',') {

        return number_format(($numerator / $denominator) * 100, $decimals, $dec_point, $thousands_sep);
    }

    /**
     * Get visitor's IP address
     *
     * @return string
     */
    public static function getIP() {

        $ip = '';

        if ($_SERVER) {

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {

                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {

                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
            else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        }
        else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {

                $ip = getenv('HTTP_X_FORWARDED_FOR');
            }
            elseif (getenv('HTTP_CLIENT_IP')) {

                $ip = getenv('HTTP_CLIENT_IP');
            }
            else {
                $ip = getenv('REMOTE_ADDR');
            }
        }
        return $ip;
    }

    /**
     * Get the current URL
     *
     * @return string
     */
    public static function getCurrentURL() {

        $url = ($ishttps = self::isHttps()) ? 'https://' : 'http://';

        if (isset($_SERVER['PHP_AUTH_USER'])) {

            $url .= $_SERVER['PHP_AUTH_USER'];

            if (isset($_SERVER['PHP_AUTH_PW'])) {

                $url .= ':' . $_SERVER['PHP_AUTH_PW'];
            }
            $url .= '@';
        }
        $url .= $_SERVER['HTTP_HOST'];

        if (($ishttps && ($_SERVER['SERVER_PORT'] != 443)) || (!$ishttps && ($_SERVER['SERVER_PORT'] != 80))) {

            $url .= ':' . $_SERVER['SERVER_PORT'];
        }

        // Get the rest of the URL
        if (!isset($_SERVER['REQUEST_URI'])) {

            // Microsoft IIS doesn't set REQUEST_URI by default
            $url .= $_SERVER['PHP_SELF'];

            if (isset($_SERVER['QUERY_STRING'])) {

                $url .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
        else {
            $url .= $_SERVER['REQUEST_URI'];
        }
        return $url;
    }

    /**
     * Get date(s) range in array format
     *
     * @param integer|string $from Start date
     * @param integer|string $to End date
     * @param integer $adjust Value to adjust for per step (between elements)
     * @param string $format Date format to return
     * @return array
     */
    public static function getDateRange($from, $to, $adjust = 1, $format = 'Y-m-d') {

        $nfrom = (is_numeric($from) || is_float($from) || is_double($from)) ? (int) $from : strtotime($from);
        $nto = (is_numeric($to) || is_float($to) || is_double($to)) ? (int) $to : strtotime($to);

        if ($nfrom > $nto) {

            $nfrom ^= $nto ^= $nfrom ^= $nto; // Swap $nfrom and $nto
        }

        if (!is_string($format) || empty($format)) {

            $format = 'Y-m-d';
        }
        $dates = array();
        $step = '+' . ((int) $adjust == 0 ? 1 : (int) $adjust) . ' day';

        for (; $nfrom <= $nto;) {

            $dates[] = date($format, $nfrom);
            $nfrom = strtotime($step, $nfrom);
        }
        return $dates;
    }

    /**
     * Get file extension
     *
     * @param string $filename File name
     * @return string
     */
    public static function getFileExt($filename) {

        return pathinfo((string) $filename, PATHINFO_EXTENSION);
    }

    /**
     * Get visitor information<br>
     * Used API useragentstring.com and geoplugin.net
     *
     * @return array
     * @api
     */
    public static function getVisitorInfo() {

        $ip = self::getIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $agent = json_decode(file_get_contents(sprintf('http://www.useragentstring.com/?uas=%s&getJSON=all', urlencode($userAgent))), TRUE);
        $geoip = unserialize(file_get_contents(sprintf('http://www.geoplugin.net/php.gp?ip=%s', $ip)));

        return array(
            'ip' => $ip,
            'agent_type' => $agent['agent_type'],
            'agent_name' => $agent['agent_name'],
            'agent_version' => $agent['agent_version'],
            'os' => sprintf('%s %s', $agent['os_name'], str_replace('_', '.', $agent['os_versionNumber'])),
            'agent_full' => $userAgent,
            'referer' => $_SERVER['HTTP_REFERER'],
            'lat' => $geoip['geoplugin_latitude'],
            'long' => $geoip['geoplugin_longitude'],
            'country_code' => $geoip['geoplugin_countryCode'],
            'country' => $geoip['geoplugin_countryName'],
            'region' => $geoip['geoplugin_regionName'],
            'city' => $geoip['geoplugin_city'],
            'currency_code' => $geoip['geoplugin_currencyCode'],
            'currency_symbol' => $geoip['geoplugin_currencySymbol'],
            'currency_converter' => $geoip['geoplugin_currencyConverter'],
            'address' => self::latLng2Addr($geoip['geoplugin_latitude'], $geoip['geoplugin_longitude'])
        );
    }

    /**
     * Get different of 2 date time in human understand format
     *
     * @param integer|string $from Start datetime
     * @param integer|string $to End datetime
     * @param string $sep Separator
     * @param string $ends_with String to add to the end
     * @param integer $precision Precision. Example for $precision = 2, 1 hour 30 minutes
     * @return string
     */
    public static function humanTimeDiff($from, $to, $sep = ' ', $ends_with = '', $precision = 6) {

        $nfrom = (is_numeric($from) || is_float($from) || is_double($from)) ? (int) $from : strtotime($from);
        $nto = (is_numeric($to) || is_float($to) || is_double($to)) ? (int) $to : strtotime($to);

        if ($nfrom > $nto) {

            $nfrom ^= $nto ^= $nfrom ^= $nto; // Swap $nfrom and $nto
        }
        $nprecision = ((int) $precision < 0) ? 1 : (int) $precision;

        $intervals = array(
            'year',
            'month',
            'day',
            'hour',
            'minute',
            'second'
        );
        $times = $diffs = array();
        $count = 0;

        foreach ($intervals as $interval) {

            $time = strtotime('+1 ' . $interval, $nfrom);

            for ($add = 1, $looped = 0; $nto >= $time;) {

                $time = strtotime('+' . ++$add . ' ' . $interval, $nfrom);
                $looped++;
            }
            $nfrom = strtotime('+' . $looped . ' ' . $interval, $nfrom);
            $diffs[$interval] = $looped;
        }

        foreach ($diffs as $interval => $value) {

            if ($count >= $nprecision) {

                break;
            }
            if ($value > 0) {

                $times[] = $value . ' ' . ($value != 1 ? $interval .= 's' : $interval);
                $count++;
            }
        }
        return implode((string) $sep, $times) . (string) $ends_with;
    }

    /**
     * Get random password
     *
     * @param integer $level Level of password. 0-5
     * @param integer $len Length of password (minimum 1)
     * @return string
     */
    public static function randPword($level = 1, $len = 6) {

        $nlevel = (int) $level;
        $nlen = ((int) $len <= 0) ? 1 : (int) $len;
        $vowels = 'aeuy';
        $consonants = '0123456789bdghjmnpqrstvz';

        if ($nlevel <= 0) {

            return str_pad(rand(1, 999999), $len, '0', STR_PAD_LEFT);
        }
        if ($nlevel >= 2) {

            $consonants .= '0123456789BDGHJLMNPQRSTVWXZ';
        }
        if ($nlevel >= 3) {

            $vowels .= 'AEUY';
        }
        if ($nlevel >= 4) {

            $consonants .= '23456789';
        }
        if ($nlevel >= 5) {

            $consonants .= '@#$%';
        }
        $password = '';
        $alt = time() % 2;

        for ($i = -1; ++$i < $nlen;) {

            if ($alt == 1) {

                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            }
            else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }
        return $password;
    }

    /**
     * Shuffle elements in array
     *
     * @param array $array Array to shuffle
     * @param integer $multiply Number to shuffle (loop)
     * @return array
     */
    public static function shuffleArr($array, $multiply = 1) {

        if (is_array($array)) {

            $multiply = (int) $multiply;

            for ($i = 0; ++$i <= $multiply;) {

                shuffle($array);
            }
        }
        return $array;
    }

    /**
     * Sort 1D datetime array
     *
     * @param array $data List to sort
     * @param string $order ASC or DESC
     * @return array
     */
    public static function sortDateTime($data, $order = 'ASC') {

        if (!is_array($data)) {

            trigger_error(sprintf('%s Expects parameter 1 to be array. %s given.', __METHOD__, gettype($data)));
            return $data;
        }

        if ((string) $order == 'DESC') {

            usort(self::rtarray_unique($data), function($a, $b) {

                return strtotime($b) - strtotime($a);
            });
        }
        else {
            usort(self::rtarray_unique($data), function($a, $b) {

                return strtotime($a) - strtotime($b);
            });
        }
        return $data;
    }

    /**
     * Logger
     *
     * @param string $msg Message
     * @param string $file Filename
     */
    public static function logger($msg, $file = '__log') {

        $fp = fopen((empty($file) ? '__log' : (string) $file), 'a');
        fwrite($fp, sprintf('[%s  Asia/Kuala_Lumpur] %s', date('d-M-Y H:i:s'), (string) $msg));
        fclose($fp);
    }

    /**
     * Returns the values from a single column of the input array, identified by the $colKey.
     *
     * @param array $data Data array
     * @param mixed $colKey Column key
     * @param mixed $indexKey Index key
     * @return array
     */
    public static function rtarray_column($data = NULL, $colKey = NULL, $indexKey = NULL) {

        if (version_compare(PHP_VERSION, '5.5.0,', '>=') || function_exists('array_column')) {

            return array_column($data, $colKey, $indexKey);
        }

        if (!is_array($data) || (!is_int($colKey) && !is_float($colKey) && !is_string($colKey) && $colKey !== NULL && !(is_object($colKey) && method_exists($colKey, '__toString'))) || (isset($indexKey) && !is_int($indexKey) && !is_float($indexKey) && !is_string($indexKey) && !(is_object($indexKey) && method_exists($indexKey, '__toString')))) {

            $msg = '%s Expects parameter 1 to be array, parameter 2 to be string or int and parameter 3 to be NULL, string or int. %s, %s, %s given.';
            trigger_error(sprintf($msg, __METHOD__, gettype($data), gettype($colKey), gettype($indexKey)));
            return array();
        }
        $params = $data;
        $colParams = ($colKey !== NULL) ? (string) $colKey : NULL;
        $indexKeyParams = NULL;

        if (isset($indexKey)) {

            $indexKeyParams = (is_float($indexKey) || is_int($indexKey)) ? (int) $indexKey : (string) $indexKey;
        }
        $result = array();

        foreach ($params as $row) {

            $key = $value = NULL;
            $keySet = $valueSet = FALSE;

            if ($indexKeyParams !== NULL && array_key_exists($indexKeyParams, $row)) {

                $keySet = TRUE;
                $key = (string) $row[$indexKeyParams];
            }

            if ($colParams === NULL) {

                $valueSet = TRUE;
                $value = $row;
            }
            elseif (is_array($row) && array_key_exists($colParams, $row)) {

                $valueSet = TRUE;
                $value = $row[$colParams];
            }

            if ($valueSet) {

                ($keySet) ? $result[$key] = $value : $result[] = $value;
            }
        }
        return $result;
    }

    /**
     * Filter array by key
     *
     * @param array $array Array
     * @param callback $callback Callback function
     * @return array
     */
    public static function rtarray_filter_key($array, $callback) {

        if (!is_array($array) || !is_callable($callback)) {

            trigger_error(sprintf('%s Expects parameter 1 to be array and parameter 2 to be Callable function', __METHOD__));
            return $array;
        }
        return array_intersect_key($array, array_flip(array_filter(array_keys($array), $callback)));
    }

    /**
     * Flatten a multi-D array into a 1D array
     *
     * @param array $array Array to convert
     * @param boolean $preserve_k True = preserve array keys
     * @return array
     */
    public static function rtarray_flatten(array $array, $preserve_k = TRUE) {

        $npreserve_k = (boolean) $preserve_k;
        $flattened = array();

        array_walk_recursive($array, function($v, $k) use (&$flattened, $npreserve_k) {

            if ($npreserve_k && !is_int($k)) {

                $flattened[$k] = $v;
            }
            else {
                $flattened[] = $v;
            }
        });
        return $flattened;
    }

    /**
     * Faster version on array unique
     *
     * @param array $array Array to format
     * @return array
     */
    public static function rtarray_unique($array) {

        return (is_array($array)) ? array_keys(array_flip($array)) : $array;
    }

}
