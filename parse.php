<?php
require 'vendor/autoload.php';

$Parser = new \macropage\xmltv\parser\parser();
$Parser->setFile($argv[1]);
$Parser->setOffset('P0Y0DT1H0M0S');
$Parser->setChannelfilter('prosiebenmaxx.de'); //optional
try {
	$Parser->parse();
} catch (Exception $e) {
	throw new \RuntimeException($e);
}
/** @noinspection ForgottenDebugOutputInspection */
print_r($Parser->getEpgdata());