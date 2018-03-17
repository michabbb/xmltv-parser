<?php
require 'vendor/autoload.php';

$Parser = new \macropage\xmltv\parser\parser();
$Parser->setFile($argv[1]);
$Parser->parse();
print_r($Parser->getChannels());