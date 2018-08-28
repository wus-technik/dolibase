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

/**
 * Version
 *
 * Possible values: a.b.c-alpha, a.b.c-beta, a.b.c-rcX or a.b.c
 */
if (! defined('DOLIBASE_VERSION')) define('DOLIBASE_VERSION', '2.1.0');

/**
 * Link
 */
if (! defined('DOLIBASE_LINK')) define('DOLIBASE_LINK', 'https://github.com/AXeL-dev/dolibase');

/**
 * Use AJAX on confirmation
 *
 * Values should be 1/0 instead of true/false because the function where this is used takes an integer value instead of boolean as parameter
 */
if (! defined('DOLIBASE_USE_AJAX_ON_CONFIRM')) define('DOLIBASE_USE_AJAX_ON_CONFIRM', 1);

/**
 * Enable Dolibase logs
 *
 * Logs are currently saved only when creating/modifying/deleting an object
 */
if (! defined('DOLIBASE_ENABLE_LOGS')) define('DOLIBASE_ENABLE_LOGS', true);
