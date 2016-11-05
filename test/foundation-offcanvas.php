<?php

// AUTOLAD
require_once '../src/loader.php';

// CLASSES SHORTCUTS
use \k1lib\html\foundation\app_layout as DOM;
use \k1lib\html\script as script;

// ENABLE THE HTML LOG
\k1lib\html\html::set_use_log(FALSE);

// START THE <html>, <body>, <head> tag inside the static class DOM
DOM::start();
DOM::set_title(1, "APP TITLE");
DOM::set_title(2, " :: ");
DOM::set_title(3, "HOME");
DOM::off_canvas()->left()->set_class('reveal-for-large', TRUE)->append_p("Hola mundo");

// Code shortcuts
$head = DOM::html()->head();
$body = DOM::html()->body();
$left_menu = DOM::off_canvas()->left_menu();

$left_menu->add_menu_item("#", 'Hola Mundo');
$left_menu->add_sub_menu("#", 'Sub menu');

/**
 * TABLE WITH DATA
 */
$data = array(
    0 => array(0 => 'Name', 1 => 'Last name', 'Full Name', 'Pic'),
    1 => array('Alejandro', 'Trujillo', "{{field:0}} {{field:1}}", 'https://66.media.tumblr.com/avatar_32dc0cfad91f_128.png'),
    2 => array('Camilo', 'Lopez', "{{field:0}} {{field:1}}", 'https://cdn1.iconfinder.com/data/icons/halloween-6/96/Zombie-128.png'),
);

$img = new \k1lib\html\img();
$img->set_attrib("alt", "Avatar of {{field:0}}");

$table_with_data = new \k1lib\html\foundation\table_from_data('foundation-table', 'table-1');
$table_with_data->set_data($data);
$table_with_data->insert_tag_on_field($img, [3], 'src');
$table_with_data->append_to($body->content());

/**
 * CALLOUT
 */
$callout = new \k1lib\html\foundation\callout("2016 Klan1 Network", NULL, FALSE);
$callout->set_class("secondary", TRUE);
$callout->set_style("border: none;", TRUE);
$callout->append_to($body->footer());

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
