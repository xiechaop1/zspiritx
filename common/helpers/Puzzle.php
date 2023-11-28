<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 3:24 PM
 */

namespace common\helpers;


class Puzzle
{

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

    public static function cutImage($imagePath, $blockSize = 200, $rows = 4, $cols = 4, $prefix = 'puzzle_image_') {
        $image = imagecreatefromjpeg($imagePath);
        $imageSize = getimagesize($imagePath);

        $width = $imageSize[0];
        $height = $imageSize[1];

        $blockSizeX = intval($width / $cols);
        $blockSizeY = intval($height / $rows);

        $ct = 0;
        for ($y = 0; $y < $cols; $y++) {
            for ($x = 0; $x < $rows; $x++) {
                $gameImage = imagecreatetruecolor($blockSizeX, $blockSizeY);
                echo $x * $blockSizeX . ' ' . $y * $blockSizeY . ' ' . $blockSizeX . ' ' . $blockSizeY . '<br>';
                imagecopy($gameImage, $image, 0, 0, $x * $blockSizeX, $y * $blockSizeY, $blockSizeX, $blockSizeY);
//
//                $blockImages[] = [
////                    'image' => imagecrop($image, ['x' => $x * $blockSize, 'y' => $y * $blockSize, 'width' => $blockSize, 'height' => $blockSize]),
//                    'image' => $gameImage,
//                    'blockSize' => $blockSize,
//                ];
//                imagejpeg($gameImage, 'puzzle_image_' . $x . '_' . $y . '.jpg');
                imagejpeg($gameImage, $prefix . $ct . '.jpg');
                $ct++;
//                 $blockImages[] = $gameImage;
            }
        }

//        return $blockImages;
    }

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
//                if ($x==0 && $y==0) continue;
//                $puzzle[$x][$y] = $ct;
                $ct++;
            }
        }



        // 随机打乱拼图
        for ($i = 0; $i < $shuffleCount; $i++) {
            shuffle($blockImages);

            /*
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
            */
        }

        // 创建游戏界面
        $gameImage = imagecreatetruecolor($blockSize * $cols, $blockSize * $rows);

        $block = 0;
        for ($x = 0; $x < $rows; $x++) {
            for ($y = 0; $y < $cols; $y++) {
//                $block = $puzzle[$x][$y];
//                if ($block !== null) {
                    imagecopy($gameImage, $blockImages[$block], $y * $blockSize, $x * $blockSize, 0, 0, $blockSize, $blockSize);
//                }
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