<?php

// Load Dolibase
require_once '../autoload.php';

// Load Dolibase Page class
dolibase_include_once('/core/pages/extrafields.php');

// Create ExtraFields Page using Dolibase
$page = new ExtraFieldsPage('books');

$page->begin();

$page->printExtraFields();

$page->end();
