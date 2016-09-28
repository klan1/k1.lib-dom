<?php

namespace k1app\test;

require_once '../src/loader.php';

use \k1lib\html\DOM as DOM;

\k1lib\html\html::set_use_log(TRUE);

DOM::start();

$head = DOM::html()->head();
$body = DOM::html()->body();

$head->set_title("Simple example");

$p = new \k1lib\html\p("Helo world", "class-here", "id-here");
$p->append_to($body);

echo DOM::generate();

echo "<!--\n\nHTML LOG\n\n";
echo \k1lib\html\tag_log::get_log();
echo "->";
