<?php

use Zaacom\controllers\FolderGeneratorController;
use Zaacom\routing\Route;

Route::get(["/Admin/Folders/generate"], [FolderGeneratorController::class, "generate"], ["name" => "admin_generate_folders"]);
Route::get(["/Admin/Objects/index"], [FolderGeneratorController::class, "index"], ["name" => "object_generator_index"]);
Route::get(["/Admin/Objects/generate"], [FolderGeneratorController::class, "generate"], ["name" => "object_generator_generate"]);
