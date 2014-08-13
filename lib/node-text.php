<?php

require_once(NX_PATH.'lib/node.php');


class NodeText extends Node {
	protected $extension = 'md';
	public $type = 'text';

	public static function open($path) {
		return is_file($path)
			? new self($path)
			: null;
	}

	public static function create($basePath, $content) {
		$path = $basePath.self::getNewName('md');
		file_put_contents($path, $content);
		setFileMode($path);

		return self::open($path);
	}

	public function getContent() {
		return file_get_contents($this->path);
	}

	public function edit($content) {
		// Write the new file and delete the old
		$newPath = dirname($this->path).'/'.self::getNewName($this->extension);

		if( file_put_contents($newPath, $content) ) {
			setFileMode($newPath);
			$this->delete();
			$this->path = $newPath;
		}
	}
}
