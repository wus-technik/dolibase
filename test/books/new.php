<?php

// Load Dolibarr environment (mandatory)
if (false === (@include '../../main.inc.php')) {  // From htdocs directory
	require '../../../main.inc.php'; // From "custom" directory
}

// Load Dolibase config file for this module (mandatory)
dol_include_once('/books/config.php');
// Load Dolibase Page class
dolibase_include_once('/core/pages/create.php');
// Load Book class
dol_include_once('/books/class/book.class.php');
// Load Dolibase Dictionary Class
dolibase_include_once('/core/class/dict.php');

// Create Page using Dolibase
$page = new CreatePage("New book", '$user->rights->books->create');

// Set fields
$page->fields[] = new Field('name', 'Name', 'required');
$page->fields[] = new Field('type', 'Type', 'required');
$page->fields[] = new Field('qty', 'Qty', 'numeric|required');
$page->fields[] = new Field('price', 'Price', 'numeric');

// Set actions
$action = GETPOST('action', 'alpha');

if ($action == 'create' && $page->checkFields())
{
	$book = new Book();

	$ref = $book->getNextNumRef();

	if (! empty($ref))
	{
		global $user;
		
		$data = array('ref' => $ref,
					  'name' => str_escape(GETPOST('name')),
					  'desc' => str_escape(GETPOST('desc')),
					  'type' => GETPOST('type'),
					  'qty' => GETPOST('qty'),
					  'price' => empty_to_null(GETPOST('price')),
					  'publication_date' => GETPOSTDATE('publication_date', true),
					  'creation_date' => dolibase_now(true),
					  'created_by' => $user->id
					);

		$id = $book->create($data);

		if ($id > 0) {
	        // Creation OK
	        header('Location: card.php?id=' . $id);
	        exit();
	    }
	}
}

$page->begin();

// Add Sub Title
$page->AddSubTitle("New book", "object_books.png@books");

// Create form
$page->openForm();

$page->openTable(array(), 'class="border" width="100%"', true, 'Use this form to add a new book<br><br>');

$page->addTextField('Name', 'name', GETPOST('name'), true, 'This is a field summary');

$page->addTextAreaField('Description', 'desc', GETPOST('desc'));

$list = Dictionary::get_active('books_dict');

$page->addRadioListField('Type', 'type', $list, GETPOST('type'), true);

$page->addNumberField('Qty', 'qty', GETPOST('qty'), true);

$page->addTextField('Price', 'price', GETPOST('price'));

$page->addDateField('Publication Date', 'publication_date', GETPOSTDATE('publication_date'));

$page->closeTable(true);

$page->generateFormButtons();

$page->closeForm();

$page->end();