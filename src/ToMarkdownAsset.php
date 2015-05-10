<?php
namespace andrew72ru\pen;

use yii\web\AssetBundle;

class ToMarkdownAsset extends AssetBundle
{
    public $sourcePath = '@bower/to-markdown';
    public $css = [];
    public $js = [
        'dist/to-markdown.js',
    ];
    public $depends = [
        'andrew72ru\pen\PenAsset',
    ];
}