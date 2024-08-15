<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\definitions\Subject;
use common\helpers\Attachment;
use common\models\EnglishWords;
use common\models\Poem;
use common\models\Qa;
use common\models\QaPackage;
use common\models\ShopWares;
use common\models\StoryMatch;
use common\models\UserQa;
use common\models\UserWare;
use yii\base\Component;
use yii;

class Qas extends Component
{

    public function getSubjectsWithUserWare($userId, $matchClass = 0, $level = 1, $ct = 10) {
        $ret = [];
        $maxWareLimit = 2;      // 暂时就支持2个商品同时运行
        $userWare = UserWare::find()
            ->where([
                'user_id' => $userId,
                'status' => UserWare::USER_WARE_STATUS_NORMAL,
                'ware_type' => ShopWares::SHOP_WARE_TYPE_PACKAGE,
            ])
            ->andFilterWhere([
                '>', 'expire_time', time(),
            ])
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit($maxWareLimit)
            ->all();


        if (!empty($userWare)) {
            foreach ($userWare as $ware) {
                switch ($ware->link_type) {
                    case ShopWares::LINK_TYPE_QA_PACKAGE:
                    default:
                        $packageClass = [];
                        if (!empty($matchClass)) {
                            $packageClass = StoryMatch::$matchClass2PackageClass[$matchClass];
                        }
                        $qaCollections = $this->getQaByPackageIds([$ware->link_id], $packageClass);

                        if (!empty($qaCollections)) {
                            foreach ($qaCollections as $qaPackageId => $qaCollection) {
                                foreach ($qaCollection as $qaModel) {
                                    $extends = [];
                                    $genSub = true;
                                    if (!empty($qaModel->prop)) {
                                        // Todo: 准备读取qaProp，判断题目模式，引入相似题（增加例题字段）
                                        $qaProp = json_decode($qaModel->prop, true);
//                                        if (!empty($qaProp['example_qa_ids'])) {$ct = $qaProp['ct'];
//                                        }
                                        if (!empty($qaProp['user_qa'])) {
                                            $userQaTmp = UserQa::find()
                                                ->where([
                                                    'user_id' => $userId,
                                                    'is_right' => UserQa::ANSWER_WRONG,
                                                ])
                                                ->andFilterWhere([
                                                    '>', 'updated_at', time() - 3600 * 24 * 3,
                                                ])
                                                ->orderBy(['updated_at' => SORT_DESC])
                                                ->limit(20)
                                                ->all();

                                            $qaIds = [];

                                            if (!empty($userQaTmp)) {
                                                foreach ($userQaTmp as $uq) {
                                                    $qaIds[] = $uq->qa_id;
                                                }

                                                $qaClass = !empty(StoryMatch::$matchClass2QaClass[$matchClass]) ? StoryMatch::$matchClass2QaClass[$matchClass] : 0;
                                                $qaTmp = Qa::find()
                                                    ->where([
                                                        'id' => $qaIds,
                                                        'qa_class' => $qaClass
                                                    ])
                                                    ->orderBy(['updated_at' => SORT_DESC])
                                                    ->limit(5)
                                                    ->all();

                                                $exampleTopics = [];
                                                if (!empty($qaTmp)) {
                                                    foreach ($qaTmp as $q) {
                                                        $exampleTopics[] = [
                                                            'topic' => $q->topic
                                                        ];
                                                    }
                                                    $extends['exampleTopics'] = $exampleTopics;
                                                } else {
                                                    $genSub = false;
                                                }
                                            } else {
                                                $genSub = false;
                                            }
                                        }
                                    } else {
                                        $qaProp = [];
                                    }
                                    if ($genSub) {
                                        $tmp = $this->getSubjectWithQa($qaModel, $matchClass, $level, $ct, $extends);

                                        if (!empty($tmp)) {
                                            foreach ($tmp as $t) {
                                                $ret[] = $t;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        break;
                }
//                $ret[] = $this->getSubjectWithWare($ware, $matchClass, $level, $ct);
            }
        }

        return $ret;
    }

    public function getQaSubjectsWithUserWare($userId, $matchClass = 0, $level = 1, $ct = 10) {
        $ret = [];
        $maxWareLimit = 2;      // 暂时就支持2个商品同时运行
        $userWare = UserWare::find()
            ->where([
                'user_id' => $userId,
                'status' => UserWare::USER_WARE_STATUS_NORMAL,
                'ware_type' => ShopWares::SHOP_WARE_TYPE_PACKAGE,
            ])
            ->andFilterWhere([
                '>', 'expire_time', time(),
            ])
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit($maxWareLimit)
            ->all();


        if (!empty($userWare)) {
            foreach ($userWare as $ware) {
                switch ($ware->link_type) {
                    case ShopWares::LINK_TYPE_QA_PACKAGE:
                    default:
                        $linkQaIds = [];
                        $packageClass = [];
                        if (!empty($matchClass)) {
                            $packageClass = StoryMatch::$matchClass2PackageClass[$matchClass];
                        }
                        $qaCollections = $this->getQaByPackageIds([$ware->link_id], $packageClass);

                        if (!empty($qaCollections)) {
                            foreach ($qaCollections as $qaPackageId => $qaCollection) {
                                foreach ($qaCollection as $qaModel) {
                                    $extends = [];
                                    $genSub = true;
                                    if (!empty($qaModel->prop)) {
                                        $linkQaIds[] = $qaModel->id;
                                    }
                                }
                            }
                        }
//                        var_dump($linkQaIds);exit;
                        if (!empty($linkQaIds)) {
                            $qas = Qa::find()
                                ->where([
                                    'link_qa_id' => $linkQaIds
                                ])
                                ->limit($ct)
                                ->orderBy('rand()')
                                ->all();
//                                ->createCommand()
//                                ->getRawSql();

                            if (!empty($qas)) {
                                foreach ($qas as $qa) {
                                    $tmp = \common\helpers\Qa::formatSubjectFromQa($qa);

                                   $ret[] = $tmp;
                                }

                            }
                        }
                        break;
                }
//                $ret[] = $this->getSubjectWithWare($ware, $matchClass, $level, $ct);
            }
        }

        return $ret;
    }


    public function getSubjectWithQa($qaModel, $matchClass = 0, $level = 1, $ct = 1, $extends = []) {
        $ret = [];

        switch ($qaModel->qa_type) {
            case Qa::QA_TYPE_CHATGPT:
            default:
                if (!empty($qaModel->prop)) {
                    $qaProp = json_decode($qaModel->prop, true);
                    if (!empty($qaProp['prompt'])) {
                        $prompt = $qaProp['prompt'];

                        if (!empty($qaProp['ct'])) {
                            $ct = $qaProp['ct'];
                        }

                        if (!empty($qaProp['level'])) {
                            $level = $qaProp['level'];
                        }

                        if (empty($extends['exampleTopics'])
                            && empty($extends['oldTopics'])
                        ) {
                            $oldQas = Qa::find()
                                ->where([
                                    'link_qa_id' => $qaModel->id
                                ])
                                ->orderBy('rand()')
                                ->limit(5)
                                ->all();

                            if (!empty($oldQas)) {
                                foreach ($oldQas as $oldQa) {
                                    $extends['oldTopics'][] = [
                                        'topic' => $oldQa->topic
                                    ];
                                }
                            }
                        }

                        $subjects = Yii::$app->doubao->generateSubject($prompt, $level, $matchClass, $ct, $extends);

//                        var_dump($subjects);exit;

                        if (!empty($subjects)) {
                            foreach ($subjects as $subj) {
                                $tmpSubj = \common\helpers\Qa::formatSubjectFromGPT($subj);
                                $tmpSubj = \common\helpers\Qa::generateChallengePropByLevel($level, $tmpSubj);
                                $tmpSubj = \common\helpers\Qa::formatChallengeProp($tmpSubj);
                                $tmpSubj['link_qa_id'] = !empty($qaModel->id) ? $qaModel->id : 0;
                                $tmpSubj['level'] = $level;


                                if (empty($extends)) {
                                    $saveQa = $this->saveQaByDoubao($tmpSubj, $qaModel->id);
                                } else {
                                    $saveQa = $this->saveQaByDoubao($tmpSubj, 0);
                                }
                                $tmpSubj['qa_id'] = !empty($saveQa->id) ? $saveQa->id : 0;
                                $ret[] = $tmpSubj;

                            }
                        }

                    }
                } else {
                    $qaProp = [];
                }
                break;
        }

        return $ret;
    }

    private function _generateSubjectWithUserWare($ware, $level = 1, $matchClass = 0, $ct = 20, $userId = 0) {
        $ret = [];
        switch ($ware->link_type) {
            case ShopWares::LINK_TYPE_QA_PACKAGE:
            default:
                $packageClass = [];
                if (!empty($matchClass)) {
                    $packageClass = StoryMatch::$matchClass2PackageClass[$matchClass];
                }
                $qaCollections = $this->getQaByPackageIds([$ware->link_id], $packageClass);

                $ret = $this->generateSubjectsWithQaCollection($qaCollections, $level, $matchClass, $ct, $userId);

                break;
        }

        return $ret;
    }

    public function generateSubjectByUserWareId($wareId, $level = 1, $matchClass = 0, $ct = 20, $userId = 0) {
        $ret = [];
        $userWare = UserWare::find()
            ->where([
                'id' => $wareId,
            ])
            ->andFilterWhere([
                '>', 'expire_time', time(),
            ])
            ->one();

        if (!empty($userWare)) {
            $ret = $this->_generateSubjectWithUserWare($userWare, $level, $matchClass, $ct, $userId);
        }

        return $ret;
    }

    public function generateTotalSubjects($level = 1, $matchClass = 0, $ct = 20, $userId = 0){
        $ret = [];
        $maxWareLimit = 5;      // 暂时就支持2个商品同时运行
        $userWare = UserWare::find()
            ->where([
                'user_id' => $userId,
                'status' => UserWare::USER_WARE_STATUS_NORMAL,
                'ware_type' => ShopWares::SHOP_WARE_TYPE_PACKAGE,
            ])
            ->andFilterWhere([
                '>', 'expire_time', time(),
            ])
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit($maxWareLimit)
            ->all();

        $wareCt = 0;
        if (!empty($userWare)) {

            foreach ($userWare as $ware) {
                $ret = array_merge($ret, $this->_generateSubjectWithUserWare($ware, $level, $matchClass, $ct, $userId));
                if (!empty($ret)) {
                    $wareCt++;
//                            $totalCt = count($ret);
                    if ($wareCt >= 2
//                                || $totalCt >= $ct
                    ) {
                        break;
                    }
                }
//                switch ($ware->link_type) {
//                    case ShopWares::LINK_TYPE_QA_PACKAGE:
//                    default:
//                        $packageClass = [];
//                        if (!empty($matchClass)) {
//                            $packageClass = StoryMatch::$matchClass2PackageClass[$matchClass];
//                        }
//                        $qaCollections = $this->getQaByPackageIds([$ware->link_id], $packageClass);
//
//                        $ret = array_merge($ret, $this->generateSubjectsWithQaCollection($qaCollections, $level, $matchClass, $ct, $userId));
//                        if (!empty($ret)) {
//                            $wareCt++;
////                            $totalCt = count($ret);
//                            if ($wareCt >= 2
////                                || $totalCt >= $ct
//                            ) {
//                                break;
//                            }
//                        }
//                        break;
//                }
//                $ret[] = $this->getSubjectWithWare($ware, $matchClass, $level, $ct);
            }
        }
        $totalCt = count($ret);

        if ($totalCt < $ct) {
            $restCt = $ct - $totalCt;
            $ret = array_merge($ret, $this->generateSubjectWithDoubao($level, $matchClass, $restCt));
        }

        return $ret;
    }

    public function generateSubjectsWithQaCollection($qaCollections, $level = 1, $matchClass = 0, $ct = 10, $userId = 0) {
        $ret = [];
        if (!empty($qaCollections)) {
            foreach ($qaCollections as $qaPackageId => $qaCollection) {
                foreach ($qaCollection as $qaModel) {
                    $extends = [];
                    $genSub = true;
                    if ($qaModel->qa_class != Subject::SUBJECT_CLASS_ANY && $qaModel->qa_class != $matchClass) {
                        continue;
                    }
                    if (!empty($qaModel->prop)) {
                        // Todo: 准备读取qaProp，判断题目模式，引入相似题（增加例题字段）
                        $qaProp = json_decode($qaModel->prop, true);
//                                        if (!empty($qaProp['example_qa_ids'])) {$ct = $qaProp['ct'];
//                                        }
                        if (!empty($qaProp['user_qa'])) {
                            $userQaTmp = UserQa::find()
                                ->where([
                                    'user_id' => $userId,
                                    'is_right' => UserQa::ANSWER_WRONG,
                                ])
                                ->andFilterWhere([
                                    '>', 'updated_at', time() - 3600 * 24 * 3,
                                ])
                                ->orderBy(['updated_at' => SORT_DESC])
                                ->limit(20)
                                ->all();

                            $qaIds = [];

                            if (!empty($userQaTmp)) {
                                foreach ($userQaTmp as $uq) {
                                    $qaIds[] = $uq->qa_id;
                                }

                                $qaClass = !empty(StoryMatch::$matchClass2QaClass[$matchClass]) ? StoryMatch::$matchClass2QaClass[$matchClass] : 0;
                                $qaTmp = Qa::find()
                                    ->where([
                                        'id' => $qaIds,
                                        'qa_class' => $qaClass
                                    ])
                                    ->orderBy(['updated_at' => SORT_DESC])
                                    ->limit(5)
                                    ->all();

                                $exampleTopics = [];
                                if (!empty($qaTmp)) {
                                    foreach ($qaTmp as $q) {
                                        $exampleTopics[] = [
                                            'topic' => $q->topic
                                        ];
                                    }
                                    $extends['exampleTopics'] = $exampleTopics;
                                } else {
                                    $genSub = false;
                                }
                            } else {
                                $genSub = false;
                            }
                        }
                    } else {
                        $qaProp = [];
                    }
                    if ($genSub) {
                        $tmp = $this->getSubjectWithQa($qaModel, $matchClass, $level, $ct, $extends);

                        if (!empty($tmp)) {
                            foreach ($tmp as $t) {
                                $ret[] = $t;
                            }
                        }
                    }
                }
            }
        }

        return $ret;

    }

    public function generateSubjectWithDoubao($level, $matchClass, $ct, $prompt = '', $extends = [], $needSave = true) {

        if (empty($prompt)) {
            switch ($matchClass) {
                case Subject::SUBJECT_CLASS_MATH:
                    if ($level >= 1 && $level < 7) {
                        $prompt = '生成' . $ct . '道数学计算题目';
                    } else if ($level >= 7 && $level < 13) {
                        $prompt = '生成' . $ct . '道数学计算或者方程类题目';
                    } else if ($level >= 13 && $level < 19) {
                        $prompt = '生成' . $ct . '道数学方程类题目';
                    } else if ($level >= 19 && $level < 25) {
                        $prompt = '生成' . $ct . '道数学代数类题目';
                    } else {
                        $prompt = '生成' . $ct . '道数学题目';
                    }
                    $prompt .= "\n" . '题目中的数字和题目内容要随机生成，不能固定，不能重复';
                    $prompt .= "\n" . '题目中的计算方式和数字换一换，很随机';
                    break;
                case Subject::SUBJECT_CLASS_ENGLISH:
                    $prompt = '生成' . $ct . '道英语单词、短句题目';
                    if ($level > 7) {
                        $prompt = '生成' . $ct . '英语小短文的阅读理解';
                    }
                    break;
                case Subject::SUBJECT_CLASS_POEM:
                    $prompt = '生成' . $ct . '道诗词题目';
                    if ($level > 7) {
                        $prompt = '生成' . $ct . '道诗词理解题目';
                    }
                    break;
                case Subject::SUBJECT_CLASS_POEM_IDIOM:
                    $prompt = '生成' . $ct . '道成语题目';
                    if ($level > 7) {
                        $prompt = '生成' . $ct . '道成语理解题目';
                    }
                    break;
                case Subject::SUBJECT_CLASS_CHINESE:
                    $prompt = '生成' . $ct . '道语文题目';
                    if ($level > 7) {
                        $prompt .= "\n" . '包括：短文理解题目，即生成或摘取一段短文，然后提问';
                        $prompt .= "\n" . '题干中不包括短文内容，放入EXTEND字段';
                    }
                    break;
                case Subject::SUBJECT_CLASS_HISTORY:
                    $prompt = '生成' . $ct . '道历史题目';
                    $prompt .= "\n" . "包括：中国古代史，中国近代史，世界史等";
                    break;
            }
        }
//        $prompt = '';

//        $needSave = false;
        $subjects = Yii::$app->doubao->generateSubject($prompt, $level, $matchClass, $ct, $extends);

        $qaClass = !empty(StoryMatch::$matchClass2QaClass[$matchClass]) ? StoryMatch::$matchClass2QaClass[$matchClass] : $matchClass;

        $ret = [];
        if (!empty($subjects)) {
            if (key($subjects) == '0') {
                foreach ($subjects as $subj) {
                    $tmpSubj = \common\helpers\Qa::formatSubjectFromGPT($subj);
                    $tmpSubj = \common\helpers\Qa::generateChallengePropByLevel($level, $tmpSubj, $qaClass);
                    $tmpSubj = \common\helpers\Qa::formatChallengeProp($tmpSubj);
                    $ret[] = $tmpSubj;

                    if ($needSave) {
                        $qa = $this->saveQaByDoubao($tmpSubj, 0);
                    }
                }
            } else {
                $subj = $subjects;
                $tmpSubj = \common\helpers\Qa::formatSubjectFromGPT($subj);
                $tmpSubj = \common\helpers\Qa::generateChallengePropByLevel($level, $tmpSubj, $qaClass);
                $tmpSubj = \common\helpers\Qa::formatChallengeProp($tmpSubj);
                $ret[] = $tmpSubj;

                if ($needSave) {
                    $qa = $this->saveQaByDoubao($tmpSubj, 0);
                }
            }
        }

        return $ret;
    }

    public function saveQaByDoubao($doubaoSubject, $linkQaId = 0) {

        $topic = !empty($doubaoSubject['formula']) ? $doubaoSubject['formula'] : '';
        $level = !empty($doubaoSubject['level']) ? $doubaoSubject['level'] : 1;
        $qaClass = !empty($doubaoSubject['qa_class']) ? $doubaoSubject['qa_class'] : 0;

        $prop = !empty($doubaoSubject['prop']) ? $doubaoSubject['prop'] : [];

        $qa = Qa::find()
            ->where([
                'topic' => $topic,
                'level' => (int)$level,
            ]);
        if (!empty($qaClass)) {
            $qa = $qa->andFilterWhere([
                'qa_class' => $qaClass,
            ]);
        }
        if (!empty($linkQaId)) {
            $qa = $qa->andFilterWhere([
                'link_qa_id' => $linkQaId,
            ]);
        }
        $qa = $qa->one();
//        echo $qa->createCommand()->getRawSql();
//        echo "<br>";

        if (empty($qa)) {
            $qa = new Qa();
            $qa->topic = $topic;
            $qa->qa_class = $qaClass;
            $qa->link_qa_id = $linkQaId;
            $qa->level = $level;
//            var_dump($qa);
        }

        $oldProp = [];
        if (!empty($qa->prop)) {
            $oldProp = json_decode($qa->prop, true);
        }
        if (!empty($prop['point'])) {
            $oldProp['point'] = $prop['point'];
        }

        $qa->prop = json_encode($oldProp, JSON_UNESCAPED_UNICODE);


        $qa->qa_type = Qa::QA_TYPE_SINGLE;
        $qa->qa_mode = Qa::QA_MODE_MATCH;
        $qa->story_id = 5;          // 写死
        $qa->selected = !empty($doubaoSubject['selected']) ? $doubaoSubject['selected'] : '';
        $qa->st_answer = !empty($doubaoSubject['st_answer']) ? $doubaoSubject['st_answer'] : '';
        $qa->st_selected = !empty($doubaoSubject['st_answer']) ? $doubaoSubject['st_answer'] : '';
        $qa->score = !empty($doubaoSubject['gold']) ? $doubaoSubject['gold'] : 0;

//        $qa->prop = '';
        $qa->save();

        return $qa;
    }

    public function getQaByPackageIds($qaPackageIds, $packageClass = []) {
        $qaPackages = QaPackage::find()
            ->where([
                'id' => $qaPackageIds
            ]);
        if (!empty($packageClass)) {
            $qaPackages = $qaPackages->andFilterWhere([
                'package_class' => [
                    QaPackage::PACKAGE_CLASS_ANY,
                    $packageClass
                ],
            ]);
        }
        $qaPackages = $qaPackages->all();

        $qaCollections = [];
        if (!empty($qaPackages)) {
            foreach ($qaPackages as $qaPackage) {
                $qaIds = explode(',', $qaPackage->qa_ids);

                if (!empty($qaIds)) {
                    $qaCollections[$qaPackage->id] = Qa::find()
                        ->where([
                            'id' => $qaIds
                        ])
                        ->all();
                }
            }
        }

        return $qaCollections;
    }

    public function getEnglishClassByLevel($level) {
        if (empty($level)) {
            $level = 1;
        }

        $classes = ['', ''];
        foreach (EnglishWords::$level2WordClass1 as $lev => $classes) {
            if ($lev >= $level) {
                return $classes;
            }
        }
        return $classes;

    }

    public function generateWordWithEng($ct = 10, $level = 1, $englishClass = '', $englishClass2 = '') {
        $ret = [];

        if ($englishClass == 'auto'
            || $englishClass2 == 'auto'
        ) {
            $classTemp = $this->getEnglishClassByLevel($level);
            list($englishClass, $englishClass2) = $classTemp;
        }

        $eng = $this->getWordsByRand($ct, $level, $englishClass, $englishClass2);
        $engWithClass = $this->formatEnglishWithClass($eng);

        if (!empty($eng)) {
            $i = 0;
            foreach ($eng as $idx => $e) {
                $opts = $this->getOptionsFromWord($e, 3, 'word', $engWithClass, $eng);

                $ret[] = [
                    'formula' => $e->chinese,
                    'topic' => $e->chinese,
                    'size' => mb_strlen($e->chinese, 'utf-8') > 16 ? 40 : 60,
                    'answer' => $e->word,
                    'st_answer' => $e->word,
                    'standFormula' => $e->chinese,
                    'answerRange' => $opts,
                    'selected' => json_encode($opts, JSON_UNESCAPED_UNICODE),
                    'selected_json' => $opts,
                    'level' => 1,
                    'hitRange' => [5, 10],
                    'gold' => 10,
                    'prop' => ['point' => 'REMEMBER'],
                    'propJson' => json_encode(['point' => 'REMEMBER'], JSON_UNESCAPED_UNICODE),
                ];

                $i++;
                if ($i >= $ct) {
                    break;
                }
            }
        }

        return $ret;
    }

    public function generateWordWithChinese($ct = 10, $level = 1, $englishClass = '', $englishClass2 = '') {
        $ret = [];


        if ($englishClass == 'auto'
            || $englishClass2 == 'auto'
        ) {
            $classTemp = $this->getEnglishClassByLevel($level);
            list($englishClass, $englishClass2) = $classTemp;
        }
        $eng = $this->getWordsByRand($ct, $level, $englishClass, $englishClass2);
        $engWithClass = $this->formatEnglishWithClass($eng);


        if (!empty($eng)) {
            $i = 0;
            foreach ($eng as $idx => $e) {
                $opts = $this->getOptionsFromWord($e, 3, 'chinese', $engWithClass, $eng);

                $ret[] = [
                    'formula' => $e->word,
                    'topic' => $e->word,
                    'answer' => $this->_cutEnglishChineseStr($e->chinese, 16),
                    'st_answer' => $this->_cutEnglishChineseStr($e->chinese, 16),
                    'standFormula' => $e->word,
                    'answerRange' => $opts,
                    'selected' => json_encode($opts, JSON_UNESCAPED_UNICODE),
                    'selected_json' => $opts,
                    'level' => 1,
                    'hitRange' => [5, 10],
                    'gold' => 10,
                    'prop' => ['point' => ['REMEMBER']],
                    'propJson' => json_encode(['point' => ['REMEMBER']], JSON_UNESCAPED_UNICODE),
                ];

                $i++;
                if ($i >= $ct) {
                    break;
                }
            }
        }

        return $ret;
    }

    public function formatEnglishWithClass($engModel) {
        $ret = [];
        if (!empty($engModel)) {
            foreach ($engModel as $eModel) {
                $wordClass1 = '';
                if (!empty($eModel->word_class1)) {
                    $wordClass1 = $eModel->word_class1;
                }
                $wordClass2 = '';
                if (!empty($eModel->word_class2)) {
                    $wordClass2 = $eModel->word_class2;
                }
//                if (!empty($englishClass)
//                    && !empty($wordClass1)
//                    && $englishClass != $wordClass1) {
//                    continue;
//                }
//                if (!empty($englishClass2)
//                    && !empty($wordClass2)
//                    && $englishClass2 != $wordClass2) {
//                    continue;
//                }
                if (!empty($wordClass2)) {
                    $ret[$wordClass1][$wordClass2][] = $eModel;
                } else {
                    $ret[$wordClass1][] = $eModel;
                }
            }
        }
        return $ret;
    }

    public function getWordsByRand($ct = 1, $level = 0, $englishClass, $englishClass2 = '') {
//        $ret = [];

        $limitCt = $ct * 4;
        $eng = EnglishWords::find();
        if (!empty($englishClass)) {
            $eng = $eng->andFilterWhere([
                    'word_class1' => $englishClass,
                ]);
        }
        if (!empty($englishClass2)) {
            $eng = $eng->andFilterWhere([
                'word_class2' => $englishClass2,
            ]);
        }
        if (!empty($level)) {
            $eng = $eng->orFilterWhere([
                'level' => $level,
            ]);
        }
        $eng = $eng->andFilterWhere([
                '<>', 'chinese', '',
            ])
            ->orderBy('rand()')
            ->limit($limitCt)
//        var_dump($eng->createCommand()->getRawSql());exit;
        ->all();

        return $eng;

    }

    private function _cutEnglishChineseStr($str, $maxLen = 18) {
        $ret = $str;
        if (mb_strlen($str, 'utf-8') > $maxLen) {
            $strs = explode('，', $str);
            $tmpRet = [];
            $tmpLen = 0;
            foreach ($strs as $tmp) {
                $tmpLen += mb_strlen($tmp, 'utf-8') + 1;
                if ($tmpLen <= $maxLen) {
                    $tmpRet[] = $tmp;
                } else {
                    break;
                }
            }
            $ret = implode('，', $tmpRet);
        }

        return $ret;
    }

    public function getOptionsFromWord($englishWord, $optCt = 3, $col = 'word', $englishClass = [], $allWords = []) {
        $getCt = $optCt + 1;
//        var_dump($englishWord);
        $wordClass1 = '';
        if (!empty($englishWord->word_class1)) {
            $wordClass1 = $englishWord->word_class1;
        }
        $wordClass2 = '';
        if (!empty($englishWord->word_class2)) {
            $wordClass2 = $englishWord->word_class2;
        }

        $englishWords = [];
        if (!empty($wordClass1) && !empty($wordClass2)) {
            if (!empty($englishClass[$wordClass1][$wordClass2])) {
                $englishWords = $englishClass[$wordClass1][$wordClass2];
            }
        } else if (!empty($wordClass1) && !empty($englishClass[$wordClass1])) {
            $englishWords = $englishClass[$wordClass1];
        }

        if (count($englishWords) < $getCt) {
            $englishWords = $allWords;
        }

        $ret = [];

        $randIdx = array_rand($englishWords, $getCt);
        $ct = 0;
        foreach ($randIdx as $rIdx) {
            if ($englishWord->$col == $englishWords[$rIdx]->$col) {
                continue;
            }
            if ($col == 'chinese') {
                $ret[] = $this->_cutEnglishChineseStr($englishWords[$rIdx]->$col, 16);
            } else {
                $ret[] = $englishWords[$rIdx]->$col;
            }
            $ct++;
            if ($ct >= $optCt) {
                break;
            }
        }
        if ($col == 'chinese') {
            $ret[] = $this->_cutEnglishChineseStr($englishWord->$col, 16);
        } else {
            $ret[] = $englishWord->$col;
        }
        shuffle($ret);

        return $ret;
    }

    public function generatePoem($level = 1, $poemType = 0, $poemClass = 0, $poemClass2 = 0, $answerType = Poem::POEM_ANSWER_TYPE_WORD) {
        $prop = [
            'poem_class' => $poemClass,
            'poem_class2' => $poemClass2,
        ];
        $poem = $this->getPoemByRand($poemType, $prop, $answerType);

        $hitRange = [
            5 * (1 + ($level - 1) / 5),
            10 * (1 + ($level - 1) / 5),
        ];
        $gold = 10 * (1 + ($level - 1) / 2);

        $showFormula = $poem['formula'];
        $answer = $poem['stAnswer'];
        $formula = $poem['poem'];
        $answerRange = $poem['selections'];
        $size = !empty($poem['size']) ? $poem['size'] : 60;
        $image = !empty($poem['image']) ? $poem['image'] : '';

        $subjects = [
            'formula' => $showFormula,
            'topic' => $showFormula,
            'answer' => $answer,
            'image' => $image,
            'size' => $size,
            'st_answer' => $answer,
            'standFormula' => $formula,
            'answerRange' => $answerRange,
            'selected' => json_encode($answerRange, JSON_UNESCAPED_UNICODE),
            'selected_json' => $answerRange,
            'level' => $level,
            'hitRange' => $hitRange,
            'gold'  => $gold,
        ];

        return $subjects;
    }

    public function generateMath($level, $ct = 100, $gold = 0) {
        $subjects = [];
        if ($level <= 3) {
            for ($i = 0; $i < $ct; $i++) {
                // Todo: 测试用
//            if ($level > 3) $level = 1;
                $subjects[] = $this->generateOneMath($level, $gold);
                if ($i == 12) {
                    $level++;
//                        $subjects[] = $this->generateMath($level);
                }
            }
        } else {
            // Todo：临时强制保护
            $ct = 20;
//            $subjects = $this->generateSubjectWithDoubao($level, Subject::SUBJECT_CLASS_MATH, $ct);
            $subjects = $this->generateSubjectWithQa($level, $ct, Qa::QA_CLASS_MATH);
            if (count($subjects) < 10) {
                $gptSubjects = $this->generateMathWithDoubao($level, 10 - count($subjects));
                foreach ($gptSubjects as $gptSub) {
                    $subjects[] = $gptSub;
                }
            }
        }
        return $subjects;
    }

    public function generateChinese($level, $ct = 100, $gold = 0) {
            // Todo：临时强制保护
//            $ct = 20;
//            $subjects = $this->generateSubjectWithDoubao($level, Subject::SUBJECT_CLASS_MATH, $ct);
            $subjects = $this->generateSubjectWithQa($level, $ct, Qa::QA_CLASS_CHINESE);
            if (count($subjects) < 10) {
                $gptSubjects = $this->generateSubjectWithDoubao($level, Qa::QA_CLASS_CHINESE, 10 - count($subjects));
                foreach ($gptSubjects as $gptSub) {
                    $subjects[] = $gptSub;
                }
            }

        return $subjects;
    }

    public function generateSubjectWithQa($level = 0, $ct = 100, $qaClass = 0, $linkQaId = 0) {
        $qa = Qa::find();
        if (!empty($qaClass)) {
            $qa = $qa->andFilterWhere([
                'qa_class' => $qaClass,
            ]);
        }
//        if (!empty($linkQaId)) {
            $qa = $qa->andFilterWhere([
                'link_qa_id' => $linkQaId,
            ]);
//        }
        if (!empty($level)) {
            $qa = $qa->andFilterWhere([
                'level' => $level,
            ]);
        }
        $qa = $qa->orderBy('rand()')
            ->limit($ct)
            ->all();


        $ret = [];
        if (!empty($qa)) {
            foreach ($qa as $q) {
                $tmp = \common\helpers\Qa::formatSubjectFromQa($q);

                $ret[] = $tmp;

            }
        }

        return $ret;
    }

    public function generateMathWithDoubao($level, $ct = 5) {
        return $this->generateSubjectWithDoubao($level, Subject::SUBJECT_CLASS_MATH, $ct);
    }


    public function generateOneMath($level = 1, $gold = 0) {
        $subjects = [];
        switch ($level) {
            case 1:
                $subjects = $this->randMathFormula(2, 20, ['+','-'], $level, 1, $gold);
                break;
            case 2:
                $randNumCt = rand(2,3);
                $numMax= 20;
                if ($randNumCt == 2) {
                    $numMax = 100;
                }
                $subjects = $this->randMathFormula($randNumCt, $numMax, ['+','-'], $level, 2, $gold);
                break;
            case 3:
                $randNumCt = rand(2,3);
                $numMax= 100;
                $computeTag = ['+','-',];
                $mode = 2;
                if ($randNumCt == 2) {
                    $numMax = 10;
                    $computeTag = ['*'];
                    $mode = 1;
                }
                $subjects = $this->randMathFormula($randNumCt, $numMax, $computeTag, $level, $mode, $gold);
                break;
            case 4:
                $randNumCt = rand(2,3);
                $numMax= 100;
                $computeTag = ['+','-','*'];
                $mode = 2;
                if ($randNumCt == 2) {
                    $numMax = 10;
                    $computeTag = ['*','/'];
                    $mode = 1;
                }
                $subjects = $this->randMathFormula($randNumCt, $numMax, $computeTag, $level, $mode, $gold);
                break;
            default:
                $subjects = $this->getSubjectsFromDbByLevel($level, Subject::SUBJECT_CLASS_MATH, 1);
                break;
        }
        return $subjects;
    }

    public function getSubjectsFromDbByLevel($level = 1, $qaClass = 0, $ct = 10) {
        $qa = Qa::find();
        if (!empty($level)) {
            $qa = $qa->where([
                'level' => $level,
            ]);
        }
        if (!empty($qaClass)) {
            $qa = $qa->andFilterWhere([
                'qa_class' => $qaClass,
            ]);
        }
        $qa = $qa->orderBy('rand()')
            ->limit($ct)
            ->all();

        $ret = [];
        if (!empty($qa)) {
            foreach ($qa as $q) {
                $tmp = \common\helpers\Qa::formatSubjectFromQa($q);

                $ret[] = $tmp;

            }
        }

        return $ret;
    }

    public function getPoemById($poemId, $qaProp = [], $answerType = 0, $poemType, $ts = 0, $qaType = Qa::QA_TYPE_VERIFYCODE, $qaSelected = []) {
        if ($ts > 0) {
            srand($ts);
        }

        $poem = Poem::find()
            ->where([
                'id' => $poemId,
            ])
            ->one();

        if (empty($poem)) {
            return [];
        }

        $ret = $this->getPoemSubject($poem, $qaProp, $answerType);


        return $ret;
    }

    public function getPoemByRand($poemType = 0, $qaProp = [], $answerType = 0, $ts = 0, $qaType = Qa::QA_TYPE_VERIFYCODE, $qaSelected = []) {
        if ($ts > 0) {
            srand($ts);
        }

        $poem = Poem::find();
        if (!empty($poemType)) {
            $poem = $poem->andFilterWhere([
                'poem_type' => $poemType,
            ]);
        }
        if (!empty($qaProp['poem_class'])) {
            $poem = $poem->andFilterWhere([
                'poem_class' => $qaProp['poem_class'],
            ]);
        }
        if (!empty($qaProp['poem_class2'])) {
            $poem = $poem->andFilterWhere([
                'poem_class2' => $qaProp['poem_class2'],
            ]);
        }
        if (!empty($qaProp['level'])) {
            $poem = $poem->andFilterWhere([
                'level' => $qaProp['level'],
            ]);
        }
        if ($answerType == Poem::POEM_ANSWER_TYPE_TITLE_FROM_IMAGE) {
            $poem = $poem->andWhere([
                '<>', 'image', '',
            ]);
        }
        if ($answerType == Poem::POEM_ANSWER_TYPE_TITLE_FROM_STORY) {
            $poem = $poem->andWhere([
                '<>', 'story', '',
            ]);
        }

        $poem = $poem->orderBy('rand()')
            ->limit(1)
            ->one();

        if (empty($poem)) {
            return [];
        }

        $ret = $this->getPoemSubject($poem, $qaProp, $answerType);

        return $ret;
    }

    public function getPoemSubject($poem, $qaProp = [], $answerType = 0) {
        if (!empty($qaProp)) {
            if (empty($answerType)) {
                $answerType = !empty($qaProp['answer_type']) ? $qaProp['answer_type'] : 1;
            }
            $hole = !empty($qaProp['hole']) ? $qaProp['hole'] : 1;
        } else {
            $answerType = empty($answerType) ? Poem::POEM_ANSWER_TYPE_WORD : $answerType;
            $hole = 1;
        }

        $sentence = '';
        $retTemp = '';
        $answer = [];
//        $content = '';
        $content = $poem->content;
        switch ($answerType) {
            case Poem::POEM_ANSWER_TYPE_SENTENCE:
                // 猜上下句
                $ret = $this->getSentence($content, $hole, 0, $poem);
                break;
            case Poem::POEM_ANSWER_TYPE_TITLE:
            case Poem::POEM_ANSWER_TYPE_TITLE_FROM_IMAGE:
            case Poem::POEM_ANSWER_TYPE_TITLE_FROM_STORY:
            case Poem::POEM_ANSWER_TYPE_AUTHOR:
                // 猜标题 or 作者
                $ret = $this->getPoemTitle($poem, $answerType);
                break;
            case Poem::POEM_ANSWER_TYPE_WORD:
            default:
                // 填字
                $ret = $this->getWordFromSentence($content, $hole, 4, 0, $poem);
                break;

        }
        return $ret;
    }

    /*
     * 获取一个文字的相似文字
     */
    public function getSimilarWord($word, $ct = 3) {

//        $converter = new Chinese2pinyin();
//        $converter->transformWithoutTone($word, '', false);
//        return $ret;
        $pinyin = Yii::$app->chinesePinyin->transformWithoutTone($word, '', false);
        $wordList = Yii::$app->chinesePinyin->getWordFromPinyinWithoutTone($pinyin);

        for ($i=0; $i<$ct; $i++) {
            $ret[] = $wordList['word'][$pinyin][array_rand($wordList['word'][$pinyin])];
        }

        return $ret;

//        exit;
    }

    public function getSimilarTitleFromPoem($ct = 3, $answerType = 0, $rightPoem = null) {
        $poems = $this->getSimilarPoems($ct, $rightPoem);

        $ret = [];

        if (!empty($poems)) {
            foreach ($poems as $poem) {
                $ret[] = $poem->title;
            }
        }

        return $ret;
    }

    public function getSimilarSentenceFromPoem($ct = 3, $rightPoem = null) {
        $poems = $this->getSimilarPoems($ct, $rightPoem);

        $ret = [];

        if (!empty($poems)) {
            foreach ($poems as $poem) {
                $content = $poem->content;
                preg_match_all('/(.*?)([，。？]+)/u', $content, $matches);
                $sentRandom = array_rand($matches[1]);
                $content = $matches[1][$sentRandom];
                $ret[] = $content;
            }
        }

        return $ret;
    }

    public function getSimlarWordFromPoem($word, $ct = 3, $rightPoem = null) {

        // Todo：确定固定180首诗，随机抽取
        // 用代码的好处是，每次随机结果固定（srand设置）
//        for ($i=0; $i<$ct; $i++) {
//            $poemIds[] = rand(1,180);
//        }

        $poems = $this->getSimilarPoems($ct, $rightPoem);

//        $poems = Poem::find()
//            ->where([
//                'id' => $poemIds,
//            ])
//            ->all();

//        $pinyin = Yii::$app->chinesePinyin->transformWithoutTone($word, '', false);
//        $wordList = Yii::$app->chinesePinyin->getWordFromPinyinWithoutTone($pinyin);

        $ret = [];

        if (!empty($poems)) {
            foreach ($poems as $poem) {
                $content = $poem->content;
                $content = preg_replace('/([。？，《》 ]+)/u', '', $content);
                $ret[] = mb_substr($content, rand(0, mb_strlen($content, 'utf-8') - 1), 1, 'utf-8');
            }
        }

        return $ret;
    }

    public function getPoemTitle($poem, $answerType = 0) {
        if ($answerType == 0) {
//            $chosen = rand(1, 2);
            $randAnswerType = [
                Poem::POEM_ANSWER_TYPE_TITLE,
                Poem::POEM_ANSWER_TYPE_AUTHOR,
                Poem::POEM_ANSWER_TYPE_TITLE_FROM_IMAGE,
                Poem::POEM_ANSWER_TYPE_TITLE_FROM_STORY,
            ];
            $answerTypeIdx = array_rand($randAnswerType);
            $answerType = $randAnswerType[$answerTypeIdx];
        }

        // 1. 猜标题
        $content = $poem->content;
        $content = preg_replace('/([。？]+)/u', '${1}<br>', $content);
        $image = '';
        $retSize = '60';
        switch ($answerType) {
            case Poem::POEM_ANSWER_TYPE_AUTHOR:
                // 2. 猜作者
    //            $retTemp = $poem->author;
                $answer[] = [
                    'author' => $poem->author,
                ];
                $stAnswer = $poem->author;
                $retTemp = $content;
                break;
            case Poem::POEM_ANSWER_TYPE_TITLE_FROM_IMAGE:
                // 3. 看图片猜名字
                $answer[] = [
                    'title' => $poem->title,
                ];
                $stAnswer = $poem->title;
                $retTemp = '看图片猜名字';
                $retSize = '40';
                $image = Attachment::completeUrl($poem->image, true);
                break;
            case Poem::POEM_ANSWER_TYPE_TITLE_FROM_STORY:
                // 4. 看故事猜名字
                $answer[] = [
                    'title' => $poem->title,
                ];
                $stAnswer = $poem->title;
                $retTemp = str_replace("\n", "<br>", $poem->story);
                break;
            case Poem::POEM_ANSWER_TYPE_TITLE:
            default:
                //            $retTemp = $poem->title;
                $answer[] = [
                    'title' => $poem->title,
                ];
                $stAnswer = $poem->title;
                //            $retTemp = str_replace($poem->title, '(?)', $retTemp);
                $retTemp = $content;
                break;
        }

        $finalCollections = $this->getSimilarTitleFromPoem(3, $answerType, $poem);
        $finalCollections[] = $stAnswer;

        shuffle($finalCollections);

        $ret = [
            'formula' => $retTemp,
            'size' => $retSize,
            'topic' => $retTemp,
            'image' => $image,
            'answer' => $answer,
            'st_answer' => $stAnswer,
            'stAnswer' => $stAnswer,
            'poem' => $poem->content,
            'data' => $poem,
            'selections' => $finalCollections,
            'selected_json' => $finalCollections,
            'selected' => json_encode($finalCollections, JSON_UNESCAPED_UNICODE),
            'prop' => ['point' => ['REMEMBER']],
            'propJson' => json_encode(['point' => ['REMEMBER']], JSON_UNESCAPED_UNICODE),
        ];

        return $ret;
    }

    public function getSentence($content, $hole = 1, $ts = 0, $rightPoem = null) {
//        if ($ts > 0) {
//            srand($ts);
//        }
        $retTemp = '';
        $answer = [];
        $sentence = '';

        preg_match_all('/(.*?)([。！？；]+)/u', $content, $matches);
        if (!empty($matches[1])) {
            $retTempIdx = array_rand($matches[1]);
            $sentence = $retTemp = $matches[1][$retTempIdx] . $matches[2][$retTempIdx];

//            var_dump($retTemp);

            $sentArray = explode('，', $sentence);

            $answerIdx = array_rand($sentArray);

            $similarCollections = $this->getSimilarSentenceFromPoem(3, $rightPoem);

            $answer[$answerIdx] = [
                'i' => $answerIdx,
                'sentence' => $sentArray[$answerIdx],
                'similar' => $similarCollections
            ];

            $stAnswer = $sentArray[$answerIdx];

            $finalCollections = $similarCollections;
            $finalCollections[] = $sentArray[$answerIdx];
            shuffle($finalCollections);

            $retTemp = str_replace($sentArray[$answerIdx], '(?)', $retTemp);

        }

        $ret = [
            'formula' => $retTemp,
            'topic' => $retTemp,
            'answer' => $answer,
            'st_answer' => $stAnswer,
            'stAnswer' => $stAnswer,
            'poem' => $content,
            'sentence' => $sentence,
            'selections' => $finalCollections,
            'selected_json' => $finalCollections,
            'selected' => json_encode($finalCollections, JSON_UNESCAPED_UNICODE),
            'prop' => ['point' => ['REMEMBER']],
            'propJson' => json_encode(['point' => ['REMEMBER']], JSON_UNESCAPED_UNICODE),
        ];

        return $ret;


    }

    public function getWordFromSentence($content, $hole = 1, $resCt = 4, $ts = 0, $rightPoem = null) {

//        if ($ts > 0) {
//            srand($ts);
//        }

        $retTemp = '';
        $answer = [];
        $sentence = '';
        $finalCollection = [];

        $stAnswer = '';

        if (!empty($rightPoem) &&
            ($rightPoem->poem_type == Poem::POEM_TYPE_POEM
                || $rightPoem->poem_type == Poem::POEM_TYPE_POETRY
            )) {
            preg_match_all('/(.*?)([。？！；]+)/u', $content, $matches);
            if (!empty($matches[1])) {
                $retTempIdx = array_rand($matches[1]);

                $sentence = $retTemp = $matches[1][$retTempIdx] . $matches[2][$retTempIdx];
            }
        } else {
            $sentence = $retTemp = $content;
        }
            for ($i=0; $i < $hole; $i++) {

                $rndCt = rand(0, mb_strlen($retTemp, 'utf-8') - 2);
                $chosenWord = mb_substr($retTemp, $rndCt, 1, 'utf-8');
                if (in_array($chosenWord, ['。', '？', '，'])
                    || isset($answer[$rndCt])
                ) {
                    $i--;
                    continue;
                }


                $answer[$rndCt] = [
                    'i' => $rndCt,
                    'word' => $chosenWord,
//                    'similarPinyin' => $this->getSimilarWord($chosenWord),
                    'similar' => $this->getSimlarWordFromPoem($chosenWord, $resCt - 1, $rightPoem),
                ];
            }
            asort($answer);

            $resCollection = [];
            $tempCollection = [];

            $py = 0; // 偏移量
            $stAnswers = [];
            foreach ($answer as $i => $a) {
                $word = $a['word'];
//                $retTemp = str_replace($word, '(?)', $retTemp);
                $retTemp = \common\helpers\Common::mbSubStrReplace($retTemp, '(?)', $i + $py, 1);
                $py += 2;

                $ans = $a['word'];
                $stAnswers [] = $ans;
                $resCollection[] = $ans;
                $tempCollection += $a['similar'];
            }
            $stAnswer = implode(',', $stAnswers);

            $tempCollection = array_unique($tempCollection);
            $tempCollection = array_slice($tempCollection, 0, $resCt - count($resCollection));

//            var_dump($tempCollection);

            $finalCollection = array_merge($resCollection, $tempCollection);
            shuffle($finalCollection);


        $ret = [
            'formula' => $retTemp,
            'topic' => $retTemp,
            'answer' => $answer,
            'stAnswer' => $stAnswer,
            'st_answer' => $stAnswer,
            'poem' => $content,
            'sentence' => $sentence,
            'selections' => $finalCollection,
            'selected_json' => $finalCollection,
            'selected' => json_encode($finalCollection, JSON_UNESCAPED_UNICODE),
            'prop' => ['point' => ['REMEMBER']],
            'propJson' => json_encode(['point' => ['REMEMBER']], JSON_UNESCAPED_UNICODE),
        ];

        return $ret;
    }

    public function getSimilarPoems($ct, $rightPoem = null) {
        if (empty($rightPoem)) {
            for ($i = 0; $i < $ct; $i++) {
                $poemIds[] = rand(1, 180);
            }

            $poems = Poem::find()
                ->where([
                    'id' => $poemIds,
                ])
                ->all();
        } else {
            $rightPoemType = $rightPoem->poem_type;
            $rightPoemLevel = $rightPoem->level;
            $rightPoemClass = $rightPoem->poem_class;
            $rightPoemClass2 = $rightPoem->poem_class2;
            $rightPoemId = $rightPoem->id;

            $poems = Poem::find();
            if (!empty($rightPoemType)) {
                $poems = $poems->andFilterWhere([
                    'poem_type' => $rightPoemType,
                ]);
            }
            if (!empty($rightPoemLevel)) {
                $poems = $poems->andFilterWhere([
                    'level' => $rightPoemLevel,
                ]);
            }
            if (!empty($rightPoemClass)) {
                $poems = $poems->andFilterWhere([
                    'poem_class' => $rightPoemClass,
                ]);
            }
            if (!empty($rightPoemClass2)) {
                $poems = $poems->andFilterWhere([
                    'poem_class2' => $rightPoemClass2,
                ]);
            }
            if (!empty($rightPoemId)) {
                $poems = $poems->andFilterWhere([
                    '<>', 'id', $rightPoemId,
                ]);
            }
            $poems = $poems->orderBy('rand()')
                ->limit($ct)
                ->all();
        }

        return $poems;
    }


    public function randMathFormula($numCt = 2, $numMax = 20, $opRange = ['+','-','*','/'], $level, $mode = 1, $gold = 0){

        // $mode = 1: 答案最后
        // $mode = 2: 答案可以在中间


        $formula = '';

        $answerTag = 0;
        $nums = [];
        $tmpAnswer = 0;
        $op = '+';
        $tmpNumMax = $numMax;
        for ($i=0; $i<$numCt; $i++) {


            if ($mode == 2
                && $answerTag == 0
            ) {
                $answerTag1 = rand(0,1);
                $answerTag = $answerTag1;
            } else {
                $answerTag1 = 0;
            }

            if ($i > 0) {
                $opIdx = array_rand($opRange);
                $op = $opRange[$opIdx];
                if ($op == '-') {
                    $tmpNumMax = $tmpAnswer;
                } else {
                    $tmpNumMax = $numMax;
                }
                $nums[] = [
                    'num'    => $op,
//                    'num'   => $num,
//                    'answerTag' => $answerTag,
                ];
            }
            if ($op == '/') {
                $num = rand(1, $tmpNumMax);
            } else {
                $num = rand(0, $tmpNumMax);
            }

            $tmpAnswer = eval('return $tmpAnswer ' . $op . ' $num;');

            $nums[] = [
                'num'  => $num,
                'answerTag' => $answerTag1,
            ];
        }

        $formula = '';
        foreach ($nums as $numOne) {
            $formula .= $numOne['num'] . ' ';
        }

        eval('$ret = ' . $formula . ';');

        $showFormula = '';
        $isAnswerTag = 0;
        foreach ($nums as $numOne) {
            if ($isAnswerTag != 1) {
                $isAnswerTag = !empty($numOne['answerTag']) ? $numOne['answerTag'] : 0;
            }
            if ( !empty($numOne['answerTag']) && $numOne['answerTag'] == 1) {
                $showFormula .= '? ';
                $answer = $numOne['num'];
            } else {
                $showFormula .= $numOne['num'] . ' ';
            }
        }
        $showFormula .= '= ';
        if ($isAnswerTag == 1) {
            $showFormula .= $ret;
        } else {
            $showFormula .= '?';
            $answer = $ret;
        }

        $answerRange = $this->randAnswerRange($answer, 6);

        // 根据level算伤害范围和金币值
        $hitRange = [
            5 * (1 + ($level - 1) / 5),
            10 * (1 + ($level - 1) / 5),
        ];

        if (empty($gold)) {
            $gold = 10 * (1 + ($level - 1) / 2);
        }

        return [
            'formula' => $showFormula,
            'topic'     => $showFormula,
            'st_answer' => $answer,
            'answer' => $answer,
            'standFormula' => $formula,
            'answerRange' => $answerRange,
            'selected_json' => $answerRange,
            'selected' => json_encode($answerRange, JSON_UNESCAPED_UNICODE),
            'level' => $level,
            'hitRange' => $hitRange,
            'gold'  => $gold,
            'size' => 60,
            'speed_rate' => 1,
            'prop' => ['point' => ['COMPUTE']],
            'propJson' => json_encode(['point' => ['COMPUTE']], JSON_UNESCAPED_UNICODE),
        ];

    }

    public function randAnswerRange($answer, $mis = 5) {
        $answerRange = [$answer];
        $range1 = $answer > $mis ? rand($answer - $mis, $answer + $mis) : rand(0, $answer + $mis);
        if (in_array($range1, $answerRange)) {
            $range1++;
        }
        $answerRange[] = $range1;
//        $range1 = $range1 == $answer ? $range1 + 1 : $range1;
        $range2 = $range1 + rand(1,$mis);
        if (in_array($range2, $answerRange)) {
            $range2 = max($answerRange) + 1;
        }
        $answerRange[] = $range2;
        $range3 = ($range1 - $mis) < 0 ? $range2 + rand(1,$mis) : $range1 - rand(1,$mis);
        if (in_array($range3, $answerRange)) {
            $range3 = max($answerRange) + 1;
        }
        $answerRange[] = $range3;
//        $answerRange = [
//            $range1,
//            $range2,
//            $range3,
//            $answer
//        ];

        shuffle($answerRange);

        return $answerRange;
    }

}