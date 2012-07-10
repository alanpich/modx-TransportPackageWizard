<?php class TransportPackageWizard_Category {
	
public $vehicle_attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
        'Chunks' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
        'Templates' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'templatename',
        ),
        'TemplateVars' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        )
    )
);
public $resource_attr = array(
    xPDOTransport::UNIQUE_KEY => 'id',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'TemplateVars' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        )
    )
);

public $snippets = array();
public $chunks = array();
public $directories = array();
public $templates = array();
public $tvs = array();
public $resolverScript = '';
public $resources = array();

	
function __construct( $name, $wizard ){
		$this->wizard =& $wizard;
		$this->name = $name;
		$this->modCategory = $wizard->modx->newObject('modCategory');
		$this->modCategory->set('id',count($this->wizard->categories));
		$this->modCategory->set('category',$name);
	}//
	

// Add a snippet to this category
//	- If only snippet name is specified then will attempt to load from MODX	
//-------------------------------------------------------------------------------------------------
public function addSnippet( $name, $filePath = false, $description = '', $properties = array() ) {
		if(!$filePath){	return $this->_addFromSnippet($name); };
		if(!file_exists($filePath)){
			$this->wizard->warn("Skipping missing Snippet [$filePath]");
			return;
		};
		$snippet = $this->wizard->modx->newObject('modSnippet');
		$snippet->fromArray(array(
			'id' => count($this->snippets),
			'name' => $name,
			'description' => $description,
			'snippet' => $this->_get_file($filePath),
		),'',true,true);
		$snippet->setProperties($properties);
		$this->snippets[] = $snippet;
	}//
	
	
// Add a chunk to this category	
//	- If only chunk name is specified then will attempt to load from MODX	
//-------------------------------------------------------------------------------------------------
public function addChunk($name, $filePath = false, $description='', $properties = array() ){
		if(!$filePath){	return $this->_addFromChunk($name); };
		if(!file_exists($filePath)){
			$this->wizard->warn("Skipping missing Chunk [$filePath]");
			return;
		};
		$chunk = $this->wizard->modx->newObject('modChunk');
		$chunk->fromArray(array(
			'id' => count($this->chunks),
			'name' => $name,
			'description' => $description,
			'snippet' => $this->_get_file($filePath),
		),'',true,true);
		$chunk->setProperties($properties);
		$this->chunks[] = $chunk;
	}//


// Add a template to this category	
//	- pass true as second param to add all associated TVs	
//-------------------------------------------------------------------------------------------------
public function addTemplate($name, $addAssociatedTVs = false ){
		$this->_addFromTemplate($name);
		if($addAssociatedTVs){
			$this->_associateAllTVsForTemplate($name);
		};
	}//


// Add a Template Variable to this category	
//-------------------------------------------------------------------------------------------------
public function addTV($name){
		$this->_addFromTV($name);
	}//

	
// Add a directory to this category	 [ Use TransportPackageWizard::addDirectory() instead ]
//-------------------------------------------------------------------------------------------------
public function addDirectory( $source, $target ){
		if(!is_dir($source)){
			$this->wizard->warn("Skipping missing Directory [$source]");
			return;
		};

		$this->directories[] = array('source'=>$source,'target'=>$target);
	}//
	
	
// Add a resource to the vehicle	 [ Use TransportPackageWizard::addResources() instead ]
//-------------------------------------------------------------------------------------------------
public function addResource( $resID ){
		$res = $this->wizard->modx->getObject('modResource',$resID);
		if(! $res instanceOf modResource ){
			$this->wizard->warn("Skipping missing Resource [#$resID]");
			return;
		};
		
		// Update resource->alias / template name map
		$this->wizard->PostInstall->updateResourceTemplateMap(&$res);
		// Maintain parental relationship map
		$this->wizard->PostInstall->updateResourceParentMap(&$res);
		
		$this->resources[] = $res;
	}//
	
	


	
	
//-------------------------------------------------------------------------------------------------
//---  D O N T   U S E   T H E S E   F U N C T I O N S   D I R E C T L Y  -------------------------
//-------------------------------------------------------------------------------------------------
	
	
	
// Adds a snippet from the current modx installation
//-------------------------------------------------------------------------------------------------
private function _addFromSnippet($name){
		$identifier = is_int($name)? $name : array( 'name' => $name);
		$snippet = $this->wizard->modx->getObject('modSnippet',$identifier);
		if(!$snippet instanceof modSnippet){
			$this->wizard->warn("Skipping nonexistant snippet [$name]");
			return;
		};
		$this->snippets[] = $snippet;
	}//
	
	
// Adds a snippet from the current modx installation
//-------------------------------------------------------------------------------------------------
private function _addFromChunk($name){
		$identifier = is_int($name)? $name : array( 'name' => $name);
		$chunk = $this->wizard->modx->getObject('modChunk',$identifier);
		if(!$chunk instanceof modChunk){
			$this->wizard->warn("Skipping nonexistant chunk [$name]");
			return;
		};
		$this->chunks[] = $chunk;
	}//
	
	
// Adds a template from the current modx installation
//-------------------------------------------------------------------------------------------------
private function _addFromTemplate($name){
		$identifier = is_int($name)? $name : array( 'templatename' => $name);
		$template = $this->wizard->modx->getObject('modTemplate',$identifier);
		if(!$template instanceof modTemplate){
			$this->wizard->warn("Skipping nonexistant template [$name]");
			return;
		};
		$this->templates[] = $template;
	}//
	
	
// Adds a template variable from the current modx installation
//-------------------------------------------------------------------------------------------------
private function _addFromTV($name){
		$identifier = is_int($name)? $name : array( 'name' => $name);
		$tv = $this->wizard->modx->getObject('modTemplateVar',$identifier);
		if(!$tv instanceof modTemplateVar){
			$this->wizard->warn("Skipping nonexistant TV [$name]");
			return;
		};
		$this->tvs[] = $tv;
	}//
	
	
// Adds post-install resolvers to associate all TVs with template $name
//-------------------------------------------------------------------------------------------------
private function _associateAllTVsForTemplate($name){
		$identifier = is_int($name)? $name : array( 'templatename' => $name);
		$tpl = $this->wizard->modx->getObject('modTemplate',$identifier);
		$tvs = $tpl->getTemplateVars();
		foreach($tvs as $tv){
			$tvName = $tv->get('name');
			$this->wizard->PostInstall->addTVtoTemplate($tvName,$name);
		};
	}//
	
	
	
// Build the transport vehicle for this category
//-------------------------------------------------------------------------------------------------
public function build() {

		$this->wizard->log("Adding category [$this->name] to package...",'ADD');

		// Add snippets
		$this->_build_snippets();
		$this->_build_chunks();
		$this->_build_templates();
		$this->_build_tvs();
		$this->_build_resources();
	
		// Build vehicle and add to transport package
		$this->vehicle = $this->wizard->builder->createVehicle($this->modCategory,$this->vehicle_attr);
		
		// Add script resolver (if one is set)
		$this->_build_post_install_script();
		
		// Add file resolvers
		$this->_build_directories();
		
		$this->wizard->builder->putVehicle($this->vehicle);
	}//


// Build Snippets for transport
//-------------------------------------------------------------------------------------------------	
private function _build_snippets(){
		if(count($this->snippets)<1){return;};
		$this->modCategory->addMany($this->snippets);
		$this->wizard->log("  &#8212; Snippets:",'');
		foreach($this->snippets as $snippet) {
			$name = $snippet->get('name');
			$description = $snippet->get('description');
			$description = empty($description) ? '' : "~ <em>$description</em>";
			$this->wizard->log("    &raquo; $name $description",'');
		};
	}//


// Build Chunks for transport
//-------------------------------------------------------------------------------------------------	
private function _build_chunks(){
		if(count($this->chunks)<1){return;};
		$this->modCategory->addMany($this->chunks);
		$this->wizard->log("  &#8212; Chunks:",'');
		foreach($this->chunks as $chunk) {
			$name = $chunk->get('name');
			$description = $chunk->get('description');
			$description = empty($description) ? '' : "~ <em>$description</em>";
			$this->wizard->log("    &raquo; $name $description",'');
		};
	}//
	
	
// Build Templates for transport
//-------------------------------------------------------------------------------------------------	
private function _build_templates(){
		if(count($this->templates)<1){return;};
		$this->modCategory->addMany($this->templates);
		$this->wizard->log("  &#8212; Templates:",'');
		foreach($this->templates as $template) {
			$name = $template->get('templatename');
			$this->wizard->log("    &raquo; $name",'');
		};
	}//
	
	
// Build TVs for transport
//-------------------------------------------------------------------------------------------------	
private function _build_tvs(){
		if(count($this->tvs)<1){return;};
		$this->modCategory->addMany($this->tvs);
		$this->wizard->log("  &#8212; Template Variables:",'');
		foreach($this->tvs as $tv) {
			$name = $tv->get('name');
			$this->wizard->log("    &raquo; $name",'');
		};
	}//


// Build Resources for transport
//-------------------------------------------------------------------------------------------------	
private function _build_resources(){
		
		if(count($this->resources)<1){return;};
		
		
		$this->wizard->log("  &#8212; Resources:",'');
		foreach($this->resources as $res) {
			$vehicle = $this->wizard->builder->createVehicle($res,$this->resource_attr);
			$this->wizard->builder->putVehicle($vehicle);
			$name = $res->get('pagetitle');
			$this->wizard->log("    &raquo; $name",'');
		};
	}//


// Build Directories for transport
//-------------------------------------------------------------------------------------------------	
private function _build_post_install_script(  ){
		if(empty($this->resolverScript)){return;};
		
		// write script to temp file
		$fh = fopen('transport.install.script.php','w+');
		fwrite($fh,$this->resolverScript);
		fclose($fh);
		
		// Add file to vehicle as php resolver
		$this->vehicle->resolve('php',array(
				'source' => 'transport.install.script.php'
			));
			
		// Log
		$this->wizard->log("  &#8212; Install Script added",'');
					
	}//
	
	
// Build Directories for transport
//-------------------------------------------------------------------------------------------------	
private function _build_directories(  ){
		if(count($this->directories)<1){return;};
		$this->wizard->log("  &#8212; Directories:",'');
		foreach($this->directories as $dir){
			
			// Prepare SOURCE
			$source = $dir['source'];
			
			// Prepare TARGET
			$rawTarget = $dir['target'];
			$target = $rawTarget;
			$target = str_replace(array_keys($this->wizard->pathShortcuts),$this->wizard->pathShortcuts,$target);
			$target = 'return "'.$target.'";';
			
			$this->vehicle->resolve('file',array(
				'source' => $source,
				'target' => $target
			));
			$this->wizard->log("    &raquo; ". $dir['source']."\n                      => ".$rawTarget,'');
		};
	}//

	
// Helper Function - Get a file contents and strip out php tags
//-------------------------------------------------------------------------------------------------	
private function _get_file($filename){
		$o = file_get_contents($filename);
		$o = trim(str_replace(array('<?php','?>'),'',$o));
		return $o;	
	}//

	
};// end class TransportPackageWizard_Category