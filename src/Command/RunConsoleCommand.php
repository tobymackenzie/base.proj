<?php
namespace TJM\BaseProj\Command;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'console', aliases: ['c'])]
class RunConsoleCommand extends Command{
	static public $defaultName = 'console';
	protected function configure(){
		$this
			->setDescription('Run a `bin/console` command on an existing project.')
			->addArgument('cmd', InputArgument::REQUIRED, 'Console command to run on project(s)')
			->addArgument('projects', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Name(s) (subpath) of project to run command for.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$command = $input->getArgument('cmd') ?: null;
		foreach($input->getArgument('projects') as $project){
			if($this->baseProj->has($project)){
				$this->baseProj->runConsole($project, [
					'command'=> $command,
					'interactive'=> $input->isInteractive(),
				]);
			}else{
				throw new Exception("Project \"{$project}\" not found");
			}
		}
		return 0;
	}
}

