# k1.lib-dom

Say good bye to the inline HTML tags as strings on your PHP code!

DOM Classes from K1.lib.

## Requirements
- PHP 5.4 with at least 2MB in memory allowed + Your code requirements. Maybe with 8MB in most cases is enough.
- BOWER and COMPOSER to run tests

## Installation

### In a Composer project:

```sh
composer require klan1/k1.lib-dom
```
### No Composer?:

```sh
git clone https://github.com/klan1/k1.lib-dom.git
```
### To run tests:

```sh
git clone https://github.com/klan1/k1.lib-dom.git
cd k1.lib-dom/test/
bower install
composer install
php -S localhost:8800
```
Then, open in your browser http://localhost:8800/foundation.php

## Basic example

```php
use \k1lib\html\DOM as DOM;

DOM::start('en');

$head = DOM::html()->head();
$body = DOM::html()->body();

$head->set_title("Simple example");

$p = new \k1lib\html\p("Helo world", "class-here", "id-here");

$p->append_to($body);

echo DOM::generate();
```
will generate:

```html
<html lang="en">
	<head>
		<title>Simple example</title>
	</head>
	<body>
		<p class="class-here" id="id-here">Helo world</p>
	</body>
</html>
```

This one, will give you the same result

```php
use \k1lib\html\DOM as DOM;

DOM::start();

DOM::html()->head()->set_title("Simple example");

DOM::html()->body()->append_p("Helo world", "class-here", "id-here");

echo DOM::generate();
```

## Foundation Top Bar

```php
$top_bar = new \k1lib\html\foundation\top_bar(DOM::html()->body()->header());

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
```
Will generate
```html
<div class="title-bar" data-responsive-toggle="responsive-menu" data-hide-for="medium">
    <button class="menu-icon" type="button" data-toggle></button>
    <h1 class="title-bar-title k1app-title-container" style="font-size:inherit;display:inline">
        <span class="k1app-title-1">APP TITLE</span>
        <span class="k1app-title-2"> :: </span>
        <span class="k1app-title-3">HOME</span>
    </h1>
</div>
<div class="top-bar hide-for-print" id="responsive-menu">
    <div class="top-bar-left">
        <ul class="dropdown menu" id="k1app-menu-left" data-dropdown-menu>
            <li class="menu-text k1app-title-container hide-for-small-only">
                <span class="k1app-title-1">APP TITLE</span>
                <span class="k1app-title-2"> :: </span>
                <span class="k1app-title-3">HOME</span>
            </li>
            <li>
                <a href="#">Sub menu 1</a>
                <ul class="menu vertical">
                    <li>
                        <a href="#">Level 1</a>
                    </li>
                    <li>
                        <a href="#">Level 2</a>
                    </li>
                    <li>
                        <a href="#">Level 2</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">Item 2</a>
            </li>
            <li>
                <a href="#">Item 3</a>
            </li>
        </ul>
    </div>
    <div class="top-bar-right">
        <ul class="dropdown menu" id="k1app-menu-right" data-dropdown-menu>
            <li>
                <a href="#" target="_self" class="button ">Ingresar</a>
            </li>
            <li>
                <a href="#" target="_self" class="button alert">Salir</a>
            </li>
        </ul>
    </div>
</div>
```
## Table from Array
```php
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
```

Will generate a pretty table:

```html
<table class="foundation-table" id="table-1">
    <thead>
        <tr>
            <th>Name</th>
            <th>Last name</th>
            <th>Full Name</th>
            <th>Pic</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Alejandro</td>
            <td>Trujillo</td>
            <td>Alejandro Trujillo</td>
            <td>
                <img alt="Avatar of Alejandro" src="https://66.media.tumblr.com/avatar_32dc0cfad91f_128.png">
            </td>
        </tr>
        <tr>
            <td>Camilo</td>
            <td>Lopez</td>
            <td>Camilo Lopez</td>
            <td>
                <img alt="Avatar of Camilo" src="https://cdn1.iconfinder.com/data/icons/halloween-6/96/Zombie-128.png">
            </td>
        </tr>
    </tbody>
</table>
```

## LICENSE
Apache License Version 2.0, January 2004.

## THANKS
Thanks to Zend Inc. for the GNU Developer License of Zend Server, it's amazing work on it!

http://www.apache.org/licenses/

Autor: Alejandro Trujillo J. - http://klan1.com

