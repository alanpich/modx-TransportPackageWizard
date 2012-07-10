<?php class TransportPackageWizard_PostInstall {
	
public $post_install_script = '';	
public $resTplMap = array();
public $resAliasMap = array();
public $resParentMap = array();
	
function __construct($wizard){
		$this->wizard = $wizard;
		
		$helperClass = $this->_get_file('tpwhelper/TransportPackageWizard_PostInstallHelper.class.php')."\n";
		$this->_add('<?php ');
		$this->_add( $helperClass );
		$this->_add('$HLPR = new TransportPackageWizard_PostInstallHelper($object,"'.$this->wizard->namespace.'");');
		$this->_add('if(!$HLPR->isInstall){return true;};');
	}//
	
	
// Associate a TV with a template
//-------------------------------------------------------------------------------------
public function addTVtoTemplate($tvName, $templateName){
		$this->_add('$HLPR->addTVtoTemplate("'.$tvName.'","'.$templateName.'");');
	}//
	
// Set a Modx config option
//-------------------------------------------------------------------------------------
public function setOption($key, $val, $xtype='', $area='', $namespace=''){
		$val = (!is_numeric($val)) ? "'$val'" : $val;
		
		$params = array();
		$params[] = "'$key'";
		$params[] = $val;
		if(!empty($xtype)){ $params[] = "'$xtype'"; };
		if(!empty($area)) { $params[] = "'$area'"; };
		if(!empty($namespace)){ $params[] = "'$namespace'"; };
		$line = '$HLPR->setOption('. implode(',',$params) .');';
		
		$this->_add($line);
	}//
	
	
	
//-------------------------------------------------------------------------------------
//---  P R I V A T E   M E T H O D S  -------------------------------------------------
//-------------------------------------------------------------------------------------
	
	
// Maintain a map of ResourceAlias -> TemplateName for resource transport
//-------------------------------------------------------------------------------------
public function updateResourceTemplateMap( $res ){
		$tplID = $res->get('template');
		$resAlias = $res->get('alias');
		$templateName = $this->wizard->modx->getObject('modTemplate',$tplID)->get('templatename');
		
		// Save the resource alias for later
		$oldAlias = $resAlias;
		$newAlias = PKG_NAME_LOWER.'-resource-'.$res->get('id');
		$this->resAliasMap[$newAlias] = $oldAlias;
		$res->set('alias',$newAlias);
		
		// Save relationship between new alias and template name
		if(!isset($this->resTplMap[$resAlias])){
			$this->resTplMap[$newAlias] = $templateName;
		};		
	}//
	
// Maintain a map of Resource parents to the parent's new alias
//-------------------------------------------------------------------------------------
public function updateResourceParentMap( $res ){
		$alias = $res->get('alias');
		$parentAlias = PKG_NAME_LOWER.'-resource-'.$res->get('parent');
		$this->resParentMap[$alias] = $parentAlias;
	}//
	
// Build resolver script and add to vehicle
//-------------------------------------------------------------------------------------
public function _build(){
		
		// Add res/tpl map
		$this->_add('$HLPR->resTplMap = json_decode(\''.json_encode($this->resTplMap).'\');');
		$this->_add('$HLPR->resAliasMap = json_decode(\''.json_encode($this->resAliasMap).'\');');
		$this->_add('$HLPR->resParentMap = json_decode(\''.json_encode($this->resParentMap).'\');');
		$this->_add('$HLPR->resAliasKEY = "'.PKG_NAME_LOWER.'";');
		$this->_add('$HLPR->restoreResourceParentalRelationships();');
		$this->_add('$HLPR->restoreResourceTemplates();');
		
		// Add final return
		$this->_add('return true;');
		
		
		// Add to root category vehicle
		$this->wizard->categories[PKG_NAME]->resolverScript = $this->post_install_script;
	}//
	
	
// Add a line to resolver script
//-------------------------------------------------------------------------------------
private function _add($line){
		$this->post_install_script.= $line."\n";
	}//
	
	
// Helper Function - Get a file contents and strip out php tags
//-------------------------------------------------------------------------------------------------	
private function _get_file($filename){
		$o = file_get_contents($filename);
		$o = trim(str_replace(array("<?php\n",'?>'),'',$o));
		return $o;	
	}//

	
};// end class TransportPackageWizard_PostInstall