<?php
/***********************************************

  "CpanelDdns.php"

  Created by Michael Cheng on 04/16/2014 14:40
            http://michaelcheng.us/
            michael@michaelcheng.us
            --All Rights Reserved--

***********************************************/
/********************************************************************************/
require('lib/HttpRequest.php');

/**
 * Cpanel object. For now, the only public facing method is to update the DDNS
 */
class CpanelDdns
 {
	//~
	/**
	 * 
	 */
	private $_url;
	//~
	/**
	 * 
	 */
	private $_user;
	//~
	/**
	 * 
	 */
	private $_pass;
	//~
	/**
	 * 
	 */
	private $_token;
	//~
	//~
	/**
	 * Update DDNS on your domain. This method requires a login, therefore be
	 * sure to provide the URL, username, and password before calling
	 * 
	 * expected url {@see https://documentation.cpanel.net/display/DD/cPanel+API+2+Functions+-+ZoneEdit%3A%3Aedit_zone_record}
	 * 	https://hostname.example.com:2087/cpsess###########/json-api/cpanel?cpanel_jsonapi_user=user&cpanel_jsonapi_apiversion=2&cpanel_jsonapi_module=ZoneEdit&cpanel_jsonapi_func=edit_zone_record&Line=5&domain=example.com&name=sub&type=A&txtdata=v=blahblahblah&cname=example.com&address=10.10.10.10&ttl=14400&class=IN
	 * 
	 * @param  [String] $subdomain Your website's subdomain, used to connect to your VNC or whatever, e.g. "vnc"
	 * @param  [String] $domain    Your website, e.g. "example.com"
	 * @return                     Returns nothing
	 */
	public function updateDdns($subdomain, $domain)
	 {
		if (!$this->login())
		 {
			return false;
		 }

		$http = new HttpRequest($this->getUrl() . $this->getToken() . "/json-api/cpanel");
		
		$http
			->setHeaders("Authorization: Basic " . 
						 base64_encode($this->getUser() . ":" . $this->getPass()) . 
						 "\n\r")
			->get(
				array(
					"address" => $_SERVER['REMOTE_ADDR'],
					"class" => "IN",
					"cpanel_jsonapi_func" => "edit_zone_record",
					"cpanel_jsonapi_module" => "ZoneEdit",
					"cpanel_jsonapi_version" => 2,
					"domain" => $domain,
					"line" => 28,
					"name" => $subdomain . "." . $domain . ".",
					"ttl" => 1200,
					"type" => "A"
				)
			);
	}
	//~
	//~
	/**
	 * Login to your cpanel.
	 * @return [Boolean] True if the login succeeded
	 */
	private function login()
	 {
		$url = $this->getUrl();
		$user = $this->getUser();
		$pass = $this->getPass();


		$params = "user=" . $user . "&pass=" . $pass;

		$http = new HttpRequest($url . "/login");
		
		$result = $http->post(
			array(
				"user" => $user,
				"pass" => $pass
			)
		);
		
		$inf = $http->getCurlInfo();
		

		// Get the session
		if(strpos($inf['url'], "cpsess"))
		 {
			$pattern = "/.*?(\/cpsess.*?)\/.*?/is";
			$preg_res = preg_match($pattern, $inf['url'], $cpsess);

			$this->setToken($cpsess[1]);
			return true;
		 }
		else
		 {
			return false;
		 }
	 }
	//~
	//~
	/**
	 * 
	 */
	function getUrl()
	 {
		return $this->_url;
	 }
	//~
	//~
	/**
	 * 
	 */
	function setUrl($url)
	 {
		$this->_url = $url;
		return $this;
	 }
	//~
	//~
	/**
	 * 
	 */
	function getUser()
	 {
		return $this->_user;
	 }
	//~
	//~
	/**
	 * 
	 */
	function setUser($user)
	 {
		$this->_user = $user;
		return $this;
	 }
	//~
	//~
	/**
	 * 
	 */
	function getPass()
	 {
		return $this->_pass;
	 }
	//~
	//~
	/**
	 * 
	 */
	function setPass($pass)
	 {
		$this->_pass = $pass;
		return $this;
	 }
	//~
	//~
	/**
	 * 
	 */
	function getToken()
	 {
		return $this->_token;
	 }
	//~
	//~
	/**
	 * 
	 */
	function setToken($token)
	 {
		$this->_token = $token;
		return $this;
	 }
	//~
}

?>
