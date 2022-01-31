<?php

namespace Zaacom\filesystem;

use Exception;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
class FileGenerator
{

	private string $filename = "cache.php";
	private string $basePath = "";
	private string $path = "";
	private string $content;

	public function __construct(string $filename, string $path = "", string $content = "<?php\n")
	{
		$this->filename = $filename;
		$this->path = $path;
		$this->content = $content;
	}

	/**
	 * Set the value of basePath default "/app/caches"
	 *
	 * @param string $basePath Based from ROOT_DIR
	 *
	 * @return  self
	 */
	public function setBasePath(string $basePath): self
	{
		$this->basePath = $basePath;

		return $this;
	}

	/**
	 * Add a line to the file content
	 *
	 * @param string $row
	 * @param int    $indent
	 *
	 * @return  self
	 */
	public function addContentLine(string $row, int $indent = 0): self
	{
		$this->content .= $this->getIndent($indent) . $row . "\n";

		return $this;
	}
	/**
	 * Add a blank line to the file content
	 *
	 * @return  self
	 */
	public function addBlankLine(): self
	{
		$this->content .= "\n";

		return $this;
	}

	/**
	 * Get the value of the file content
	 *
	 * @return string
	 */
	public function getContent(): string
	{
		return $this->content;
	}

	/**
	 * Set the value of the file content
	 *
	 * @param string $content
	 *
	 * @return  self
	 */
	public function setContent(string $content): self
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * Generate file with content
	 *
	 * @return bool true if file is created successfully, else false
	 * @throws Exception
	 */
	public function generate(): bool
	{

		if (!defined("ROOT_DIR")) {
			define("ROOT_DIR", __DIR__ . "/../../../../../thomasft");
		}
		if ((!empty($this->basePath) || !empty($this->path)) && !is_dir(ROOT_DIR . $this->basePath . $this->path)) {
			if (mkdir(ROOT_DIR . $this->basePath . $this->path, 0777, true) === false) {
				throw new Exception(ROOT_DIR . $this->basePath . $this->path);
			}
		}

		$result = file_put_contents(ROOT_DIR . $this->basePath . $this->path . "/" . $this->filename, $this->content);

		if ($result === false) {
			throw new Exception("b");
		}

		return $result != false;
	}

	private function getIndent(int $indent): string
	{
		return str_repeat("    ", $indent);
	}

	/**
	 * Get the value of basePath
	 */
	public function getBasePath(): string
	{
		return $this->basePath;
	}

	public function fileExist(): bool
	{
		return file_exists(ROOT_DIR . $this->basePath . $this->path . "/" . $this->filename);
	}
}
