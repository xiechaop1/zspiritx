<?php

$arr = array (
  'Name' => '小犀牛Jack',
  'Intro' => 'Rhino-Jack-dialog-1',
  'ActionOnPlaced' => 
  array (
    'localID' => 'Rhino-Jack-OnPlaced',
    'hideModels' => 
    array (
    ),
    'moveX' => 0,
    'moveY' => 0,
    'moveZ' => 0,
  ),
  'Dialog' => 
array (
    array (
      'localID' => 'Rhino-Jack-dialog-1',
      'name' => '小犀牛Jack',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '谢谢你！不过虽然我被救下来了，但是其实好多犀牛哥哥姐姐都被杀死了',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-Jack-dialog-2',
      ),
    ),
    array (
      'localID' => 'Rhino-Jack-dialog-2',
      'name' => '小犀牛Jack',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '我看到他们一个个倒下，哀怨的眼神，我真的无助而又害怕！',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-Jack-dialog-3',
      ),
    ),
    array (
      'localID' => 'Rhino-Jack-dialog-3',
      'name' => '小犀牛Jack',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '我们的种族就是这么慢慢没了，越来越少的亲人了，未来还不知道是什么样子……',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-Jack-dialog-4',
      ),
    ),
    array (
      'localID' => 'Rhino-Jack-dialog-4',
      'name' => '小犀牛Jack',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '这次多亏了你！我要去找妈妈了！',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-Jack-dialog-5',
      ),
    ),
    array (
      'localID' => 'Rhino-Jack-dialog-5',
      'name' => '小犀牛Jack',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '对了！我刚才和小斑马Lucy在这里玩，它碰到了迁徙的斑马群，就跟着过去了。临走的时候他说他要去和大象伯伯告个别，后来我就不知道了',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-Jack-dialog-6',
      ),
    ),
    array (
      'localID' => 'Rhino-Jack-dialog-6',
      'name' => '小犀牛Jack',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '大象伯伯在那幅画那里，它经常出来陪我们玩，你去看看吧',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-Jack-dialog-7',
      ),
    ),
    array (
      'localID' => 'Rhino-Jack-dialog-7',
      'name' => '小犀牛Jack',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '大象伯伯可能在睡觉，但是他鼻子很灵，你用他喜欢的香炉吸引，他就会出来了，但是香炉找不到了',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-Jack-dialog-8',
      ),
    ),
    array (
      'localID' => 'Rhino-Jack-dialog-8',
      'name' => '',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => 'https://h5.zspiritx.com.cn/processh5/actions?user_id={$user_id}&session_id={$session_id}&session_stage_id={$session_stage_id}&story_id={$story_id}&act_type=11&act_detail=ZRBWG-EXP-ElephantSoul&expiration_interval=600',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-Jack-dialog-End',
      ),
    ),
    array (
      'localID' => 'Rhino-Jack-dialog-End',
      'name' => '小犀牛Jack',
      'sentence' => '',
      'quizID' => 0,
      'sentenceClip' => '',
      'url' => '',
      'userSelections' => 
      array (
      ),
      'nextID' => 
      array (
        0 => 'Rhino-Jack-dialog-4',
      ),
    ),
  ),
);

var_dump(json_encode($arr));
