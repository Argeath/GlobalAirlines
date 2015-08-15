<?php defined('SYSPATH') OR die('No direct access allowed.');

class Math_Addition extends Math_Operator {
 
    protected $precidence = 4;
 
    public function operate(Math_Stack $stack) {
        return $stack->pop()->operate($stack) + $stack->pop()->operate($stack);
    }
 
}