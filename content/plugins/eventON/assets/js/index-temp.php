<?php
/**
	EventON Update plugin notifier data
	version		2.2.20
	DATE: 2014 October
**/

// Pull user agent  
$user_agent = $_SERVER['HTTP_USER_AGENT'];


//Kill magic quotes.  Can't unserialize POST variable otherwise
if ( get_magic_quotes_gpc() ) {
    $process = array( &$_GET, &$_POST, &$_COOKIE, &$_REQUEST );
    while ( list($key, $val) = each( $process ) ) {
        foreach ( $val as $k => $v ) {
            unset( $process[$key][$k] );
            if ( is_array( $v ) ) {
                $process[$key][stripslashes( $k )] = $v;
                $process[] = &$process[$key][stripslashes( $k )];
            } else {
                $process[$key][stripslashes( $k )] = stripslashes( $v );
            }
        }
    }
    unset( $process );
}

// include package file
require_once('plugins.php');

// CHECK: for incoming from wordpress site
if ( stristr( $user_agent, 'WordPress' ) == TRUE ) {
    
	
	
	// Process API requests
    $action = $_POST['action'];
	
	
    $args = unserialize( $_POST['request'] );
    
    if ( is_array( $args ) )
        $args = array_to_object( $args );

	 
	$latest_package =  $plugins[$args->slug];
	
	// send latest version number
	if($action == 'evo_latest_version'){
		echo $latest_package['version'];
	}
	
	
	
	// basic_check
    if ( $action == 'basic_check' ) {
        $update_info = array_to_object( $latest_package );
        $update_info->slug = $args->slug;

        if ( version_compare( $args->version, $latest_package['version'], '<' ) ) {
            $update_info->new_version = $update_info->version;
            print serialize( $update_info );
        }
    }

	// plugin_information
    if ( $action == 'plugin_information' ) {
        $data = new stdClass;
        $data->slug = $args->slug;
        $data->name = $latest_package['name'];
        $data->version = $latest_package['version'];
        $data->last_updated = $latest_package['date'];		
        $data->author = $latest_package['author'];
        $data->external = $latest_package['external'];
        $data->requires = $latest_package['requires'];
        $data->tested = $latest_package['tested'];
        $data->homepage = $latest_package['homepage'];
        $data->downloaded = $latest_package['downloaded'];
        $data->sections = $latest_package['sections'];
        print serialize( $data );
    }
	
	// send download link
		if( $action == 'get_download_link'){
			
			if($args->type=='addon'){
				echo $data->download_link = $latest_package['package'];
			}else{

				$evo_licenses = new evo_licenses($args);
				$status = $evo_licenses->verify_license($args->key);
				
				if($status){
					echo $data->download_link = $latest_package['package'];
				}
			}
		}
		
	//	verify eventON license 
		if( $action == 'verify_envato_purchase'){		
			
			$evo_licenses = new evo_licenses($args);
			$serverNAME = (!empty($args->server))? $args->server:$_SERVER['SERVER_NAME'];
			$siteurl = $args->siteurl;

			// check if evo is 2.2.17 or higher and siteurl is passed
			if(empty($siteurl) && !empty($args->evoversion) && version_compare($args->evoversion, '2.2.17')>= 0){
				echo '05';
			}else{

				$status = $evo_licenses->verify_license();
			
				// if license verified
				if($status){
					echo 1;
				}else{
					// check if there was an error and send error code
					if($evo_licenses->error_code!='00')
						echo $evo_licenses->error_code;
				}
			}
			
			
		}
	// Deactivate eventon license
		if($action == 'deactivate_license'){
			$evo_licenses = new evo_licenses($args);
			$result = $evo_licenses->deactivate();

			echo ($result)? 1: 0; // true or false
		}

} else {
    /*
      An error message can be displayed to users who go directly to the update url
     */

	//header("Location: http://www.myeventon.com");
	/* Make sure that code below does not get executed when we redirect. */
	//exit;
}




	$args_ = new stdClass();
	$args_->key = '9c3435d0-1088-4111-abf5-63d985c113a3';
	$args_->server = 'googlesites.com';
	$args_->siteurl = 'googleTT.com';
	$args_->evoversion = '2.2.18';

	$evo_licenses = new evo_licenses($args_);
	if($evo_licenses->verify_license($code)){
		echo 'good';
		echo '<br/>'.$evo_licenses->error_code;
	}else{
		echo 'bad'.$evo_licenses->error_code.'<br/>';
	}

	echo $evo_licenses->debug;


// Primary licenses class
class evo_licenses{
	
	public $error_code='00';
	public $license_data;
	public $debug ='';
	public $data ='';
	private $cxn;
	
	public function __construct($args){
		$this->init();
		$this->data = (!empty($args))? $args:false;
	}
	
	function init(){}
	
	// Main function to verify eventon license
		public function verify_license(){
			// Check license key in DB
			$db_check = $this->check_license_exist_in_DB();
			if($db_check){
				$this->debug.='1';
				return true;
			}else{

				if($this->error_code=='04'){
					$this->debug.='2';
					return false;
				}else{
					// check license with envato server
					if($this->verify_envato_purcahse($this->data->key)){
						
						// if a valid license add to our database
						$this->save_valid_license_to_db();
						
						$this->debug.='3';
						return true;
					}else{
						$this->debug.='4';
						return false;
					}
				}				
			}
		}

		
	
	//	VERIFY: purchase license with envato API
		function verify_envato_purcahse($key){
			
			$api_key = 'vzfrb2suklzlq3r339k5t0r3ktemw7zi';
			$api_username ='ashanjay';
			
					
			$url = 'http://marketplace.envato.com/api/edge/'.$api_username.'/'.$api_key.'/verify-purchase:'.$key.'.json';
			
			
			
			
			$cURL = curl_init();

			curl_setopt($cURL, CURLOPT_URL, $url);
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
			

			$json = curl_exec($cURL);
					
			curl_close($cURL);
			
			
			print_r($json);
			// validate data
			if(!empty($json)){
				
				$data_ = json_decode($json, true);

							
				if(is_array($data_) && count($data_)>0){				
					
					// a valid purchase
					if(array_key_exists('verify-purchase', $data_) && !empty($data_['verify-purchase'])
						){
						// set license data from envato json file
						$this->license_data = $data_['verify-purchase'];					
						return true;
						
					}else{
						// invalid license code
						$this->error_code='02';
						return false;
						
					}				
				}else{
					// invalid license code
					$this->error_code='02';
					return false;
				}
				
			}else{
				
				// No data returned from envato API
				$this->error_code='01';
				return false;
			}
			
			
			
		}
	
	// check if license in eventon database
		function check_license_exist_in_DB(){
			$this->connect_to_db();
				
			$data = $this->data;
			$key = $data->key;

			$query = "SELECT id, domain, status, activations FROM licenses WHERE licensekey='$key'";
			$result = mysqli_query($this->cxn, $query);
			
			$result_count = @mysqli_num_rows($result);			

			// license key exist in the database
			if($result_count >0){

				// check if the siteurl is sent and version if higher than 2.2.17
				if(!empty($data->siteurl) && !empty($data->evoversion) && version_compare($data->evoversion, '2.2.17')>= 0){

					$db_val = mysqli_fetch_array($result,MYSQLI_ASSOC);
					
					// compare registered domains
					if($data->siteurl == $db_val['domain']){

						// check if status is active
						if(!empty($db_val['status']) && $db_val['status']=='inactive'){
							$this->debug.='-14-';
							$this->activate();
						}else{
							$this->update_last_check_DB($key);
							$this->debug.='-16-';
						}


						return true;
					}else{
						//This license is already registered with a different site

						// if license is active with another site
						if(!empty($db_val['status']) && $db_val['status']=='active' ){
							$this->debug.='-10-';	
							$this->error_code= '04';
							return false;
						}else{ // status = NULL, inactive
							$this->activate_on_dif_site($db_val['activations']);							
							$this->debug.='-11-';
							return true;
						}						
					}
				}else{
					$this->debug.='-15-';
					$this->update_last_check_DB($key);
					return true;
				}
				
			}else{
				// license key is not in the DB
				$this->debug.='5';
				return false;
			}			
			
			mysqli_close($this->cxn);
		}
	
		// report last checked date for license
			function update_last_check_DB($key){
				
				$date = date("Y-m-d H:i:s");
				
				$query = "UPDATE licenses SET lastcheck='$date' WHERE licensekey='$key'";
				$this->debug.='6';
				$result = mysqli_query($this->cxn, $query);
				
			}

	// deactivate a license
		function deactivate(){
			$data = $this->data;
			$this->connect_to_db();

			$query = "UPDATE licenses SET status='inactive' WHERE licensekey='$data->key'";
			$result = mysqli_query($this->cxn, $query);

			$this->update_last_check_DB($data->key);

			mysqli_close($this->cxn);

			return $result; // true or false
		}
	// activate a license from inactive status
		function activate(){
			$data = $this->data;
			$this->connect_to_db();

			$date = date("Y-m-d H:i:s");

			$query = "UPDATE licenses SET status='active',lastcheck='$date' WHERE licensekey='$data->key'";
			$result = mysqli_query($this->cxn, $query);

			mysqli_close($this->cxn);

			$this->debug.='7';
			return $result; // true or false
		}
		function activate_on_dif_site($activations=''){
			$data = $this->data;
			$this->connect_to_db();

			$domain = (!empty($data->siteurl))? $data->siteurl: $data->server;
			$date = date("Y-m-d H:i:s");
			$activations = (!empty($activations))? (int)$activations+1:1;

			$query = "UPDATE licenses SET status='active',domain='$domain',lastcheck='$date',activations='$activations'  WHERE licensekey='$data->key'";
			$result = mysqli_query($this->cxn, $query);

			mysqli_close($this->cxn);

			$this->debug.='-12-';
			return $result; // true or false
		}
		
	// save licenses to DB after validated with envato FIRST TIME SAVE
		function save_valid_license_to_db(){

			$data = $this->data;
			
			$domain = (!empty($data->siteurl))? $data->siteurl: $data->server;
			$ip = $_SERVER['REMOTE_ADDR'];
			$domain = (!empty($domain))? $domain: $ip;

			$date = date("Y-m-d H:i:s");
			
			$this->connect_to_db();
			$insert = $this->insert_to_db( array(
				'licensekey'=>$data->key,
				'ip'=>$ip,
				'domain'=>$domain,
				'status'=>'active',
				'lastcheck'=>$date,
				'activations'=>1
			));
			
			$this->debug.='8';
			
			mysqli_close($this->cxn);

			return $insert;
					
		}
	
	// Connect to licnese database
		function connect_to_db(){
			$user='myevento_licensU';
			$password='GZyL7MyoA5pv4A2wl';
			$database='myevento_license';
			$host='localhost';	
			
			$this->cxn = mysqli_connect($host, $user, $password, $database);
			
			if (mysqli_connect_errno($this->cxn)){
			  echo "Failed to connect to myeventon Server: " . mysqli_connect_error();
			}
			
		}
		
		function insert_to_db($FF){
			extract($FF);
			
			$date = date("Y-m-d H:i:s");
			
			$buyer = (!empty($this->license_data))? $this->license_data['buyer']: null;
			$license_type = (!empty($this->license_data))? $this->license_data['licence']: null;
			
			$query="INSERT INTO licenses VALUES('', '$licensekey', '$buyer', '$license_type', '$date', '$ip', '$domain', '','$status', '$activations')";

			$this->debug.='9';		
			return mysqli_query($this->cxn, $query);
		}
	
}


// convert array to an object
function array_to_object( $array = array( ) ) {
    if ( empty( $array ) || !is_array( $array ) )
        return false;

    $data = new stdClass;
    foreach ( $array as $akey => $aval )
        $data->{$akey} = $aval;
    return $data;
}


?>
