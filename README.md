#Modx Transport Package Wizard#
###An easier way to create Transport Packages for Modx 2.2+###
--------------------------------------------------------------
An ongoing project to create an easy-to-use library for transport package creation in modx.
Very basic at this point, usage is outlined below

```php
<?php
/** Initialise the Wizard class */
$Package = new TransportPackageWizard(array(
  				DEFINE => array(
							'MODX_CORE_PATH' => dirname(dirname(__FILE__)).'/core/',
							'PKG_NAME' => 'My Package Name',
							'PKG_NAME_LOWER' => 'mypackagename',
							'PKG_VERSION' => '1.0',
							'PKG_RELEASE' => 'beta1'
						)
				));

/** Add files & directories to package */
$Package->addDirectory('path/to/directory', "{assets_path}components/mypackagename/");

/** Create a Category element */
$myCategory = $Package->addCategory('My Category Name'); 

/** Add Elements to the category */
$myCategory->addSnippet('Snippet-Name');
$myCategory->addChunk('Chunk-Name');
$myCategory->addTV('TV-Name');

/** Add Template to category (2nd param `true` to include all associated TVs) */
$myCategory->addTemplate('Template-Name',true);

/** Add Resources to transport (2nd param `true` to include child resources) */
$Package->addResources(array(1,2,3),true);

/** Set a new System Setting after install */
$Package->PostInstall->setOption('key','value','xtype','area','namespace');

/** Update an existing System Setting after install */
$Package->PostInstall->setOption('key','value');

/** Build the transport package for deployment */
$Package->build();

```
