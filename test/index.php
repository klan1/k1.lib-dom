<?php

namespace k1app\test;

require_once '../src/loader.php';

use \k1lib\html\DOM as DOM;

DOM::start();

\k1lib\html\html::set_use_log(TRUE);

$head = DOM::html()->head();
$body = DOM::html()->body();

$body->append_div("hello-world-class", "hello-world-id")->append_p("Hello world");

$body->append_div()->append_p("Hello people")->append_span()->set_value("SPAN 1 element :)");

$body->append_div("another-div")->set_value("A free text inside the DIV element");
$a = $body->get_element_by_id("hello-world-id")->append_div()->append_a("#null");
$a->append_span()->set_value("SPAN 2 element :)")->set_id("find-me-1");
$a->append_span()->set_value("SPAN 3 element :)")->set_id("find-me-2")->set_class("find-me-class");
$id_element = $body->get_element_by_id("find-me-1");

$tag_elements = $body->get_elements_by_tag("p");

$i = 1;
foreach ($tag_elements as $tag_element) {
//    $tag_element->append_p("With TAG search object insert! {$i}");
    $tag_element->append_p("With TAG search object insert! {$i}")->set_class("find-me-class");
    $i++;
}

$class_elements = $body->get_elements_by_class("find-me-class");

$div_classes = $body->append_div();
$div_classes->append_child((new \k1lib\html\h3("This values found with class find-me-class")));
foreach ($class_elements as $element) {
    $div_classes->append_child($element);
}

$head->set_title("HTML TEST");
$head->link_css("https://cdnjs.cloudflare.com/ajax/libs/foundation/6.2.3/foundation.css");
$body->append_child_tail(new \k1lib\html\script("https://code.jquery.com/jquery-2.2.0.min.js"));
$body->append_child_tail(new \k1lib\html\script("https://cdnjs.cloudflare.com/ajax/libs/foundation/6.2.3/foundation.min.js"));
$body->append_child_tail((new \k1lib\html\script())->set_value("$(document).foundation();"));

$body->append_child((new \k1lib\html\h3("Log")));
$body->append_child((new \k1lib\html\textarea("log"))->set_value(\k1lib\html\tag_log::get_log()));
echo DOM::generate();