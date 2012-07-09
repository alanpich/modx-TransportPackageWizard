<?php 
define('DEFINE','DEFINE');
require_once dirname(__FILE__).'/TransportPackageWizard_Category.class.php';



class TransportPackageWizard {
	
private $startTime,$endTime,$buildTime;
public $categories = array();

public $pathShortcuts = array(
	'{base_path}' => '".MODX_BASE_PATH."',
	'{core_path}' => '".MODX_CORE_PATH."',
	'{assets_path}' => '".MODX_ASSETS_PATH."'
);


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
		
		// Create an initial category
		$this->addCategory(PKG_NAME);
	}//
	
	
	
	
	
// Run the builder
///------------------------------------------------------------------------------------------------
public function build() {
	
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
	
	

//-------------------------------------------------------------------------------------------------
//---  P R I V A T E   M E T H O D S  -------------------------------------------------------------
//-------------------------------------------------------------------------------------------------

	
// Build all categories into transport package
//-------------------------------------------------------------------------------------------------
private function _build_categories(){
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
		$this->log("<strong>Package $this->pkg_name Built in {$totalTime}</strong>\n",'DONE');
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
		$this->builder->registerNamespace($NAMESPACE,false,true,'{core_path}components/'.$NAMESPACE.'/');
		
		$this->pkg_name = PKG_NAME_LOWER.'-'.PKG_VERSION.'-'.PKG_RELEASE;
		$this->log('Initialising build ['.$this->pkg_name.']','INIT');
	}//
	
	

public function log($msg, $key='LOG',$color='#5F9EA0'){
		if(!empty($key)){ $key = "[$key]"; };
		$key = str_pad("$key",8);
		echo '<span style="font-weight:bold; color:'.$color.';">'.$key.'</span> '.$msg."\n";
	}//

	
	
};// end class TransportPackageWizard
















