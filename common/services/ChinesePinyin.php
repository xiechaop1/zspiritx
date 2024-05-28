<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\services\Curl;
use common\models\User;
use yii\base\Component;
use yii;

class ChinesePinyin extends Component
{

    private $ChineseCharacters;
    private $ChineseCharactersWithoutTone;
    //编码
    private $charset = 'utf-8';

    public function __construct($config = [])
    {
        if (empty($this->ChineseCharacters)) {
            $this->ChineseCharacters = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ChineseCharacters.dat');
        }
        parent::__construct($config);
    }

    /*
     * 通过没有声调的拼音获得汉字
     */
    public function getWordFromPinyinWithoutTone($inputPinyin) {
//        $toneList = array('ā', 'á', 'ǎ', 'à', 'ō', 'ó', 'ǒ', 'ò', 'ē', 'é', 'ě', 'è', 'ī', 'í', 'ǐ', 'ì', 'ū', 'ú', 'ǔ', 'ù', 'ǖ', 'ǘ', 'ǚ', 'ǜ', 'ü');
//        $wordList = array('a', 'a', 'a', 'a', 'o', 'o', 'o', 'o', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'u', 'u', 'u', 'u', 'v', 'v', 'v', 'v', 'v');
//
//        for ($i=0; $i<count($wordList); $i++) {
//            $findStr = $inputPinyin;
//            $replaceStr = $wordList[$i];
//            $findPinyin = str_replace($toneList, $wordList, $findStr);
//
//            preg_match_all('/\,(.*?)' . preg_quote($findPinyin) . '\,/', $this->ChineseCharacters, $matches);
//        }
        if (empty($this->ChineseCharactersWithoutTone)) {
            $this->ChineseCharactersWithoutTone = str_replace(array('ā', 'á', 'ǎ', 'à', 'ō', 'ó', 'ǒ', 'ò', 'ē', 'é', 'ě', 'è', 'ī', 'í', 'ǐ', 'ì', 'ū', 'ú', 'ǔ', 'ù', 'ǖ', 'ǘ', 'ǚ', 'ǜ', 'ü'),
                array('a', 'a', 'a', 'a', 'o', 'o', 'o', 'o', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'u', 'u', 'u', 'u', 'v', 'v', 'v', 'v', 'v')
                , $this->ChineseCharacters);
        }

        preg_match_all('/\,([\x{4e00}-\x{9fa5}])' . preg_quote($inputPinyin) . '\,/u', $this->ChineseCharactersWithoutTone, $matches);
        $ret = [];

//        var_dump($matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $word) {
                $retTemp[$inputPinyin][] = $word;
            }
            $ret = [
                'pinyin' => $inputPinyin,
                'word' => $retTemp,
            ];
        }

        return $ret;
    }

    /*
    * 转成带有声调的汉语拼音
    * param $input_char String  需要转换的汉字
    * param $delimiter  String   转换之后拼音之间分隔符
    * param $outside_ignore  Boolean     是否忽略非汉字内容
    */
    public function transformWithTone($input_char, $delimiter = ' ', $outside_ignore = false)
    {
        $input_len = mb_strlen($input_char, $this->charset);
        $output_char = '';
        for ($i = 0; $i < $input_len; $i++) {
            $word = mb_substr($input_char, $i, 1, $this->charset);
            if (preg_match('/^[\x{4e00}-\x{9fa5}]$/u', $word) && preg_match('/\,' . preg_quote($word) . '(.*?)\,/', $this->ChineseCharacters, $matches)) {
                $output_char .= $matches[1] . $delimiter;
            } else if (!$outside_ignore) {
                $output_char .= $word . $delimiter;
            }
        }
        return $output_char;
    }

    /*
    * 转成带无声调的汉语拼音
    * param $input_char String  需要转换的汉字
    * param $delimiter  String   转换之后拼音之间分隔符
    * param $outside_ignore  Boolean     是否忽略非汉字内容
    */
    public function transformWithoutTone($input_char, $delimiter = '', $outside_ignore = false)
    {
        $char_with_tone = $this->transformWithTone($input_char, $delimiter, $outside_ignore);
        $char_without_tone = str_replace(array('ā', 'á', 'ǎ', 'à', 'ō', 'ó', 'ǒ', 'ò', 'ē', 'é', 'ě', 'è', 'ī', 'í', 'ǐ', 'ì', 'ū', 'ú', 'ǔ', 'ù', 'ǖ', 'ǘ', 'ǚ', 'ǜ', 'ü'),
            array('a', 'a', 'a', 'a', 'o', 'o', 'o', 'o', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'u', 'u', 'u', 'u', 'v', 'v', 'v', 'v', 'v')
            , $char_with_tone);
        return $char_without_tone;

    }

    /*
    * 转成汉语拼音首字母
    * param $input_char String  需要转换的汉字
    * param $delimiter  String   转换之后拼音之间分隔符
    */
    public function transformUcWords($input_char, $delimiter = '', $outside_ignore = true)
    {
        $char_without_tone = ucwords($this->transformWithoutTone($input_char, ' ', $outside_ignore));

        $uc_words = preg_replace('/[a-z\s]/', '', $char_without_tone);
        if (!empty($delimiter)) {
            $uc_words = implode($delimiter, str_split($uc_words));
        }
        return strtolower($uc_words);
    }

}