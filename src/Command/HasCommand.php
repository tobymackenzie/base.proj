<?php
namespace TJM\BaseProj\Command;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TJM\BaseProj\BaseProj;

class HasCommand extends Command{
	static public $defaultName = 'has';
	protected $baseProj;
	public function __construct(BaseProj $baseProj){
		$this->baseProj = $baseProj;
		parent::__construct();
	}
	protected function configure(){
		$this
			->setDescription('Determine if project already exists.')
			->addArgument('project', InputArgument::REQUIRED, 'Name (subpath) of project to open.  Must be inside configured project folder.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$project = $input->getArgument('project');
		if($this->baseProj->has($project)){
			if($output->isQuiet()){
				return;
			}else{
				$output->writeln("true");
			}
		}else{
			if($output->isQuiet()){
				throw new Exception("Project '{$project}' doesn't exist");
			}else{
				$output->writeln("false");
			}
		}
		return 0;
	}
}
