<?php defined('SYSPATH') or die('No direct script access.');

class Math_Stack {
 
    protected $data = array();
 
    public function push($element) {
        $this->data[] = $element;
    }
 
    public function poke() {
        return end($this->data);
    }
 
    public function pop() {
        return array_pop($this->data);
    }
 
}