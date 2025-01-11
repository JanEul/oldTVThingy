<?php
define("ROOT",                      $_SERVER["DOCUMENT_ROOT"]);
define("FFPROBE",                   "C:/ffmpeg/bin/ffprobe.exe");
define("SUBTRACT_YEARS",            23);
define("ONLY_TIME_SPECIFIC_SHOWS",  false);

define('TIMEZONE', 'Europe/Berlin');
date_default_timezone_set(TIMEZONE);

require "functions.php";

spl_autoload_register(function ($class_name) { include ROOT."/includes/classes/$class_name.class.php"; });

$_DB = new dB();