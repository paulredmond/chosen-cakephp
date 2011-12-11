# ChosenHelper for CakePHP 2.0x

ChosenHelper is a class for integrating HarvestHQ [Chosen](https://github.com/harvesthq/chosen/) select boxes in CakePHP 2.0x.

Check out HarvestHQ's [demo](http://harvesthq.github.com/chosen/) for documentation and usage.

### Installation

Chosen CakePHP 2 plugin supports [Composer](https://github.com/composer/composer) and [Packagist](http://packagist.org/). After you [download](http://packagist.org/) composer.phar and put it in your path:

```
cd path/to/app/Plugin or /plugins
git clone git@github.com:paulredmond/chosen-cakephp.git chosen
cd chosen
php composer.phar install
```


### Optional webroot symlink
```
cd /path/to/app/webroot
ln -s ../path/to/chosen/plugin/webroot underscored_plugin_name
```
*If you clone chosen-cakephp to app/Plugin/MyChosen, symlink would be called app/webroot/my_chosen*

### Setup

In /app/Config/bootstrap.php:

```php
<?php
//...

// "HarvestChosen" should match the folder where you cloned this plugin
// Bootstrap contains plugin configuration, such as the proper plugin webroot url.
CakePlugin::load('HarvestChosen', array('bootstrap' => true));
```

In /app/Controller/AppController.php:

```php
<?php

public $helpers = array(
    'HarvestChosen.Chosen', // app/Plugin/Chosen
);
```

### JQuery
Make sure that you are loading JQuery (1.4+) however you want:

```php
<?php

// One way in In default.ctp
echo $this->Html->script('jquery'); // sets src to /js/jquery.js
```

*Note: Chosen CSS/JS files are only loaded if the helper select method is called at least once.*

### Examples
Chosen inputs behave identically to the FormHelper::input() method.

Multi-select:

```php
<?php
echo $this->Chosen->select(
    'Article.category_id',
    array(1 => 'Category 1', 2 => 'Category 2'),
    array('data-placeholder' => 'Pick categories...', 'multiple' => true)
);
?>
```

Default selected:

```php
<?php
echo $this->Chosen->select(
    'Article.category_id',
    array(1 => 'Category 1', 2 => 'Category 2'),
    array(
        'data-placeholder' => 'Pick categories...',
        'default' => 1,
    ) 
);
?>
```

Grouped:

```php
<?php
echo $this->Chosen->select(
    'Profile.favorite_team',
    array(
        'NFC East' => array(
            'Dallas Cowboys',
            'New York Giants',
            'Philadelphia Eagles',
            'Washington Redskins'
        ),
        'NFC North' => array(
            'Chicago Bears',
            'Detroit Lions',
            'Greenbay Packers',
            'Minnesota Vikings'
        ),
        // ....
    ),
    array(
        'data-placeholder' => 'Pick your favorite NFL team...',
        'style' => 'width: 350px'
    )
);
?>
```