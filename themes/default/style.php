<?php
	date_default_timezone_set('Europe/Paris');
	/**
	 * Minify a CSS string
	 *
	 * @param string $str A CSS string.
	 * @Author Hack_Jack modified by FRD
	 */
	function minify_css($str) {
		$str = preg_replace('#@charset "UTF-8";#isU', '', $str);
		$str = str_replace(array("\r", "\n"), '', $str);
		$str = preg_replace('`([^*/])\/\*([^*]|[*](?!/)){5,}\*\/([^*/])`Us', '$1$3', $str);
		$str = preg_replace('`\s*({|}|,|:|;)\s*`', '$1', $str);
		$str = str_replace(';}', '}', $str);
		$str = preg_replace('`(?=|})[^{}]+{}`', '', $str);
		$str = preg_replace('`[\s]+`', ' ', $str);
		$str = preg_replace('#/\*(.*)\*/#isU', '', $str);
		return $str;
	}
	
	$duration = 3600 * 24 * 30;
	//list all files of current directory
	$dir = ".";
	$cache = "design_min.css";
	
	$all_files = scandir($dir);
	$css_files = array();
	foreach ($all_files as $file) {
		// only files with css extensions && not the minimify file
		$path_info = pathinfo($file);
		if ($path_info['extension'] == "css" && $file != $cache) {
			$css_files[] = $file;
		}
	}
	
	//if ther is a newer file in dir, or there is no $cache, re-process minimify
	$css_newer = false;
	if (file_exists($cache)) {
		foreach ($css_files as $file) {
			if (filemtime($cache) < filemtime($file)) {
				$css_newer = true;
				break;
			}
		}
	}
	if (!file_exists($cache) || $css_newer) {
		$contenu = '@charset "UTF-8";';
		foreach ($css_files as $file) {
			$contenu .= minify_css(file_get_contents($file));
		}
		file_put_contents($cache, $contenu);
	}
	
	
	
	$last_modified = filemtime($cache);
	foreach ($css_files as $file) {
		$last_modified = max($last_modified, filemtime($file));
	}
	
	header('Content-Type: text/css; charset=utf-8');
	header('Cache-Control: public, max-age=' . $duration);
	header('Last-Modified: ' . date('r', $last_modified));
	readfile($cache);
?>