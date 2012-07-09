<?php
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
		'root' => $root,
		'build' => $root . '_build/',
		'data' => $root . '_build/data/',
		'resolvers' => $root . '_build/resolvers/',
		'chunks' => $root.'core/components/'.PKG_NAME_LOWER.'/chunks/',
		'lexicon' => $root . 'core/components/'.PKG_NAME_LOWER.'/lexicon/',
		'docs' => $root.'core/components/'.PKG_NAME_LOWER.'/docs/',
		'elements' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/',
		'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
		'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
	);


require_once 'TransportPackageWizard.class.php';
$Package = new TransportPackageWizard(array(
					DEFINE => array(
							'MODX_CORE_PATH' => dirname(dirname(__FILE__)).'/core/',
							'PKG_NAME' => 'MWIB',
							'PKG_NAME_LOWER' => 'mwib',
							'PKG_VERSION' => '1.0',
							'PKG_RELEASE' => 'beta5'
						),
					'root' =>  dirname(dirname(__FILE__)).'/'
				));


// Add Category 'Test Category' -----------------------------------------------------------------------------------------------------------------
$Test_Category = $Package->addCategory('Test Category'); 
	$Test_Category->addSnippet('Test-Snippet',dirname(__FILE__).'/test.snippet.php');
	$Test_Category->addChunk('Alans-Test-Chunk',dirname(__FILE__).'/chunk.inc.html','A demo chunk inserted into the category `Test Category`');
 
 
// Add Files ------------------------------------------------------------------------------------------------------------------------------------
$Package->addDirectory($root.'assets/components/mwib', "return MODX_CORE_PATH.'components/demo/'");
 
  
$Package->build();
exit();


