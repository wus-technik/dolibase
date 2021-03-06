<?php

// Load Dolibase
require_once 'autoload.php';

// Load Dolibase Page class
dolibase_include_once('/core/pages/document.php');

// Load object class
dol_include_once('/books/class/book.class.php');

// Create Page using Dolibase
$page = new DocumentPage("Book Documents", '$user->rights->books->read');

// Get parameters
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');

// Init object
$book = new Book();

if (($id > 0 || ! empty($ref)) && $book->fetch($id, $ref))
{
	// Add tabs
	$page->addTab("Card", "/books/card.php?id=".$id.'&ref='.$ref);
	$page->addTab(DocumentPage::getTabTitle($book), "/books/document.php?id=".$id.'&ref='.$ref, true);
	$page->addTab("Log", "/books/log.php?id=".$id.'&ref='.$ref);

	$page->begin($book);

	// Banner
	$page->showBanner($book, '/books/list.php', $book->getImage('books.png'));

	// Documents
	$page->printDocuments($book);
}
else $page->begin()->notFound();

$page->end();
