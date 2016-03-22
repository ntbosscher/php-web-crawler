<?php
/**
 * @Author: Nate Bosscher (c) 2015
 * @Date:   2016-03-22 17:47:36
 * @Last Modified by:   Nate Bosscher
 * @Last Modified time: 2016-03-22 18:24:01
 */

namespace Crawler\Test;

require_once __DIR__ . "/functions.php";

require_once __DIR__ . "/../include.php";

addTest("constructor-defaults", function(){
	$c = new \Crawler\Crawler("http://google.ca");
	if(is_dir("./output")){
		pass();
	}else{
		fail("should have created ./output");
	}
});

addFatalTest("invalid-dir", function(){
	$c = new \Crawler\Crawler("http://google.ca", "/somedir");
});

addTest("valid-dir", function(){
	$c = new \Crawler\Crawler("http://google.ca", "./somedir");
	if(is_dir("./somedir")){
		pass();
	}else{
		fail("should have created ./somedir");
	}
});

addTest("resolve-path", function(){
	$c = new \Crawler\Crawler("http://google.ca");

	$paths = array(
		"/image/favicon.png" => "/image/favicon.png",
		"/image//favicon.png" => "/image/favicon.png",
		"//image/favicon.png" => "/image/favicon.png",
		"../image/favicon.png" => NULL,
		"/image/../favicon.png" => "/favicon.png",
		"/" => "/"
	);

	foreach($paths as $k => $v){
		echo "Testing path '$k'\n";

		if($c->resolvePath($k) != $v){
			fail("Dirs don't match '$k' and '$v'");
		}
	}

	pass();
});

run();





