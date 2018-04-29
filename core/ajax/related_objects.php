<?php
/**
 * Dolibase
 * 
 * Open source framework for Dolibarr ERP/CRM
 *
 * Copyright (c) 2018 - 2019
 *
 *
 * @package     Dolibase
 * @author      AXeL
 * @copyright	Copyright (c) 2018 - 2019, AXeL-dev
 * @license
 * @link
 * 
 */

// Load Dolibarr environment
if (false === (@include '../../../main.inc.php')) { // From htdocs directory (dolibase is in htdocs)
	if (false === (@include '../../../../main.inc.php')) { // From module directory (module is in htdocs)
		require '../../../../../main.inc.php'; // From "custom" directory (module is in custom directory)
	}
}

// Load related objects lib
include_once '../lib/related_objects.php';

top_httphead();

//print '<!-- Ajax page called with url '.$_SERVER["PHP_SELF"].'?'.$_SERVER["QUERY_STRING"].' -->'."\n";

$action = GETPOST('action');

if ($action == 'search')
{
	$result = search_object(GETPOST('key'));

	print json_encode($result);
}