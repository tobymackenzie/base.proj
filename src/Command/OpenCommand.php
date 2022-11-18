<?php
namespace TJM\BaseProj\Command;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TJM\BaseProj\BaseProj;

class OpenCommand extends Command{
	static public $defaultName = 'open';
	protected $baseProj;
	public function __construct(BaseProj $baseProj){
		$this->baseProj = $baseProj;
		parent::__construct();
	}
	protected function configure(){
		$this
			->setDescription('Open an existing project.')
			->addArgument('project', InputArgument::REQUIRED, 'Name (subpath) of project to open.  Must be inside configured project folder.')
			->addOption('command', 'c', InputOption::VALUE_REQUIRED, 'Command to run for opening project.  Defaults to value configured on BaseProj')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$project = $input->getArgument('project');
		if($this->baseProj->has($project)){
			$this->baseProj->open($project, $input->getOption('command'));
		}else{
			throw new Exception("Project \"{$project}\" not found");
		}
	}
}
