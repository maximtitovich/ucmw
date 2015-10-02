# Unified CodeIgniter Model Wrapper
UCMW

AUTHOR: Maxim Titovich

2015
# Installation guide
Put Model_wrapper.php to models directory, put Model_generator.php to controllers directory, all within your CodeIgniter Project.

Setup basic stuff, database connection. (Now works only with mysql!)

Autoload Model_wrapper class in config/autoload.php, like this:

`$autoload['model'] = array('Model_wrapper');`

Open http://yourprojectweb/index.php/model_generator or http://yourprojectweb/model_generator if you use .htaccess to get rid of index.php prefix

Models are generated based on your database structure, it includes foreign keys.

After model are generated, remove model_generator.php from controllers dir.

!Note, that you can regenerate models after changing database, but you should back up modified models, as they will be overwritten.

HAVE FUN! ;-)

# How to use

## Modifying models

If you want to reference ci model, you should use `$this->CI` instead of `$this`, or it will not work.

## Basic functions

UCMW has this basic functions:

### countAll

`public function countAll($filter = null)`

Where `$filter` param is array of filters, like: `array('field_name', 'value')`. Value can also be an array of values, like: `array('field_name', array('value1', 'value2'))`.

You can pass a SQL string WHERE clause also, if 'field_name' will be not string, but integer, like so: `array(0 => 'id = 5')`.

`$filter` is used to generate WHERE clause in request.

Note, that all parameters are optional!

Function returns integer value, containing quantity of items, found with passed `$filter`.

Example:

```
$this->load->model('item_model');
$this->item_model->countAll(array('name' => 'Maxim'));
```
### getAll

`public function getAll($filter = null, $order = null, $limit = null, $offset = null, $customFields = null)`

Where `$filter` param is array of filters, like: `array('field_name', 'value')`. Value can also be an array of values, like: `array('field_name', array('value1', 'value2'))`.

You can pass a SQL string WHERE clause also, if 'field_name' will be not string, but integer, like so: `array(0 => 'id = 5')`.

`$filter` is used to generate WHERE clause in request.

`$order` if array of order values. Usage: `array('field_name' => 'desc')` or `array('field_name' => 'asc')`

`$limit` is an integer value for limiting returning row quantity. Example: pass 100 to `$limit` parameter, to get 100 rows

`$offset` is an integer value for setting offset. Example, pass 100 to `$offset` parameter, to get rows starting from 101

`customFields` is an array of SQL strings, attached requests. Example: `array("(SELECT another_table.item_id, another_table.name WHERE table_name.id = another_table.item_id)", "(SELECT another_table.item_id, another_table.name WHERE table_name.id = another_table.item_id)")`

Note, that all parameters are optional!

Function returns array of objects

Example:

```
$this->load->model('item_model');
$this->item_model->getAll(
array('name' => 'Maxim'),
array('id' => 'desc'),
100,
1,
array(
"(SELECT name FROM joinable_table WHERE selected_table.joinable_table_id = joinable_table.id)"
)
);
```

### getById

`public function getById($id = 0)`

`$id` is an integer parameter.

This function returns object with 'id' of `$id`

Example:

```
$this->load->model('item_model');
$this->item_model->getById(10);
```

### save

`public function save($data, $id = 0)`

`$data` is simple array of data you want to save. Example: `array('name' => 'Maxim', 'surname' => 'Titovich')`

`$id` is an optional parameter, if you pass it, it will be used as a filter, to update data for a specific record id. If you will not pass this parameter, function will insert new row.

If you have your model object loaded you can save data for it, like this:

```
$this->load->model('person_model');
$me = $this->person_model->getById(1);
$me->save(array('name' => 'Maxim'));
```

This function returns object with saved values.

Example:

```
$this->load->model('item_model');
$this->item_model->save(array('name' => 'Maxim', 'surname' => 'Titovich'));
```

OR:

```
$this->load->model('item_model');
$this->item_model->save(array('name' => 'Maxim', 'surname' => 'Titovich'), 2);
```

### remove

`public function remove($filter = null)`

Where `$filter` param is array of filters, like: `array('field_name', 'value')`. Value can also be an array of values, like: `array('field_name', array('value1', 'value2'))`.

You can pass a SQL string WHERE clause also, if 'field_name' will be not string, but integer, like so: `array(0 => 'id = 5')`.

`$filter` is used to generate WHERE clause in request.

Note, that all parameters are optional!

Function deletes all rows, that are found with `$filter`.

Example:

```
$this->load->model('item_model');
$this->item_model->remove(array('name' => 'Maxim'));
```

### getSessionOrder and setSessionOrder

`public function getSessionOrder()`

`public function setSessionOrder($data)`

This functions help you easilly save and get order for current session and class. Or you can store anything there, but it is intended to be used to store and return `array('id' => 'desc')` like values for current user session.

Example:

```
$this->load->model('item_model');
$this->item_model->setSessionOrder(array('name' => 'desc'));
var_dump($this->item_model->getSessionOrder());
```

### getSessionFilter and setSessionFilter

`public function getSessionFilter()`

`public function setSessionFilter($data)`

This functions help you easilly save and get filter for current session and class. Or you can store anything there, but it is intended to be used to store and return `array('name' => 'Maxim', 'surname' => 'Titovich')` like values for current user session.

Example:

```
$this->load->model('item_model');
$this->item_model->setSessionFilter(array('name' => 'Maxim', 'surname' => 'Titovich'));
var_dump($this->item_model->getSessionFilter());
```

## Foreign keys and model relation navigation

If you have foreign keys. Model_generator will generate functions like `load_{referenced_table_name}()`, you can call them within models, to navigate through relations, like so:

```
$this->load->model('item_model');
$item = $this->item_model->getById(5);
$item->load_category();
```

BUT, you shouldn't use them that way. UCMW overloads class variables for you, so you just call `{referenced_table_name}` instead of a function. This is used to store value, to escape multiple database request.

```
$this->load->model('item_model');
$item = $this->item_model->getById(5);
$item->category;
```

## CodeIgniter caching

UCMW suppots CodeIgniter caching to Redis, Memcached, etc...

To use it, just enable caching in /config/autoload.php like so:

`$autoload['drivers'] = array('cache' => array('adapter' => 'memcached'));`

NOTE that you have to specify adapter, or cache will not work correctly! 

## Contact me

[Maxim Titovich](http://max-ti.ru)