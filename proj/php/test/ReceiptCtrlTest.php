<?php

require_once dirname(__FILE__).'/../../config.php';
  
class StackTest extends PHPUnit_Framework_TestCase{
  public function test_push_and_pop(){
    $stack = array();
    $this->assertEquals(0,count($stack));

    array_push($stack, 'foo');

    $this->assertEquals('foo', $stack[count($stack)-1]);
    $this->assertEquals(1, count($stack));

    $this->assertEquals('foo', array_pop($stack));
    $this->assertEquals(0, count($stack));

  }
}

?>
