<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/14
 * Time: 下午11:30
 */

namespace frontend\controllers;


use liyifei\base\controllers\ViewController;
use yii\web\Controller;

class Puzzleh5Controller extends Controller
{
    public $layout = '@frontend/views/layouts/main_h5.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'picture' => [
                'class' => 'frontend\actions\puzzleh5\Picture',
            ],
            'puzzle' => [
                'class' => 'frontend\actions\puzzleh5\Puzzle',
            ],
            'puzzle_word' => [
                'class' => 'frontend\actions\puzzleh5\PuzzleWord',
            ],
            'puzzle_sudoku' => [
                'class' => 'frontend\actions\puzzleh5\PuzzleSudoku',
            ],
            'puzzle_image' => [
                'class' => 'frontend\actions\puzzleh5\PuzzleImage',
            ],
        ];
    }
}