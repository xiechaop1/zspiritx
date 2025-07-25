<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/17
 * Time: 下午11:41
 */

namespace console\controllers;

use common\models\UserEBookRes;
use liyifei\chinese2pinyin\Chinese2pinyin;
use yii\db\Query;

use yii\console\Controller;
use Yii;

use OSS\OssClient;
use OSS\Core\OssException;

class JncityController extends Controller
{
    public function actionGetvideo() {
        $model = UserEBookRes::find();
        $model->andFilterWhere([
            'ebook_res_status' => UserEBookRes::USER_EBOOK_RES_STATUS_VIDEO_GENERATE
        ]);
        $model = $model->all();

        if (empty($model)) {
            return [];
        }

        $ret = [];
        foreach ($model as $row) {
            $videoId = $row->ai_video_m_id;

            $ret = Yii::$app->ebook->searchWithId($videoId);
            $status = !empty($ret['status']) ? $ret['status'] : '';
            if ($status == 'succeeded') {
                $videoUrl = !empty($ret['content']['video_url']) ? $ret['content']['video_url'] : '';
                if (!empty($videoUrl)) {
                    // 1. 下载视频到本地
                    $tmpVideo = '/tmp/video_' . uniqid() . '.mp4';
                    file_put_contents($tmpVideo, file_get_contents($videoUrl));

                    // 2. 指定本地 MP3 路径
                    $mp3Path = '/Users/choice/Projects/zspiritx/a.mp3'; // 你可以根据实际情况修改

                    // 3. 合成新视频
                    $outputVideo = '/tmp/output_' . uniqid() . '.mp4';
                    $cmd = "ffmpeg -y -i {$tmpVideo} -i {$mp3Path} -c:v copy -c:a aac -strict experimental -map 0:v:0 -map 1:a:0 -shortest {$outputVideo}";
                    exec($cmd, $out, $retCode);

                    if ($retCode === 0 && file_exists($outputVideo)) {
                        // OSS配置
                        $accessKeyId = '你的AccessKeyId';
                        $accessKeySecret = '你的AccessKeySecret';
                        $endpoint = '你的Endpoint'; // 例如: oss-cn-shanghai.aliyuncs.com
                        $bucket = '你的Bucket名称';

                        $ossFileName = 'videos/' . basename($outputVideo);

                        try {
                            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                            $ossClient->uploadFile($bucket, $ossFileName, $outputVideo);

                            $newVideoUrl = 'https://' . $bucket . '.' . $endpoint . '/' . $ossFileName;

                            $row->video_url = $newVideoUrl;
                            $row->ebook_res_status = UserEBookRes::USER_EBOOK_RES_STATUS_VIDEO_GENERATE_SUCCESS;
                        } catch (OssException $e) {
                            $row->ebook_res_status = UserEBookRes::USER_EBOOK_RES_STATUS_VIDEO_GENERATE_FAIL;
                            echo "OSS上传失败: " . $e->getMessage() . "\n";
                        }
                    } else {
                        $row->ebook_res_status = UserEBookRes::USER_EBOOK_RES_STATUS_VIDEO_GENERATE_FAIL;
                    }

                    // 5. 清理临时文件
                    @unlink($tmpVideo);
                    @unlink($outputVideo);
                } else {
                    $row->ebook_res_status = UserEBookRes::USER_EBOOK_RES_STATUS_VIDEO_GENERATE_FAIL;
                }
                $r = $row->save();
            } else if ($status != 'running') {
                $row->ebook_res_status = UserEBookRes::USER_EBOOK_RES_STATUS_VIDEO_GENERATE_FAIL;
                $r = $row->save();
            }

        }

        return $ret;

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
                $eng = EnglishWords::find()->where([
                    'word' => $wordMatch[2][$key],
                    'word_class1' => !empty($class[0]) ? $class[0] : '',
                    'word_class2' => !empty($class[1]) ? $class[1] : '',
                ])->one();
                if (empty($eng)) {
                    $eng = new EnglishWords();
                    $eng->word = htmlspecialchars_decode(strip_tags(str_replace('&#039;', "'", $word)));
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
                if (!in_array($menuUrl, $this->urlList) && !in_array($menuUrl, $this->saveList)) {
                    $this->urlList[] = $menuUrl;
                    $this->urlConf[$menuUrl] = [
//                    'url' => $menuUrl,
                        'class' => $tmpClass,
                    ];
                }
            }
        }
//        var_dump($this->urlList);

        return true;
    }



}