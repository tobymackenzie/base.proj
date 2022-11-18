<?php
namespace TJM\BaseProj;
use TJM\ShellRunner\Location\Location;
use TJM\ShellRunner\ShellRunner;

class BaseProj{
	protected $projPath = __DIR__ . '/../proj';
	protected $shell;
	protected $tmpIncrement = 0;
	public function __construct(ShellRunner $shell = null, $opts = []){
		$this->shell = $shell ?: new ShellRunner();
		foreach($opts as $key=> $value){
			$this->$key = $value;
		}
	}
	public function create($locations, array $types = [], array $opts = []){
		array_unshift($types, 'base');
		if(!is_array($locations)){
			$locations = [$locations];
		}
		$tmpDir = $this->getTmpName();
		$projCommands = ['mkdir ' . $tmpDir];
		foreach($types as $type){
			$projCommands[] = "echo 'copying '{$type} && rsync -Dglopr {$this->projPath}/{$type}/ {$tmpDir}";
		}
		$this->shell->run(array_merge($opts, ['command'=> $projCommands]));
		foreach($locations as $location){
			if($location instanceof Location){
				$isLocal = $location->getProtocol() === 'file';
				$path = $location->getPath();
			}else{
				$isLocal = true;
				$path = $location;
				$location = new Location('file', '.');
			}
			$commands = [];
			if(!preg_match('/^\.+$/', $path)){
				$commands[] = 'mkdir -p ' . $path;
			}
			if($isLocal){
				$commands[] = 'shopt -s dotglob';
				$commands[] = 'cp -ir ' . $tmpDir . '/* ' . $path;
			}else{
				$this->shell->run(array_merge($opts, ['command'=> $commands]), $location);
				$commands = ["rsync -Dglopr {$tmpDir}/ " . (string) $location];
			}
			$this->shell->run(array_merge($opts, ['command'=> $commands]));
		}
		$this->shell->run(array_merge($opts, ['command'=> 'rm -r ' . $tmpDir]));
	}

	/*=====
	==helpers
	=====*/
	protected function getTmpName(){
		return '_tmp' . date('Ymd-His') . '-' . ++$this->tmpIncrement;
	}
}
