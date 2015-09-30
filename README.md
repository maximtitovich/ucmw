# ucmw
Unified CodeIgniter Model Wrapper

AUTHOR: Maxim Titovich

2015
# Installation guide
Put Model_wrapper.php to models directory, put Model_generator.php to controllers directory, all within your CodeIgniter Project.

Setup basic stuff, database connection. (Now works only with mysql!)

Open http://yourprojectweb/index.php/model_generator or http://yourprojectweb/model_generator if you use .htaccess to get rid of index.php prefix

Models are generated based on your database structure, it includes foreign keys.

After model are generated, remove model_generator.php from controllers dir.

!Note, that you can regenerate models after changing database, but you should back up modified models, as they will be overwritten.

# How to use

## Basic functions

UCMW has this basic functions:

-countAll

`public function countAll($filter = null)`

Where `$filter` param is array of filters, like: `array('field_name', 'value')`. Value can also be an array of values, like: `array('field_name', array('value1', 'value2'))`.

You can pass a SQL string WHERE clause also, if 'field_name' will be not string, but integer, like so: `array(0 => 'id = 5')`.

`$filter` is used to generate WHERE clause in request.

Function returns integer value, containing quantity of items, found with passed `$filter`.

Example:

```
$this->load->model('item_model');
$this->item_model->countAll(array('name' => 'Maxim'));
```
-getAll

`public function getAll($filter = null, $order = null, $limit = null, $offset = null, $customFields = null)`

Where `$filter` param is array of filters, like: `array('field_name', 'value')`. Value can also be an array of values, like: `array('field_name', array('value1', 'value2'))`.

You can pass a SQL string WHERE clause also, if 'field_name' will be not string, but integer, like so: `array(0 => 'id = 5')`.

`$filter` is used to generate WHERE clause in request.

`$order` if array of order values. Usage: `array('field_name' => 'desc')` or `array('field_name' => 'asc')`

`$limit` is an integer value for limiting returning row quantity. Example: pass 100 to `$limit` parameter, to get 100 rows

`$offset` is an integer value for setting offset. Example, pass 100 to `$offset` parameter, to get rows starting from 101

`customFields` is an array of SQL strings, attached requests. Example: `array("(SELECT another_table.item_id, another_table.name WHERE table_name.id = another_table.item_id)", "(SELECT another_table.item_id, another_table.name WHERE table_name.id = another_table.item_id)")`

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

-getById

`public function getById($id = 0)`

`$id` is an integer parameter.

This function returns object with 'id' of `$id`

Example:
```
$this->load->model('item_model');
$this->item_model->getById(10);
```