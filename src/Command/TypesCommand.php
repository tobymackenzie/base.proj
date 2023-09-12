<?php
namespace TJM\BaseProj\Command;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TJM\BaseProj\BaseProj;

class TypesCommand extends Command{
	static public $defaultName = 'types';
	protected $baseProj;
	public function __construct(BaseProj $baseProj){
		$this->baseProj = $baseProj;
		parent::__construct();
	}
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
