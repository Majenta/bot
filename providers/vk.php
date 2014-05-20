<?php
class vk extends bot {

	public $user = array();

	public function __construct() {

	}

	public function login($login, $pass) {
		$cookie = ROOT . $this -> cookie_dir . substr(md5($login), 0, 16);
		$this -> setCookieFile($cookie);
		$user['login'] = $login;

		$this -> getUserInfo();

		$data = array(
			'act' => 'login',
			'email' => $login,
			'pass' => $pass,
		);


		/*
		if(!file_exists($cookie)) // Куки ещё не существует
		{
			$this -> post('https://login.vk.com', $data);
		}
		else // Кука существует, но нужно сделать проверку на валидность
		{
			if($this -> get('http://vk.com/al_page.php', false) -> header['Status'] != 'HTTP/1.1 200 OK')
			{
				unlink($cookie);
				$this -> login($login, $pass);
			}

		}
		*/

		if(!file_exists($cookie)) // Куки ещё не существует
		{
			$this -> post('https://login.vk.com', $data);
		}

		if($this -> get('http://vk.com/al_page.php', false) -> header['Status'] != 'HTTP/1.1 200 OK')
		{
			unlink($cookie);
			$this -> login($login, $pass);
		}
	}

	public function setStatus($status)
	{
		$status = urlencode($status);

		$response = $this -> get('http://vk.com/al_page.php?act=current_info&al=1&hash='. $this -> user['info_has'] .'&info='. $status .'&oid='. $this -> user['id']) -> body;
		if($response != '15238<!><!>0<!>6556<!>0<!>')
		return json_encode(array('status' => 'error', 'msg' => 'Error. Can\'t change status'));

		return json_encode(array('status' => 'ok'));
	}

function getUserInfo() {	
	/*
	$page = json_decode($this -> get('http://vk.com/feed2.php') -> body);
	$this -> user['id'] = $page -> user -> id;
	$this -> user['hash'] = $page -> activity -> hash;
	// Available fields
	// sex,bdate,city,country,photo_50,photo_100,photo_200_orig,photo_200,photo_400_orig,photo_max,photo_max_orig,online,online_mobile,lists,domain,has_mobile,contacts,connections,site,education,universities,schools,can_post,can_see_all_posts,can_see_audio,can_write_private_message,status,last_seen,relation,relatives,counters,screen_name,timezone

	$page = json_decode($this -> get('https://api.vk.com/method/users.get?user_ids=' . $this -> user['id'] . '&fields=domain') -> body);
	$this -> user['alias'] = $page -> response[0] -> domain;
	*/
	$page = $this -> get('http://vk.com/al_profile.php') -> body;

	$pattern = '/"user_id":([0-9]*),"loc":(["a-z0-9_-]*),"back":"([\W ]*|[\w ]*)",.*,"post_hash":"([a-z0-9]*)",.*,"info_hash":"([a-z0-9]*)"/';
	preg_match($pattern, $page, $matches);
	array_shift($matches);
	
	$this -> user['id'] = $matches[0];
	$this -> user['alias'] = $matches[1];
	$this -> user['name'] = $matches[2];
	$this -> user['post_hash'] = $matches[3];
	$this -> user['info_hash'] = $matches[4];

	return $this -> user;
	}

}