<?php namespace thiagoalessio\TesseractOCR;

class Command
{
	public $executable = 'tesseract';
	public $useFileAsInput = true;
	public $useFileAsOutput = true;
	public $options = [];
	public $configFile;
	public $tempDir;
	public $threadLimit;
	public $image;
	public $imageSize;
	private $outputFile;

	public function __construct($image=null, $outputFile=null)
	{
		$this->image = $image;
		$this->outputFile = $outputFile;
	}

	public function build() { return "$this"; }

	public function __toString()
	{
		$cmd = [];
		if ($this->threadLimit) $cmd[] = "OMP_THREAD_LIMIT={$this->threadLimit}";
		$cmd[] = self::escape($this->executable);
		$cmd[] = $this->useFileAsInput ? self::escape($this->image) : "-";
		$cmd[] = $this->useFileAsOutput ? self::escape($this->getOutputFile()) : "-";

		$version = $this->getTesseractVersion();

		foreach ($this->options as $option) {
			$cmd[] = is_callable($option) ? $option($version) : "$option";
		}
		if ($this->configFile) $cmd[] = $this->configFile;

		return join(' ', $cmd);
	}

	public function getOutputFile()
	{
		if (!$this->outputFile) $this->outputFile = tempnam($this->getTempDir(), 'ocr');
		return $this->outputFile;
	}

	public function getOutputFileWithExt()
	{
		$hasCustomExt = ['hocr', 'tsv', 'pdf'];
		$ext = in_array($this->configFile, $hasCustomExt) ? $this->configFile : 'txt';
		return "{$this->getOutputFile()}.{$ext}";
	}

	public function getTempDir()
	{
		return $this->tempDir ?: sys_get_temp_dir();
	}

	public function getTesseractVersion()
	{
		exec(self::escape($this->executable).' --version 2>&1', $output);
		return explode(' ', $output[0])[1];
	}

	public function getAvailableLanguages()
	{
		exec(self::escape($this->executable) . ' --list-langs 2>&1', $output);
		array_shift($output);
		sort($output);
		return $output;
	}

	public static function escape($str)
	{
		return '"'.str_replace('$', '\$', addcslashes($str, '\\"')).'"';
	}
}
