
<?php 

	/*
	
		Push Sender Library
		
		@author  : eravse hello@logpusher.com / Merc - merhaba@mrmerc.net
		@package : Push Log Sender
		@version : 1.0.1
		@release : 26.11.2016
		
	*/
	
	date_default_timezone_set("Europe/Istanbul");
	
	class pushSender {
		
		/*
			@var $url
			@access private
			Request url adress
			İstek gönderim adresi
		*/
		private $url = "https://api.logpusher.com/api/agent/savelog";
		/*
			@var $email
			@access private
			API E-mail Adress
			APİ E-mail adresi
		*/
		private $email = "hello@profectsoft.com";
		/*
			@var $api_key
			@access private
			API Key
			APİ Anahtarı
		*/
		private $api_key = "";
		/*
			@var $auth_key
			@access private
			Auth Key
			Yetkilendirme Anahtarı
			Kontrol Panelden oluşturulur
		*/
		private $auth_key;
		/*
			@var $pwd
			@access private
			Api access password
			İstek gönderim adresi
		*/
		private $pwd = "";
		/*
			@var $date_time
			@access public
			Date time variable 
			Tarih - saat değişkeni (Dışarıdan değiştirilebilir)
		*/
		public $date_time;
		 
		
		/*
			@constant SEPERATOR
			@access public
			Seperator used for creating auth key
			Auth Key'de kullanılan ayraç
		*/
		
		const SEPERATOR = "|";
		
		/*
			@constant DATE_TIME_FORMAT
			@access public
			Date - time format in date function
			Fonksiyonda kullanılan tarih - saat formatı
			m: ay/month, d: gün/day, Y: 4 haneli yıl/ 4 digit year
			H: saat/hour, i: dakika/minute, s: saniye,second
		*/
		
		const DATE_TIME_FORMAT = "c"; //  
		
		/*
			@var $options
			@access private
			Options to send in request. Email, password, auth and api keys are default
			İstek yaparken gönderilecek opsiyonlar. E-posta, parola, auth ve api key default olarak gelir.
			Eğer make_request() fonksiyonuna yeni parametreler gelirse default olanlar değişir !
		*/
		
		private $options = array();
		
		/*
			@var $result
			@access public
			Context result from returned request
			İstekten dönen context sonuç değişkeni
		*/
		
		public $result;
		
		/*
			@function __construct()
			@access public
			Class Consturcturer with inital params. 
			Varsayılan değerlerle kurulan sınıf kurucusu
			Sınıf tanımlandığında parametleri kurmaya yarar
		*/
		
		public function __construct(){
			$this->init();
		}
		
		/*
			@function init()
			@access public
			Initialize default values and sets the options
			Varsayılan değerleri ayarlar ve gönderir
		*/
		
		/*
			@function pwd()
			@access public
			@return String
			Sets the password and returns 
			Tanımlanmış şifreyi ayarlar ve döndürür
		*/
		
		public function pwd() {
			return md5($this->pwd);
		}
		
		public function init(){
			$this->options["AuthKey"] = $this->_initAuthKey();
			$this->options["ApiKey"] = $this->api_key;
			$this->options["email"] = $this->email;
			$this->options["pwd"] = $this->pwd();
		}
		
		/*
			@function _initAuthKey()
			@access private
			@return $auth_key/String
			Sets Autkey and returns
			Authkey ayarlar
		*/
		
		private function _initAuthKey() {
			$_str = $this->email."|".$this->pwd()."|".$this->get_datetime();
			$this->auth_key = base64_encode($_str);
			return $this->auth_key;
		}
		
		/*
			@function make_request()
			@access public
			@param @params = array()
			@param @credientals = array()
			@return $result / Context
			Request function. It can make the request with default values 
			or free to change. If $params changed, authkey and api key will not add automaticly
			İstek yapan fonksiyon. Varsayılan parametrelerle istek yapabileceği gibi 
			parametreler değiştirilerekte yapılabilir. DİKKAT: parametreler değiştiği zaman
			authkey ve apikey otomatik olarak eklenmez ! Kullanıcı bunları manuel olarak girmesi gerekir.
		*/
		
		public function make_request($params = array(), $credientals = array()) {
			
			$dataToSend = array(); $HttpOptions = null;
			
			// Senaryo 1: Eğer $params ve $credientals alanı boşsa default değerleri gönder
			
			if(empty($params) && empty($credientals)) {
				foreach($this->options as $key=>$value) {
					$dataToSend[$key] = $value;
				}
			}
			
			// Senaryo 2: Eğer $params değişmiş ve credientals değişmemişse
			// yeni değerleri al ve credientals ile gönder
			
			if( !empty($params) && empty($credientals) ) {
				
				if(! array_key_exists('AuthKey', $params) && !array_key_exists('ApiKey', $params)) {
					$params['AuthKey'] = $this->__get("auth_key");
					$params['ApiKey'] = $this->__get("api_key");
				} 
				
				foreach($params as $new_keys => $new_values) {
					$dataToSend[$new_keys] = $new_values;
				}
				foreach($this->get_credientals() as $crediental_keys => $crediental_values) {
					$dataToSend[$crediental_keys] = $crediental_values;
				}
			}
			
			// Senaryo 3: Eğer $params değerleri aynı lakin $credientals değişmişse 
			// eski değerleri al ve yeni credientals bilgileri ile gönder
			
			if( empty($params) || is_null($params) && !empty($credientals) ) {
				$this->set_credientals($credientals);
				foreach($this->get_credientals() as $crediental_keys => $crediental_values) {
					$dataToSend[$crediental_keys] = $crediental_values;
				}
				$dataToSend['AuthKey'] = $this->__get("auth_key");
				$dataToSend['ApiKey'] = $this->__get("api_key");
			}
			
			// Senaryo 4: Eğer hem $params hemde $credientals değişmişse 
			// yeni değerleri al ve gönder
			
			if( !empty($params) && !empty($credientals) ) { 
				$this->set_credientals($credientals);
				if(! array_key_exists('AuthKey', $params) && !array_key_exists('ApiKey', $params)) {
					$params['AuthKey'] = $this->__get("auth_key");
					$params['ApiKey'] = $this->__get("api_key");
				} 
				foreach($params as $new_keys => $new_values) {
					$dataToSend[$new_keys] = $new_values;
				}
				foreach($this->get_credientals() as $crediental_keys => $crediental_values) {
					$dataToSend[$crediental_keys] = $crediental_values;
				}
			}
			
			$HttpOptions = array('http' =>
						array(
							'method'  => 'POST',
							'header'  => 'Content-type: application/x-www-form-urlencoded',
							'content' => http_build_query($dataToSend)
						)
					);

			$context = stream_context_create($HttpOptions);
			$this->result = file_get_contents($this->url, false, $context);
			return $this->result;
			
		}
		
		/*
			@function get_datetime()
			@access public
			@return $date_time/String
			Sets the datetime and returns it
			Tarih saati ayarlar ve döndürür
		*/
		
		public function get_datetime() {
			return date(self::DATE_TIME_FORMAT);
		}
		
		/*
			@function get_credientals()
			@access public
			@return Array
			Returns credientals info
			E-mail ve şifre bilgilerini döndürür.
		*/
		
		public function get_credientals(){
			return array(
				'email'=>$this->email,
				'pwd' =>$this->pwd()
			);
		}
		
		/*
			@function set_credientals()
			@access public
			@param  $credientals Array
			Sets the new credientals and override defaults 
			Yeni e-posta ve şifreyi ayarlar ve varsayılanların üstüne yazar
		*/
		
		public function set_credientals($vars = array()){
			if( array_key_exists("email", $vars) && array_key_exists("pwd", $vars)) {
				$this->email = $vars['email'];
				$this->pwd = md5($vars['pwd']);
			}
		}
		
		/*
			@function __get()
			@access public
			@param  $varname
			Generic Class getter function
			Genel sınıf değişkeni döndüren fonksiyon
		*/
		
		public function __get($varname) {
			return $this->$varname;
		}
		
		/*
			@function __set()
			@access public
			@param  $varname
			@param  $value
			Sets a new value for chosen variable
			Seçilen değişkenin değerini değiştirir
		*/
		
		public function __set($varname, $value) {
			$this->$varname = $value;
		}
		
	}
  
 ?php>
	
