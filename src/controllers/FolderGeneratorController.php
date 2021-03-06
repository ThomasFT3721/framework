<?php

namespace Zaacom\controllers;



use Zaacom\attributes\Controller;
use Zaacom\attributes\Route;

/**
 * @author Thomas FONTAINE--TUFFERY
 */
#[Controller]
class FolderGeneratorController extends BaseController
{

	#[Route(path: 'zf-admin/Folders/generate')]
	public function generate()
	{
		$directories = [
			"/views",
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
