<?php

use yii\db\Migration;
use yii\helpers\Json;
use yiier\region\models\Region;

/**
 * Handles the creation of table `region`.
 */
class m180607_101730_create_region_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('region', [
            'id' => $this->primaryKey(),
            'code' => $this->integer()->notNull()->defaultValue(0),
            'name' => $this->string(50)->notNull(),
            'parent_id' => $this->integer()->notNull()->defaultValue(0),
            'type' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('类型 0省 1市 3区')
        ]);
        $this->initData();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('region');
    }


    public function initData()
    {
        $path = \Yii::getAlias("@yiier/region");
        $filename = $path . DIRECTORY_SEPARATOR . "city.json";
        $handle = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        $items = Json::decode($contents);
        $model = new Region();
        foreach ($items as $provinceItem) {
            $_provinceModel = clone $model;
            $_provinceModel->setAttributes([
                'code' => $provinceItem['ad_code'],
                'name' => $provinceItem['title'],
                'parent_id' => 0,
                'type' => 0,
            ]);
            $_provinceModel->save();
            foreach ($provinceItem['child'] as $cityItem) {
                $_cityModel = clone $model;
                $_cityModel->setAttributes([
                    'code' => $cityItem['ad_code'],
                    'name' => $cityItem['title'],
                    'parent_id' => $_provinceModel->id,
                    'type' => 1,
                ]);
                $_cityModel->save();
                foreach ($cityItem['child'] as $districtItem) {
                    $_districtModel = clone $model;
                    $_districtModel->setAttributes([
                        'code' => $districtItem['ad_code'],
                        'name' => $districtItem['title'],
                        'parent_id' => $_cityModel->id,
                        'type' => 2,
                    ]);
                    $_districtModel->save();
                }
            }
        }
    }
}
