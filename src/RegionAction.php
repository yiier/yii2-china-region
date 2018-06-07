<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2018/6/7 20:10
 * description:
 */

namespace yiier\region;

use yii\base\Action;
use yii\helpers\Html;
use Yii;
use yiier\region\models\Region;

class RegionAction extends Action
{
    /**
     * @return array|string
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $parentId = Yii::$app->request->get('parent_id');
        /** @var Region $modelClass */
        $modelClass = Yii::createObject(Region::className());
        if ($parentId > 0) {
            return Html::renderSelectOptions('district', $modelClass::getRegion($parentId));
        } else {
            return [];
        }
    }
}