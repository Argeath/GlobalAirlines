<?php defined('SYSPATH') OR die('No direct access allowed.');

class Math_Division extends Math_Operator {
 
    protected $precidence = 5;
 
    public function operate(Math_Stack $stack) {
        $left = $stack->pop()->operate($stack);
        $right = $stack->pop()->operate($stack);
        return $right / $left;
    }
 
}