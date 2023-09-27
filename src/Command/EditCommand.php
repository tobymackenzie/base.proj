<?php
namespace TJM\BaseProj\Command;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TJM\BaseProj\BaseProj;

class EditCommand extends Command{
	static public $defaultName = 'edit';
	protected $baseProj;
	public function __construct(BaseProj $baseProj){
		$this->baseProj = $baseProj;
		parent::__construct();
	}
	protected function configure(){
		$this
			->setDescription('Edit file for project.')
			->addArgument('file', InputArgument::REQUIRED, 'File name to edit, path from project root.')
			->addArgument('projects', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Name(s) (subpath) of project to open.  Must be inside configured project folder.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		foreach($input->getArgument('projects') as $project){
			if($this->baseProj->has($project)){
				$this->baseProj->edit($project, $input->getArgument('file'));
			}else{
				throw new Exception("Project \"{$project}\" not found");
			}
		}
		return 0;
	}
}
