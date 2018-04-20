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

dolibase_include_once('/core/class/form_page.php');

/**
 * ListPage class
 */

class ListPage extends FormPage
{
	/**
	 * @var array Fields to show or not (used in openList function)
	 */
	protected $arrayfields = array();
	/**
	 * @var string Page context
	 */
	protected $contextpage = '';

	/**
	 * Constructor
	 * 
	 * @param     $page_title     HTML page title
	 * @param     $access_perm    Access permission
	 */
	public function __construct($page_title, $access_perm = '')
	{
		parent::__construct($page_title, $access_perm);
	}

	/**
	 * Load default actions
	 *
	 */
	protected function loadDefaultActions()
	{
		global $hookmanager, $dolibase_config;

		$hook_name = strtolower($dolibase_config['rights_class']).'list';
		$this->contextpage = str_replace('_', '', $hook_name);

		// Initialize technical object to manage hooks of thirdparties.
		$hookmanager->initHooks(array($this->contextpage));

		// Selection of new fields
		global $db, $conf, $user, $contextpage;
		$contextpage = $this->contextpage;
		include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

		// Purge search criteria
		if (GETPOST("button_removefilter_x") || GETPOST("button_removefilter")) // Both test are required to be compatible with all browsers
		{
		    $_POST = array();
		    $_GET  = array();
		}
	}

	/**
	 * Open list / print list head
	 *
	 * @param   $title               List title
	 * @param   $picture             List picture
	 * @param   $list_fields         List fields
	 * @param   $param               List parameters
	 * @param   $nbofshownrecords    Number of shown records
	 * @param   $nbtotalofrecords    Total number of records
	 * @param   $fieldstosearchall   Fields to search all
	 */
	public function openList($title, $picture = 'title_generic.png', $list_fields, $param, $nbofshownrecords, $nbtotalofrecords, $fieldstosearchall = array())
	{
		global $langs, $conf;

		// Get parameters
		$contextpage = GETPOST('contextpage','aZ') ? GETPOST('contextpage','aZ') : $this->contextpage;
		$sall = GETPOST('sall', 'alphanohtml');
		$page = GETPOST('page', 'int') ? GETPOST('page', 'int') : 0;
		$sortorder = GETPOST('sortorder', 'alpha');
		$sortfield = GETPOST('sortfield', 'alpha');
		$limit = GETPOST('limit') ? GETPOST('limit', 'int') : $conf->liste_limit;
		$optioncss = GETPOST('optioncss', 'alpha');

		// List form
		print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
		print '<input type="hidden" name="action" value="list">';
    	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
    	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';

    	// Add some list parameters
    	if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.= '&contextpage='.urlencode($contextpage);
    	if ($limit > 0 && $limit != $conf->liste_limit) $param.= '&limit='.urlencode($limit);
    	if ($optioncss != '') $param.= '&optioncss='.urlencode($optioncss);

    	// List title
        print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $nbofshownrecords, $nbtotalofrecords, $picture, 0, '', '', $limit);

        if ($sall)
        {
            foreach($fieldstosearchall as $key => $val) $fieldstosearchall[$key] = $langs->trans($val);
            print $langs->trans("FilterOnInto", $sall) . join(', ',$fieldstosearchall);
        }

        print '<div class="div-table-responsive">';
        print '<table class="tagtable liste">'."\n";

        // Generate $arrayfields
        $this->arrayfields = array();
        foreach ($list_fields as $field) {
        	$checked = (isset($field['checked']) ? $field['checked'] : 1);
        	$enabled = (isset($field['enabled']) ? verifCond($field['enabled']) : 1);
        	$this->arrayfields[$field['name']] = array('label' => $field['label'], 'checked' => $checked, 'enabled' => $enabled);
        }
        // This change content of $arrayfields
        $varpage = empty($contextpage) ? $_SERVER["PHP_SELF"] : $contextpage;
        $selectedfields = $this->form->multiSelectArrayWithCheckbox('selectedfields', $this->arrayfields, $varpage);

        // List fields
        print '<tr class="liste_titre">';
        foreach ($list_fields as $field) {
        	if (! empty($this->arrayfields[$field['name']]['checked'])) {
	        	$field_align = (isset($field['align']) ? 'align="'.$field['align'].'"' : '');
	        	$field_class = (isset($field['class']) ? $field['class'].' ' : '');
	        	print_liste_field_titre($langs->trans($field['label']), $_SERVER["PHP_SELF"], $field['name'], '', $param, $field_align, $sortfield, $sortorder, $field_class);
	        }
        }
        print_liste_field_titre($selectedfields, $_SERVER["PHP_SELF"], '', '', '', 'align="right"', $sortfield, $sortorder, 'maxwidthsearch ');
        print "</tr>\n";

        // List search fields
        if ($optioncss != 'print')
        {
        	print '<tr class="liste_titre liste_titre_filter">';
        	foreach ($list_fields as $field) {
        		if (! empty($this->arrayfields[$field['name']]['checked'])) {
	        		$field_align = (isset($field['align']) ? ' align="'.$field['align'].'"' : '');
	        		$field_class = (isset($field['class']) ? ' '.$field['class'] : '');
	        		$search_input = (isset($field['search_input']) ? $field['search_input'] : '');
	        		print '<td class="liste_titre'.$field_class.'"'.$field_align.'>'.$search_input.'</td>';
	        	}
	        }
	        // search buttons
	        print '<td class="liste_titre" align="right"><input type="image" class="liste_titre" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
            print '<input type="image" class="liste_titre" name="button_removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
            print "</td></tr>\n";
        }
	}

	/**
	 * Add a table column
	 *
	 * @param   $field_name   field name
	 * @param   $content      column content
	 * @param   $attr         column attributes
	 */
	public function addColumn($field_name, $content, $attr = '')
	{
		if (! empty($this->arrayfields[$field_name]['checked'])) {
			parent::addColumn($content, $attr);
		}
	}

	/**
	 * Return a text input
	 *
	 * @param   $name    input name
	 * @param   $value   input value
	 * @param   $size    input size
	 * @return  string   input HTML
	 */
	public function textInput($name, $value, $size = 8)
	{
		return '<input type="text" class="flat" name="'.$name.'" value="'.$value.'" size="'.$size.'">';
	}

	/**
	 * Return a date input
	 *
	 * @param   $name    input name
	 * @param   $value   input value
	 * @return  string   input HTML
	 */
	public function dateInput($name, $value)
	{
		return $this->form->select_date($value, $name, 0, 0, 1, '', 1, 0, 1);
	}

	/**
	 * Return a list input
	 *
	 * @param   $name      list name
	 * @param   $values    list values
	 * @param   $selected  list selected value
	 * @return  string     input HTML
	 */
	public function listInput($name, $values, $selected)
	{
		global $langs;

		// Translate list values
		foreach ($values as $key => $value) {
			$values[$key] = $langs->trans($value);
		}

		return $this->form->selectarray($name, $values, $selected, 1);
	}

	/**
	 * Close list
	 *
	 * @param   $close_form   close list form or not
	 */
	public function closeList($close_form = true)
	{
		print "</table></div>\n";

		if ($close_form) print "</form>\n";
	}

	/**
	 * Close table row
	 *
	 */
	public function closeRow()
	{
		print '<td></td>';

		parent::closeRow();
	}
}