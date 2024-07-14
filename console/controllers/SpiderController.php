<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/17
 * Time: 下午11:41
 */

namespace console\controllers;

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

    public function actionEnglish() {
        $url = 'http://www.danciku.cn/words/xiaoxueyingyu/';

        $menuHtml = file_get_contents($url);

        // 解析menuHtml
        preg_match_all('/<a href="\/w\/(.*?)" title="(.*?)">/', $menuHtml, $menuMatch);

        var_dump($menuMatch);exit;

        if (!empty($menuMatch[1])) {
            foreach ($menuMatch[1] as $key => $value) {
                $urlList[] = [
                    'url' => 'http://www.danciku.cn/words/' . $value,
                    'class' => $menuMatch[2][$key],
                ];
            }
        }

    }

    private function _spiderMenuWithUrl($url, $class = []) {
        $menuHtml = file_get_contents($url);

        // 解析menuHtml
        preg_match_all('/<a href="\/words\/(.*?)" title="(.*?)">/', $menuHtml, $menuMatch);

        $urlList = [];
        if (!empty($menuMatch[1])) {
            foreach ($menuMatch[1] as $key => $value) {
                $tmpClass = $class;
                $tmpClass[] = $menuMatch[2][$key];
                $urlList[] = [
                    'url' => 'http://www.danciku.cn/words/' . $value,
                    'class' => $tmpClass,
                ];
            }
        }

        return $urlList;
    }



}