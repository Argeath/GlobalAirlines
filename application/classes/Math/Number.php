<?php defined('SYSPATH') or die('No direct script access.');

class Math_Number extends Math_TerminalExpression {
 
    public function operate(Math_Stack $stack) {
        return $this->value;
    }
 
}