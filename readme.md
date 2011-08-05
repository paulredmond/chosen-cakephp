# ChosenHelper for CakePHP

ChosenHelper is a class for integrating HarvestHQ Chosen select boxes.

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

Chosen JS files are only loaded if the helper select method is called at least once.

```
echo $this->Chosen->select('Model.field');
```
