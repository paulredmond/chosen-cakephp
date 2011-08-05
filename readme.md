# ChosenHelper for CakePHP

ChosenHelper is a class for integrating HarvestHQ [Chosen](https://github.com/harvesthq/chosen/) select boxes.

Check out HarvestHQ's [demo](http://harvesthq.github.com/chosen/) for documentation / and usage.

### Setup

From /plugins or /app/plugins run:

```
git clone git@github.com:paulredmond/chosen-cakephp.git chosen
cd chosen
git submodule init
git submodule update
```

In /app/AppController.php setup helper:

```php
public $helpers = array(
    'Chosen.Chosen',
);
```

Make sure that you are loading JQuery (1.4+) however you want:

```php
// One way in In default.ctp
echo $this->Html->script('jquery'); // sets src to /js/jquery.js
```

Chosen CSS/JS files are only loaded if the helper select method is called at least once.

```
echo $this->Chosen->select('Model.field');
```

_* Chosen assets are loaded through CakePHP request, I would recommend symlinking or copying Chosen CSS/JS/Img assets to webroot/chosen/chosen to match the paths set by the ChosenHelper._