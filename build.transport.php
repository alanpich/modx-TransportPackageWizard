<?php

// Include the Library files --------------------------------------------------------------------------------------------------------------------
require_once 'TransportPackageWizard.class.php';

// Create an instance of the Transport Wizard ---------------------------------------------------------------------------------------------------
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

// Add Category 'Test Category' -----------------------------------------------------------------------------------------------------------------
$myCategory = $Package->addCategory('My Category'); 

// Add Elements to Category ---------------------------------------------------------------------------------------------------------------------
$myCategory->addSnippet('Test-Snippet',MODX_BASE_PATH.'_build/test.snippet.php');
$myCategory->addChunk('Alans-Test-Chunk',dirname(__FILE__).'/chunk.inc.html','A demo chunk inserted into the category `Test Category`');
 
 
// Add Files ------------------------------------------------------------------------------------------------------------------------------------
$Package->addDirectory(MODX_ASSETS_PATH.'components/mwib', '{assets_path}components/mwib/uploaded/');
 
// Build the transport package for deployments --------------------------------------------------------------------------------------------------  
$Package->build();
exit();


