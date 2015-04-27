<?php defined('SYSPATH') or die('No direct script access.');

class Parenthesis extends TerminalExpression {
 
    protected $precidence = 7;
 
    public function operate(Stack $stack) {
    }
 
    public function getPrecidence() {
        return $this->precidence;
    }
 
    public function isNoOp() {
        return true;
    }
 
    public function isParenthesis() {
        return true;
    }
 
    public function isOpen() {
        return $this->value == '(';
    }
 
}