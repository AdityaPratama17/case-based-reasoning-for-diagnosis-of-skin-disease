<?php 

class App{
	protected $controller='cbr';
	protected $method='index';
	protected $params=[];

	public function __construct(){
		$url=$this->parseURL();
		
		// controller
		if (isset($url[0])) {
			if (file_exists('../app/controllers/'.$url[0].'.php')) { #cek apakah ada controller pada folder controllers
				$this->controller=$url[0]; #ubah controller
				unset($url[0]); #keluarkan url ke-0 (home) dari url
			}
		}

		require_once '../app/controllers/'.$this->controller.'.php';
		$this->controller = new $this->controller;

		//method
		if (isset($url[1])) {
			if (method_exists($this->controller, $url[1])) { //cek apakah ada method (url[1]) pada class controller
				$this->method=$url[1];
				unset($url[1]);
			}
		}

		//params
		if (!empty($url)) {
			$this->params = array_values($url);
		}

		//jalankan controller dan method, serta kirimkan params jika ada
		call_user_func_array([$this->controller, $this->method], $this->params);

	}

	public function parseURL(){
		if (isset($_GET['url'])) {
			$url = rtrim($_GET['url'], '/'); #untuk menghapus slash pada akhir url
			$url = filter_var($url, FILTER_SANITIZE_URL); #untuk mengahapus karakter aneh
			$url = explode('/', $url); #pecah string url berdasarkan tanda slash
			return $url;
		}
	}
}