<?php
include('model.php');

$project = new Project();
$sql = 'SELECT * FROM event';
$data = $project->custosql($sql);

?>