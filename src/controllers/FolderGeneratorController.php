<?php

namespace Zaacom\controllers;



/**
 * @author Thomas FONTAINE--TUFFERY
 */
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
			if (!is_dir(ROOT_DIR . $directory)) {
				mkdir(ROOT_DIR . $directory, recursive: true);
			}
		}
		return "all folders be generated";
	}
}
