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
		
		$url = explode("/", $f3->get('URI'));
		$url = array_filter($url);
		if (in_array("edit", $url)) {
			$f3->set('editpage',true);
		}else{
			$f3->set('editpage',false);
		}
		if (empty($url)) {
			$f3->set('homepage',true);
		}else{
			$f3->set('homepage',false);
		}
		
		$md_index = $f3->get('index');
		$index_json = file_get_contents($md_path."/".$md_index);
		$f3->set('index', json_decode($index_json, true));
	}
	
	function login($f3) {
		$f3->clear('SESSION');
		$requestpage = $f3->get('COOKIE.requestpage');
		$f3->clear('COOKIE');
		$f3->set('COOKIE.requestpage', $requestpage, 1296000);

		//create new session with 15gg live cookie, rootpath, server, secure, httponly and samesite strict
		session_set_cookie_params(1296000, '/', $_SERVER['HTTP_HOST'], false, true);
		ini_set('session.cookie_samesite', 'Strict');
		$token = bin2hex(random_bytes(32));
		$f3->set('SESSION.token', $token);
		$f3->set('token', $token);

		echo Template::instance()->render('login.html');
	}

	function auth($f3) {
		$username = filter_var($f3->get('POST.username'), FILTER_SANITIZE_STRING);
		$password = filter_var($f3->get('POST.password'), FILTER_SANITIZE_STRING);
		$page_token = filter_var($f3->get('POST.csrf_token'), FILTER_SANITIZE_STRING);

		$session_csrf = $f3->get('SESSION.token');
		
		$userok = false;
		
		$user_path="../data/users";
		$files = array_diff(scandir($user_path), array('..', '.'));
		$json_data = [];
		$file = "";
		foreach($files as $file){
			$json_data = file_get_contents('../data/users/'.$file);

			$json_data = (array) json_decode($json_data, false);
			
			if($json_data['username']===$username && $json_data['password']===$password){
				$userok = true;
				$json_data['token'] = $session_csrf;
				break;
			}
		}
	
		if (!empty($page_token) && !empty($session_csrf) && $userok && hash_equals($page_token, $session_csrf)) {
			$requestpage = $f3->get('COOKIE.requestpage');
			
			file_put_contents('../data/users/'.$file, json_encode($json_data));
			$f3->set('SESSION.username', $json_data['username']);
			$f3->set('SESSION.password', $json_data['password']);
			$f3->set('SESSION.filename', $file);
			
			if(empty($requestpage)){
				$f3->reroute('/'.$f3->get('homefile').'/edit');
			}else{
				$f3->reroute('/'.$requestpage.'/edit');
			}
		} elseif (!empty($user) && empty($userok) && hash_equals($page_token, $session_csrf)) {
			$f3->set('login_error', true);
			$this->login($f3);
		}
	}
	
	function checklogged($f3){
		$session_username = $f3->get('SESSION.username');
		$session_password = $f3->get('SESSION.password');
		$session_token = $f3->get('SESSION.token');
		$session_file = $f3->get('SESSION.filename');
		
		$json_data = file_get_contents('../data/users/'.$session_file);
		$json_data = (array) json_decode($json_data, false);
		
		if (!empty($session_file) && !empty($session_token) && !empty($session_password) && !empty($session_username) && $session_username===$json_data['username'] && $session_password===$json_data['password'] && hash_equals($session_token, $json_data['token'])) {
			return true;
		}else{
			return false;
		}
		
		
	}
}