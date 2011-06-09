<?php

include_once dirname(__FILE__)."/../../config.php";
include_once "recTest.php";
include_once "userCtrlTest.php";

//use global varible to pass the success/false flag to prevent $this and return

//Assertion
static $testResult = true;

function should_be_true($value){
  $testResult = ($value == true);
}

function should_be_false($value){
  $testResult = ($value == false);
}

function should_be_success($value){
  $testResult = ($value == true);
}

function should_be_equal($value,$value2){
  $testResult = ($value === $value2);
}

//base TestStory
//inherit TestStory to implement testCase
abstract class TestStory{

  public function runBefore(){
  
  }

  public function runAfter(){
  
  }


  //get Tests from class by get_class_methods and regex for method
  //start with feature,describe,scenario,test,it,integrate
  //TODO: seperate different test type
  public function getTests(){
    $methods = array();
    foreach(get_class_methods(get_class($this)) as $method){
      if ($this->isTest($method)){
        $methods[] = $method;
      }
    }
    return $methods;
  }

  protected function isTest($method){
    if (preg_match('/^(test|feature|describe|scenario|it|integrate)/',strtolower($method))){
      return true;
    }
  }

}

class Runner{

  public $success = 0;
  public $fail = 0;
  public $count = 0;

  public function run($testCase){

    foreach($testCase->getTests() as $test){
      $testCase->runBefore();
      //set test flag
      try{
        //execute test case
        $testCase->$test();
      }catch(Exception $exception){
        $testResult = false;
      }
      $this->count += 1;
      if($testResult == true){
        $this->success += 1;
        echo "T";
      }else{
        $this->fail += 1;
        echo "F(",get_class($testCase),"::$test)";
      }
      $testCase->runAfter();
      //reset global flag
      $testResult = true;
    }  
  }

  public function report(){
    echo "\nTest $this->count Case, $this->success successes , $this->fail fails\n";
  }
}

class TestSuite{

  public $testClass = array(); 

  public function loadTests($path){
    if ($handle = opendir($path)){
      while (false !== ($file = readdir($handle))){
        if($this->isDir($file)){
          $this->loadTests($path.DIRECTORY_SEPARATOR.$file);
        }else if(preg_match("/\w*Test\.php$/",$file)){
          $this->testClass[] = $this->testClassName($file); 
        }
      }
      closedir($handle);
    }

  }

  public function isDir($file){
    return is_dir($file) && $file !== "." && $file !== "..";
  }

  public function testClassName($file){
    return ucfirst(substr($file,0,-4));
  }

  public function runTests(){

    $runner = new Runner();

    $this->loadTests(dirname(__FILE__));

    foreach($this->testClass as $testClass){
      try{
        $runner->run(new $testClass());
      }catch (Exception $exception){
        echo "F(Fail to load class $testClass)\n";
      }
    }

    $runner->report();
  }
}

$test = new TestSuite();
$test->runTests();

?>
