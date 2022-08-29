<?php
namespace TJM\Project\Tests;
use TJM\Project\Project;
use PHPUnit\Framework\TestCase;

class Test extends TestCase{
	public function test(){
		$this->assertTrue((new Project())());
	}
}
