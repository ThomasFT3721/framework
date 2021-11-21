<?php

use Models\Framework\Project;

require_once __DIR__ . '/vendor/autoload.php';
define('ROOT_DIR', __DIR__);
/*var_dump(Project::__create([
    Project::PRO_ID => 478,
    Project::PRO_NAME => 'Project 0',
])->getProTeaId());*/
var_dump(Project::orWhere([[Project::PRO_ID, 456]])->get());
echo "<br>";
echo "<br>";
$project = Project::findById(456);
$project->getProjectUserList()[0]->getUser();
print_r($project);
echo "<br>";
//var_dump(Project::findByIdOrFail(123));
