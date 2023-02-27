<?php
class mdwiki extends controller {
	
	function home($f3){
		$parsedown = new Parsedown();
		
		$md_path="../data/md-wiki-docs";
		$md_homename = $f3->get('homefile');
		
		$f3->set('md_text',$parsedown->text(file_get_contents($md_path."/".$md_homename)));
	}
	
	function list($f3){
		$parsedown = new Parsedown();
		
		$md_path="../data/md-wiki-docs";
		
		$md_name = $f3->get('PARAMS.name');
		$md_error = $f3->get('error');
		
		if(file_exists($md_path."/".$md_name)){
			$f3->set('md_text',$parsedown->text(file_get_contents($md_path."/".$md_name)));
		}else{
			$f3->set('md_text',$parsedown->text(file_get_contents($md_path."/".$md_error)));	
		}
	}
}