<?php
class TransportPackageWizard_PostInstallHelper {
	
public $isInstall = false;
public $isUpdate = false;
	
function __construct($object,$namespace){
		global $modx, $options;
		$this->namespace = $namespace;
		if(!$modx){ 
			return;
		};
		
		$this->modx = &$modx;
		
		switch($options[xPDOTransport::PACKAGE_ACTION]){
			case xPDOTransport::ACTION_INSTALL :	$this->isInstall = true;
													$this->isUpdate = false;
													break;
			case xPDOTransport::ACTION_UPGRADE:		$this->isInstall = false;
													$this->isUpdate = true;		
		};
													
	}//
	
// Associate a TV with a Template
//------------------------------------------------------------------------------------------------------	
public function addTVtoTemplate($tvName,$templateName){
		$this->modx->log(modX::LOG_LEVEL_INFO,"Associating TV[$tvName] with Template[$templateName]");
	}//
	
// Set/Create a modx system setting
//------------------------------------------------------------------------------------------------------
public function setOption($key,$val, $xtype="textfield", $area="", $namespace="" ){
		// Check if option already exists
		$opt = $this->modx->getObject('modSystemSetting',$key);
		if( $opt instanceof modSystemSetting ){
			// Option already exists - update value
			$opt->set('value',$val);
			$opt->save();
			$this->modx->log(modX::LOG_LEVEL_INFO,"Modx Option [$key] updated");
		} else {
			// Option does not exist, create it
			$opt = $this->modx->newObject('modSystemSetting');
			$opt->set('key', $key);
			$opt->set('value', $val);
			$opt->set('xtype', $xtype);
			if(empty($namespace)){ $namespace=$this->namespace; };
			$opt->set('namespace', $namespace);
			if(!empty($area)){ $opt->set('area', $area); };
			$opt->save();
			$this->modx->log(modX::LOG_LEVEL_INFO,"Modx Option [$key] created");
		};
	}//
	
	
};// end class TransportPackageWizard_HelperClass