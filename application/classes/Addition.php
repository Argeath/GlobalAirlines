<?php defined('SYSPATH') OR die('No direct access allowed.');

class Addition extends Operator {
 
    protected $precidence = 4;
 
    public function operate(Stack $stack) {
        return $stack->pop()->operate($stack) + $stack->pop()->operate($stack);
    }
 
}