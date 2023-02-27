<?php
class controller {

	//instantiate class
	function __construct() {
		$f3 = Base::instance();

		//abilito il parser per Markdown
		include_once('../plugin/parsedown/Parsedown.php');
	}
	
	//HTTP route pre-processor
	function beforeroute($f3) {
		$f3->set('LOCALES','../dict/');
		$f3->set('FALLBACK','en');
		
		$md_path="../data/md-wiki-docs";
		$files = array_diff(scandir($md_path), array('..', '.', '.json'));
		
		$md_index = $f3->get('index');
		$index_json = file_get_contents($md_path."/".$md_index);
		$f3->set('index', json_decode($index_json, true));
		
		
	}
	
	//HTTP route post-processor
	function afterroute($f3) {
		// Render HTML layout
		echo Template::instance()->render('layout.html');
	}
}