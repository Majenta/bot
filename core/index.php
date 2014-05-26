<?php
set_exception_handler('error');

class bot {
	public $host;
	public $cookie_dir = '/cookies/';

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

	public function loadProvider($provider) {
		require_once('providers/' . $provider . '.php');
		return new $provider();
	}


	public function test($m) {
		static $d = 0;
		$d++;
		echo $d;

		if($d < 3)
		{
			return $this -> test($m);
		}
		$d = 0;
		return $m;
	}

	public function get($url, $followLocation = true) {
		$this -> setOpts(array(CURLOPT_URL => $url));
		$response = curl_exec(self :: $connection);
		$response = new response($response);

		if($followLocation && isset($response -> header['Location']))
		return $this -> redirect($response -> header['Location']);

		return $response;
	}

	private function redirect($redirectUrl) {
		static $depth = 0;

		if($depth < FOLLOW_LOCATION_DEPTH)
		{
			$depth ++;
			$host = parse_url($this -> getOpt(CURLINFO_EFFECTIVE_URL));
			$host['dir'] = dirname($host['path']);
			$host['script'] = basename($host['path']);

			$location = parse_url($redirectUrl);
			$location['dir'] = @dirname($location['path']);

			if(!isset($location['host']))
			{
				$redirect = $host['scheme'] . '://' . $host['host'];

				if(preg_match('/./', $location['dir']))
				$redirect .= $host['dir'] . '/' . $location['dir'];

				$redirect .= '/' . basename($location['path']);
			}
			else
			$redirect = $response -> header['Location'];

			return $this -> get($redirect);
		}
		else
			throw new Exception('Recursion depth limit ('. FOLLOW_LOCATION_DEPTH .') exceeded', 100);

		$depth = 0;
	}

	public function post($url, $data = array(), $followLocation = true) {
		$this -> setOpts(array(
			CURLOPT_URL => $url,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($data),
		));

		$response = curl_exec(self :: $connection);
		$this -> setOpts(array(CURLOPT_POST => false)); // POST mode disable
		
		$response = new response($response);

		if($followLocation && isset($response -> header['Location']))
		return $this -> redirect($response -> header['Location']);

		return $response;
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
	public $header;
	public $body;
	public $cookies;

	public function __construct($response) {
		require_once('core/modules/simple_html_dom.php');
		$headerSize = $this -> getOpt(CURLINFO_HEADER_SIZE);
		$raw = trim(substr($response, 0, $headerSize));

		$head = array();
		$headers = explode("\n", $raw);
		foreach ($headers as $id => $header) {
			$header = explode(': ', $header);
			if($id == 0)
			{
				$header[1] = $header[0];
				$header[0] = 'Status';
			}

			$head[$header[0]] = trim($header[1]);
		}
		$this -> header = $head;
		$this -> header['raw'] = $raw;
		if(isset($this -> header['Set-Cookie']))
		$this -> cookies = explode(';', $this -> header['Set-Cookie']);

		$this -> body = new simple_html_dom();
		$this -> body -> load(substr($response, $headerSize, strlen($response) - $headerSize));
	}
}

function error($exception) {
	$code = $exception -> getCode();
	$file = $exception -> getFile();
	$line = $exception -> getLine();
	$trace = $exception -> getTrace();
	$message = $exception -> getMessage();

	require_once(ROOT . '/tpl/error.php');
}