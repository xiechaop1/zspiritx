<?php

$arr = array (
  'Name' => '犀牛叔叔Rhino',
  'Intro' => 'Rhino-dialog-1',
  'ActionOnPlaced' => 
  array (
    'localID' => 'Rhino-OnPlaced',
    'hideModels' => 
    array (
    ),
    'moveX' => 0.25,
    'moveY' => 0,
    'moveZ' => 0.25,
  ),
  'Dialog' => 
  array (
    0 => 
    array (
      'localID' => 'Rhino-dialog-1',
      'name' => '犀牛叔叔Rhino',
      'sentence' => '你是谁？你不要过来，我不相信你们人类！',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-dialog-pause-1',
      ),
    ),
    array (
      'localID' => 'Rhino-dialog-2',
      'name' => '犀牛叔叔Rhino',
      'sentence' => '唔，你就站在那里！虽然我们犀牛没有天敌，但是现在快濒临灭绝，全是你们人类害的！',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-dialog-3',
      ),
    ),
    array (
      'localID' => 'Rhino-dialog-3',
      'name' => '犀牛叔叔Rhino',
      'sentence' => '看在你喂我吃的份上，这样，你回答上我的问题，让我知道你是无害的，咱们再说！',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-QA-1',
      ),
    ),
    array (
      'localID' => 'Rhino-QA-1',
      'name' => '犀牛叔叔Rhino',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => 'https://h5.zspiritx.com.cn/qah5/qa_one?id=16&user_id={$user_id}&session_id={$session_id}&session_stage_id={$session_stage_id}',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-QA-2',
        ‘Rhino-QA-Wrong',
      ),
    ),
    array (
      'localID' => 'Rhino-QA-2',
      'name' => '犀牛叔叔Rhino',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => 'https://h5.zspiritx.com.cn/qah5/qa_one?id=16&user_id={$user_id}&session_id={$session_id}&session_stage_id={$session_stage_id}',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-QA-3',
        ‘Rhino-QA-Wrong',
      ),
    ),
    array (
      'localID' => 'Rhino-QA-3',
      'name' => '犀牛叔叔Rhino',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => 'https://h5.zspiritx.com.cn/qah5/qa_one?id=16&user_id={$user_id}&session_id={$session_id}&session_stage_id={$session_stage_id}',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-QA-4',
        ‘Rhino-QA-Wrong',
      ),
    ),
    array (
      'localID' => 'Rhino-QA-4',
      'name' => '犀牛叔叔Rhino',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => 'https://h5.zspiritx.com.cn/qah5/qa_one?id=16&user_id={$user_id}&session_id={$session_id}&session_stage_id={$session_stage_id}',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-dialog-4',
        ‘Rhino-QA-Wrong',
      ),
    ),
    array (
      'localID' => 'Rhino-dialog-4',
      'name' => '犀牛叔叔Rhino',
      'sentence' => 'Jack真的是太不听话了！这去哪儿了呢？要知道，虽然我们犀牛，除了人类，是没有天敌的，但是我的宝宝Jack实在太小了，还不会用角防御，这就有可能有危险了！',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-dialog-4',
      ),
    ),
    array (
      'localID' => 'Rhino-dialog-5',
      'name' => '',
      'sentence' => '谁会吃肉呢？谁呢？会不会是鳄鱼！帮我去问问他，是不是他吃了我的宝宝！',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-dialog-END',
      ),
    ),
    array (
      'localID' => 'Rhino-dialog-End',
      'name' => '犀牛叔叔Rhino',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-dialog-3',
      ),
    ),
    array (
      'localID' => 'Rhino-dialog-pause-1',
      'name' => '犀牛叔叔Rhino',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-dialog-1',
      ),
    ),
    array (
      'localID' => 'Rhino-QA-Wrong',
      'name' => '犀牛叔叔Rhino',
      'sentence' => '你这看来也不太知道，你再去学学，我等等你！',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-dialog-End',
      ),
    ),
  ),
);

var_dump(json_encode(arr));
