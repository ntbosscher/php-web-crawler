<?php
/**
 * @Author: Nate Bosscher (c) 2015
 * @Date:   2016-03-22 16:21:30
 * @Last Modified by:   Nate Bosscher
 * @Last Modified time: 2016-03-22 18:38:22
 */

namespace Crawler;

class Crawler{

	private $_activeUrls = array();
	private $_downloadedUrls = array();

	private $_downloadImages = false;
	private $_removeHashTrail = true;

	/**
	 * @param $base_name the base url to crawl from (e.g. http://www.google.ca or http://www.google.ca/dogs)
	 */
	function __construct($base_name, $output_dir = false){
		$this->base = $base_name;

		$this->host = parse_url($this->base, PHP_URL_SCHEME) . "://" . parse_url($this->base, PHP_URL_HOST);
		$this->path = parse_url($this->base, PHP_URL_PATH);
		$this->path = realpath($this->path);
		
		if($this->host == NULL){
			echo "base_name '$base_name' is not valid. Couldn't parse host name\nExiting...\n";
			exit(-1);
		}

		if($this->path == NULL){
			echo "base_name '$base_name' is not valid. Couldn't parse path\nExiting...\n";
			exit(-1);
		}

		if(!$output_dir)
			$output_dir = __dir__ . "/output";

		$this->output = $output_dir;

		if(!is_dir($this->output)){
			if(!mkdir($this->output, 0777, true)){
				echo "Couldn't make directory '$this->output'\nExiting...\n";
				exit(-1);
			}
		}

		$this->addUrlIfValid($this->base);
	}

	function set_DownloadImages($tf = false){
		$this->_downloadImages = $tf;

		if($this->_downloadImages == true){
			if(!mkdir($this->output . "/images", 0777)){
				echo "Couldn't make directory '$this->output'\nExiting...";
				exit(-1);
			}
		}
	}

	function run(){
		while(count($this->_activeUrls) > 0){
			foreach($this->_activeUrls as $k => $v){
				echo "(downloading ".basename($k).")\n";

				$dp = new CrawlPage($k);
				$dp->download();

				$this->_downloadedUrls[$k] = 1;
				unset($this->_activeUrls[$k]);

				foreach($dp->fetchUrls() as $u)
					$this->addUrlIfValid($u);

				$filename = $this->fileNameFromUrl($k);
				$dirname = dirname($filename);

				if(!is_dir($dirname)){
					if(!mkdir($dirname, 0777, true)){
						echo "Couldn't create directory '" . dirname($filename) . "'\nExiting...\n";
						exit(-1);
					}
				}

				file_put_contents($filename, $dp->getContents());

				// update status
				echo "\033[A\033[2K"; // clear line
				echo "Downloaded " . count($this->_downloadedUrls) . " pages ";
			}
		}

		echo "\n";
		echo "Done!\n\n";
	}

	/**
	 * converts given url to a local filename
	 * @param  string $url
	 * @return string
	 */
	function fileNameFromUrl($url){

		// remove leading http
		$url = preg_replace("#http[s]{0,1}://#", "", $url);

		// if no slashes exist, add a trailing slash
		if(strpos($url, "/") === false){
			$url .= "/";
		}

		// remove domain name
		$upath = substr($url, strpos($url, "/"));

		if(substr($upath, -1) == "/")
			$upath = "/__root__.html";

		return $this->output . $upath;
	}

	/**
	 * resolves ../ and ./ and dir//subdir...
	 * @param  string $path
	 * @return string on success
	 * @return null on failure
	 */
	function resolvePath($path){

		// remove double fwd slash
		$path = str_replace("//", "/", $path);

		$list = explode("/", $path);
		for($i = 0; $i < count($list); $i++){
			if($list[$i] == "."){
				// remove this item
				array_splice($list, $i, 1);

				// update index (remember the for loop will increment before next iteration)
				$i--;
				if($i < -1) $i = 0;
			}else if($list[$i] == ".."){
				// remove last 2 items (the .. and the one before)
				array_splice($list, $i-1, 2);

				// update index (remember the for loop will increment before next iteration)
				$i-=2;
				if($i < -1){
					return null;
				}
			}
		}

		return implode("/", $list);
	}

	/**
	 * Adds the url to _activeUrls if it has the same host as $this->host
	 * and the path is below or equal to $this->path
	 * 
	 * @param [type] $url [description]
	 */
	private function addUrlIfValid($url){

		// remove trailing hash
		if($this->_removeHashTrail){
			$url = preg_replace("/#.*$/", "", $url);
		}

		// check that host is the same
		$host = parse_url($url, PHP_URL_SCHEME) . "://" . parse_url($url, PHP_URL_HOST);
		if($host != $this->host || $host == NULL){
			return;
		}

		// check that path is the same or below the path specified
		$path = parse_url($url, PHP_URL_PATH);
		// ensure that there's at least a slash
		if($path == "")
			$path = "/";

		$path = $this->resolvePath($path);

		if($path == NULL || strpos($this->path, $path) != 0){
			return;
		}

		// already downloaded
		if(array_key_exists($url, $this->_downloadedUrls)){
			return;
		}

		$this->_activeUrls[$url] = 1;
	}
}