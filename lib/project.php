<?php

require_once(NX_PATH.'lib/node-image.php');
require_once(NX_PATH.'lib/node-text.php');


class Project {
	protected static $dirProtectIndex = '<?php header( "HTTP/1.1 403 forbidden" );';
	protected static $fileGlob = '*.{md,jpg,jpeg,png,gif}';
	protected static $titleImageGlob = '*.{jpg,jpeg,png,gif}';
	protected static $sharekeyGlob = '*.sharekey';

	protected $name = null;
	protected $sharekey = null;

	public static function create($name) {
		$path = self::sanitizePath($name);

		// Create the project directory, the subdirectory for big images
		// and a dummy index.php to prevent dir listing
		mkdir($path);
		setFileMode($path);

		mkdir($path.CONFIG::IMAGE_BIG_PATH);
		setFileMode($path.CONFIG::IMAGE_BIG_PATH);
		
		file_put_contents($path.'index.php', self::$dirProtectIndex);
		setFileMode($path.'index.php');

		return Project::open($name);
	}

	public static function open($name) {
		$path = self::sanitizePath($name);
		return is_dir($path)
			? new Project($path)
			: null;
	}

	public static function openWithSharekey($name, $sharekey) {
		$project = self::open($name);

		// Make sure the project is shared and the sharekey matches
		if( $project && $project->isShared() && $project->getSharekey() == $sharekey ) {
			return $project;
		}
		return null;
	}

	protected function __construct($path) {
		$this->path = $path;
	}

	protected static function sanitizePath($name) {
		$name = iconv('UTF-8', 'ASCII//IGNORE', $name);
		$name = preg_replace('/\W+/', '-', $name);
		return CONFIG::PROJECTS_PATH.$name.'/';
	}

	public function getPath() {
		return $this->path;
	}

	public function getName() {
		return basename($this->path);
	}

	public function getTitleImage() {
		$images = saneGlob($this->path.self::$titleImageGlob, GLOB_BRACE);
		if( !empty($images) ) {
			rsort($images);	
			return "url('".$this->path.basename($images[0])."')";
		}
		else {
			return 'none';
		}
	}

	public function delete() {
		foreach( $this->getNodes() as $node ) {
			$node->delete();
		}
		unlink($this->path.'index.php');
		$this->removeSharekey();

		rmdir($this->path.CONFIG::IMAGE_BIG_PATH);
		rmdir($this->path);
	}

	public function isShared() {
		$key = $this->getSharekey();
		return !empty($key);
	}

	public function getSharekey() {
		// Load sharekey if we didn't have one already
		if( empty($this->sharekey) ) {
			$sharekeys = saneGlob($this->path.self::$sharekeyGlob, GLOB_BRACE);
			if( !empty($sharekeys) && preg_match('/(\w{32})\.sharekey$/', $sharekeys[0], $match) ) {
				$this->sharekey = $match[1];
			}	
		}

		return $this->sharekey;
	}

	public function removeSharekey() {
		$key = $this->getSharekey();
		if( !empty($key) ) {
			unlink($this->path.$key.'.sharekey');
		}
		$this->sharekey = null;
	}

	public function createSharekey() {
		// Remove old sharekey, if present
		$this->removeSharekey();

		// Create a new .sharekey file with a random name
		$key = md5(rand().time());
		$keyfile = $this->path.$key.'.sharekey';
		file_put_contents($keyfile, time());
		setFileMode($keyfile);

		$this->sharekey = $key;
		return $this->sharekey;
	}

	public function getNode($name) {
		if( preg_match('/\.(jpg|jpeg|png|gif)$/i', $name) ) {
			return NodeImage::open($this->path.$name);
		}
		else if( preg_match('/\.md$/i', $name) ) {
			return NodeText::open($this->path.$name);
		}
		return null;
	}

	public function getNodes() {
		$nodes = array();
		foreach( $this->getFiles() as $file ) {
			$nodes[] = $this->getNode(basename($file));
		}
		return $nodes;
	}

	public function getNodeCount() {
		return count($this->getFiles());
	}

	protected function getFiles() {
		$files = saneGlob($this->path.self::$fileGlob, GLOB_BRACE);
		rsort($files);
		return $files;
	}

	public function createZIP($zipPath) {
		$zip = new ZipArchive;
		if( $zip->open($zipPath, ZipArchive::CREATE) ) {
			foreach( $this->getNodes() as $node ) {
				$zip->addFile($node->getOriginalPath(), $node->getName());
			}

			$zip->close();
			return true;
		}
		return false;
	}

	public static function getProjectList() {
		$projects = array();
		foreach( saneGlob(CONFIG::PROJECTS_PATH.'*', GLOB_ONLYDIR) as $dir ) {
			$project = self::open( basename($dir) );
			if( $project ) { // Make sure the project could be opened
				$projects[] = $project;
			}
		}
		return $projects;
	}
}
