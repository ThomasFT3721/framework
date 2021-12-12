<?php

namespace Zaacom\controllers;


class FolderGeneratorController extends BaseController
{

    public function generate()
    {
		$directories = [
			"/views",
			"/routes",
			"/assets",
			"/assets/css",
			"/assets/scss",
			"/assets/js",
			"/assets/fonts",
			"/assets/images",
			"/controllers",
			"/models",
		];
		foreach ($directories as $directory) {
			mkdir(ROOT_DIR.$directory, recursive: true);
		}
		return "all folders be generated";
    }
}
