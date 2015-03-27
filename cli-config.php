<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
$app = include("core/app.php");

return ConsoleRunner::createHelperSet($app['em']);