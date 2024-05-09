<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 3:24 PM
 */

namespace common\helpers;


class Common
{
    const PI = 3.1415926535898;
    const EARTH_RADIUS=6378.137;

    public static function getRealIP()
    {
        $ip = '';
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_FROM', 'REMOTE_ADDR') as $v) {
            if (isset($_SERVER[$v])) {
                if (! preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $_SERVER[$v])) {
                    continue;
                }
                $ip = $_SERVER[$v];
                break;
            }
        }

        return $ip;
    }

    public static function chooseSystem() {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if(strpos($agent, 'iphone') || strpos($agent, 'ipad')){
            return 'ios';
        }elseif(strpos($agent, 'micromessenger')){
            return 'wechat';
        }elseif(strpos($agent, 'android')){
            return 'android';
        }else{
            return 'other';
        }
    }

    /**
     * @param int $len
     * @param int $type ( 0 - 混合; 1 - 小写; 2 - 大写; 3 - 数字 )
     * @return string
     */
    public static function makeStr($len = 8, $initType = 0) {

        $lowerStart = ord('a');
        $lowerEnd   = ord('z');

        $upperStart = ord('A');
        $upperEnd   = ord('Z');

        $numStart   = 0;
        $numEnd     = 9;

        $str = '';
        for ($i=0; $i<$len; $i++) {

            $type = ($initType == 0) ? rand(1,3) : $initType;
            switch ($type) {
                case 1:
                    $str .= chr(rand($lowerStart, $lowerEnd));
                    break;
                case 2:
                    $str .= chr(rand($upperStart, $upperEnd));
                    break;
                case 3:
                    $str .= rand($numStart, $numEnd);
                    break;
            }

        }
        return $str;

    }

    public static function generateNo($prefix, $dateTag, $sort, $randMin = 1000, $randMax = 9999) {
        $rand = rand($randMin, $randMax);
        return $prefix . $dateTag . $sort . $rand;
    }

    // 生成固定长度数字
    public static function generateFullNumber($num, $len = 6) {
        $numLen = strlen($num);
        if ($len > $numLen) {
            $pre = str_repeat('0', $len - $numLen);
        } else {
            $pre = '';
        }
        $num = $pre . $num;
        return $num;
    }

    public static function showList($array, $val, $default = '')
    {

        return isset($array[$val])
            ? $array[$val]
            : $default;
    }

    public static function formatUrlParams($ret, $params = [])
    {
//        if (isset($params['user_id'])) {
//            $ret = str_replace('{$user_id}', $params['user_id'], $ret);
//        }
//        if (isset($params['session_id'])) {
//            $ret = str_replace('{$session_id}', $params['session_id'], $ret);
//        }
//        if (isset($params['session_stage_id'])) {
//            $ret = str_replace('{$session_stage_id}', $params['session_stage_id'], $ret);
//        }
//        if (isset($params['story_id'])) {
//            $ret = str_replace('{$story_id}', $params['story_id'], $ret);
//        }
//        if (isset($params['model_id'])) {
//            $ret = str_replace('{$model_id}', $params['model_id'], $ret);
//        }
//        if (isset($params['story_model_id'])) {
//            $ret = str_replace('{$story_model_id}', $params['story_model_id'], $ret);
//        }
//        if (isset($params['story_model_detail_id'])) {
//            $ret = str_replace('{$story_model_detail_id}', $params['story_model_detail_id'], $ret);
//        }
//        if (isset($params['model_inst_u_id'])) {
//            $ret = str_replace('{$model_inst_u_id}', $params['model_inst_u_id'], $ret);
//        }

        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $ret = str_replace('{$' . $key . '}', $val, $ret);
            }
        }

        return $ret;
    }

    public static function encodeJson($str) {
        $ret = $str;
        if (!empty($str)) {
            eval('$tmp = ' . $str);
            $ret = json_encode($tmp, true);
        }
        return $ret;
    }

    // 判断数组1是否包含另一个数组2
    public static function arrayContains($array1, $array2) {
        $difference = array_diff($array1, $array2);
        if (count($difference) === 0) {
            return true;
        } else {
            return $difference;
        }
    }
    public static function decodeJsonToVarexport($json, $needNum = false) {
        if (!empty($json)) {
            $txt = var_export(\common\helpers\Model::decodeDialog($json), true);
            // 去掉数组中下标
            // 让数组内容在textarea中文本显示
            if (!$needNum) {
                $txt = preg_replace('/\s*\d+\s*=>\s*/', "\n", $txt) . ';';
            }
        } else {
            $txt = '';
        }
        return $txt;
    }

    /**
     * 给定一个ip 一个网段 判断该ip是否属于该网段
     * @param $ip
     * @param $networkRange
     * @return bool 属于返回true 不属于返回false
     */
    public static function judge($ip, $networkRange)
    {
        $ip = (double) (sprintf("%u", ip2long($ip)));
        $s = explode('/', $networkRange);
        $network_start = (double) (sprintf("%u", ip2long($s[0])));
        $network_len = pow(2, 32 - $s[1]);
        $network_end = $network_start + $network_len - 1;

        if ($ip >= $network_start && $ip <= $network_end) {
            return true;
        }
        return false;
    }

    public static function isChinaIp() {
        $ip = self::getRealIP();
        $ipRange = file( dirname(__FILE__) . '/../../china_ip.txt');

        foreach ($ipRange as $chinaIp) {
            $chinaIp = str_replace("\n", '', $chinaIp);
            if (self::judge($ip, $chinaIp)) {
                return true;
            }
        }
        return false;
    }

    public static function isJson($str) {
        $r = json_decode($str, true);
        if (json_last_error() == JSON_ERROR_NONE
//            && is_array($r)
        ) {
            return true;
        }
        return false;
    }

    public static function formatTimeToStr($timeStamp, $returnFormat = 'H:i:s.ms', $needMicSec = true) {
        $time = self::formatTime($timeStamp, $returnFormat, $needMicSec);
        return !empty($time['str']) ? $time['str'] : $timeStamp;
    }
    public static function formatTime($timeStamp, $returnFormat = 'H:i:s.ms', $needMicSec = true) {
        if ($needMicSec) {
            $timestr = floor($timeStamp / 1000);
            $micSec = $timeStamp % 1000;
        } else {
            $timestr = $timeStamp;
            $micSec = 0;
        }
        $hour = floor($timestr / 3600);
        $minute = floor(($timestr - $hour * 3600) / 60);
        $second = $timestr - $hour * 3600 - $minute * 60;

        $str = $returnFormat;
        $str = str_replace('ms', sprintf('%03d', $micSec), $str);
        $noHour = false;
        if ($hour == 0) {
            $str = str_replace('H:', '', $str);
            $str = str_replace('H', '', $str);
            $noHour = true;
        } else {
            $str = str_replace('H', sprintf('%02d', $hour), $str);
        }
        $noMinute = false;
        if ($minute == 0 && $noHour) {
            $str = str_replace('i:', '', $str);
            $str = str_replace('i', '', $str);
            $noMinute = true;
        } else {
            $str = str_replace('i', sprintf('%02d', $minute), $str);
        }
        $str = str_replace('s', sprintf('%02d', $second), $str);

        return [
            'hour' => $hour,
            'minute' => $minute,
            'second' => $second,
            'micSec' => $micSec,
            'str' => $str,
        ];
    }

    /**
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @param int $lenType 1 - 米; 1000 - 千米
     * @param int $decimal
     * @return float
     */
    public static function computeDistanceWithLatLng($lat1, $lng1, $lat2, $lng2, $lenType = 1, $decimal = 2) {
        $radLat1 = $lat1 * self::PI / 180.0;
        $radLat2 = $lat2 * self::PI / 180.0;
        $a = $radLat1 - $radLat2;
        $b = $lng1 * self::PI / 180.0 - $lng2 * self::PI / 180.0;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) +
                cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s = $s * self::EARTH_RADIUS;
        $s = round($s * 1000);

        if ($lenType > 1) {
            $s /= $lenType;
        }

        return round($s, $decimal);

    }

    public static function compareUnityVersion($ver1, $ver2) {
        $ver1Arr = explode('.', $ver1);
        $ver2Arr = explode('.', $ver2);

        $ver1Arr = array_map('intval', $ver1Arr);
        $ver2Arr = array_map('intval', $ver2Arr);

        $len = min(count($ver1Arr), count($ver2Arr));
        for ($i=0; $i<$len; $i++) {
            if ($ver1Arr[$i] > $ver2Arr[$i]) {
                return 1;
            } elseif ($ver1Arr[$i] < $ver2Arr[$i]) {
                return -1;
            }
        }

        if (count($ver1Arr) > count($ver2Arr)) {
            return 1;
        } elseif (count($ver1Arr) < count($ver2Arr)) {
            return -1;
        }

        return 0;
    }

    public static function generateWordPuzzle($words, $mixWords, $rows = 8, $cols = 8, $width = 400, $height = 400) {
        $wordLen = mb_strlen($words, 'UTF-8');

//        $map = [][];
        $map = [];
        for ($j=0; $j<$rows; $j++) {
            for ($i = 0; $i<$cols; $i++) {
                $map[$j][$i] = 0;
            }
        }

//        $col = rand(0, $cols - 1);
        $col = 0;
        $row = 0;
        for ($w = 0; $w < $wordLen; $w++) {
            $word = mb_substr($words, $w, 1, 'UTF-8');
            $map[$row][$col] = [
                'word' => $word,
                'style' => 'background-color: #3fe52d; font-weight: bold;',
            ];

            $forbid = [];
            if ($row == 0 || !empty($map[$row-1][$col])) {
                $forbid[] = 1;
            }
            if ($col == 0 || !empty($map[$row][$col-1])) {
                $forbid[] = 3;
            }
            if ($row == $rows - 1 || !empty($map[$row+1][$col])) {
                $forbid[] = 2;
            }
            if ($col == $cols - 1 || !empty($map[$row][$col+1])) {
                $forbid[] = 4;
            }

            $tarDirArr = [];
            for ($dir = 1; $dir <= 4; $dir++) {
                if (!in_array($dir,$forbid)) {
                    $tarDirArr[] = $dir;
                }
            }

            $randDirNo = rand(0, sizeof($tarDirArr) - 1);
            $tarDir = $tarDirArr[$randDirNo];

            switch ($tarDir) {
                case 1:
                    $row--;
                    break;
                case 2:
                    $row++;
                    break;
                case 3:
                    $col--;
                    break;
                case 4:
                    $col++;
                    break;
            }

        }

        $colorArray = [
            'red', 'yellow', 'blue', 'grey',
        ];
        $mw = 0;
        for ($j=0; $j<$rows; $j++) {
            for ($i = 0; $i<$cols; $i++) {
                if (empty($map[$j][$i])) {
                    $map[$j][$i] = [
                        'word' => !empty(mb_substr($mixWords, $mw, 1, 'UTF-8')) ? mb_substr($mixWords, $mw, 1, 'UTF-8') : 'A',
                        'style' => 'background-color: ' . $colorArray[rand(0, sizeof($colorArray) -1 )],
                    ];
                    $mw++;
                }
            }
        }


        $ret = '<table style="padding: 5px; border: 1px solid #c0c0c0;">';
        for ($j=0; $j<$rows; $j++) {
            $ret .= '<tr>';
            for ($i = 0; $i<$cols; $i++) {
                $ret .= '<td style="padding: 5px; border: 1px solid #c0c0c0;' . $map[$j][$i]['style'] . '">' . $map[$j][$i]['word'] . '</td>';
            }
            $ret .= '</tr>';
        }
        $ret .= '</table>';

        return $ret;
    }

    public static function generateMazeImage($width, $height, $cellSize)
    {
        $image = imagecreatetruecolor($width, $height);
        $wallColor = imagecolorallocate($image, 0, 0, 0); // 墙壁颜色
        $pathColor = imagecolorallocate($image, 255, 255, 255); // 路径颜色

        // 创建迷宫数组，1表示墙壁，0表示路径
        $maze = array(
            array(1, 1, 1, 1, 1),
            array(1, 0, 0, 0, 1),
            array(1, 1, 1, 0, 1),
            array(1, 0, 0, 0, 1),
            array(1, 1, 1, 1, 1)
        );

        for ($y = 0; $y < count($maze); $y++) {
            for ($x = 0; $x < count($maze[$y]); $x++) {
                $cell = $maze[$y][$x];
                $color = ($cell == 1) ? $wallColor : $pathColor;
                imagefilledrectangle($image, $x * $cellSize, $y * $cellSize, ($x + 1) * $cellSize, ($y + 1) * $cellSize, $color);
            }
        }

        header("Content-Type: image/png");
        imagepng($image);
        imagedestroy($image);
    }

// 调用生成迷宫图片函数
//generateMazeImage(400, 400, 40);

    public static function createPuzzle($imagePath, $blockSize = 10, $rows = 2, $cols = 2, $shuffleCount = 2) {
        // 初始化拼图游戏
//        $blockSize = 200; // 拼图块的尺寸
//        $rows = 2; // 行数
//        $cols = 2; // 列数
////        $imagePath = 'puzzle_image.jpg'; // 拼图图片路径
//        $shuffleCount = 100; // 打乱拼图的次数

        // 创建拼图数组
        $puzzle = array(
            array(null, null),
            array(null, null)
        );

        // 加载拼图图片
        $image = imagecreatefromjpeg($imagePath);


        // 切割拼图图片为小块
        $blockImages = array();
        $ct = 0;
        for ($x = 0; $x < $rows; $x++) {
            for ($y = 0; $y < $cols; $y++) {
                $blockImages[$ct] = imagecrop($image, ['x' => $x * $blockSize, 'y' => $y * $blockSize, 'width' => $blockSize, 'height' => $blockSize]);
                if ($x==0 && $y==0) continue;
                $puzzle[$x][$y] = $ct;
                $ct++;
            }
        }



        // 随机打乱拼图
        for ($i = 0; $i < $shuffleCount; $i++) {
//            shuffle($blockImages);


            $emptyX = null;
            $emptyY = null;
            for ($x = 0; $x < $rows; $x++) {
                for ($y = 0; $y < $cols; $y++) {
                    if ($puzzle[$x][$y] === null) {
                        $emptyX = $x;
                        $emptyY = $y;
                        break;
                    }
                }
                if ($emptyX !== null && $emptyY !== null) {
                    break;
                }
            }

            $possibleMoves = array();
            if ($emptyX > 0) {
                $possibleMoves[] = 'up';
            }
            if ($emptyX < $rows - 1) {
                $possibleMoves[] = 'down';
            }
            if ($emptyY > 0) {
                $possibleMoves[] = 'left';
            }
            if ($emptyY < $cols - 1) {
                $possibleMoves[] = 'right';
            }

            $randomMove = $possibleMoves[array_rand($possibleMoves)];

            if ($randomMove === 'up') {
                $puzzle[$emptyX][$emptyY] = $puzzle[$emptyX - 1][$emptyY];
                $puzzle[$emptyX - 1][$emptyY] = null;
            } elseif ($randomMove === 'down') {
                $puzzle[$emptyX][$emptyY] = $puzzle[$emptyX + 1][$emptyY];
                $puzzle[$emptyX + 1][$emptyY] = null;
            } elseif ($randomMove === 'left') {
                $puzzle[$emptyX][$emptyY] = $puzzle[$emptyX][$emptyY - 1];
                $puzzle[$emptyX][$emptyY - 1] = null;
            } elseif ($randomMove === 'right') {
                $puzzle[$emptyX][$emptyY] = $puzzle[$emptyX][$emptyY + 1];
                $puzzle[$emptyX][$emptyY + 1] = null;
            }

        }

        // 创建游戏界面
        $gameImage = imagecreatetruecolor($blockSize * $cols, $blockSize * $rows);

        $block = 0;
        for ($x = 0; $x < $rows; $x++) {
            for ($y = 0; $y < $cols; $y++) {
                $block = $puzzle[$x][$y];
                if ($block !== null) {
                    imagecopy($gameImage, $blockImages[$block], $y * $blockSize, $x * $blockSize, 0, 0, $blockSize, $blockSize);
                }
                $block++;
            }
        }

        // 输出游戏图像
        header('Content-Type: image/jpeg');
        imagejpeg($gameImage);

// 释放资源
//        imagedestroy($image);
//        imagedestroy($gameImage);
    }

}