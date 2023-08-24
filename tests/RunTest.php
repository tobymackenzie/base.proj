<?php
namespace TJM\BaseProj\Tests;
use TJM\BaseProj\BaseProj;
use PHPUnit\Framework\TestCase;

class RunTest extends TestCase{
	public function setUp(): void{
		chdir(__DIR__);
	}
	public function tearDown(): void{
		passthru('rm -r ' . __DIR__ . '/tmp*');
	}
	public function testRunInProjFolder(){
		mkdir('tmpproj');
		$proj = new BaseProj(null, [
			'projPath'=> __DIR__ . '/tmpproj',
		]);
		$proj->create(':tmp1', ['php']);
		$this->assertEquals(__DIR__ . '/tmpproj/tmp1', $proj->run('tmp1', 'pwd'));
		$this->assertEquals(__DIR__ . '/tmpproj/tmp1', $proj->run('tmp1', [
			'command'=> 'pwd'
		]), 'Should be able to pass command as array');
		$this->assertEquals(
			trim(file_get_contents(__DIR__ . '/data/create-php.txt')),
			str_replace(".DS_Store\n", '', $proj->run('tmp1', 'ls -1A'))
		);
	}
}
