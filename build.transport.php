<?php

require_once 'TransportPackageWizard.class.php';
$Package = new TransportPackageWizard(array(
					DEFINE => array(
							'MODX_CORE_PATH' => dirname(dirname(__FILE__)).'/core/',
							'MODX_ASSETS_PATH' => dirname(dirname(__FILE__)).'/assets/',
							'PKG_NAME' => 'MWIB',
							'PKG_NAME_LOWER' => 'mwib',
							'PKG_VERSION' => '1.0',
							'PKG_RELEASE' => 'beta14'
						)
				));


// CATEGORY:  Google Analytics
$Security = $Pachage->addCategory('Security');

// Add Category 'Test Category' -----------------------------------------------------------------------------------------------------------------
$Test_Category = $Package->addCategory('Test Category'); 
	$Test_Category->addSnippet('Test-Snippet',MODX_BASE_PATH.'_build/test.snippet.php');
	$Test_Category->addChunk('Alans-Test-Chunk',dirname(__FILE__).'/chunk.inc.html','A demo chunk inserted into the category `Test Category`');
 
 
// Add Files ------------------------------------------------------------------------------------------------------------------------------------
$Package->addDirectory(MODX_ASSETS_PATH.'components/mwib', '{assets_path}components/mwib/uploaded/');
 
  
$Package->build();
exit();


