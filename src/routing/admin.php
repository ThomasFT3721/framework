<?php

use Zaacom\controllers\ClassGeneratorController;
use Zaacom\controllers\FolderGeneratorController;
use Zaacom\routing\Route;

Route::get("/zf-admin/Folders/generate", [FolderGeneratorController::class, "generate"])->name("admin_generate_folders");
Route::get("/zf-admin/Objects", [ClassGeneratorController::class, "index"])->name("object_generator_index");
Route::get("/zf-admin/Objects/generate", [ClassGeneratorController::class, "generate"])->name("object_generator_generate");
