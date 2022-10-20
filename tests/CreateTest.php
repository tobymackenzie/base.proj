<?php
namespace TJM\BaseProj\Tests;
use TJM\BaseProj\BaseProj;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase{
	public function setUp(): void{
		chdir(__DIR__);
	}
	public function tearDown(): void{
		passthru('rm -r ' . __DIR__ . '/tmp*');
	}
	public function testCreateBase(){
		$proj = new BaseProj();
		$proj->create('tmp1');
		$this->assertLs('create-base.txt', 'tmp1');
	}
	public function testCreateTwoBase(){
		$proj = new BaseProj();
		$proj->create(['tmp1', 'tmp2']);
		$this->assertLs('create-base.txt', 'tmp1');
		$this->assertLs('create-base.txt', 'tmp2');
	}
	public function testCreatePHP(){
		$proj = new BaseProj();
		$proj->create('tmp1', ['php']);
		$this->assertLs('create-php.txt', 'tmp1');
		$this->assertLs('create-php-src.txt', 'tmp1/src');
		$this->assertLs('create-php-tests.txt', 'tmp1/tests');
	}

	/*=====
	==helpers
	=====*/
	protected function assertLs($expect, $path){
		$expect = file_get_contents(__DIR__ . '/data/' . $expect);
		$result = str_replace(".DS_Store\n", '', shell_exec('ls -1A ' . $path));
		$this->assertEquals($expect, $result);
	}
}
