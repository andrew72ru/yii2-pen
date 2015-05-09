<?php

namespace andrew72ru\pen;
use yii\helpers\VarDumper;
use yii\widgets\InputWidget as Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * This is just an example.
 */
class Pen extends Widget
{
    public $clientOptions;
    private $initJs;
    private $targetId;

    public function init()
    {
        parent::init();
        $this->targetId = $this->options['id'] . '-e';
        $view = $this->getView();
        PenAsset::register($view);
        $this->initJs = <<<JS

JS;

    }

    public function run()
    {
        if($this->hasModel())
        {
            $tagOptions =  ArrayHelper::merge($this->options, [
                'id' => $this->targetId,
                'class' => $this->clientOptions['class'] ?: '',
                'data' => [
                    'target' => $this->options['id']
                ]
            ]);
            if(isset($this->clientOptions['inline']) && isset($this->clientOptions['tag']))
                echo Html::tag($this->clientOptions['tag'], (Html::getAttributeValue($this->model, $this->attribute)), $tagOptions);
            else
                echo Html::tag('section', Html::tag('p', Html::getAttributeValue($this->model, $this->attribute)), $tagOptions);

            echo Html::activeHiddenInput($this->model, $this->attribute);
        }

        $this->registerPlugin();
    }

    public function registerPlugin()
    {
        if(isset($this->clientOptions['list']) && is_array($this->clientOptions['list']))
        {
            $menuList = $this->clientOptions['list'];
        } else
        {
            $menuList = [
                'insertimage',
                'blockquote',
                'h2',
                'h3',
                'p',
                'insertorderedlist',
                'insertunorderedlist',
                'inserthorizontalrule',
                'indent',
                'outdent',
                'bold',
                'italic',
                'underline',
                'createlink',
            ];
        }
        if(isset($this->clientOptions['inline']))
            $menuList = ['italic'];

        $defaultOptions = [
            'editor' => new JsExpression('document.getElementById(\'' . $this->targetId . '\')'),
            'class' => 'pen',
            'debug' => false,
            'stay' => false,
            'list' => $menuList
        ];
        $json = Json::encode($defaultOptions);
        $this->getView()->registerJs("var editor = new Pen({$json});");
        $this->getView()->registerJs("$('#{$this->targetId}').parents('.form-group').find('label,.help-block').remove()");
        $this->getView()->registerJs("$('#{$this->targetId}').on('click focus blur keyup', function(e){ $('#' + $(this).data('target')).val($(this).html()); });");
    }
}
