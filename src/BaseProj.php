<?php
namespace TJM\BaseProj;
use Exception;
use TJM\FileMergeTasks\MergeDirectoryTask;
use TJM\ShellRunner\Location\Location;
use TJM\ShellRunner\ShellRunner;

class BaseProj{
	protected $editor = '${VISUAL:-${EDITOR:-vi}}'; //--editor command for `edit()` method
	protected $openCommand; //--default opens `$SHELL` at directory
	protected $projPath =  __DIR__ . '/../projects';
	protected $templatePath = __DIR__ . '/../templates';
	protected $shell;
	protected $tmpIncrement = 0;
	protected $viewer = '${PAGER:-less}'; //--command for `view()` method
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
		//--need `:` for interactive mode because process will hang otherwise
		$this->shell->run(array_merge($opts, ['command'=> ': \mkdir ' . $tmpDir]));
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
			}
			$commands = [];
			if(!preg_match('/^\.+$/', $path)){
				$commands[] = '\mkdir -p ' . $path;
			}
			if($isLocal){
				$commands[] = 'shopt -s dotglob';
				if(isset($opts['interactive']) && $opts['interactive']){
					$commands[] = '\cp -iR ' . $tmpDir . '/* ' . $path;
				}else{
					$commands[] = "\\rsync -Dglopr {$tmpDir}/ " . $path;
				}
			}else{
				$this->shell->run(array_merge($opts, ['command'=> $commands]), $location);
				$commands = ["\\rsync -Dglopr {$tmpDir}/ " . (string) $location];
			}
			$this->shell->run(array_merge($opts, ['command'=> $commands]));
		}
		$this->shell->run(array_merge($opts, ['command'=> '\rm -r ' . $tmpDir]));
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
				'command'=> '\ls ' . escapeshellarg($this->projPath . '/' . $name) . ' 2> /dev/null',
				'interactive'=> false,
			]);
			return true;
		}catch(Exception $e){
			return false;
		}
	}

	/*
	Method: getPath
	Get path to project, or `projPath` if no name
	*/
	public function getPath($name = null){
		if(isset($name)){
			if($this->isValidName($name)){
				return $this->getProjectRoot($name);
			}else{
				throw new Exception('Project name is invalid');
			}
		}else{
			return $this->projPath;
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
			'command'=> '\ls -1 ' . escapeshellarg($path),
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
				$this->openCommand = '\cd {{path}} && $SHELL';
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
	==shell
	=====*/
	/*
	Method: edit
	Edit a project's file.
	*/
	public function edit($name, $file){
		return $this->run($name, [
			'command'=> $this->editor . ' ' . escapeshellarg($file),
			'interactive'=> true,
		]);
	}
	/*
	Method: editFirst
	Edit first project file found, or first if creating
	 */
	public function editFirst($name, array $files){
		foreach($files as $file){
			if($this->has($name. '/' . $file)){
				$match = $file;
				break;
			}
		}
		if(!isset($match)){
			$match = $files[0];
		}
		return $this->edit($name, $match);
	}

	/*
	Method: run
	Run a shell command in a given site's project root.
	*/
	public function run($name, $command){
		if(!$this->isValidName($name) && !$this->isPwdProject($name)){
			throw new Exception('Project name is invalid');
		}
		$path = $this->getProjectRoot($name);
		if($path){
			return $this->shell->run($command, $path);
		}else{
			throw new Exception('Project path not found.');
		}
	}

	/*
	Method: runBin
	Run a command in a given site's project "bin" folder.
	*/
	public function runBin($name, $command){
		if(is_array($command)){
			$command['command'] = escapeshellcmd('./bin/' . $command['command']);
		}else{
			$command = escapeshellcmd('./bin/' . $command);
		}
		return $this->run($name, $command);
	}

	/*
	Method: runConsole
	Run a site's `bin/console` command, useful for eg Symfony projects.
	*/
	public function runConsole($name, $command){
		if(is_array($command)){
			$command['command'] = escapeshellcmd('./bin/console ' . $command['command']);
		}else{
			$command = escapeshellcmd('./bin/console ' . $command);
		}
		return $this->run($name, $command);
	}

	/*
	Method: runGit
	Run a git command in a given site's project root.
	*/
	public function runGit($name, $command){
		if(is_array($command)){
			$command['command'] = escapeshellcmd('git ' . $command['command']);
		}else{
			$command = escapeshellcmd('git ' . $command);
		}
		return $this->run($name, $command);
	}

	/*
	Method: view
	View a project's file.
	*/
	public function view($name, $file){
		return $this->run($name, [
			'command'=> $this->viewer. ' ' . escapeshellarg($file),
			'interactive'=> true,
		]);
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
			'command'=> '\ls -1 ' . escapeshellarg($this->templatePath),
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
			$path = $this->shell->run('\cd ' . $path . ' && \git rev-parse --show-toplevel 2> /dev/null || echo ""');
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
		return $path && $this->shell->run('\cd ' . $path . ' && \git rev-parse --is-inside-work-tree 2> /dev/null || echo "false"') === 'true';
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
