<?php

namespace App\Tools\Files;

class CacheFileGenerator extends FileGenerator
{
    public function __construct(string $filename, string $path, string $content = "<?php\n")
    {
        parent::__construct($filename, $path, $content);
        $this->setBasePath("/app/caches");
    }
}
