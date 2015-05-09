Content editor like Medium.com for yii-2
========================================
Content editor like Medium.com for yii-2 based on https://github.com/sofish/pen

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist andrew72ru/yii2-pen "*"
```

or add

```
"andrew72ru/yii2-pen": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Use this in Active Form.

There is two cases of usage.

First, for multiple string text (like textarea).

This code add a editable `section` element to your form and make a `p` tag in there for each paragraph.

```php
    use andrew72ru\pen\Pen;
    
    echo $form->field($model, 'text')->widget(andrew72ru\pen\Pen::className());
```

Second add a inline-editing feature – for headers and other one-line texts.

This code make a `h1` tag with `page-geader` class an add `editablecontent` to there.

```php
    use andrew72ru\pen\Pen;
    
    echo $form->field($model, 'title')->widget(Pen::className(), [
        'clientOptions' => [
            'inline' => true,
            'tag' => 'h1',
            'class' => 'page-header'
        ]
    ]);
```

