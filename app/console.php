<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Contrib\Bundle\TumblrBundle\Command\Tumblr2MarkdownCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new Tumblr2MarkdownCommand());
$application->run();
