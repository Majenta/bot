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
		return true;
	}

	public function get($url, $canRedirect = true) {
		$this -> setOpts(array(CURLOPT_URL => $url));
		$response = curl_exec(self :: $connection);
		$response = new response($response);
	
		return $response;
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
		require_once('core/modules/simple_html_dom.php');
		$headerSize = $this -> getOpt(CURLINFO_HEADER_SIZE);
		$raw = trim(substr($response, 0, $headerSize));

		$head = array();
		$headers = explode("\n", $raw);
		foreach ($headers as $id => $header) {
			$header = explode(':', $header);
			if($id == 0)
			{
				$header[1] = $header[0];
				$header[0] = 'Status';
			}

			$head[$header[0]] = $header[1];
		}
		$this -> head = $head;
		$this -> head['raw'] = $raw;

		$this -> body = new simple_html_dom();
		$this -> body -> load(substr($response, $headerSize, strlen($response) - $headerSize));
	}
}