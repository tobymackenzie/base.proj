<?php
namespace TJM\BaseProj;
use Exception;
use TJM\FileMergeTasks\MergeDirectoryTask;
use TJM\ShellRunner\Location\Location;
use TJM\ShellRunner\ShellRunner;

class BaseProj{
	protected $openCommand; //--default opens `$SHELL` at directory
	protected $projPath =  __DIR__ . '/../projects';
	protected $templatePath = __DIR__ . '/../templates';
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
		$this->shell->run(array_merge($opts, ['command'=> 'mkdir ' . $tmpDir]));
		foreach($types as $type){
			(new MergeDirectoryTask(["{$this->templatePath}/{$type}"], $tmpDir))->do();
		}
		foreach($locations as $location){
			if($location instanceof Location){
				$isLocal = $location->getProtocol() === 'file';
				$path = $location->getPath();
			}else{
				$isLocal = true;
				$path = $this->getNormalizedLocationPath($location);
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
	==project path
	=====*/
	/*
	Method: has
	Whether project exists in project path
	*/
	public function has($name){
		try{
			if(!$this->isValidName($name)){
				throw new Exception('Project name is invalid');
			}
			$this->shell->run([
				'command'=> 'ls ' . escapeshellarg($this->projPath . '/' . $name),
				'interactive'=> false,
			]);
			return true;
		}catch(Exception $e){
			return false;
		}
	}

	/*
	Method: list
	List projects or project files
	*/
	public function list($name = null){
		$path = $this->projPath . '/';
		if($name){
			if(!$this->isValidName($name)){
				throw new Exception('Project name is invalid');
			}
			$path .= $name;
		}
		return explode("\n", $this->shell->run([
			'command'=> 'ls -1 ' . escapeshellarg($path),
			'interactive'=> false,
		]));
	}

	/*
	Method: isValidName
	Whether project name is valid, to prevent accessing some outside directories
	*/
	protected function isValidName($name){
		return
			is_string($name)
			&& strlen($name)
			&& strpos($name, '../') === false
			&& strpos($name, '/..') === false
			&& substr($name, 0, 1) !== '/'
			&& $name !== '..'
		;
	}

	/*
	Method: open
	Open existing project in editor / etc
	*/
	public function open($name, $command = null){
		if(!$this->isValidName($name) && !$this->isPwdProject($name)){
			throw new Exception('Project name is invalid');
		}
		if(empty($command)){
			if(empty($this->openCommand)){
				$this->openCommand = 'cd {{path}} && $SHELL';
				if(substr(getenv('SHELL'), -3) === 'zsh'){
					$this->openCommand .= ' -i';
				}else{
					$this->openCommand .= ' -l';
				}
			}
			$command = $this->openCommand;
		}
		if(is_array($command)){
			foreach($command as $c){
				$this->open($name, $c);
			}
		}else{
			$path = $this->getProjectRoot($name);
			if(strpos($command, '{{path}}') === false){
				$command .= ' ' . $path;
			}else{
				$command = str_replace('{{path}}', $path, $command);
			}
			$opts = [
				'command'=> $command,
			];
			if($this->isCommandInteractive($command)){
				$opts['interactive'] = true;
			}
			$this->shell->run($opts);
		}
	}

	/*=====
	==templates
	=====*/
	/*
	Method: getTypes
	List available project types / templates
	*/
	public function getTypes(){
		return explode("\n", $this->shell->run([
			'command'=> 'ls -1 ' . escapeshellarg($this->templatePath),
			'interactive'=> false,
		]));
	}

	/*=====
	==helpers
	=====*/
	protected function isCommandInteractive($command){
		return !in_array(explode(' ', $command)[0], ['open']);
	}
	protected function getProjectRoot($name){
		if($this->isPwdProject($name)){
			$path = realpath($name);
			if(!$path){
				throw new Exception("BaseProject::getProjectRoot(): pwd path not found");
			}
			$path = $this->shell->run('cd ' . $path . ' && git rev-parse --show-toplevel 2> /dev/null || echo ""');
			if(!$path){
				throw new Exception("BaseProject::getProjectRoot(): Project root not found");
			}
			return $path;
		}elseif($this->isValidName($name)){
			return $this->projPath . '/' . $name;
		}else{
			throw new Exception("BaseProject::getProjectRoot(): Name {$name} does not appear to be a valid project");
		}
	}
	protected function isPwdProject($name){
		//--is considered a generic project if path exists and is a git working tree
		//-# should work for other paths, but we don't want to support those currently
		if($name !== '.'){
			return false;
		}
		$path = realpath($name);
		return $path && $this->shell->run('cd ' . $path . ' && git rev-parse --is-inside-work-tree 2> /dev/null || echo "false"') === 'true';
	}
	protected function getTmpName(){
		return '_tmp' . date('Ymd-His') . '-' . ++$this->tmpIncrement;
	}
	protected function getNormalizedLocationPath($path){
		if(is_string($path) && substr($path, 0, 1) === ':'){
			$path = $this->projPath . '/' . substr($path, 1);
		}
		return $path;
	}
}
