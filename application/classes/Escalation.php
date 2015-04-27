<?php defined('SYSPATH') OR die('No direct access allowed.');

class Escalation extends Operator {
 
    protected $precidence = 6;
 
    public function operate(Stack $stack) {
        $left = $stack->pop()->operate($stack);
        $right = $stack->pop()->operate($stack);
        return pow($right, $left);
    }
 
}