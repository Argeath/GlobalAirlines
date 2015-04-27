<?php defined('SYSPATH') OR die('No direct access allowed.');

class Subtraction extends Operator {
 
    protected $precidence = 4;
 
    public function operate(Stack $stack) {
        $left = $stack->pop()->operate($stack);
        $right = $stack->pop()->operate($stack);
        return $right - $left;
    }
 
}