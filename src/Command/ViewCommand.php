<?php
namespace TJM\BaseProj\Command;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'view', aliases: ViewCommand::ALIASES)]
class ViewCommand extends Command{
	const ALIASES = ['license', 'readme'];
	const EXPANSIONS = [
		'license'=> 'license.md',
		'readme'=> 'readme.md',
	];
	static public $defaultName = 'view';
	protected function configure(){
		$this
			->setDescription('View file for project.')
			->addArgument('file', InputArgument::REQUIRED, 'File name to edit, path from project root.')
			->addArgument('projects', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Name(s) (subpath) of project to open.  Must be inside configured project folder.')

			->addOption('edit', 'e', InputOption::VALUE_NONE, 'If passed, will edit file instead of view.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$projects = $input->getArgument('projects');
		$called = $input->getArgument('command');
		if(in_array($called, static::ALIASES)){
			$file = $called;
			array_unshift($projects, $input->getArgument('file'));
		}else{
			$file = $input->getArgument('file');
		}
		if(isset(static::EXPANSIONS[$file])){
			$file = static::EXPANSIONS[$file];
		}
		foreach($projects as $project){
			if($this->baseProj->has($project)){
				if($input->getOption('edit')){
					$this->baseProj->edit($project, $file);
				}else{
					$this->baseProj->view($project, $file);
				}
			}else{
				throw new Exception("Project \"{$project}\" not found");
			}
		}
		return 0;
	}
}
