<?php
namespace TJM\BaseProj\Command;
use Symfony\Component\Console\Command\Command as Base;
use TJM\BaseProj\BaseProj;

class Command extends Base{
	protected $baseProj;
	public function __construct(BaseProj $baseProj){
		$this->baseProj = $baseProj;
		parent::__construct();
	}
}
