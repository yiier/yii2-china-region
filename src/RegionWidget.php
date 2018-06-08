<?php

namespace yiier\region;

use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2018/6/7 20:07
 * description:
 */
class RegionWidget extends Widget
{
    public $model = null;

    /**
     * @var string 此属性不用处理
     */
    public $attribute;

    /**
     * @var array 省份配置
     */
    public $province = [];

    /**
     * @var array 城市配置
     */
    public $city = [];

    /**
     * @var array 县/区配置
     */
    public $district = [];

    /**
     * @var array 街道配置
     */
    public $street = [];

    /**
     * @var mixed 数据源
     */
    public $url;

    /**
     * @var bool 是否展示包括超出范围的区
     */
    public $showOutOfRange = false;


    public function init()
    {
        if (!$this->model) {
            throw new InvalidParamException('model不能为null!');
        }
        if (empty($this->province) || empty($this->city)) {
            throw new InvalidParamException('province和city不能为空！');
        }
        $cityId = Html::getInputId($this->model, $this->city['attribute']);
        if (empty($this->city['options']['prompt'])) {
            $this->city['options']['prompt'] = '选择城市';
        }
        $cityDefault = Html::renderSelectOptions('city', ['' => $this->city['options']['prompt']]);
        $joinChar = strripos($this->url, '?') ? '&' : '?';
        $url = $this->url . $joinChar;
        $outOfRangeUrl = ($this->showOutOfRange) ? '""' : '"&out_of_range=0"';

        $streetJs = '';
        if (!empty($this->street)) {
            if (empty($this->street['options']['prompt'])) {
                $this->street['options']['prompt'] = '选择街道';
            }
            $streetId = Html::getInputId($this->model, $this->street['attribute']);
            $streetDefault = Html::renderSelectOptions('street', ['' => $this->street['options']['prompt']]);
            $this->district['options'] = ArrayHelper::merge($this->district['options'], [
                'onchange' => "
                    if($(this).val() != ''){
                        $.get('{$url}parent_id='+$(this).val()+{$outOfRangeUrl}, function(data) {
                            $('#{$streetId}').html('{$streetDefault}'+data);
                        })
                    }else{
                        $('#{$streetId}').html('{$streetDefault}');
                    }
                "
            ]);
            $streetJs = "$('#{$streetId}').html('{$streetDefault}');";
        }

        $districtJs = '';
        if (!empty($this->district)) {
            if (empty($this->district['options']['prompt'])) {
                $this->district['options']['prompt'] = '选择县/区';
            }
            $districtId = Html::getInputId($this->model, $this->district['attribute']);
            $districtDefault = Html::renderSelectOptions('district', ['' => $this->district['options']['prompt']]);
            $this->city['options'] = ArrayHelper::merge($this->city['options'], [
                'onchange' => "
                    if($(this).val() != ''){
                        $.get('{$url}parent_id='+$(this).val()+{$outOfRangeUrl}, function(data) {
                            $('#{$districtId}').html('{$districtDefault}'+data);
                        })
                    }else{
                        $('#{$districtId}').html('{$districtDefault}');
                    }
                    {$streetJs}
                "
            ]);
            $districtJs = "$('#{$districtId}').html('{$districtDefault}');";

        }

        $this->province['options'] = ArrayHelper::merge($this->province['options'], [
            'onchange' => "
                if($(this).val()!=''){
                    $.get('{$url}parent_id='+$(this).val()+{$outOfRangeUrl}, function(data) {
                        $('#{$cityId}').html('{$cityDefault}'+data);
                    })
                }else{
                    $('#{$cityId}').html('{$cityDefault}');
                }
                {$districtJs}
                {$streetJs}
            "
        ]);
    }

    public function run()
    {
        $output[] = Html::activeDropDownList($this->model, $this->province['attribute'], $this->province['items'],
            $this->province['options']);
        $output[] = Html::activeDropDownList($this->model, $this->city['attribute'], $this->city['items'],
            $this->city['options']);
        if (!empty($this->district)) {
            $output[] = Html::activeDropDownList($this->model, $this->district['attribute'], $this->district['items'],
                $this->district['options']);
        }
        if (!empty($this->street)) {
            $output[] = Html::activeDropDownList($this->model, $this->street['attribute'], $this->street['items'],
                $this->street['options']);
        }
        return implode("\n", $output);
    }
}