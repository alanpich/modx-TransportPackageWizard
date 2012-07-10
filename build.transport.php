<?php

// Include the Library files -----------------------------------------------------------------
require_once 'TransportPackageWizard.class.php';

// Create an instance of the Transport Wizard ------------------------------------------------
$Package = new TransportPackageWizard(array(
					DEFINE => array(
							'MODX_CORE_PATH' => dirname(dirname(__FILE__)).'/core/',
							'MODX_ASSETS_PATH' => dirname(dirname(__FILE__)).'/assets/',
							'PKG_NAME' => 'MWIB',
							'PKG_NAME_LOWER' => 'mwib',
							'PKG_VERSION' => '1.2',
							'PKG_RELEASE' => 'resolver22'
						)
				));

// Add Category 'Google Analytics' -----------------------------------------------------------
$GA = $Package->addCategory('Google Analytics'); 
	  $GA->addSnippet('Google-Analytics');
	  $GA->addTV('Google-Analytics-Code');

// Add Category 'Offline Cacheing' -----------------------------------------------------------
$OC = $Package->addCategory('Offline Cacheing');
	  $OC->addSnippet('Cache-Manifest');
	  $OC->addChunk('offline-capable-includes');
	  
// Add Category 'Security' -------------------------------------------------------------------
$SE = $Package->addCategory('Security');
	  $SE->addSnippet('LOGIN_AUTHORITY');	  
	  
// Add Category 'Page Layout' ----------------------------------------------------------------
$PL = $Package->addCategory('Page Layout');
	  $PL->addTemplate('Article',true);
	  $PL->addTemplate('Gateway',true);
	  $PL->addTemplate('Issue Homepage',true);
	  $PL->addChunk('Page-Header');
	  $PL->addChunk('Page-Footer');
	  $PL->addChunk('Page-Article-Nav');
	  $PL->addChunk('tpl_Page-Article-Nav_home');
	  $PL->addChunk('tpl_Page-Article-Nav_next');
	  $PL->addChunk('tpl_Page-Article-Nav_prev');
	  $PL->addChunk('Page-Issue-Contents');
	  $PL->addChunk('tpl_Page-Issue-Contents_Item');
	  $PL->addChunk('tpl_Page-Issue-Contents_ItemOdd');
	  $PL->addChunk('tpl_Page-Issue-Contents_LeadStory'); 
	  $PL->addSnippet('Issue-Title');
	  $PL->addSnippet('PreviousHomeNextLinks');
	  $PL->addSnippet('Get_Current_Issue_Homepage');	
	  	   
// Add Files ---------------------------------------------------------------------------------
$Package->addDirectory(MODX_ASSETS_PATH.'components/mwib', '{assets_path}components/');

// Add Resources -----------------------------------------------------------------------------
$Package->addResources(array(1,2),true);
 
 
// Add post-install functions ----------------------------------------------------------------
$Package->PostInstall->setOption('mwib.createdSetting','Alan edited this','textfield','core');
 
 
// Build the transport package for deployments -----------------------------------------------
$Package->build();


echo "<pre>POST INSTALL SCRIPT\n---------------\n".str_replace('<?php','&lt;?php',$Package->getPostInstallScript())."</pre>";



