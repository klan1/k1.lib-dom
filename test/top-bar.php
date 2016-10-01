<?php
// AUTOLAD
require_once '../src/loader.php';

// CLASSES SHORTCUTS
use \k1lib\html\DOM as DOM;
use \k1lib\html\script as script;

// ENABLE THE HTML LOG
\k1lib\html\html::set_use_log(TRUE);

// START THE <html>, <body>, <head> tag inside the static class DOM
DOM::start();

// Code shortcuts
$head = DOM::html()->head();
$body = DOM::html()->body();

// Initialize the <section id="k1app-header"></section>,<section id="k1app-content"></section>,<section id="k1app-footer"></section>
$body->init_sections();

// Code shortcut to <section id="k1app-header"></section>
$body_header = $body->header();

$top_bar = new \k1lib\html\foundation\top_bar($body_header);
$top_bar->append_to($body_header);

$top_bar->set_title(1, "APP TITLE");
$top_bar->set_title(2, " :: ");
$top_bar->set_title(3, "HOME");

$li = $top_bar->add_menu_item("#", "Sub menu 1");
$top_bar->add_menu_item("#", "Item 2");
$top_bar->add_menu_item("#", "Item 3");

$sub_menu = $top_bar->add_sub_menu($li);
$top_bar->add_menu_item("#", "Level 1", $sub_menu);
$top_bar->add_menu_item("#", "Level 2", $sub_menu);
$top_bar->add_menu_item("#", "Level 2", $sub_menu);

$top_bar->add_button("#", "Ingresar");
$top_bar->add_button("#", "Salir", "alert");

// HTML HEAD
$head->set_title("FOUNDATION TEST");
$head->link_css("vendor/zurb/foundation/dist/foundation.min.css");
$head->link_css("bower_components/foundation-icon-fonts/foundation-icons.css");

// HTML BODY
$body->append_child_tail(new script("bower_components/jquery/dist/jquery.min.js"));
$body->append_child_tail(new script("vendor/zurb/foundation/dist/foundation.min.js"));
$body->append_child_tail(new script("bower_components/what-input/what-input.min.js"));
$body->append_child_tail((new script())->set_value("$(document).foundation();"));

// Execute DOM::html()->generate() with all its child objects
echo DOM::generate();

// HTML LOG OUTPUT
echo "<!--\n\nHTML LOG\n\n";
echo \k1lib\html\tag_log::get_log();
echo "->";
