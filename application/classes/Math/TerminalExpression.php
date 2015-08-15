<?php defined('SYSPATH') or die('No direct script access.');

abstract class Math_TerminalExpression {
 
    protected $value = '';
 
    public function __construct($value) {
        $this->value = $value;
    }
 
    public static function factory($value) {
        if (is_object($value) && $value instanceof Math_TerminalExpression) {
            return $value;
        } elseif (is_numeric($value)) {
            return new Math_Number($value);
        } elseif ($value == '+') {
            return new Math_Addition($value);
        } elseif ($value == '-') {
            return new Math_Subtraction($value);
        } elseif ($value == '*') {
            return new Math_Multiplication($value);
        } elseif ($value == '/') {
            return new Math_Division($value);
        } elseif ($value == '^') {
            return new Math_Escalation($value);
        } elseif (in_array($value, array('(', ')'))) {
            return new Math_Parenthesis($value);
        }
        throw new Exception('Undefined Value ' . $value);
    }
 
    abstract public function operate(Math_Stack $stack);
 
    public function isOperator() {
        return false;
    }
 
    public function isParenthesis() {
        return false;
    }
 
    public function isNoOp() {
        return false;
    }
 
    public function render() {
        return $this->value;
    }
}