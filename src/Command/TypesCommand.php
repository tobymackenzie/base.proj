<?php
namespace TJM\BaseProj\Command;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TypesCommand extends Command{
	static public $defaultName = 'types';
	protected function configure(){
		$this
			->setDescription('List project types available')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$output->writeln($this->baseProj->getTypes());
		return 0;
	}
}
