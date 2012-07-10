<?php
class TransportPackageWizard_PostInstallHelper {
	
public $isInstall = false;
public $isUpdate = false;
public $resTplMap = array();
public $resAliasMap = array();
public $resParentMap = array();
public $resAliasKEY = '';
	
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



// Restore transported resources to their templates
//------------------------------------------------------------------------------------------------------
public function restoreResourceTemplates(){
		$this->modx->log(modX::LOG_LEVEL_INFO,"Restoring resource/template relationships ");
		foreach( $this->resTplMap as $alias => $tplName ){
			$this->modx->log(modX::LOG_LEVEL_INFO,"Tying res [$alias] to tpl[$tplName] ");
			$res = $this->modx->getObject('modResource',array('alias'=>$alias));
			$resID = $res->get('id');
			$tplID = $this->modx->getObject('modTemplate',array('templatename'=>$tplName))->get('id');
			$res->set('template',$tplID);
			$this->modx->log(modX::LOG_LEVEL_INFO,"Saving res #$resID to tpl[$tplName] ");
			$res->save();
			$this->modx->log(modX::LOG_LEVEL_INFO,"Saved res #$resID to tpl[$tplName] ");
		};
	}//

// Restore parental relationships between resources
//------------------------------------------------------------------------------------------------------
public function restoreResourceParentalRelationships(){
		$this->modx->log(modX::LOG_LEVEL_INFO,"Restoring parent/child resource relationships");
		foreach($this->resParentMap as $resAlias => $parentAlias){
			if( $parentAlias == $this->resAliasKEY.'-resource-0'){
				$parentId = 0;
			} else {
				$parentRes = $this->modx->getObject('modResource',array('alias'=>$parentAlias));
				$parentId = $parentRes->get('id');
			};
			$res = $this->modx->getObject('modResource',array('alias'=>$resAlias)); 
			$resId = $res->get('id');
			$res->set('parent',$parentId);
			$res->save();
		};
	}//
	
};// end class TransportPackageWizard_HelperClass