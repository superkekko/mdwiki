<?php
class mdwiki extends controller {
	
	function home($f3){
		$parsedown = new Parsedown();
		
		$md_path="../data/md-wiki-docs";
		$md_homename = $f3->get('homefile');
		
		$f3->set('md_text',$parsedown->text(file_get_contents($md_path."/".$md_homename)));
		$f3->set('COOKIE.requestpage', $md_homename, 1296000);
	}
	
	function list($f3){
		$parsedown = new Parsedown();
		
		$md_path="../data/md-wiki-docs";
		
		$md_name = $f3->get('PARAMS.name');
		$f3->set('COOKIE.requestpage', $md_name, 1296000);
		$md_error = $f3->get('error');
		
		if(file_exists($md_path."/".$md_name)){
			$f3->set('md_text',$parsedown->text(file_get_contents($md_path."/".$md_name)));
		}else{
			$f3->reroute("/".$f3->get('error'));
		}
	}
	
	function edit($f3) {
		$md_name = $f3->get('PARAMS.name');
		if(!$this->checklogged($f3) || empty($md_name)){
			$f3->reroute("/login");
		}
		
		$md_path="../data/md-wiki-docs";
		$md_error = $f3->get('error');
		
		$form_pre='<form action="'.$f3->get('URI').'" method="post" enctype="application/x-www-form-urlencoded">
				<div class="row g-4 d-flex justify-content-end align-items-stretch">
					<div class="col-sm-12">
						<div class="form-group">
							<textarea class="form-control" name="text" rows="40">';
							
		$form_post='</textarea>
						</div>
					</div>
				</div>
				<div class="row py-3 d-flex justify-content-end">
					<div class="col-1 d-flex justify-content-end">
						<button class="btn btn-custom" style="margin-right: 0.5rem;" type="submit">'.$f3->get('send').'</button>
						<button class="btn btn-secondary" type="submit">'.$f3->get('cancel').'</button>
					</div>
				</div>
			</form>';
		
		if(file_exists($md_path."/".$md_name)){
			$data= file_get_contents($md_path."/".$md_name);
			$f3->set('md_text',$form_pre.$data.$form_post);
		}else{
			$f3->reroute("/".$f3->get('error'));
		}
	}
	
	function editSave($f3) {
		$md_name = $f3->get('PARAMS.name');
		if(!$this->checklogged($f3) || empty($md_name)){
			$f3->reroute("/".$f3->get('error'));
		}
		
		$md_path="../data/md-wiki-docs";
		$text = $f3->get('POST.text');
		
		if(file_exists($md_path."/".$md_name)){
			file_put_contents($md_path."/".$md_name, $text);
			$f3->reroute("/".$md_name);
		}else{
			$f3->reroute("/".$f3->get('error'));
		}
	}
	
	//HTTP route post-processor
	function afterroute($f3) {
		// Render HTML layout
		echo Template::instance()->render('layout.html');
	}
}