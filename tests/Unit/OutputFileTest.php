<?php namespace thiagoalessio\TesseractOCR\Tests\Unit;

use thiagoalessio\TesseractOCR\Tests\Common\TestCase;
use thiagoalessio\TesseractOCR\Command;

class OutputFileTest extends TestCase
{
	public function beforeEach()
	{
		$this->cmd = new Command('image', '/path/to/output/file');
	}

	public function testTxt()
	{
		foreach (['digits', 'quiet', 'txt', 'anything', 'else'] as $ext) {
			$this->cmd->configFile = $ext;
			$expected = "/path/to/output/file.txt";
			$this->assertEquals($expected, $this->cmd->getOutputFile());
		}
	}

	public function testHocr()
	{
		$this->cmd->configFile = 'hocr';
		$expected = '/path/to/output/file.hocr';
		$this->assertEquals($expected, $this->cmd->getOutputFile());
	}
}
