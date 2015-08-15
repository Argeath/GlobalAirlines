<?php defined('SYSPATH') OR die('No direct access allowed.');

class Math_Escalation extends Math_Operator {
 
    protected $precidence = 6;
 
    public function operate(Math_Stack $stack) {
        $left = $stack->pop()->operate($stack);
        $right = $stack->pop()->operate($stack);
        return pow($right, $left);
    }
 
}