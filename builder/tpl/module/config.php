<?php

return array(
	/**
	 * Module configuration
	 */
	'module' => array(
		'name'                      => '${name}',
		'desc'                      => '${description}',
		'version'                   => '${version}',
		'number'                    => '${number}',
		'family'                    => '${family}',
		'position'                  => ${position},
		'rights_class'              => '${rights_class}',
		'url'                       => '${url}',
		'folder'                    => '${folder}',
		'picture'                   => '${picture}',
		'dirs'                      => array(),
		'dolibarr_min'              => array(3, 8),
		'php_min'                   => array(5, 3),
		'depends'                   => array(),
		'required_by'               => array(),
		'conflit_with'              => array(),
		'check_updates'             => ${check_updates},
		'enable_logs'               => ${enable_logs},
		'enable_triggers'           => ${enable_triggers},
		'enable_for_external_users' => ${enable_for_external_users}
	),
	/**
	 * Author informations
	 */
	'author' => array(
		'name'          => '${author_name}',
		'url'           => '${author_url}',
		'email'         => '${author_email}',
		'dolistore_url' => '${author_dolistore_url}'
	),
	/**
	 * Numbering model (optional)
	 */
	'numbering_model' => array(
		'table'  => '${num_model_table}',
		'field'  => '${num_model_field}',
		'prefix' => '${num_model_prefix}'
	),
	/**
	 * Other (default)
	 */
	'other' => array(
		'setup_page'     => 'setup.php',
		'about_page'     => 'about.php',
		'lang_files'     => array('${folder}@${folder}'),
		'menu_lang_file' => '${folder}@${folder}',
		'top_menu_name'  => str_replace('_', '', '${folder}')
	)
);
