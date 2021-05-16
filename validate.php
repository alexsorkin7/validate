<?php
namespace Also;

class Validate {
    function __construct($obj,$data,$model = '') {
        $this->_errors = [];
        $this->errors['errors'] = 0;
        $this->obj = $obj;
        $this->data = $data;
        $this->model = $model;
    }

    public function run() {
        $obj = $this->obj;
        $data = $this->data;
        foreach ($obj as $field => $rules) {
            $value = $data[$field];
            $rulesArray = explode('|',$rules);
            foreach ($rulesArray as $rule) {
                $_rule = explode(':',$rule)[0];
                if(method_exists($this,$_rule)) {
                    $result = false;
                    if(strpos($rule,':') !== false) {
                        $_ruleValue = explode(':',$rule)[1];
                        $result = $this->{$_rule}(
                            $this->spaces($field),
                            $value,$_ruleValue
                        );
                    } else {
                        $result = $this->{$_rule}($this->spaces($field),$value);
                    }
                    if($result !== true) {
                        if(!isset($this->_errors[$field])) $this->_errors[$field] = [];
                        $this->_errors[$field][] = $result;
                    }
                }
            }
        }
        return $this->_errors;
    }

    private function spaces($field) {
        while(strpos($field,'_') !== false) {
            $field = str_replace('_',' ',$field);
        }
        return $field;
    }

    private function buildError($errorName,$params) {
        $template = $this->errors[$errorName];
        $i = 0;
        foreach($params as $key => $value) {
            $template = str_replace("~$i~",$value,$template);
            $i++;
        }
        return $template;
    }

    public $errors = [
        'email' => 'Please provide valid email',
        'required' => 'The ~0~ can\'t be empty',
        'min' => 'The ~0~ has to be at least ~1~ characters long',
        'max' => 'The ~0~ can\'t be bigger then ~1~ characters',
        'lowers' => 'The ~0~ has to contain at least ~1~ lowercase characters',
        'uppers' => 'The ~0~ has to contain at least ~1~ uppercase characters',
        'nums' => 'The ~0~ has to contain at least ~1~ number characters',
        'symbols' => 'The ~0~ has to contain at least ~1~ special symbol characters',
        'url' => 'The ~0~ is not valid url',
        'unique' => 'The ~0~ has repeated characters',
        'confirm' => 'The ~0~ not confirmed',
        'exists' => 'The ~0~ allready exists',
        'notExists' => 'The ~0~ not exists',
        'password' => 'The ~0~ is not match',
    ];

    private function confirm($field,$value,$field2) {
        if(isset($this->data[$field2])) {
            if($this->data[$field2] == $value) return true;
            else return $this->buildError('confirm',[$field2]);
        }
    }

    private function required($field,$value) {
        if($value !== null && strlen($value) == 0) return $this->buildError('required',[$field]);
        else return true;
    }

    private function email($field,$email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) return true;
        else return $this->buildError('email',compact('field'));
    }

    private function min($field,$value,$length=3) {
        if(!strlen($value) <= $length) return true;
        else return $this->buildError('min',compact('field','length'));
    }

    private function max($field,$value,$length=10) {
        if(!strlen($value) > $length) return true;
        else return $this->buildError('max',compact('field','length'));
    }

    private function lowers($field,$value,$times=1) {
        preg_match('(?=.*[a-z])',$value,$matches);
        if(count($matches) >= $times) return true;
        else return $this->buildError('lowers',compact('field','times'));
    }

    private function uppers($field,$value,$times=1) {
        preg_match('(?=.*[A-Z])',$value,$matches);
        if(count($matches) >= $times) return true;
        else return $this->buildError('uppers',compact('field','times'));
    }

    private function nums($field,$value,$times=1) {
        preg_match('(\d)',$value,$matches);
        if(count($matches) >= $times) return true;
        else return $this->buildError('nums',compact('field','times'));
    }

    private function symbols($field,$value,$times=1) {
        preg_match('[-+_!@#$%^&*.,?]',$value,$matches);
        if(count($matches) >= $times) return true;
        else return $this->buildError('symbols',compact('field','times'));
    }

    private function url($field,$value) {
        if (filter_var($value, FILTER_VALIDATE_URL)) return true;
        else return $this->buildError('url',compact('field','value'));

    }

    public function unique($field,$value) {
        $inArray = [];
        $array= str_split($value);
        foreach ($array as $key => $value) {
            if(in_array($value,$inArray)) {
                break;
                return $this->buildError('unique',compact('field','value'));  
            } else continue;
        }
        return true;
    }

    private function exists($field,$value,$tableName) {
        if($this->model !== '') {
            $this->model->
            $model($tableName)->
            where(["$field = $value"]);
            if(count($result['result']) > 0) {
                return $this->buildError('exists',compact('field','value'));
            } else return true;
        } else return null;
    }

    private function notExists($field,$value,$tableName) {
        if($this->model !== '') {
            $this->model->
            $model($tableName)->
            where(["$field = $value"]);
            if(count($result['result']) == 0) {
                return $this->buildError('notExists',compact('field','value'));
            } else return true;
        } else return null;
    }

    private function password($field,$value,$tableName) {
        if($this->model !== '') {
            $where = [];
            if(isset($this->data['id'])) {
                $where[] = "id=".$this->data['id'];
            }
            if(isset($this->data['email'])) {
                $where[] = "email=".$this->data['email'];
            }

            $this->model->
            $model($tableName)->
            where(["$field = $value"]);
            if(count($result['result']) == 0) {
                return $this->buildError('password',compact('field'));
            } else return true;

        } else return null;
     }

    // private function name() {
    //     $name = test_input($_POST["name"]);
    //     if (!preg_match("/^[a-zA-Z-' ]*$/",$name)) {
    //         $nameErr = "Only letters and white space allowed";
    //     }
    // }


}

