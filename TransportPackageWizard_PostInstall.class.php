<?php class TransportPackageWizard_PostInstall {
	
public $post_install_script = '';	
	
function __construct($wizard){
		$this->wizard = $wizard;
		
		$this->_add('global $modx;');
		$this->_add('require_once $modx->getOption(\'core_path\').\'components/tpwhelper/TransportPackageWizard_PostInstallHelper.class.php\';');
		$this->_add('$HLPR = new TransportPackageWizard_PostInstallHelper();');
	}//
	
	
// Associate a TV with a template
//-------------------------------------------------------------------------------------
public function addTVtoTemplate($tvName, $templateName){
		$this->_add('$HLPR->addTVtoTemplate("'.$tvName.'","'.$templateName.'");');
	}//
	
	
private function _add($line){
		$this->post_install_script.= $line."\n";
	}//
	
};// end class TransportPackageWizard_PostInstall