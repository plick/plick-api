<?php
/**
 * Plick Public API Class
 * @author Owen Hardman <support@plick.com>
 * @copyright Plick Pty Ltd 2013
 * @version 1.2
 */
namespace Plick;
// {{{ Plick\PublicApi
final class PublicApi {
	// {{{ Configuration
	/**
	 * The API Endpoint
	 *
	 * @var string
	 * @access private
	 */
	private $endpoint = 'api.plickmail.com/';

	/**
	 * The API version
	 *
	 * @var string
	 * @access private
	 */
	private $version = '1.0';

	/**
	 * API access code | This is generated under Settings > Developers
	 * Both $$access_code and $access_pass regenerate simultaneously when
	 * requested
	 *
	 * @var string
	 * @access public
	 */
	public $access_code = '<YOUR_API_CODE>';

	/**
	 * API access password | This is generated under Settings > Developers
	 * Both $access_pass and $access_code regenerate simultaneously when
	 * requested
	 *
	 * @var string
	 * @access public
	 */
	public $access_pass = '<YOUR_API_PASSWORD>';

	/**
	 * How you would like data returned
	 * Your options are 'array','json','object'
	 *
	 * @var string
	 * @access public
	 */
	public $return = 'array';

	/**
	 * Regular expression used to type-check email addresses
	 * Note that changing this won't let you bypass a check by the Plick API Reciever
	 * @var string
	 * @access public
	 */
	public $email_regex = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/";

	/**
	 * Temporary access token | This is auto-populated
	 *
	 * @var string
	 */
	private $token = NULL;

	// }}}
	// {{{ Public Methods
	// {{{ -> info($type)

	/**
	 * Retrieve an information listing on the Plick account
	 *
	 * Example:
	 * <code>
	 * $plick = new Plick\PublicApi();
	 * try{
	 *   $info = $plick->info();
	 * } catch (Plick\PublicApiException $e){
	 *   // Problem exists in configuration;
	 * } catch (Exception $e) {
	 *   // Generic PHP problem
	 * }
	 * // Carry on
	 * </code>
	 *
	 * @access public
	 * @param $type string	Type of listing to be retrieved
	 *       	 			Options include 'full','short'
	 * @throws \Exception
	 * @throws Plick\PublicApiException
	 */
	public function info($type = 'full') { 
		if(!in_array($type,array('full','short'))){
			
			throw new PublicApiException('Your info type does not match \'full\', \'short\': '.$type);
			return false;
		}
		if ($this->token === NULL) {
			try {
				$this->connect ();
			} catch ( PublicApiException $e ) {
				throw $e;
				return FALSE;
			} catch ( \Exception $e ) {
				throw $e;
				return FALSE;
			}
		}

		$url = $this->buildBaseUrl ( 'info' ) . '?type=' . $type . '&hash=' . $this->token;

		try{
			$data = $this->callUrl($url);
		} catch ( PublicApiException $e ) {
			throw $e;
			return FALSE;
		} catch ( \Exception $e ) {
			throw $e;
			return FALSE;
		}
		try{
			return $this->handleOutput($data);
		} catch ( PublicApiException $e ) {
			throw $e;
			return FALSE;
		}
	}

	// }}}
	// {{{ -> database($dbid)

	/**
	 * Retrieve a listing on the Plick database $dbid
	 *
	 * Example:
	 * <code>
	 * $plick = new Plick\PublicApi();
	 * try{
	 *   $database = $plick->database('your-database-guid');
	 * } catch (Plick\PublicApiException $e){
	 *   // Problem exists in configuration;
	 * } catch (Exception $e) {
	 *   // Generic PHP problem
	 * }
	 * // Carry on
	 * </code>
	 *
	 * @access public
	 * @param $dbid string	Database ID
	 * @throws \Exception
	 * @throws Plick\PublicApiException
	 */
	public function database($dbid) {
		if(strlen($dbid) != 32){
			throw new PublicApiException('Your database ID fails typecheck: '.$dbid);
			return false;
		}
		if ($this->token === NULL) {
			try {
				$this->connect ();
			} catch ( PublicApiException $e ) {
				throw $e;
				return FALSE;
			} catch ( \Exception $e ) {
				throw $e;
				return FALSE;
			}
		}

		$url = $this->buildBaseUrl ( 'database' ) . '?database_id=' . $dbid . '&hash=' . $this->token;

		try{
			$data = $this->callUrl($url);
		} catch ( PublicApiException $e ) {
			throw $e;
			return FALSE;
		} catch ( \Exception $e ) {
			throw $e;
			return FALSE;
		}
		try{
			return $this->handleOutput($data);
		} catch ( PublicApiException $e ) {
			throw $e;
			return FALSE;
		}
	}

	// }}}
	// {{{ -> contact($dbid, $email)

	/**
	 * (Attempt to) fish out $email from Plick database $dbid
	 *
	 * Example:
	 * <code>
	 * $plick = new Plick\PublicApi();
	 * try{
	 *   $database = $plick->database('your-database-guid');
	 * } catch (Plick\PublicApiException $e){
	 *   // Problem exists in configuration;
	 * } catch (Exception $e) {
	 *   // Generic PHP problem
	 * }
	 * // Carry on
	 * </code>
	 *
	 * @access public
	 * @param $dbid string	Database ID
	 * @param $email string Email address
	 * @throws \Exception
	 * @throws Plick\PublicApiException
	 */
	public function contact($dbid, $email) {
		if(strlen($dbid) != 32){
			throw new PublicApiException('Your database ID fails typecheck: '.$dbid);
			return false;
		}
		if(strlen($email) < 1){
			throw new PublicApiException('Your email address fails typecheck: '.$email);
			return false;
		}
		if ($this->token === NULL) {
			try {
				$this->connect ();
			} catch ( PublicApiException $e ) {
				throw $e;
				return FALSE;
			} catch ( \Exception $e ) {
				throw $e;
				return FALSE;
			}
		}

		$url = $this->buildBaseUrl ( 'contact' ) . '?database_id=' . $dbid . '&email='.$email.'&hash=' . $this->token;

		try{
			$data = $this->callUrl($url);
		} catch ( PublicApiException $e ) {
			throw $e;
			return FALSE;
		} catch ( \Exception $e ) {
			throw $e;
			return FALSE;
		}
		try{
			return $this->handleOutput($data);
		} catch ( PublicApiException $e ) {
			throw $e;
			return FALSE;
		}
	}

	// }}}
	// }}}
	// {{{ Private/Protected methods
	// {{{ ->connect()

	/**
	 * Establishes an API session with the API server, and generates a token.
	 *
	 * @access protected
	 * @throws \Exception
	 * @throws Plick\PublicApiException
	 */
	protected function connect() {
		/**
		 * Build our URL first
		 *
		 * @var string
		 */
		$url = $this->buildBaseUrl ( 'login' ) . '?access_code=' . $this->access_code . '&access_password=' . $this->access_pass;

		/**
		 * Retrieve our URL payload
		 */
		try {
			$data = $this->callUrl ( $url );
		} catch ( PublicApiException $e ) {
			throw $e;
			return FALSE;
		} catch ( \Exception $e ) {
			throw $e;
			return FALSE;
		}

		/**
		 * OK, we're looking pretty good - let's parse our JSON.
		 */
		$json = json_decode ( $data );
		unset ( $data );
		if ($json->code != '200') {
			unset ( $curl_response );
			throw new PublicApiException ( $json->message, $json->code);
			return FALSE;
		}

		/**
		 * Finally - all OK.
		 * Let's store our hash.
		 */
		$this->token = $json->hash;
		unset ( $json );
		return TRUE;
	}

	// }}}
	// {{{ ->handleOutput

	/**
	 * Handles final conversion from a string to requested data type
	 * @param string $data JSON string
	 * @throws Plick\PublicApiException
	 * @return Of type $this->return
	 */
	private function handleOutput($data=''){
		if($this->return == 'array'){
			return json_decode ( $data , true );
		} elseif($this->return == 'object'){
			return json_decode ( $data );
		} elseif($this->return == 'json'){
			return $data;
		} else {
			throw new PublicApiException('Your return type does not match \'array\', \'object\' or \'json\': '.$this->return);
			return false;
		}
	}

	// }}}
	// {{{ ->callUrl($url)

	/**
	 * Calls a URL
	 * This thing is ultra safe with it's exceptions
	 *
	 * @access protected
	 * @param $url string The URL to call
	 * @throws \Exception
	 * @throws Plick\PublicApiException
	 */
	protected function callUrl($url) {
		/**
		 * Attempt to inialise cURL
		 * Setup to catch in case cURL isn't loaded
		 */
		try {
			$curl_session = curl_init ();
		} catch ( \Exception $phpException ) {
			unset ( $url, $curl_session );
			throw new \Exception ( 'Failed to initialise cURL: ' . $phpException->getMessage (), $phpException->getCode () );
			return FALSE;
		}

		/**
		 * Setup our cURL options
		 * Again catch in case of a crazy old PHP which doesn't support it
		 */
		try {
			curl_setopt ( $curl_session, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt ( $curl_session, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt ( $curl_session, CURLOPT_FORBID_REUSE, TRUE );
			curl_setopt ( $curl_session, CURLOPT_FRESH_CONNECT, TRUE );
			curl_setopt ( $curl_session, CURLOPT_HEADER, TRUE );
			curl_setopt ( $curl_session, CURLOPT_RETURNTRANSFER, TRUE );
			curl_setopt ( $curl_session, CURLOPT_URL, $url );
		} catch ( \Exception $phpException ) {
			unset ( $url, $curl_session );
			throw new \Exception ( 'Failed to set cURL options: ' . $phpException->getMessage (), $phpException->getCode () );
			return FALSE;
		}

		/**
		 * Process the cURL session
		 */
		try {
			$curl_response = curl_exec ( $curl_session );
		} catch ( \Exception $phpException ) {
			throw new \Exception ( 'Failed to execute cURL: ' . $phpException->getMessage (), $phpException->getCode () );
			return FALSE;
		}

		unset ( $url, $curl_session );

		/**
		 * First we check our HTTP response code
		 * If this fails, we throw a PublicApiException
		 */
		preg_match ( '/HTTP\/1\.1 (\d{3})/', $curl_response, $matches );
		$http_response = $matches [1];
		
		unset ( $matches );
		if ($http_response != "200") {
			throw new PublicApiException ( 'Plick API returned a HTTP status code other than 200: ' . $http_response );
			return FALSE;
		}
		unset ( $http_response );

		/**
		 * All checks out, return our JSON
		 */
		return substr ( $curl_response, strpos ( $curl_response, '{' ) );
	}

	// }}}
	// {{{ ->buildBaseUrl()

	/**
	 * Constructs the baseUrl
	 *
	 * @access protected
	 * @param $action string
	 *       	 the action to base
	 * @return string
	 */
	protected function buildBaseUrl($action = '') {
		return 'https://' . trim ( str_replace ( '//', '/', $this->endpoint . '/' . $this->version . '/' . $action ) );
	}

	// }}}
	// }}}

}

// }}}
// {{{ Plick\PublicApiException
class PublicApiException extends \Exception { }
// }}}
?>