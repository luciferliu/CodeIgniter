<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('zhjson')) {
    /**
     * 中文json_encode
     * @param $v
     * @return array|string
     */
    function zhjson($v) {
        if (is_array($v)) {
            foreach ($v as $key => $value) {
                $v[$key] = zhjson($value);
            }
            return $v;
        } else {
            return preg_match('//u', $v) ? $v : iconv('gbk', 'utf-8//IGNORE', $v);
        }
    }
}


if (!function_exists('gbk_json_encode')) {
    /**
     * gbk json 编码函数
     * @param array $arr
     * @return string
     */
    function gbkJsonEncode($arr) {
        $arr = zhjson($arr);
        $str = json_encode($arr);
        return $str;
    }
}


if (!function_exists('mkdirs')) {
    /**
     * 根据路径建立多级目录
     * @param string $dir
     * @param int $mode
     * @return bool
     */
    function mkdirs($dir, $mode = 0777) {
        if (!is_dir($dir)) {
            if (!mkdirs(dirname($dir), $mode)) {
                return false;
            }
            if (!mkdir($dir, $mode)) {
                return false;
            }
        }
        return true;
    }
}


if (!function_exists('safe_file_rewrite')) {
    /**
     * 安全的覆盖写文件
     * @param unknown_type $file_name
     * @param unknown_type $data_to_save
     */
    function safe_file_rewrite($file_name, $data_to_save) {
        if ($fp = fopen($file_name, 'w')) {
            $startTime = microtime(true);
            do {
                $canWrite = flock($fp, LOCK_EX);
                // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                if (!$canWrite) {
                    usleep(round(rand(0, 100) * 1000));
                }
            } while ((!$canWrite) and ((microtime(true) - $startTime) < 5));

            //file was locked so now we can store information
            if ($canWrite) {
                fwrite($fp, $data_to_save);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }
    }
}


/**
 * 格式化字符串
 */
if (!function_exists('explodeAndTrim')) {
    function explodeAndTrim($delimiter, $string) {
        if(empty($string)) {
            return array();
        }
        $values = explode($delimiter, $string);
        $values = array_unique($values);
        foreach($values as $i=>$value) {
            $value = trim($value);
            if ($value) {
                $values[$i] = $value;
            }
        }
        return $values;
    }
}


if (!function_exists('explodeAndIntval')) {
    function explodeAndIntval($delimiter, $string) {
        if(empty($string)) {
            return array();
        }
        $values = explode($delimiter, $string);
        $values = array_unique($values);
        foreach($values as $i=>$value) {
            $value = trim($value);
            if ($value) {
                $values[$i] = intval($value);
            }
        }
        return $values;
    }
}


/**
 * 随机产生N个字符
 * @version V1.0R02
 * @date 2012-9-10
 *
 * @param 生成的字符串长度 $length
 */
if (!function_exists('randomkeys')) {
    function randomkeys($length=10)
    {
        $pattern='1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key = '';
        for(;strlen($key)<$length;)
        {
            $key .= $pattern{mt_rand(0,60)};    //生成php随机数
        }
        return $key;
    }
}


if (!function_exists('guid')) {
    function guid( $opt = true ){       //  Set to true/false as your default way to do this.
        mt_srand( (double)microtime() * 10000 );    // optional for php 4.2.0 and up.
        $charid = strtoupper( md5(uniqid(rand(), true)) );
        $hyphen = chr( 45 );    // "-"
        $left_curly = $opt ? chr(123) : "";     //  "{"
        $right_curly = $opt ? chr(125) : "";    //  "}"
        $uuid = $left_curly
        . substr( $charid, 0, 8 ) . $hyphen
        . substr( $charid, 8, 4 ) . $hyphen
        . substr( $charid, 12, 4 ) . $hyphen
        . substr( $charid, 16, 4 ) . $hyphen
        . substr( $charid, 20, 12 )
        . $right_curly;
        return $uuid;
    }
}


if (!function_exists('json_encode_jp')) {
    function json_encode_jp($array) {
        return preg_replace_callback(
                '/\\\\u([0-9a-zA-Z]{4})/',
                function ($matches) {
                    return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');
                },
                json_encode($array)
        );
    }
}


if (!function_exists('startsWith')) {
    function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the
        // end
        return $needle === "" ||
            strrpos($haystack, $needle, - strlen($haystack)) !== FALSE;
    }
}


if (!function_exists('getallheaders')) {
    function getallheaders() {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}


if (!function_exists('Str2Boolean')) {
    function Str2Boolean($value) {
        if (empty($value)) {
            return null;
        }
        if (strtolower($value) === 'false') {
            return false;
        } else if (strtolower($value) === 'true') {
            return true;
        } else {
            return null;
        }
    }
}



if (!function_exists('boolean2Str')) {
    function boolean2Str($value) {
        if (empty($value)) {
            return 'false';
        } else {
            return 'true';
        }
    }
}


if (!function_exists('getIP')) {
    function getIP()
    {
        if ($_SERVER['REMOTE_ADDR']) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } elseif ($_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ($_SERVER['HTTP_CLIENT_IP']) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else {
            $ip = '';
        }
        return $ip;
    }
}


if (!function_exists('username2List')) {
    function username2List($username) {
        $usernames = trim(trim($username), ';');
        $usernameList = explode(';', $usernames);
        return $usernameList;
    }
}

