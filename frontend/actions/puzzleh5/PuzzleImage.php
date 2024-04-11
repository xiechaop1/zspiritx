<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\puzzleh5;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\models\Order;
use common\models\Story;
use common\models\StoryModels;
use common\models\User;
use common\models\UserModels;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class PuzzleImage extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;

        $qaId = !empty($_GET['qa_id']) ? $_GET['qa_id'] : 0;

        $qaOne = Qa::find()
            ->where([
                'id'    => $qaId,
            ])
            ->one();

        if (empty($qaOne)) {
            throw new NotFoundHttpException('QA not found');
        }
        $qaOne = $qaOne->toArray();
        $qaOne['selected_json'] = \common\helpers\Common::isJson($qaOne['selected']) ? json_decode($qaOne['selected'], true) : $qaOne['selected'];
        $qaOne['attachment'] = \common\helpers\Attachment::completeUrl($qaOne['attachment'], true);

        if (!empty($qaOne['selected_json']['rows'])) {
            $rows = $qaOne['selected_json']['rows'];
        } else {
            $rows = !empty($_GET['rows']) ? $_GET['rows'] : 3;
        }

        if (!empty($qaOne['selected_json']['cols'])) {
            $cols = $qaOne['selected_json']['cols'];
        } else {
            $cols = !empty($_GET['cols']) ? $_GET['cols'] : 3;
        }

        $keyStoryModels = [];
        $iList = [];
        if (!empty($qaOne['selected_json'])
            && $qaOne['selected_json']['keyboard'] == 'bagitems'
        ) {
            $keyboardConfig = $qaOne['selected_json'];

            if ($keyboardConfig['keyboard'] == 'bagitems') {
                if (!empty($keyboardConfig['inc_story_model_ids'])) {
                    $incIds = [];
                    $keyStoryModelsConf = [];
                    foreach ($keyboardConfig['inc_story_model_ids'] as $incStoryModelId => $incStoryModelConf) {
                        $incIds[] = $incStoryModelId;
                        $keyStoryModelsConf[$incStoryModelId] = $incStoryModelConf;
                    }

                    if (!empty($keyboardConfig['force_exist'])
                        && $keyboardConfig['force_exist'] == 1
                    ) {
                        $tmpStoryModels = StoryModels::find()
                            ->where([
                                'id' => $incIds,
                            ])
                            ->all();
                        if (!empty($tmpStoryModels)) {
                            foreach ($tmpStoryModels as $sm) {
                                $incStoryModelConf = !empty($keyStoryModelsConf[$sm->id]) ? $keyStoryModelsConf[$sm->id] : [];
                                if (!empty($incStoryModelConf['right_val'])) {
                                    $iList[$incStoryModelConf['right_val']] = [
                                        'conf' => $incStoryModelConf,
                                        'storyModel' => $sm
                                    ];
                                    $keyStoryModels[$incStoryModelConf['right_val']] = $sm;
                                } else {
                                    $iList[] = [
                                        'conf' => $incStoryModelConf,
                                        'storyModel' => $sm
                                    ];
                                    $keyStoryModels[] = $sm;
                                }

                            }
                        }
                        shuffle($keyStoryModels);
                    } else {
                        $incBagItems = UserModels::find()
                            ->where([
                                'story_model_id' => $incIds,
                                'user_id' => $userId,
                                'session_id' => $sessionId,
                                'is_delete' => \common\definitions\Common::STATUS_NORMAL,
                            ]);
                        if (!empty($storyId)) {
                            $incBagItems = $incBagItems->andFilterWhere(['story_id' => $storyId]);
                        }
                        $incBagItems = $incBagItems->all();

                        if (!empty($incBagItems)) {
                            foreach ($incBagItems as $ibi) {
                                if (!empty($ibi->storyModel)) {
                                    $incStoryModelConf = !empty($keyStoryModelsConf[$ibi->storyModel->id]) ? $keyStoryModelsConf[$ibi->storyModel->id] : [];
                                    if (!empty($incStoryModelConf['right_val'])) {
                                        $iList[$incStoryModelConf['right_val']] = [
                                            'conf' => $incStoryModelConf,
                                            'storyModel' => $ibi->storyModel
                                        ];
                                        $keyStoryModels[$incStoryModelConf['right_val']] = $ibi->storyModel;
                                    } else {
                                        $iList[] = [
                                            'conf' => $incStoryModelConf,
                                            'storyModel' => $ibi->storyModel
                                        ];
                                        $keyStoryModels[] = $ibi->storyModel;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        return $this->controller->render('puzzle_image', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'sessionStageId'    => $sessionStageId,
            'iList'      => $iList,
            'keyStoryModels'      => $keyStoryModels,
            'qaId'          => $qaId,
            'rows'          => $rows,
            'cols'          => $cols,
            'storyId'       => $qaOne['story_id'],
            'rightAnswer'   => $qaOne['st_answer'],
            'keyboard'      => $qaOne['selected_json'],
//            'keyboardArray'    => $keyboardArray,
            'qa'         => $qaOne,
        ]);
    }

}