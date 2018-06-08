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
            'code' => $this->integer()->notNull()->defaultValue(0)->comment('省市区编码'),
            'name' => $this->string(50)->notNull(),
            'parent_id' => $this->integer()->notNull()->defaultValue(0),
            'out_of_range' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('是否超区 0否 1超过范围'),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('状态 1正常 0停用'),
            'type' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('类型 0省 1市 2区 3街道')
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
        // 2018年6月8日 数据来源为中通全部的省市区地址集信息： http://japi.zto.cn/baseAreaGetAll?msg_type=GET_ALL
        $filename = $path . DIRECTORY_SEPARATOR . "baseAreaGetAll.json";
        $handle = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        $items = Json::decode($contents)['result'];
        $model = new Region();
        foreach ($items as $provinceItem) {
            $_provinceModel = clone $model;
            $_provinceModel->setAttributes([
                'code' => $provinceItem['code'],
                'name' => $provinceItem['fullName'],
                'out_of_range' => $provinceItem['outofrange'],
                'parent_id' => 0,
                'type' => 0,
            ]);
            $_provinceModel->save();
            $provinceItems = $provinceItem['sub'];
            foreach ($provinceItems as $cityItem) {
                $_cityModel = clone $model;
                $_cityModel->setAttributes([
                    'code' => $cityItem['code'],
                    'name' => $cityItem['fullName'],
                    'out_of_range' => $cityItem['outofrange'],
                    'parent_id' => $_provinceModel->id,
                    'type' => 1,
                ]);
                $_cityModel->save();
                $cityItems = $cityItem['sub'];
                foreach ($cityItems as $districtItem) {
                    $_districtModel = clone $model;
                    $_districtModel->setAttributes([
                        'code' => $districtItem['code'],
                        'name' => $districtItem['fullName'],
                        'out_of_range' => $districtItem['outofrange'],
                        'parent_id' => $_cityModel->id,
                        'type' => 2,
                    ]);
                    $_districtModel->save();
                    $districtItems = $districtItem['sub'];
                    foreach ($districtItems as $streetItem) {
                        $_streetModel = clone $model;
                        $_streetModel->setAttributes([
                            'code' => $streetItem['code'],
                            'name' => $streetItem['fullName'],
                            'out_of_range' => $streetItem['outofrange'],
                            'parent_id' => $_districtModel->id,
                            'type' => 3,
                        ]);
                        $_streetModel->save();
                    }
                }
            }
        }

    }
}
