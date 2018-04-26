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

dolibase_include_once('/core/class/page.php');
dolibase_include_once('/core/class/logs.php');
include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

/**
 * LogPage class
 */

class LogPage extends Page
{
	/**
	 * Constructor
	 * 
	 * @param     $page_title     HTML page title
	 * @param     $access_perm    Access permission
	 */
	public function __construct($page_title, $access_perm = '')
	{
		global $langs, $dolibase_config;

		// Load lang files
		$langs->load("log_page@".$dolibase_config['module_folder']);

		parent::__construct($page_title, $access_perm);
	}

	/**
	 * Show banner
	 *
	 * @param     $object         object
	 * @param     $list_link      link to list
	 * @param     $morehtmlleft   more html in the left
	 */
	public function showBanner($object, $list_link = '', $morehtmlleft = '')
	{
		global $langs;

		$morehtml = (empty($list_link) ? '' : '<a href="'.dol_buildpath($list_link, 1).'">'.$langs->trans("BackToList").'</a>');

		dol_banner_tab($object, 'ref', $morehtml, 1, 'ref', 'ref', '', '', 0, $morehtmlleft);

		print '<div class="underbanner clearboth"></div>';
	}

	/**
	 * Print logs
	 *
	 * @param     $object_id     object id
	 */
	public function printLogs($object_id)
	{
	    global $dolibase_config, $langs;

	    $log = new Logs();
	    $where = "(module_id = ".$dolibase_config['module_number'];
	    $where.= " || module_name = '".$dolibase_config['module_name']."'";
	    $where.= ") AND object_id = ".$object_id;

	    // Fetch logs
	    if ($log->fetchAll(0, 0, 't.datec', 'DESC', '', '', $where))
	    {
		    $deltadateforserver = getServerTimeZoneInt('now');
		    $deltadateforclient = ((int) $_SESSION['dol_tz'] + (int) $_SESSION['dol_dst']);
		    $deltadateforuser   = round($deltadateforclient-$deltadateforserver);

		    // Show logs
		    print '<table class="border" width="100%">';

		    foreach ($log->lines as $line)
		    {
		    	// Action
			    print '<tr><td class="titlefield">';
			    print $langs->trans("LogAction");
			    print '</td><td>';
			    print $langs->trans($line->action);
			    print '</td></tr>';

			    // Date
			    $datec = $log->db->jdate($line->datec);
			    print '<tr><td class="titlefield">';
			    print $langs->trans("LogDate");
			    print '</td><td>';
			    print dol_print_date($datec, 'dayhour');
			    if ($deltadateforuser) {
			    	print ' '.$langs->trans("CurrentHour").' &nbsp; / &nbsp; '.dol_print_date($datec+($deltadateforuser*3600),"dayhour").' &nbsp;'.$langs->trans("ClientHour");
			    }
			    print '</td></tr>';

			    // User
			    $userstatic = new User($log->db);
			    $userstatic->fetch($line->fk_user);
			    print '<tr><td class="titlefield">';
			    print $langs->trans("MadeBy");
			    print '</td><td>';
			    print $userstatic->getNomUrl(1, '', 0, 0, 0);
			    print '</td></tr>';
			}

			print '</table>';
	    }
	    else
	    {
	    	print '<br>'.$langs->trans('NoLogsAvailable');
	    }
	}
}