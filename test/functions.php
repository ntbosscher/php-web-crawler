<?php
/**
 * @Author: Nate Bosscher (c) 2015
 * @Date:   2016-03-22 17:58:34
 * @Last Modified by:   Nate Bosscher
 * @Last Modified time: 2016-03-22 18:18:50
 */

namespace Crawler\Test;

function pass(){
	echo "TEST PASSED\n";
}

function fail($msg = false){
	if(!$msg){
		echo "TEST FAILED\n";
	}else{
		echo "TEST FAILED '$msg'\n";
	}
	die();
}

$tests = array();
$fatalTests = array();

function addTest($name, $function){
	global $tests, $fatalTests;

	if(array_key_exists($name, $fatalTests)){
		die("Test '$name' already exists in 'fatalTests'\n");
	}
	if(array_key_exists($name, $tests)){
		die("Test '$name' already exists in 'tests'\n");
	}

	
	$tests[$name] = $function;
}

function addFatalTest($name, $function){
	global $fatalTests, $tests;

	if(array_key_exists($name, $tests)){
		die("Test '$name' already exists in 'tests'\n");
	}

	if(array_key_exists($name, $fatalTests)){
		die("Test '$name' already exists in 'fatalTests'\n");
	}

	$fatalTests[$name] = $function;
}

function run(){
	global $argv, $fatalTests, $tests;

	if(count($argv) != 2){
		echo "Usage: test.php <test-name>\n";
		echo "Available tests are:\n";
		echo "\t" . implode("\n\t", array_keys($tests));
		echo "\n";
		echo "\t" . implode("*\n\t", array_keys($fatalTests)) . "*";
		echo "\n* represents fatal tests (it should kill the program if successful)\n";
	}else{
		$funct = false;

		if(array_key_exists($argv[1], $tests)){
			$funct = $tests[$argv[1]];
		}

		if(array_key_exists($argv[1], $fatalTests)){
			$funct = $fatalTests[$argv[1]];
		}

		if($funct)
			call_user_func($funct);
		else
			echo "Test '$argv[1]' doesn't exist\n";
	}
}