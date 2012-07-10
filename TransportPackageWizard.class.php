<?php 
define('DEFINE','DEFINE');
require_once dirname(__FILE__).'/TransportPackageWizard_Category.class.php';
require_once dirname(__FILE__).'/TransportPackageWizard_PostInstall.class.php';

// Transport Package Wizard Class
//-------------------------------------------------------------------------------------------------
class TransportPackageWizard {
	
private $startTime,$endTime,$buildTime;
public $categories = array();
public $pathShortcuts = array(
		'{base_path}' => '".MODX_BASE_PATH."',
		'{core_path}' => '".MODX_CORE_PATH."',
		'{assets_path}' => '".MODX_ASSETS_PATH."'
	);
public $PostInstall;


function __construct( $config ){
		$this->config = $config;
		
		
		// Define all nescesary definables
		$this->_define_constants();
	
		// Start build timer
		$this->_start_timer();
		
		// Create ModX manager instance
		$this->_initialize_modx();
		
		// Create a namespace for this extra
		$this->_register_namespace(PKG_NAME_LOWER);
		
		// Set up initial requirements
		$this->requiredSetup();
	}//
	
	
	
	
	
// Run the builder
//-------------------------------------------------------------------------------------------------
public function build() {
	
		// ADD POST INSTALL RESOLVER
		$this->PostInstall->_build();
		
		// ADD CATEGORIES 
		$this->_build_categories();
		
		
		// BUILD & ZIP UP TRANSPORT PACKAGE 
		$this->log('Packing up transport package zip...');
		$this->builder->pack();
		
		// CLEAN UP & EXIT
		$this->_stop_timer();
	}//
	
	
	
	
// Create a category
///------------------------------------------------------------------------------------------------
public function addCategory( $name ){
		$this->categories[$name] = new TransportPackageWizard_Category($name, &$this);
		return $this->categories[$name];
	}//
	
	
// Add a directory for transport
//-------------------------------------------------------------------------------------------------
public function addDirectory($source,$target){
		$this->categories[PKG_NAME]->addDirectory($source,$target);
	}//
	
// Add one or many resources for transport
//-------------------------------------------------------------------------------------------------
public function addResources($IDs,$includeChildren=false){
		if(!is_array($IDs) && !is_integer($IDs)){
			$this->warn('Bad resource identifier, should be integer or array');
			return;
		};
		if(!is_array($IDs)){ $IDs = array($IDs); };
		
		$allIDs = $IDs;
		if($includeChildren){
			foreach($IDs as $ID){
				$allIDs = array_merge($allIDs,$this->getResourceTree($ID));
			};
		}
		
		$allIDs = array_unique($allIDs);

		foreach($allIDs as $resID){
			$this->categories[PKG_NAME]->addResource($resID);
		};
	}//
	


public function getPostInstallScript(){
		return $this->PostInstall->post_install_script;
	}//


//-------------------------------------------------------------------------------------------------
//---  P R I V A T E   M E T H O D S  -------------------------------------------------------------
//-------------------------------------------------------------------------------------------------


// Do all required setup shiznit (for PostInstallHelper etc)
//-------------------------------------------------------------------------------------------------
private function requiredSetup(){
		// Create a root category for this extra
		$this->addCategory(PKG_NAME);
		
		// Include tpw helper classes in transport
		$this->addDirectory(dirname(__FILE__).'/tpwhelper','{core_path}components/');
	}//

	
// Build all categories into transport package
//-------------------------------------------------------------------------------------------------
private function _build_categories(){
		// Move root category to end of stack to install it last
		$rootCat = array_shift($this->categories);
		array_push($this->categories,$rootCat);
		
		foreach($this->categories as $category){ 
			$category->build();	
		};
	}//
	
	
// Define package constants
//------------------------------------------------------------------------------------------------
private function _define_constants() {
		foreach($this->config['DEFINE'] as $key => $val){
			define($key,$val);
		};
	}//


// Start timer	
//------------------------------------------------------------------------------------------------	
private function _start_timer(){
		$tstart = explode(' ', microtime());
		$this->startTime = $tstart[1] + $tstart[0];
		set_time_limit(0);
		echo '<html><head><meta charset="utf8" /></head><body><pre>';
	}//
	
	
// Stop Timer when complete
//------------------------------------------------------------------------------------------------	
private function _stop_timer(){
		$tend = explode(" ", microtime());
		$tend = $tend[1] + $tend[0];
		$totalTime= sprintf("%2.4f s",($tend - $this->startTime));
		$this->log("<strong>Package $this->pkg_name Built in {$totalTime}</strong>",'DONE');
		$this->log("Download package ".$this->_getDownloadLink(),'LINK');
		
		// Delete transport resolver script file if exists
		if( file_exists('transport.install.script.php')){
			unlink('transport.install.script.php');
		};
		echo '</pre></body></html>';
	}//
	
	
// Initialize Modx manager class
//-------------------------------------------------------------------------------------------------
private function _initialize_modx(){
		require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
		$this->modx= new modX();
		$this->modx->initialize('mgr');
		$this->modx->setLogLevel(modX::LOG_LEVEL_ERROR);
		$this->modx->setLogTarget('ECHO');
		 
		$this->modx->loadClass('transport.modPackageBuilder','',false, true);
		$this->builder = new modPackageBuilder($this->modx);
		$this->builder->createPackage(PKG_NAME_LOWER,PKG_VERSION,PKG_RELEASE);
	}//

	
// Create a namespace for this extra
//-------------------------------------------------------------------------------------------------
private function _register_namespace($NAMESPACE){
		$this->namespace = $NAMESPACE;
		$this->builder->registerNamespace($NAMESPACE,false,true,'{core_path}components/'.$NAMESPACE.'/');
		
		$this->pkg_name = PKG_NAME_LOWER.'-'.PKG_VERSION.'-'.PKG_RELEASE;
		$this->log('Initialising build ['.$this->pkg_name.']','INIT');

		$this->PostInstall = new TransportPackageWizard_PostInstall(&$this);

	}//
	
	
// Get a download link for the created package
//-------------------------------------------------------------------------------------------------
private function _getDownloadLink(){
		$link = $this->modx->getOption('site_url')."core/packages/$this->pkg_name.transport.zip";
		return '<a href="'.$link.'">here</a>';
	}//
	
	
// Output to browser (todo - make able to run in shell/bash as CLI script)
//-------------------------------------------------------------------------------------------------
public function log($msg, $key='LOG',$color='#5F9EA0'){
		if(!empty($key)){ $key = "[$key]"; };
		$key = str_pad("$key",8);
		echo '<span style="font-weight:bold; color:'.$color.';">'.$key.'</span> '.$msg."\n";
	}//

// Shortcut to log($msg,'WARN','orange')
//-------------------------------------------------------------------------------------------------
public function warn($msg){
		$this->log($msg, $key='WARN',$color='orange');
	}//
	
	
// Shortcut to log($msg,'ERROR','#c00')
//-------------------------------------------------------------------------------------------------
public function error($msg){
		$this->log($msg, $key='ERROR',$color='#c00');
	}//
	
	
	
private function getResourceTree($ID){
		$id_array = array($ID);
		$kids = $this->modx->getObject('modResource',$ID)->getMany('Children');
		foreach($kids as $kid){
			$id = $kid->get('id');
			$kidKids = $this->getResourceTree($kid->get('id'));
			$id_array = array_merge($id_array,$kidKids);
		};
		return $id_array;
	}//
	
	
};// end class TransportPackageWizard
















