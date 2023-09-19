<?php
namespace TJM\BaseProj\Command;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TJM\BaseProj\BaseProj;

class RunBinCommand extends Command{
	static public $defaultName = 'bin';
	protected $baseProj;
	public function __construct(BaseProj $baseProj){
		$this->baseProj = $baseProj;
		parent::__construct();
	}
	protected function configure(){
		$this
			->setDescription('Run a `bin` folder command on an existing project.')
			->addArgument('cmd', InputArgument::REQUIRED, 'Bin command to run on project(s)')
			->addArgument('projects', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Name(s) (subpath) of project to run command for.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$command = $input->getArgument('cmd') ?: null;
		foreach($input->getArgument('projects') as $project){
			if($this->baseProj->has($project)){
				$this->baseProj->runBin($project, [
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
