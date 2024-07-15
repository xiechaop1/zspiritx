<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/17
 * Time: 下午11:41
 */

namespace console\controllers;

use common\models\EnglishWords;
use common\models\Poem;
use common\models\UserCompany;
use liyifei\chinese2pinyin\Chinese2pinyin;
use common\models\City;
use common\models\MemberSpecial;
use common\models\Orders;
use common\models\ConsultantCompany;
use common\models\Job;
use common\models\Documents;
use common\models\Company;
use common\models\Industry;
use common\models\IndustryLink;
use common\models\Post;
use common\models\Recommend;
use common\models\RecommendHistory;
use common\models\Tag;
use common\models\PostBasic;
use common\models\Member;
use common\helpers\No;
use yii\db\Query;

use yii\console\Controller;
use Yii;

class SpiderController extends Controller
{
    public $urlList = [];
    public $urlConf = [];

    public function actionEnglish() {
        $url = 'http://www.danciku.cn/words/';
//        $url = 'http://www.danciku.cn/words/xiaoxueyingyu/';
//        $url = 'http://www.danciku.cn/words/renjiaobanxiaoxueyingyusannianjidanci/';

        $this->urlList[] = $url;

        while (!empty($this->urlList)) {
            var_dump($this->urlList);
            $url = array_shift($this->urlList);

            $html = $this->_getHtml($url);

            $wordRet = $this->_spiderWordWithUrl($html, $url);

            if ($wordRet === false) {
                $this->_spiderMenuWithUrl($html);
            }
        }
        return true;

    }

    private function _getHtml($url) {
        echo 'get url: ' . $url . "\n";
        return file_get_contents($url);
    }


    private function _spiderWordWithUrl($wordHtml, $url, $page = '') {
        if (empty($wordHtml)) {
            if (!empty($page)) {
                $getUrl = $url . $page . '/';
                echo 'get page url: ' . $getUrl . "\n";
            }
            $wordHtml = file_get_contents($getUrl);
        }
        preg_match_all('/<a href="\/w\/(.*?)" title="(.*?)">.*?<\/a>.*?<span>(.*?)<\/span>/s', $wordHtml, $wordMatch);
        if (!empty($wordMatch[1])) {
//            var_dump($wordMatch);exit;
            echo 'url ' . $url . ' is word! ' . "\n";
            $this->urlConf[$url]['is_word'] = true;

            $class = !empty($this->urlConf[$url]['class']) ? $this->urlConf[$url]['class'] : [];
            foreach ($wordMatch[1] as $key => $value) {
                $word = htmlspecialchars_decode($wordMatch[2][$key]);
                echo 'Insert Word ' . $word . ' Into DB ... ' . "\n";
                $eng = EnglishWords::find()->where(['word' => $wordMatch[2][$key]])->one();
                if (empty($eng)) {
                    $eng = new EnglishWords();
                    $eng->word = $word;
                    $eng->first_word = strtoupper(substr($wordMatch[2][$key], 0, 1));
                }
                $chinese = $wordMatch[3][$key];
                if (strpos($chinese, '. ') !== false) {
                    $chinese = explode('. ', $chinese);
                    $adv = $chinese[0] . '.';
                    $chinese = $chinese[1];
                } else {
                    $adv = '';
                }
                $eng->chinese = htmlspecialchars_decode(strip_tags($chinese));
                $eng->adv = $adv;

                $eng->word_class1 = !empty($class[0]) ? $class[0] : '';
                $eng->word_class2 = !empty($class[1]) ? $class[1] : '';
                $eng->save();

            }
            $nextPage = empty($page) ? 2 : $page + 1;
            return $this->_spiderWordWithUrl('', $url, $nextPage);
        } else {
            $isWord = !empty($this->urlConf[$url]['is_word']) ? $this->urlConf[$url]['is_word'] : false;

            if (!$isWord) {
                return false;
            } else {
                return [];
            }
        }

    }

    private function _spiderMenuWithUrl($menuHtml, $class = []) {
//        $menuHtml = file_get_contents($url);

        // 解析menuHtml
        preg_match_all('/<a href="\/words\/(.*?)" title="(.*?)">/', $menuHtml, $menuMatch);

        $urlList = [];
        if (!empty($menuMatch[1])) {
            foreach ($menuMatch[1] as $key => $value) {
                echo 'Get Directory ' . $value . ' ... ' . "\n";
                $tmpClass = $class;
                $tmpClass[] = $menuMatch[2][$key];
                $menuUrl = 'http://www.danciku.cn/words/' . $value;
                $this->urlList[] = $menuUrl;
                $this->urlConf[$menuUrl] = [
//                    'url' => $menuUrl,
                    'class' => $tmpClass,
                ];
            }
        }
//        var_dump($this->urlList);

        return true;
    }



}