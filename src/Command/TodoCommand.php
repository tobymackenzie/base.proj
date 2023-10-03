<?php
namespace TJM\BaseProj\Command;
use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TodoCommand extends Command{
	static public $defaultName = 'todo';
	protected function configure(){
		$this
			->setDescription('Edit todo file for project.')
			->addArgument('projects', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Name(s) (subpath) of project to open.  Must be inside configured project folder.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		foreach($input->getArgument('projects') as $project){
			if($this->baseProj->has($project)){
				$this->baseProj->edit($project, 'todo.md');
			}else{
				throw new Exception("Project \"{$project}\" not found");
			}
		}
		return 0;
	}
}
