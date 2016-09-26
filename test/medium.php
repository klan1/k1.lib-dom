<?php

namespace k1app\test;

require_once '../src/loader.php';

use \k1lib\html\DOM as DOM;

\k1lib\html\html::set_use_log(TRUE);

DOM::start();

$head = DOM::html()->head();
$body = DOM::html()->body();

$inline_span1 = (new \k1lib\html\span("use-me", "inline-span-1"))->set_value("Inline tag 1");
$inline_span2 = (new \k1lib\html\span("use-me", "inline-span-2"))->set_value("Inline tag 2");

$body->append_div('class-a', 'id-1')->append_p("Hello world", 'hello-class', 'hello-id');

$body->append_div('class-a', 'id-2')->append_p("Inline tags: $inline_span1 AND $inline_span2");

$body->append_div("class-3", 'id-3')->set_value("This is the VALUE of this DIV element");

$body->get_element_by_id("inline-span-1")->append_a("#null", 'New link here');

$p1 = $body->get_element_by_id("id-2")->append_p("New P element")->append_span('class-a', 'span-id-1');

$p2 = $body->get_element_by_id("id-2")->append_p("Another P element");

$span_by_id = $body->get_element_by_id("span-id-1")->append_a("#link", "Another link");

$tag_elements = $body->get_elements_by_tag("span");
$i = 1;
foreach ($tag_elements as $tag_element) {
//    $tag_element->append_p("With TAG search object insert! {$i}");
    $tag_element->append_a("#span-{$i}", 'Span found');
    $i++;
}
$tag_elements = $body->get_elements_by_class("class-a");
$i = 1;
foreach ($tag_elements as $tag_element) {
//    $tag_element->append_p("With TAG search object insert! {$i}");
    $tag_element->append_a("#class-{$i}", 'Class found');
    $i++;
}


$head->set_title("HTML TEST");
$head->link_css("https://cdnjs.cloudflare.com/ajax/libs/foundation/6.2.3/foundation.css");
$body->append_child_tail(new \k1lib\html\script("https://code.jquery.com/jquery-2.2.0.min.js"));
$body->append_child_tail(new \k1lib\html\script("https://cdnjs.cloudflare.com/ajax/libs/foundation/6.2.3/foundation.min.js"));
$body->append_child_tail((new \k1lib\html\script())->set_value("$(document).foundation();"));

if ($body->get_element_by_id("inline-span-2")) {
    $body->get_element_by_id("inline-span-2")->set_value("another value");
}

echo DOM::generate();

echo "\n<!--\n\nHTML LOG\n\n";
echo \k1lib\html\tag_log::get_log();
echo "->";
