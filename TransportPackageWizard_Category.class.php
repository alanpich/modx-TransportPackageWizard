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
        )
    ),
);

public $snippets = array();
public $chunks = array();
public $directories = array();

	
function __construct( $name, $wizard ){
		$this->wizard =& $wizard;
		$this->name = $name;
		$this->modCategory = $wizard->modx->newObject('modCategory');
		$this->modCategory->set('id',count($this->wizard->categories));
		$this->modCategory->set('category',$name);
	}//
	

public function add( $objects ){
		$this->modCategory->addMany($objects);
	}//
	
	
public function addSnippet( $name, $filePath, $description = '', $properties = array() ) {
		if(!file_exists($filePath)){
			$this->wizard->log("Skipping missing Snippet [$filePath]",'WARN','orange');
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
	
public function addChunk($name, $filePath, $description='', $properties = array() ){
		if(!file_exists($filePath)){
			$this->wizard->log("Skipping missing Chunk [$filePath]",'WARN','orange');
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
	
	
public function addDirectory($source,$target){
		if(!is_dir($source)){
			$this->wizard->log("Skipping missing Directory [$source",'WARN','orange');
			return;
		};
		$this->directories[] = array('source'=>$source,'target'=>$target);
	}//
	
	
	
	
public function build() {

		$this->wizard->log("Adding category [$this->name] to package...",'ADD');

		// Add snippets
		$this->_build_snippets();
		$this->_build_chunks();
	
		// Build vehicle and add to transport package
		$this->vehicle = $this->wizard->builder->createVehicle($this->modCategory,$this->vehicle_attr);
		
		// Add file resolvers
		$this->_build_directories();
		
		$this->wizard->builder->putVehicle($this->vehicle);
	}//
	
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



	
	
private function _get_file($filename){
		$o = file_get_contents($filename);
		$o = trim(str_replace(array('<?php','?>'),'',$o));
		return $o;	
	}//

	
};// end class TransportPackageWizard_Category

