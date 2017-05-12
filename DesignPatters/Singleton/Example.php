<?php
require_once "Preferences.php";

use Bigperson\DesignPatterns\Preferences;

$objA = Preferences::getInstance();

$objB = Preferences::getInstance();

var_dump($objA);

var_dump($objB);
