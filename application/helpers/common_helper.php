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
