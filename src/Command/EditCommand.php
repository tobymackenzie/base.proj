<?php
namespace TJM\BaseProj\Command;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'edit', aliases: EditCommand::ALIASES)]
class EditCommand extends Command{
	const ALIASES = ['e', 'todo'];
	static public $defaultName = 'edit';
	protected function configure(){
		$this
			->setDescription('Edit file for project.')
			->addArgument('file', InputArgument::REQUIRED, 'File name to edit, path from project root.')
			->addArgument('projects', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Name(s) (subpath) of project to open.  Must be inside configured project folder.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$projects = $input->getArgument('projects');
		$called = $input->getArgument('command');
		if(in_array($called, static::ALIASES) && $called !== 'e'){
			$file = $called;
			array_unshift($projects, $input->getArgument('file'));
		}else{
			$file = $input->getArgument('file');
		}
		if(isset(ViewCommand::EXPANSIONS[$file])){
			$file = ViewCommand::EXPANSIONS[$file];
		}
		foreach($projects as $project){
			if($this->baseProj->has($project)){
				if(is_array($file)){
					$this->baseProj->editFirst($project, $file);
				}else{
					$this->baseProj->edit($project, $file);
				}
			}else{
				throw new Exception("Project \"{$project}\" not found");
			}
		}
		return 0;
	}
}
