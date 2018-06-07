china region for Yii2
=====================
Yii2中国省市区三级联动

[![Latest Stable Version](https://poser.pugx.org/yiier/yii2-china-region/v/stable)](https://packagist.org/packages/yiier/yii2-china-region) 
[![Total Downloads](https://poser.pugx.org/yiier/yii2-china-region/downloads)](https://packagist.org/packages/yiier/yii2-china-region) 
[![Latest Unstable Version](https://poser.pugx.org/yiier/yii2-china-region/v/unstable)](https://packagist.org/packages/yiier/yii2-china-region) 
[![License](https://poser.pugx.org/yiier/yii2-china-region/license)](https://packagist.org/packages/yiier/yii2-china-region)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiier/yii2-china-region "*"
```

or add

```
"yiier/yii2-china-region": "*"
```

to the require section of your `composer.json` file.

Migrations
------------

Run the following command

```
php yii migrate --migrationPath=@yiier/region/migrations/
```

配置
-------

在 controller 中添加以下 action

```php
public function actions()
{
    $actions = parent::actions();
    $actions['get-region'] = [
        'class' => \yiier\region\RegionAction::className(),
    ];
    return $actions;
}
```

使用
-------

view 页面

```php
<?= $form->field($model, 'district')->widget(\yiier\region\RegionWidget::className(), [
    'model' => $model,
    'url' => \yii\helpers\Url::toRoute(['get-region']),
    'province' => [
        'attribute' => 'province',
        'items' => Region::getRegion(),
        'options' => ['class' => 'form-control form-control-inline', 'prompt' => '选择省份']
    ],
    'city' => [
        'attribute' => 'city',
        'items' => Region::getRegion($model->province),
        'options' => ['class' => 'form-control form-control-inline', 'prompt' => '选择城市']
    ],
    'district' => [
        'attribute' => 'district',
        'items' => Region::getRegion($model['city']),
        'options' => ['class' => 'form-control form-control-inline', 'prompt' => '选择县/区']
    ]
])->label(false); ?>
```
province 为省份配置，可用的选项可以查看 Html::dropdownList。如果不需要县/区，可以把 district 删除。

**可选功能：使用 behaviors 轻松获取省、市、区的名称**

你需要的 Model 主题 里添加 behaviors（注意：Model 主表字段不要命名为 province，会冲突，会导致此功能用不了，建议字段命名为 province_id；city 和 district 字段同理）

```php
/**
* @inheritdoc
*/
public function behaviors()
{
    return [
        'region' => [
            'class' => \yiier\region\RegionBehavior::className(),
            'provinceAttribute' => 'provinceId', // 可选参数 默认 province_id
            'cityAttribute' => 'cityId', // 可选参数 默认 city_id
            'districtAttribute' => 'districtId' // 可选参数 默认 district_id
        ],
    ];
}
```

然后可以通过下面方法获取到省、市、区以及省市区的名称：

```php
$model = Model::findOne($id);
$model->province['name'];
$model->city['name'];
$model->district['name'];
$model->fullRegion;
```

Credits
--------

[chenkby/yii2-region](https://github.com/chenkby/yii2-region)