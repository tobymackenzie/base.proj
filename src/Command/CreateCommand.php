<?php
namespace TJM\BaseProj\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TJM\BaseProj\BaseProj;

class CreateCommand extends Command{
	static public $defaultName = 'create';
	protected $baseProj;
	public function __construct(BaseProj $baseProj){
		$this->baseProj = $baseProj;
		parent::__construct();
	}
	protected function configure(){
		$this
			->setDescription('Create a project.')
			->addArgument('args', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Project types to add.  Final argument is path to create project at, if no location option provided.')
			->addOption('forward-agent', 'f', InputOption::VALUE_NONE, 'Forward local credentials for connecting to other servers from remote when running over SSH.')
			->addOption('locations', 'l', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Location(s) / path(s) to create project at. Can be file paths or "ssh://" type path')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$opts = [
			'interactive'=> $input->isInteractive(),
		];
		if($input->getOption('forward-agent')){
			$opts['forwardAgent'] = true;
		}
		$args = $input->getArgument('args');
		$locations = $input->getOption('locations') ?: [array_pop($args)];
		$this->baseProj->create($locations, $args, $opts);
	}
}
