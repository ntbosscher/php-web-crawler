<?php
/**
 * @Author: Nate Bosscher (c) 2015
 * @Date:   2016-03-22 16:48:00
 * @Last Modified by:   Nate Bosscher
 * @Last Modified time: 2016-03-22 18:38:32
 */

namespace Crawler;

include_once "include.php";

echo "\nWeb Crawler 1.0 by Nate Bosscher\n";

if(count($argv) == 2){
	$c = new Crawler($argv[1]);
	$c->run();
}else if(count($argv) == 3){
	$c = new Crawler($argv[1], $argv[2]);
	$c->run();
}else{
	echo "Usage: php crawl.php <base-url> <optional:output-dir>\n\n";
}

