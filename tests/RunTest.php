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
	public function testRunBinInProjFolder(){
		mkdir('tmpproj');
		$proj = new BaseProj(null, [
			'projPath'=> __DIR__ . '/tmpproj',
			'templatePath'=> __DIR__ . '/data/templates',
		]);
		$proj->create(':tmp1', ['bin']);
		$this->assertEquals('in foo', $proj->runBin('tmp1', 'foo'));
		$this->assertEquals('in foo', $proj->runBin('tmp1', [
			'command'=> 'foo',
		]));
	}
	public function testRunConsoleInProjFolder(){
		mkdir('tmpproj');
		$proj = new BaseProj(null, [
			'projPath'=> __DIR__ . '/tmpproj',
			'templatePath'=> __DIR__ . '/data/templates',
		]);
		$proj->create(':tmp1', ['bin']);
		$this->assertEquals('in console', $proj->runBin('tmp1', 'console'));
		$this->assertEquals('in console', $proj->runBin('tmp1', [
			'command'=> 'console',
		]));
	}
	public function testRunGitInProjFolder(){
		mkdir('tmpproj');
		$proj = new BaseProj(null, [
			'projPath'=> __DIR__ . '/tmpproj',
		]);
		$proj->create(':tmp1', ['git']);
		$this->assertEquals('Initialized empty Git repository in ' . __DIR__ . '/tmpproj/tmp1/.git/', $proj->runGit('tmp1', 'init'));
		$this->assertEquals(trim(file_get_contents(__DIR__ . '/data/git-status.txt')), $proj->runGit('tmp1', [
			'command'=> '-c color.status=never status -sb',
		]));
	}
}
