<?php
namespace TJM\BaseProj\Tests;
use TJM\BaseProj\BaseProj;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase{
	public function setUp(): void{
		chdir(__DIR__);
	}
	public function tearDown(): void{
		passthru('rm -r ' . __DIR__ . '/tmp*');
	}
	public function testPathForExistingProjectRoot(){
		mkdir('tmpproj');
		$proj = new BaseProj(null, [
			'projPath'=> __DIR__ . '/tmpproj',
		]);
		$this->assertEquals(__DIR__ . '/tmpproj', $proj->getPath());
	}
	public function testPathForExistingProject(){
		mkdir('tmpproj');
		$proj = new BaseProj(null, [
			'projPath'=> __DIR__ . '/tmpproj',
		]);
		$proj->create(':tmp1', ['php']);
		$this->assertEquals(__DIR__ . '/tmpproj/tmp1', $proj->getPath('tmp1'));
	}
	public function testPathForNonExistingProject(){
		mkdir('tmpproj');
		$proj = new BaseProj(null, [
			'projPath'=> __DIR__ . '/tmpproj',
		]);
		$this->assertEquals(__DIR__ . '/tmpproj/tmp1', $proj->getPath('tmp1'));
	}
	public function testPathForInvalidProject(){
		mkdir('tmpproj');
		$proj = new BaseProj(null, [
			'projPath'=> __DIR__ . '/tmpproj',
		]);
		$this->expectException('Exception');
		$proj->getPath('../tmp1');
		$this->expectException('Exception');
		$proj->getPath('tmp1/../..');
		$this->expectException('Exception');
		$proj->getPath('/tmp1');
	}
}
