<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL^ E_WARNING);
  
// set the error handler
set_error_handler(function($code, $message, $file, $line) {
    throw new ErrorException($message, 500, $code, $file, $line);
}, E_ALL);