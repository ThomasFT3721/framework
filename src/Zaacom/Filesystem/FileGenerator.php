<?php

namespace Zaacom\Filesystem;

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
    public function setBasePath(string $basePath)
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * Add a line to the file content
     * 
     * @param string $row
     * 
     * @return  self
     */
    public function addContentLine(string $row, int $indent = 0)
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
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Generate file with content
     * 
     * @return bool true if file is created successfully, else false
     */
    public function generate()
    {

        if ((!empty($this->basePath) || !empty($this->path)) && !is_dir(\Zaacom\Foundation\App::$path . $this->basePath . $this->path)) {
            if (mkdir(\Zaacom\Foundation\App::$path . $this->basePath . $this->path, 0777, true) === false) {
                throw new \Exception(\Zaacom\Foundation\App::$path . $this->basePath . $this->path);
            }
        }

        $result = file_put_contents(\Zaacom\Foundation\App::$path . $this->basePath . $this->path . "/" . $this->filename, $this->content);

        if ($result === false) {
            throw new \Exception("b");
        }

        return $result !== false;
    }

    private function getIndent(int $indent)
    {
        $res = "";
        for ($i = 0; $i < $indent; $i++) {
            $res .= "    ";
        }
        return $res;
    }

    /**
     * Get the value of basePath
     */
    public function getBasePath()
    {
        return $this->basePath;
    }
}
