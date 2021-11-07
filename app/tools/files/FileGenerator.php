<?php

namespace App\Tools\Files;

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
    public function addContentLine(string $row)
    {
        $this->content .= $row . "\n";

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

    public function generate(bool $return = false)
    {

        if ((!empty($this->basePath) || !empty($this->path)) && !is_dir(ROOT_DIR . $this->basePath . $this->path)) {
            if (mkdir(ROOT_DIR . $this->basePath . $this->path, 0777, true) === false) {
                throw new \Exception(ROOT_DIR . $this->basePath . $this->path);
            }
        }

        $result = file_put_contents(ROOT_DIR . $this->basePath . $this->path . "/" . $this->filename, $this->content);

        if ($result === false) {
            throw new \Exception("b");
        }

        if ($return) {
            return $this;
        }
    }

    /**
     * Get the value of basePath
     */ 
    public function getBasePath()
    {
        return $this->basePath;
    }
}
