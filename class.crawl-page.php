<?php
/**
 * @Author: Nate Bosscher (c) 2015
 * @Date:   2016-03-22 16:38:33
 * @Last Modified by:   Nate Bosscher
 * @Last Modified time: 2016-03-22 18:26:26
 */

namespace Crawler;

class CrawlPage{

	function __construct($url){
		$this->url = $url;
		$this->c = new Curl($this->url);
	}

	function download(){
		$this->c->download();
		$status = $this->c->getHttpStatus();

		// ensure we get something in the success status range
		if($status >= 200 && $status < 300){
			return;
		}else{
			echo "\nError: got response '$status' for url '$this->url'\n";
		}
	}

	/**
	 * returns the contents of the url that was downloaded
	 * @return [type] [description]
	 */
	function getContents(){
		return $this->c->__tostring();
	}

	function fetchUrls(){
		$list = array();

		// search links
		$matches = array();
		preg_match_all("#<a .*?href=[\"']([^\"']*)[\"']#", $this->c->__tostring(), $matches);

		foreach($matches[1] as $v)
			$list[] = $v;

		// search images
		$matches = array();
		preg_match_all("#<img .*?src=[\"']([^\"']*)[\"']#", $this->c->__tostring(), $matches);
		
		foreach($matches[1] as $v)
			$list[] = $v;

		// remove garbage
		$filtered = array_filter($list, function($v){
			$u = strtolower($v);
			if(strpos($u, "mailto:") === 0){
				return false;
			}

			if(strpos($u, "#") === 0){
				return false;
			}

			return true;
		});

		// canonize relative links
		foreach($filtered as $k => $v){
			if(strpos($v, "http://") === 0 || strpos($v, "https://") === 0){
				continue;
			}

			// not a valid url
			if(parse_url($this->url, PHP_URL_HOST) == null)
				continue;

			// cannonize
			$filtered[$k] = parse_url($this->url, PHP_URL_SCHEME) . "://" . parse_url($this->url, PHP_URL_HOST) . $this->getDirFromUrl(parse_url($this->url, PHP_URL_PATH)) . "/" . $v;
		}

		return $filtered;
	}

	/**
	 * takes a urlPath and returns it's directory
	 */
	private function getDirFromUrl($urlPath){
		if($urlPath == "/" || $urlPath == "")
			return "/";

		return substr($urlPath, 0, strrpos($urlPath, "/"));
	}
}