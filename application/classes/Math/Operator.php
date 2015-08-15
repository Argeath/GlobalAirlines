<?php defined('SYSPATH') or die('No direct script access.');

abstract class Math_Operator extends Math_TerminalExpression {
 
    protected $precidence = 0;
    protected $leftAssoc = true;
 
    public function getPrecidence() {
        return $this->precidence;
    }
 
    public function isLeftAssoc() {
        return $this->leftAssoc;
    }
 
    public function isOperator() {
        return true;
    }
 
}