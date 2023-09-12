<?php
namespace TJM\BaseProj\Command;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TJM\BaseProj\BaseProj;

class ListCommand extends Command{
	static public $defaultName = 'ls';
	protected $baseProj;
	public function __construct(BaseProj $baseProj){
		$this->baseProj = $baseProj;
		parent::__construct();
	}
	protected function configure(){
		$this
			->setDescription('List existing projects or their files.')
			->addArgument('project', InputArgument::OPTIONAL, 'Name (subpath) of project to open.  If not provided, will list all project folders.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$project = $input->getArgument('project');
		try{
			$output->writeln($this->baseProj->list($project));
		}catch(Exception $e){
			if($project){
				throw new Exception("Error finding project '{$project}'");
			}else{
				throw new Exception("Error finding project folder");
			}
		}
		return 0;
	}
}
