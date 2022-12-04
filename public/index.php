<?php
chdir(__DIR__ . '/..');

require_once 'vendor/autoload.php';

$app = include 'boostrap.php';
$app->run();
