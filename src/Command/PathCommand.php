<?php
namespace TJM\BaseProj\Command;
use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PathCommand extends Command{
	static public $defaultName = 'path';
	protected function configure(){
		$this
			->setDescription('Get path of a project.  If no name, get root projPath.')
			->addArgument('project', InputArgument::OPTIONAL, 'Name (subpath) of project to open.  Must be inside configured project folder.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$output->writeln($this->baseProj->getPath($input->getArgument('project')));
		return 0;
	}
}
