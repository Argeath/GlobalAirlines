<?php defined('SYSPATH') or die('No direct script access.');

class Number extends TerminalExpression {
 
    public function operate(Stack $stack) {
        return $this->value;
    }
 
}