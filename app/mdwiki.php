<?php
class mdwiki extends controller {

	function home($f3) {
		$parsedown = new Parsedown();

		$md_path = "../data/md-wiki-docs";
		$md_homename = $f3->get('homefile');

		$f3->set('md_text', $parsedown->text(file_get_contents($md_path."/".$md_homename)));
		$f3->set('COOKIE.requestpage', $md_homename, 1296000);
	}

	function page($f3) {
		$parsedown = new Parsedown();

		$md_path = "../data/md-wiki-docs";

		$md_name = $f3->get('PARAMS.name');
		$f3->set('COOKIE.requestpage', $md_name, 1296000);
		$md_error = $f3->get('error');

		if (file_exists($md_path."/".$md_name)) {
			$f3->set('md_text', $parsedown->text(file_get_contents($md_path."/".$md_name)));
		} else {
			$f3->reroute("/".$f3->get('error'));
		}
	}

	function edit($f3) {
		$md_name = $f3->get('PARAMS.name');
		if (!$this->checklogged($f3) || empty($md_name)) {
			$f3->reroute("/login");
		}

		$md_path = "../data/md-wiki-docs";
		$md_error = $f3->get('error');

		$form_pre = '<form id="text" action="'.$f3->get('URI').'" method="post" enctype="application/x-www-form-urlencoded">
				<div class="row g-4 d-flex justify-content-end align-items-stretch">
					<div class="col-sm-12">
						<div class="form-group">
							<textarea class="form-control" name="text" style="height: 60vh;">';

		$form_post = '</textarea>
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
			
			
		$image_form='<div class="row d-flex align-items-center">
						<div class="col-sm-2">
						<form id="uploadform" action="'.$f3->get('URI').'" method="post" enctype="multipart/form-data">
							<input type="file" name="upload[]" id="upload" multiple hidden><label class="btn btn-custom mb-1" for="upload">'.$f3->get('upload').'</label>
							</form>
						</div>
						<div class="col-sm-10">
						'.$f3->get('image_load_output').'
						</div>
						</div>';
			
		$image_files = array_values(array_diff(scandir('img/upload/'), array('..', '.', '.keep')));
		if(count($image_files)<=3){
			$image_in_groups = count($image_files);
		}else{
			$image_in_groups = round(count($image_files)/3,0,PHP_ROUND_HALF_UP);	
		}
		$start=0;
		
		$image_html='<div class="row">';
		for($i = 0; $i <= 4; $i++){
			$image_html.='<div class="col-sm-4 text-truncate">
							<ul class="list-group list-group-flush">';
			for($t = $start; $t < $image_in_groups; $t++){
				$image_html.='<li class="list-group-item d-flex align-items-center">
						  <span class="flex-grow-1 text-truncate"><i class="fa-regular fa-file-image"></i> '.$image_files[$t].'</span>
						  <form action="'.$f3->get('URI').'" method="post" enctype="application/x-www-form-urlencoded">
					      <input type="hidden" name="delete-file" value="'.$image_files[$t].'">
					      <button class="btn" type="submit"><i class="fa-solid fa-trash"></i></button>
					      </form>
					  </li>';
			}
			$image_html.='</li>
					</ul>
				</div>';
			$start=$start+$image_in_groups;
		}
		$image_html.='</div>';

		if (file_exists($md_path."/".$md_name)) {
			$data = file_get_contents($md_path."/".$md_name);
			$f3->set('md_text', $form_pre.$data.$form_post.$image_form.$image_html);
		} else {
			$f3->reroute("/".$f3->get('error'));
		}
	}

	function editSave($f3) {
		$md_name = $f3->get('PARAMS.name');
		if (!$this->checklogged($f3) || empty($md_name)) {
			$f3->reroute("/".$f3->get('error'));
		}
		
		$text = $f3->get('POST.text');
		if(!empty($text)){
			$md_path = "../data/md-wiki-docs";
			
			if (file_exists($md_path."/".$md_name)) {
				file_put_contents($md_path."/".$md_name, $text);
				$this->edit($f3);
			}
		}
		
		$delete_img = $f3->get('POST.delete-file');
		if(!empty($delete_img)){
			$target_dir = "img/upload/";
			unlink($target_dir.$delete_img);
			$this->edit($f3);
		}

		$image = $f3->get('FILES.upload');
		if(!empty($image)){
			$target_dir = "img/upload/";
			
			for ($i = 0; $i <= count($image['name']); $i++) {
			    $filename = $image['name'][$i];
				$size = $image['size'][$i];
				$type = $image['type'][$i];
				$path = pathinfo($file);
				$ext = $path['extension'];
				$temp_name = $image['tmp_name'][$i];
				$path_filename_ext = $target_dir.$filename;
				
				$folder_files = array_diff(scandir($target_dir), array('..', '.', '.keep'));

				if(in_array($filename, $folder_files)){
					$f3->set('image_load_output', $f3->get('file_present'));
				}elseif ($size > 10000000) {
					$f3->set('image_load_output', "too_big");
				}elseif (!in_array($type, ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml', 'image/tiff', 'image/webp'])) {
					$f3->set('image_load_output', $f3->get('type_mismatch'));
				}else{
					move_uploaded_file($temp_name, $path_filename_ext);
					$f3->set('image_load_output', $f3->get('upload_ok'));
				}
				
				$this->edit($f3);
			}
			
		}
	}

	//HTTP route post-processor
	function afterroute($f3) {
		// Render HTML layout
		echo Template::instance()->render('layout.html');
	}
}