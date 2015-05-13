<?php

namespace andrew72ru\pen;
use yii\helpers\VarDumper;
use yii\widgets\InputWidget as Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;
use Michelf\MarkdownExtra;
use yii\helpers\Markdown;

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
        ToMarkdownAsset::register($view);

        $this->initJs = <<<JS
var tomd = function(text) {
    var options = {
        converters: [
            {
                filter: ['br'],
                replacement: function(content) { return '<br>'; }
            },
            {
                filter: ['div'],
                replacement: function(content, node) {
                    var retObj = document.createElement('div');
                    $(node).each(function(){
                        $.each(this.attributes, function(){
                            if(this.specified) { $(retObj).attr(this.name, this.value) }
                        });
                    });
                    $(retObj).attr('markdown', 1);
                    $(retObj).html(content);
                    var wrapper = document.createElement('div');
                    wrapper.appendChild(retObj)
                    return wrapper.innerHTML;
                }
            }
        ]
    };
    var converted = toMarkdown(text, options);
    return converted;
}
JS;
        $view->registerJs($this->initJs, $view::POS_HEAD);

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

            if(isset($this->clientOptions['inline']))
            {
                $value = Markdown::processParagraph(Html::getAttributeValue($this->model, $this->attribute));
                echo Html::tag($this->clientOptions['tag'] ?: 'p', $value, $tagOptions);
            }
            else
            {
                $value = MarkdownExtra::defaultTransform(Html::getAttributeValue($this->model, $this->attribute));
                echo Html::tag('section', Html::tag($this->clientOptions['tag'] ?: 'p', $value), $tagOptions);
            }

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
            $menuList = ['italic', 'bold', 'underline'];

        $defaultOptions = Json::encode([
            'editor' => new JsExpression('document.getElementById(\'' . $this->targetId . '\')'),
            'class' => 'pen',
            'debug' => false,
            'stay' => false,
            'list' => $menuList
        ]);

        $view = $this->getView();

        // Add editor to object
        $view->registerJs("var editor = new Pen({$defaultOptions});");

        // Remove additional form elements (because i can, that why)
        $view->registerJs("$('#{$this->targetId}').parents('.form-group').find('label,.help-block').remove()");

        // Add a html to markdown converter to content
        $view->registerJs("$('#{$this->targetId}').on('click focus blur keyup',
            function(e){ $('#' + $(this).data('target')).val(tomd($(this).html())); });");

        // Do not add line break if it inline editor
        if(isset($this->clientOptions['inline']))
        {
            $view->registerJs("$('#{$this->targetId}').on('keypress', function(e){ if(e.keyCode == 13) return false; });");
        }
    }
}
