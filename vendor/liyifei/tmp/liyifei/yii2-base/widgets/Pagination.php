<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2018/2/23
 * Time: 下午4:20
 */

namespace liyifei\base\widgets;

use yii;

class Pagination extends yii\data\Pagination
{

    public $defaultPageSize = 10;

    public function init()
    {
        parent::init();

        $currentPage = Yii::$app->request->get('page', 0);
        if (!$currentPage) {
            $currentPage = Yii::$app->request->post('page', 0);
        }
        $this->setPage($currentPage);
    }

    public static function applyQuery(yii\db\ActiveQuery $query, $defaultPageSize = 10)
    {
        $pagination = new self([
            'totalCount' => $query->count(),
            'defaultPageSize' => $defaultPageSize
        ]);

        $query->offset($pagination->offset);
        $query->limit($pagination->limit);

        return $pagination;
    }

}