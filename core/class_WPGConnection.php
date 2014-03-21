<?php 

defined('ABSPATH') OR exit; //prevent from direct access

/**
 * Comunication with server. 
 * 
 */

class WPGConnection extends WPGuards{
	
	private $apiKey; 
	private $userName = 'deprecated'; 
	
	/**
	 * Constructor 
	 * 
	 * @param Object &WPGuards
	 * @return void
	 */
	public function __construct( $WPGuards ){
		//global $WPGuards; 
		
		$this->apiKey = $WPGuards->WPGAdmin->options['crm_apikey'];
		

		//print_r( $WPGuards );
	}
	/**
	 * Setting new api key
	 * 
	 * @param string 64 
	 * @return void
	 */
	public function setApiKey( $ApiKey ){
		$this->apiKey = $ApiKey;
	} 
	
	/**
	 * Method usign to remote send request
	 * 
	 * @param mixet 
	 * @param string set action of request
	 * @param string set method of request
	 */
	public function curlCall(  $dataToSend, $action='', $method='POST', $write = false){
		
		$dataToSend = json_encode( $dataToSend );
		
		$ch = curl_init();

		if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off')
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		
		//curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt($ch, CURLOPT_URL, WPGUARDS_API.$action);
		curl_setopt($ch, CURLOPT_USERPWD, "{$this->userName}:{$this->apiKey}");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
		switch($method) {
			case "POST":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $dataToSend);
				//if( $write )
				//	curl_setopt($ch, CURLOPT_WRITEFUNCTION, array($this, 'writeFunction'));
				break;
			case "GET":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
				break;
			case "PUT":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $dataToSend);
			default:
				break;
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
		if( $write )
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
		else curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
		$decoded = json_decode($output);
		// print_r($output);
		return  $output;
	}

	
	/**
	 * Add site to remote IWP
	 * @return array install response
	 */
	public function remoteInstall( ){
		global $blog_id; 
		$current_user = wp_get_current_user();
		$activationKey = get_option('iwp_client_activate_key');
		$dataToSend = array(
							'siteUrl' => admin_url(),
							'userLogin' =>  $current_user->user_login, 
							'acivateKey' => $activationKey,
							'name' => get_bloginfo('url'), 
							'isOpenSsl' => ( function_exists( 'openssl_verify' ) ) ? 1 : 0 ,
							'WPVersion' => get_bloginfo('version'),
							'iwpVersion' => '2.2.2', //Manual change when WPguards updated
							'ip' => $_SERVER['SERVER_ADDR'], 
							'network' => is_multisite(), 
							'mulitisiteID' => $blog_id, 
							
							);
		 
		$backups = WPGBackups::getBackups(); 
		if( !empty($backups ) ) $dataToSend['backupTask'] = $backups; 
		
		$response =  $this->curlCall( $dataToSend, 'install' );
		//print_r($response);
		return json_decode( $response );
	}
	
	/**
	 * Check API KEY 
	 * 
	 * @return bool
	 */
	public function checkApiKey(){
		$dataToSend = '';
		$response = $this->curlCall($dataToSend, 'keyCheck'); 
		
		$response = json_decode( $response );
		//print_r($response); 
		if(is_object($response)){
			if(isset($response->error)){
				WPGAdmin::setGlobalNotice(1000, 'error', $response->error->message);
				return false;
			}
			WPGAdmin::unsetGlobalNotice(1000);
			return true;
		}else{
			if($response == '1'){
				WPGAdmin::unsetGlobalNotice(1000);
				return true;
			}
			
		}
		
		return true; 
	}

	/**
	 * Check Connection
	 * 
	 * @return bool
	 */
	public function checkConnection(){
		return json_decode($this->curlCall('', 'checkConnection' ) ); 
	}
	
	/**
	 * Get ticket
	 * 
	 * @param null
	 * @return array of ticket
	 */
	 public function getTickets(){
	 	return json_decode($this->curlCall('', 'getTickets' ) ); 
	 }
	 
	 /**
	  * Get ticket Comments
	  * 
	  * @param int Ticket id 
	  * @return array of ticket
	  */
	  
	  public function getTicketComments( $ticketID ){
	  	return json_decode($this->curlCall( $ticketID, 'getTicketComments' ) ); 
	  }
	  
	  /**
	   * Add Ticket
	   * 
	   * @param array subject, comment, type
	   * @return object of addet ticket
	   */
	   
	   public function addTicket( $data ){
	   	return json_decode($this->curlCall( $data, 'addTicket' ) );
	   }

	   /**
	   * Get Ticket cost
	   * 
	   * @param array time, plan
	   * @return cost
	   */
	   public function getPrices(){
	   	return json_decode($this->curlCall( '', 'getPrices' ) );
	   }

	   /**
	   * Mark Ticket as paid
	   * 
	   * @param array id
	   * @return object of updated ticket
	   */
	   public function paidTicket( $data ){
	   	return json_decode($this->curlCall( $data, 'paidTicket' ) );
	   }

	   /**
	   * Mark Tickets as paid
	   * 
	   * @param array ids
	   * @return object of updated ticket
	   */
	   public function paidTickets( $data ){
	   	return json_decode($this->curlCall( $data, 'paidTickets' ) );
	   }
	   
	   /**
	    * Add ticket Comment
	    * 
	    * @param array ticketID, comment
	    * 
	    */
	    
	    public function addTicketComment( $data ){
	    	return json_decode($this->curlCall( $data, 'addTicketComment' ) );
	    }
		
		/**
		 * Getting user info 
		 * 
		 * @param int userID
		 * @return object of user
		 */
		
		public function getCommentUser( $userID ){
	    	return json_decode( $this->curlCall( $userID, 'getCommentUser' ) );
	    }

	    /**
		 * Getting user info 
		 * 
		 * @return object of user
		 */
		public function getZendeskUser(){
	    	return json_decode( $this->curlCall( '', 'getZendeskUser' ) );
	    }

	    /**
		 * Gets user currency 
		 * 
		 * @return string plan
		 */
		public function getUserCurrency(){
	    	return json_decode( $this->curlCall( '', 'getUserCurrency' ) );
	    }

	    /**
		 * Gets site plan 
		 * 
		 * @return string plan
		 */
		public function getSitePlan(){
	    	return json_decode( $this->curlCall( '', 'getSitePlan' ) );
	    }

	    /**
		 * Gets user tickets plan 
		 * 
		 * @return string plan
		 */
		public function getSiteTicketsPlan() {
	    	return json_decode( $this->curlCall( '', 'getSiteTicketsPlan' ) );
	    }

	    /**
		 * Getting features
		 * 
		 * @return array of features
		 */
		public function getFeatures(){
	    	return json_decode( $this->curlCall( '', 'getFeatures' ) );
	    }
		
		/**
		 * Add backup to dropbox
		 */
		 
		 public function sendBackup( $data )
		 {
		 	return json_decode( $this->curlCall( $data, 'addBackupToRemoteServer', 'POST', TRUE ) );
		 }
		
		/**
		 * Get dropbox Link
		 */
		 public function getBackupLink( $fileName ){
		 	
		 	return json_decode( $this->curlCall( $fileName , 'getBackupLink' ) );
		 	
		 }

		 /**
		 * Get next backup time
		 */
		 public function getNextBackupTime() {
		 	
		 	return json_decode( $this->curlCall( '', 'getNextBackupTime' ) );
		 	
		 }

		 /**
		 * Get uptime
		 */
		 public function getUptime() {
		 	
		 	return json_decode( $this->curlCall( '', 'getUptime' ) );
		 	
		 }

		 /**
		 * Get scans from queue
		 */
		 public function getScans() {
		 	
		 	return json_decode( $this->curlCall( '', 'getScans' ) );
		 	
		 }

		 /**
		 * Get expiration date
		 */
		 public function getExpirationDate() {
		 	
		 	return json_decode( $this->curlCall( '', 'getExpirationDate' ) );
		 	
		 }

		  /**
		 * Restore Backup
		 */
		 public function restoreBackup($data) { 
		 	
		 	return json_decode( $this->curlCall( $data, 'restoreBackup', 'POST' ) );
		 	
		 }
		 
		 /**
		  * Where Backup 
		  */
		 public function whereGoBackup( $data ) {
		 	return json_decode( self::curlCall( $data, 'whereGoBackup', 'POST' ) );
		 }
		 
		 public function whereIsBackup( $fileName ) {
		 	return json_decode( $this->curlCall( $fileName, 'whereIsBackup', 'POST' ) );
		 }

		public function get_next_scan_time() {
			return json_decode( $this->curlCall( '', 'getNextScanTime', 'POST' ) );
		}

		/**
		 * Set website notitication
		 */
		public function set_notification($name) {
			return json_decode( $this->curlCall( $name, 'setNotification', 'POST' ) );
		}

		/**
		 * Unset website notitication
		 */
		public function remove_notification($name) {
			return json_decode( $this->curlCall( $name, 'removeNotification', 'POST' ) );
		}
		 
}
