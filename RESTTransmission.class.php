<?php
	class RESTTransmission {
		protected $request;
		
		/**
		 * @param array $route
		 *   An array of two elements, with `$route[0]` containing the Request Method, and `$route[1]` containing the route with parameters as `:{name}` declarations.
		 * 
		 * @param array $params optional
		 *   An associative array matching parameter names to values for substitution in the URL.
		 *   Default: array()
		 * 
		 * @param array $values optional
		 *   An associative array of values `key => value` to be sent in the data body. This will be sent as a raw JSON for POST and PUT Requests.
		 *   Default: array()
		 * 
		 * @param array $authToken optional
		 *   The AuthToken to use. This will be sent via a `Token:` header unless its value is `NULL` or the global variable `$OptionStudIPRESTUseTokenAuth` is `false`. In that case, a BasicAuth `Auth:` header will be generated from the global variables `$OptionStudIPRESTBasicAuthUsername` and `$OptionStudIPRESTBasicAuthPassword`
		 *   Default: null
		 * 
		 */
		public function __construct($route, $params = array(), $values = array(), $authToken = null) {
			$this->request = curl_init();
			curl_setopt($this->request, CURLOPT_URL, $this->formatUrl($route[1], $params));
			curl_setopt($this->request, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
			$this->setupRequestMethod($route[0], $values);
			$this->setupHeaders($authToken);
			
			// test global scope, can be removed
			print_r($GLOBALS);
			print_r("\n");
			print_r("URL at construct of TranmissionObj: " . $this->formatUrl($route[1], $params) . "\n");
		}
		
		/**
		 *
		 */
		public function __destruct() {
			curl_close($this->request);
		}
		
		/**
		 * @retval stdClass if a JSON is returned, otherwise returns the HTTP Status Code as a string
		 */
		public function execute() {
			global $echoForDebug;
			curl_setopt($this->request, CURLOPT_VERBOSE, true);
			curl_setopt($this->request, CURLOPT_RETURNTRANSFER, true);

			$response = curl_exec($this->request);
			$decode = json_decode($response);
			
			// Debug Only: This block could be removed.
			if (!is_null($echoForDebug)) {
			//if (curl_getinfo($this->request, CURLINFO_HTTP_CODE) == "401") {
				echo "\n\nHttp-Code: ".curl_getinfo($this->request, CURLINFO_HTTP_CODE)."\n";
				echo "Request: ".curl_getinfo($this->request)."\n";
				var_dump(curl_getinfo($this->request));
				echo "Response: ".$response."\n\n";
			}
			
			return empty($decode) ? curl_getinfo($this->request, CURLINFO_HTTP_CODE)."" : $decode;
		}
		
		/**
		 * @param string $route
		 *   The route string to append to the global variable `$OptionStudIPRESTUrl` after substitution of all declared parameters.
		 * 
		 * @param array $params optional
		 *   An associative array of parameter names to values to be substituted into the URL.
		 *   Default: array()
		 * 
		 */
		protected function formatUrl($route, $params = array()) {
			global $OptionStudIPRESTUrl;
			
			$matches = array();
			$url = $route;
			if(preg_match_all("/[:]([a-zA-Z_]+)/", $route, $matches) && isset($matches[1])) {
				foreach($matches[1] as $match) {
					if(isset($params[$match])) $url = str_replace(":".$match, $params[$match], $url);
				}
			}
			
			return $OptionStudIPRESTUrl.$url;
		}
		
		/**
		 * @param string $method
		 *   The method string, f.e.: "GET"
		 * 
		 * @param array $values optional
		 *   An associative array of `key => value` that will be encoded as a JSON object and sent via raw Request Body for POST and PUT Requests.
		 *   Default: array()
		 * 
		 */
		protected function setupRequestMethod($method, $values = array()) {
			switch($method) {
				case "PUT":
					curl_setopt($this->request, CURLOPT_POSTFIELDS, json_encode($values));
					curl_setopt($this->request, CURLOPT_CUSTOMREQUEST, "PUT");
					break;
				case "POST":
					curl_setopt($this->request, CURLOPT_POSTFIELDS, json_encode($values));
					curl_setopt($this->request, CURLOPT_POST, 1);
					break;
				case "GET":
				default:
					curl_setopt($this->request, CURLOPT_HTTPGET, true);
			}
		}
		
		/**
		 * @param string $authToken optional
		 *   The AuthToken to use in a `Token:` Header field, unless its value is `NULL` or the global variable `$OptionStudIPRESTUseTokenAuth` is `false`.
		 *   Default: NULL
		 * 
		 */
		protected function setupHeaders($authToken = null) {
			global $OptionStudIPRESTUseTokenAuth, $OptionStudIPRESTBasicAuthUsername, $OptionStudIPRESTBasicAuthPassword, $echoForDebug;
			
			$header = array();
			$header[] = "Accept: application/json";
			$header[] = "Content-Type: application/json";
			if(!is_null($authToken) && $OptionStudIPRESTUseTokenAuth) {
				$header[] = "private-token: ".$authToken;
				
				// Debug Only: This block could be removed.
				if (!is_null($echoForDebug)) {
					echo "Using private-token.\n";
				}
				
			}
			else {
				curl_setopt($this->request, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($this->request, CURLOPT_USERPWD, $OptionStudIPRESTBasicAuthUsername.":".$OptionStudIPRESTBasicAuthPassword);
				
				// Debug Only: This block could be removed.
				if (!is_null($echoForDebug)) {
					echo "Use strange old method of identification.\n";
					echo "Use Token?: ".$OptionStudIPRESTUseTokenAuth."\n";
					echo "Token: ".$authToken."\n";
				}
			}
			curl_setopt($this->request, CURLOPT_HTTPHEADER, $header);
			curl_setopt($this->request, CURLINFO_HEADER_OUT, true);
		}
	}
