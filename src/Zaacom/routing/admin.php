<?php

use Zaacom\controllers\ClassGeneratorController;
use Zaacom\controllers\FolderGeneratorController;
use Zaacom\routing\Route;

Route::get(["/Admin/Folders/generate"], [FolderGeneratorController::class, "generate"], ["name" => "admin_generate_folders"]);
Route::get(["/Admin/Objects/index"], [ClassGeneratorController::class, "index"], ["name" => "object_generator_index"]);
Route::get(["/Admin/Objects/generate"], [ClassGeneratorController::class, "generate"], ["name" => "object_generator_generate"]);
