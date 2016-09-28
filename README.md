# k1.lib-dom

Say good bye to the inline HTML tags as strings on your PHP code!

DOM Classes from K1.lib.

## Requirements
- Apache with rewrite enabled.
- Optional: MySQL Database.
- PHP 5.4 with at least 2MB in memory allowed + Your code requirements. Maybe with 8MB in most cases is enough.

## Installation

```sh
composer require klan1/k1.lib-dom
```

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
## LICENSE
Apache License Version 2.0, January 2004.

## THANKS
Thanks to Zend Inc. for the GNU Developer License of Zend Server, it amazing work on it!

http://www.apache.org/licenses/

Autor: Alejandro Trujillo J. - http://klan1.com

