<?php

namespace yiier\region\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%region}}".
 *
 * @property int $id
 * @property int $code
 * @property string $name
 * @property int $parent_id
 * @property int $type 类型 0省 1市 3区
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * @var integer 省
     */
    const TYPE_PROVINCE = 0;

    /**
     * @var integer 市
     */
    const TYPE_CITY = 1;

    /**
     * @var integer 区
     */
    const TYPE_DISTRICT = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%region}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'parent_id', 'type'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'type' => Yii::t('app', 'Type'),
        ];
    }

    public static function getRegion($parentId = 0)
    {
        $result = static::find()->where(['parent_id' => $parentId])->asArray()->all();
        return ArrayHelper::map($result, 'id', 'name');
    }
}
