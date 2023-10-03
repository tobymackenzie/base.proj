<?php
namespace TJM\BaseProj\Command;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'run', aliases: RunCommand::ALIASES)]
class RunCommand extends Command{
	const ALIASES = ['awk', 'cat', 'chmod', 'composer', 'find', 'grep', 'gvim', 'less', 'll', 'nano', 'npm', 'phpunit', 'sed', 'vagrant', 'vi', 'vim'];
	static public $defaultName = 'run';
	protected function configure(){
		$this
			->setDescription('Run a shell command on an existing project.')
			->addArgument('cmd', InputArgument::REQUIRED, 'Command to run on project(s)')
			->addArgument('projects', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Name(s) (subpath) of project to run command for.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$command = $input->getArgument('cmd') ?: null;
		$called = $input->getArgument('command');
		if(in_array($called, static::ALIASES)){
			$command = "{$called} {$command}";
		}
		foreach($input->getArgument('projects') as $project){
			if($this->baseProj->has($project)){
				$this->baseProj->run($project, [
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

