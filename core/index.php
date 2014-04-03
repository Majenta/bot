<?php
class bot {
	private $defaults = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_FOLLOWLOCATION => false,
		CURLOPT_HEADER => true,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.152 Safari/537.36',
	);

	static $connection;

	public function __construct() {
		self :: $connection = curl_init();
		curl_setopt_array(self :: $connection, $this -> defaults);
		$this -> connect();

	}

	private function connect() {

	}

	public function get($url, $canRedirect = true) {
		$this -> setOpts(array(CURLOPT_URL => $url));
		$response = curl_exec(self :: $connection);
		return new response($response);
	}

	public function post($url, $data = array()) {

	}

	public function setOpts($opts = array()) {
		curl_setopt_array(self :: $connection, $opts);
	}

	public function getOpt($opt) {
		return curl_getinfo(self :: $connection, $opt);
	}

	public function setHeaders($headers) {
		$this -> setOpts(array(CURLOPT_HTTPHEADER => $headers));
	}

	public function setCookieFile($filename) {
		$this -> setOpts(array(
			CURLOPT_COOKIEFILE => $filename,
			CURLOPT_COOKIEJAR => $filename,
		));
	}
}

class response extends bot {
	public $head;
	public $body;

	public function __construct($response) {
		$headerSize = $this -> getOpt(CURLINFO_HEADER_SIZE);
		$this -> head = substr($response, 0, $headerSize);
		$this -> body = substr($response, $headerSize, strlen($response) - $headerSize);
	}
}