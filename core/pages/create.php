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
dolibase_include_once('/core/class/field.php');

/**
 * CreatePage class
 */

class CreatePage extends FormPage
{
	/**
	 * @var array Fields to check on validation
	 */
	public $fields = array();


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
	 * Check page fields
	 *
	 * @return     boolean     true or false
	 */
	public function checkFields()
	{
		$error = 0;

		foreach($this->fields as $field) {
			$error += $this->checkField($field->name, $field->trans, $field->validation_rules, true);
		}

		return $error > 0 ? false : true;
	}

	/**
	 * Check specified field
	 *
	 * @param      $field_name                 Field name
	 * @param      $field_trans                Field translation
	 * @param      $field_validation_rules     Field validatin rules
	 * @param      $return_err_number          return errors number or boolean value
	 * @return     boolean|int                 true/false | errors number
	 */
	public function checkField($field_name, $field_trans = '', $field_validation_rules = '', $return_err_number = false)
	{
		global $langs;

		$langs->load("errors");

		$error = 0;

		$field_value = GETPOST($field_name);

		if (empty($field_trans) || empty($field_validation_rules)) {
			$field = $this->getField($field_name);

			if (empty($field)) {
				return 1;
			}
			else {
				$field_trans = $field->trans;
				$field_validation_rules = $field->validation_rules;
			}
		}

		$validation_rules = explode('|', $field_validation_rules);

		// required
		$is_required = in_array('required', $validation_rules);
		if ($is_required && $field_value == '') {
            setEventMessage($langs->transnoentities("ErrorFieldRequired", $langs->transnoentities($field_trans)), 'errors');
            $error++;
        }

        // numeric (escape if empty)
        else if (in_array('numeric', $validation_rules) && $field_value != '' && ! is_numeric($field_value)) {
            setEventMessage($langs->transnoentities("ErrorFieldFormat", $langs->transnoentities($field_trans)), 'errors');
            $error++;
		}

		// greaterThanZero
        else if (in_array('greaterThanZero', $validation_rules) && $field_value != '' && (! is_numeric($field_value) || $field_value <= 0)) {
        	$error_msg = ($is_required ? "ErrorFieldRequired" : "ErrorFieldFormat");
            setEventMessage($langs->transnoentities($error_msg, $langs->transnoentities($field_trans)), 'errors');
            $error++;
		}

		if ($return_err_number) {
			return $error;
		}
		else {
			return $error > 0 ? false : true;
		}
	}

	/**
	 * Return specified field if found
	 *
	 * @return     Field|empty     field object or empty value
	 */
	protected function getField($field_name)
	{
		foreach($this->fields as $field) {
			if ($field->name == $field_name) {
				return $field;
			}
		}

		return '';
	}

	/**
	 * add a table field
	 *
	 * @param     $field_name     field name
	 * @param     $field_content  field content
	 * @param     $is_required    is field required or not
	 * @param     $field_summary  field summary
	 * @param     $more_attr      more attributes to add
	 */
	public function addField($field_name, $field_content, $is_required = false, $field_summary = '', $more_attr = '')
	{
		global $langs;

		print '<tr>';
		print '<td width="25%"'.($is_required ? ' class="fieldrequired"' : '').$more_attr.'>' . $langs->trans($field_name) . '</td>';
		print '<td colspan="2">' . $field_content;
		if (! empty($field_summary)) print $this->form->textwithpicto(' ', $langs->trans($field_summary));
		print '</td>';
		print '</tr>';
	}

	/**
	 * add a table field with a text input
	 *
	 * @param     $field_name        field name
	 * @param     $input_name        input name
	 * @param     $input_value       input value
	 * @param     $is_required       is field required or not
	 * @param     $field_summary     field summary
	 * @param     $input_size        input size
	 */
	public function addTextField($field_name, $input_name, $input_value = '', $is_required = false, $field_summary = '', $input_size = 20)
	{
		$field_content = $this->form->textInput($input_name, $input_value, $input_size);

		$this->addField($field_name, $field_content, $is_required, $field_summary);
	}

	/**
	 * add a table field with a text area
	 *
	 * @param     $field_name            field name
	 * @param     $text_area_name        text area name
	 * @param     $text_area_value       text area value
	 * @param     $is_required           is field required or not
	 * @param     $field_summary         field summary
	 * @param     $toolbarname           Editor toolbar name, values: 'Full', dolibarr_details', 'dolibarr_notes', 'dolibarr_mailings', 'dolibarr_readonly'
	 * @param     $height                text area height
	 * @param     $valign                field vertical align
	 */
	public function addTextAreaField($field_name, $text_area_name, $text_area_value = '', $is_required = false, $field_summary = '', $toolbarname = 'dolibarr_details', $height = 100, $valign = 'top')
	{
	    $field_content = $this->form->textArea($text_area_name, $text_area_value, $toolbarname, $height);

		$more_attr = ' valign="'.$valign.'"';
		$this->addField($field_name, $field_content, $is_required, $field_summary, $more_attr);
	}

	/**
	 * add a table field with a number input
	 *
	 * @param     $field_name        field name
	 * @param     $input_name        input name
	 * @param     $input_value       input value
	 * @param     $is_required       is field required or not
	 * @param     $field_summary     field summary
	 * @param     $min               input minimum number
	 * @param     $max               input maximum number
	 */
	public function addNumberField($field_name, $input_name, $input_value = '', $is_required = false, $field_summary = '', $min = 0, $max = 100)
	{
		$input_value = (empty($input_value) ? $min : $input_value);

		$field_content = $this->form->numberInput($input_name, $input_value, $min, $max);

		$this->addField($field_name, $field_content, $is_required, $field_summary);
	}

	/**
	 * add a table field with a date picker
	 *
	 * @param     $field_name        field name
	 * @param     $input_name        input name
	 * @param     $input_value       input value
	 * @param     $is_required       is field required or not
	 * @param     $field_summary     field summary
	 */
	public function addDateField($field_name, $input_name, $input_value = '', $is_required = false, $field_summary = '')
	{
		$field_content = $this->form->dateInput($input_name, $input_value);

		$this->addField($field_name, $field_content, $is_required, $field_summary);
	}

	/**
	 * add a table field with a list
	 *
	 * @param     $field_name       field name
	 * @param     $list_name        list name
	 * @param     $list_choices     list choices, e.: array('choice_1' => 'Choice 1', 'choice_2' => 'Choice 2')
	 * @param     $selected_choice  selected choice
	 * @param     $is_required      is field required or not
	 * @param     $field_summary    field summary
	 */
	public function addListField($field_name, $list_name, $list_choices, $selected_choice = '', $is_required = false, $field_summary = '')
	{
		$field_content = $this->form->listInput($list_name, $list_choices, $selected_choice);

		$this->addField($field_name, $field_content, $is_required, $field_summary);
	}

	/**
	 * add a table field with a radio input(s)
	 *
	 * @param     $field_name       field name
	 * @param     $radio_name       radio inputs name
	 * @param     $radio_list       list of radio inputs, e.: array('radio_1' => 'Radio 1', 'radio_2' => 'Radio 2')
	 * @param     $selected         selected radio input
	 * @param     $is_required      is field required or not
	 * @param     $field_summary    field summary
	 * @param     $valign           field vertical align
	 */
	public function addRadioListField($field_name, $radio_name, $radio_list, $selected = '', $is_required = false, $field_summary = '', $valign = 'middle')
	{
		$field_content = $this->form->radioList($radio_name, $radio_list, $selected);

		$more_attr = ' valign="'.$valign.'"';
		$this->addField($field_name, $field_content, $is_required, $field_summary, $more_attr);
	}

	/**
	 * add a table field with a color picker
	 *
	 * @param     $field_name        field name
	 * @param     $input_name        input name
	 * @param     $input_value       input value
	 * @param     $is_required       is field required or not
	 * @param     $field_summary     field summary
	 */
	public function addColorField($field_name, $input_name, $input_value = '', $is_required = false, $field_summary = '')
	{
		$field_content = $this->form->colorInput($input_name, $input_value);
		
		$this->addField($field_name, $field_content, $is_required, $field_summary);
	}

	/**
	 * generate form buttons
	 *
	 */
	public function generateFormButtons()
	{
		global $langs;

		print '<div class="center">';
	    print '<input type="submit" class="button" value="' . $langs->trans("Create") . '">';
	    print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	    print '<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
	    print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	    print '<input type="reset" class="button" value="' . $langs->trans("Reset") . '">';
	    print '</div>';
	}
}