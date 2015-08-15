<?php defined('SYSPATH') OR die('No direct access allowed.');

class Math_Multiplication extends Math_Operator {
 
    protected $precidence = 5;
 
    public function operate(Math_Stack $stack) {
        return $stack->pop()->operate($stack) * $stack->pop()->operate($stack);
    }
 
}