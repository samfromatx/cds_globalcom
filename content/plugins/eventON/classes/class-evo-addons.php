<?php
/**
 * 
 * eventon addons class
 * This will be used to control everything about eventon addons
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Classes
 * @version     2.2.19
 */

if(!class_exists('evo_addon')){


class evo_addon{

	private $addon_data;
	private $urls;

	function __construct($arr){

		// assign initial values for instance of addon
		$this->addon_data = $arr;

		// save main plugin file urls to be used from options
		$init = get_option('eventon_addon_urls');
		if(empty($init)){
			$path = AJDE_EVCAL_PATH;
			$arr = array(
				'addons'=>$path .'/classes/class-evo-addons.php',
				'date'=> time()
			);
			update_option('eventon_addon_urls',$arr);
			$init = $arr;
		}

		$this->urls = $init;
	}

		public function get_eventon_version(){
			global $eventon;
			return $eventon->version;
		}

	// REQUIREMENT check
		// not using this since 2.2.19 addons will have its own version of this
		public function requirment_check(){
			
			// eventon exist if addon connect to this function
			// check if eventon version is compatible and return true of false
			global $eventon;

			$eventON_version = $eventon->version;

			// if eventON version is lower than what we need
			if(!empty($eventON_version) && version_compare($this->addon_data['evo_version'], $eventON_version)>0){				
				add_action('admin_notices', array($this, '_old_eventon_warning'));
			}
			return true;
		}

		// display warning if EventON version is old
			function _no_eventon_warning(){
		        ?>
		        <div class="message error"><p><?php printf(__('Well... looks like you dont have eventON main plugin installed... %s needs <a href="%s">EventON</a> to work properly, my friend!', 'eventon'),
		        	$this->addon_data['name'], 'http://www.myeventon.com/'); ?></p></div>
		        <?php
		    }
		    function _old_eventon_warning(){
		        ?>
		        <div class="message error"><p><?php printf(__('oh no.. your eventON version is old...  <b>%s</b> need eventON version %s or higher to work correctly! ', 'eventon'),  $this->addon_data['name'], $this->addon_data['evo_version']); ?></p></div>
		        <?php
		    }



	// Activate addon
		public function activate(){
			global $pagenow;

			// activate only in these pages
			$__needed_pages = array('plugins.php', 'admin.php');
			if(!empty($this->urls)  && !empty($pagenow) && is_admin() && in_array($pagenow, $__needed_pages) ){
				$this->add_addon();
			}
		}



	// return the current page names that should be used to check updates
		function get_check_pages(){

			$opt = get_option('evcal_options_evcal_1');

			// limit remote check pages
			if(!empty($opt['evcal_lmtcheks']) && $opt['evcal_lmtcheks']=='yes'){
				return array('update-core.php',
				'admin-ajax.php', 'plugin-install.php');
			}else{			
				return array('update-core.php',
				'plugins.php', 'admin.php',
				'admin-ajax.php', 'plugin-install.php');
			}
		}


	/// the MAIN updater function
		public function updater(){
			global $pagenow, $eventon;
			
			$__needed_pages = $this->get_check_pages();

			// only for admin
			if(is_admin() && !empty($pagenow) && in_array($pagenow, $__needed_pages) ){
				//$screen = get_current_screen();

				//echo $pagenow;
				
				if($pagenow == 'admin.php' && isset($_GET['tab']) && $_GET['tab']=='evcal_4' 
					|| $pagenow!='admin.php'){
					
					//echo 'tt';
					// AUTO UPDATE notifier -- using main eventon updater class
					$path = AJDE_EVCAL_PATH;
					require_once( $path .'/classes/class-evo-updater.php' );		
					$api_url = 'http://update.myeventon.com/';
					$this->evo_updater = new evo_updater( 
						$this->addon_data['version'], $api_url, 
						$this->addon_data['plugin_slug']
					);
					
					// new notification system for updates
					$server_version = $this->evo_updater->remote_version;

					// check if there is a new version compared to server
					if( version_compare($this->addon_data['version'], $server_version, '<')){
						
						//$this->have_new_version();
						$this->update_addon($this->addon_data['slug'], 'remote_version',$server_version);
					}
				}
			}
		}






	// Add Addon to the list
		public function add_addon(){	
			
			$eventon_addons_opt = get_option('eventon_addons');
			
			// the array of data for the new addon that will be added to list
			$eventon_addons_ar[$this->addon_data['slug']]=array(
				'name'=>$this->addon_data['name'],
				'version'=>$this->addon_data['version'],
				'slug'=>$this->addon_data['slug'],
				'guide_file'=>( file_exists($this->addon_data['plugin_path'].'/guide.php') )? 
					$this->addon_data['plugin_url'].'/guide.php':null,
				'last_remote_check'=>'',
				'remote_version'=>'',
			);

			if(is_array($eventon_addons_opt)){
				$eventon_addons_new_ar = array_merge($eventon_addons_opt, $eventon_addons_ar );
			}else{
				$eventon_addons_new_ar = $eventon_addons_ar;
			}
			
			update_option('eventon_addons',$eventon_addons_new_ar);
			
			
		}

		// remove adodn from eventon addons array
		public function remove_addon(){
			$evo_addons = get_option('eventon_addons');
				
			if(is_array($evo_addons) && array_key_exists($this->addon_data['slug'], $evo_addons)){
				
				$_new_addons = $evo_addons;
				unset($_new_addons[$this->addon_data['slug']]);

				update_option('eventon_addons',$_new_addons);

				return $_new_addons;
			}else{
				return false;
			}
			
		}

	/*** update a field for addon */
		public function update_addon($slug, $field_name, $new_value){
			$eventon_addons_opt = get_option('eventon_addons');
			
			$newarray = array();
			
			// the array that contain addon details in array
			$addon_array = $eventon_addons_opt[$slug];
			
			if(is_array($addon_array)){
				
				$__new_addon = $eventon_addons_opt;
				$__new_addon[$slug][$field_name] = $new_value;
				
				update_option('eventon_addons',$__new_addon);
			}
		}
	

	// return a field value for a given addon slug
		public function get_value($slug, $field){
			$eventon_addons_opt = get_option('eventon_addons');
			$output = false;
			if(!empty($eventon_addons_opt) && is_array($eventon_addons_opt)){
				$output = $eventon_addons_opt[$slug][$field];
			}
			return $output;
		}
}

}

?>