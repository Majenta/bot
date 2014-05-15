<?php
class vk extends bot {
	public function __construct() {
		$this -> setCookieFile(__DIR__ . '/../cookies/' . time());
	}

	public function login($login, $pass) {
		$data = array(
			'act' => 'login',
			'email' => $login,
			'pass' => $pass,
		);

		//$redirect = $this -> post('https://login.vk.com', $data) -> header['Location'];
		//$this -> get($redirect) -> header['raw'];
		//echo $this -> get('http://vk.com/feed') -> body;
		//echo $this -> post('https://login.vk.com', $data) -> body;
		echo $this -> get('http://ya.ru') -> body;
	}
}