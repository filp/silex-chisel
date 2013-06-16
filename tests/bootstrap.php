<?php
/**
 * silex-chisel
 * 
 * @author  Filipe Dobreira <https://github.com/filp>
 * @license MIT
 * 
 * Unit & Functional test bootstrapper
 */

error_reporting(E_ALL | E_STRICT);
$loader = require_once __DIR__ . "/../vendor/autoload.php";
$loader->add("", __DIR__);