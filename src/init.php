<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Say good bye to inline <html> tag on your code!
 * 
 * HTML Classes for general purposes use. Say good bye to the inline HTML tags as strings on your PHP code!
 * 
 * @category library
 * @package klan1/html
 * @link https://github.com/klan1/k1.lib-dom
 * @author Alejandro Trujillo J. - https://github.com/j0hnd03
 * @version 0.8
 * @since 0.6
 * @license https://github.com/klan1/k1.lib-dom/blob/master/LICENSE Apache License 2.0
 */

namespace k1lib\html;

const IS_SELF_CLOSED = TRUE;
const IS_NOT_SELF_CLOSED = FALSE;
const NO_CLASS = NULL;
const NO_ID = NULL;
const NO_VALUE = NULL;
const APPEND_ON_HEAD = 1;
const APPEND_ON_MAIN = 2;
const APPEND_ON_TAIL = 3;
const INSERT_ON_PRE_TAG = -1;
const INSERT_ON_AFTER_TAG_OPEN = 2;
const INSERT_ON_VALUE = 0;
const INSERT_ON_BEFORE_TAG_CLOSE = 3;
const INSERT_ON_POST_TAG = 1;

