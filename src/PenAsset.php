<?php
namespace andrew72ru\pen;

use yii\web\AssetBundle;

class PenAsset extends AssetBundle
{
    public $sourcePath = '@andrew72ru/pen';
    public $css = [
        'custom-pen.css',
    ];
    public $js = [
        'custom-pen.js',
        'custom-markdown.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}