# Validator

## About
Validator is a small php class.
The class validate if data inside array match the requirements.


Example:
```php
$data = [
    'email' =>'Alex@mail.com',
    'password' =>'q6we4r%ty',
    'url' =>'alex.com',
];

$obj = [
    'email' => 'email',
    'password' => 'min:7|required|symbols|nums:3',
    'url' => 'url',
];

$errors = new Validate($obj,$data).run();
print_r($errors); // Output: ['password' => [0: "The password has to contain at least 3 number characters"]]
```

## Validation rules
List of validation rules for run method
* required - checks if field is empty or null
* email - checks if field is an email
* lowers:times - checks if field has lowercase characters * times
* uppers:times - checks if field has uppercase characters * times
* nums:times - checks if field has numeric characters * times
* symbols:times - checks if field has special symbol characters * times
* url - checks if field is an url
* unique - checks if field has no repeated characters

Method which use the async method and model
* confirm:field2 - checks if field's value = field2's value
* exists:tableName - checks if field's value exist in given table in db
* notexists:tableName - checks if field's value not exist in given table in db
* password:tableName - checks if field's value matches value in given table in db
  * If req.data containes id, the match check will occure only at id
  * If req.data not containes id field, the match check will occure in all recordes in a table


## Basics
Validator is a class and it's constructor gets two parameters: obj and data. 
* obj - is an object with ruels for validation
* data - is an object with data for validation
* model - model is an als-model object

The syntax: 
```php
$errors = new Validator($obj,$data).run();
```
The run method, runs loop for checking each field in obj and validate fields from data.
If all fields pass validation, it returns empty array. Else, it returns array with errors. 

### data
Data has to be array with ``[field=>value,field=>value]`` format. Value has to be string. 

### obj
The object has to be with ``[field=>rules,field=>rules]`` format.
The rules are separetad by | and parameter separated with :. 

For example:
```php
$data = ['password'=>"aaa333\$F"];
$obj = ['password'=>"unique|num|uppers:2|symbols|required"];
$errors = new Validator($obj,$data).run();
print_r($errors);

// The output:
// [
//     password => [
//         0: "The aaa333$F has repeated characters"
//         1: "The password has to contain at least 2 uppercase characters"
//     ]
// ]

```
The example above checking password field ``"aaa333$F"`` for:
1. unique - repeated characters
2. num - at least one numeric character
3. apper:2 - at least two uppercase characters
4. symbols - at least one special symbol character
5. required - the field is not empty


## Model and async method

To use model, you need to install ``also\model`` and create new model object with sqlite or mysql data base. 

For example, you can do the folowing:

```console
composer require also/model
```

```php
$conData = __DIR__.'db/data.db';
$model = new Model($conData);
```

## Customizing arrors
```php
$validate = new Validate($obj,$data);
$validate->errors['min'] = 'The ~0~ has to be at least ~1~ characters long';
$errors = $validate->run();

// ~0~ - field name
//~1~ - parameter name

```
