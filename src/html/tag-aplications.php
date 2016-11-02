<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Say good bye to inline <html> tag on your code!
 * 
 * HTML Classes for general purposes use. Say good bye to the inline HTML tags as strings on your PHP code!
 * 
 * @category library
 * @package klan1/html/foundation
 * @link https://github.com/klan1/k1.lib-dom
 * @author Alejandro Trujillo J. - https://github.com/j0hnd03
 * @version 0.8
 * @since 0.8
 * @license https://github.com/klan1/k1.lib-dom/blob/master/LICENSE Apache License 2.0
 */

namespace k1lib\html\foundation;

trait foundation_methods {

    /**
     * Will search for the $text as small-1, medium-12 as: /({$text}-[0-9]+)/
     * and replace the number part with the new number
     * @param type $attribute
     * @param type $text
     * @param type $new_number
     * @return type
     */
    public function replace_attribute_number($attribute, $text, $new_number) {
        $attribute_value = $this->get_attribute($attribute);
        $text_regexp = "/({$text}-[0-9]+)/";
        $regexp_match = [];
        if (preg_match($text_regexp, $attribute_value, $regexp_match)) {
            $string_new = str_replace($regexp_match[1], "{$text}-{$new_number}", $attribute_value);
            $this->set_attrib($attribute, $string_new);
            return $string_new;
        } else {
            $this->set_attrib($attribute, $attribute_value . " {$text}-{$new_number}");
            return $attribute_value . " {$text}-{$new_number}";
        }
    }

    public function remove_attribute_text($attribute, $text) {
        $attribute_value = $this->get_attribute($attribute);
        $text_regexp = "/(\s*$text\s*)/";
        $regexp_match = [];
        if (preg_match($text_regexp, $attribute_value, $regexp_match)) {
            $string_new = str_replace($regexp_match[1], "", $attribute_value);
            $this->set_attrib($attribute, $string_new);
            return $string_new;
        } else {
            return $attribute_value;
        }
    }

    public function append_close_button() {
        $close_button = new \k1lib\html\button(NULL, "close-button");
        $close_button->set_attrib("data-close", TRUE);
        $close_button->set_attrib("aria-label", "Close reveal");
        $close_button->append_span()->set_attrib("aria-hidden", TRUE)->set_value("&times;");
        $this->append_child_tail($close_button);
    }

}

class top_bar extends \k1lib\html\tag {

    use foundation_methods;
    use \k1lib\html\append_shotcuts;

    /**
     * @var \k1lib\html\tag
     */
    protected $parent;

    /**
     * @var \k1lib\html\ul
     */
    protected $menu_left;

    /**
     * @var \k1lib\html\ul
     */
    protected $menu_right;

    function __construct(\k1lib\html\tag $parent) {

        $this->parent = $parent;
        $this->init_title_bar();

        parent::__construct("div", FALSE);
        $this->set_class("top-bar hide-for-print", TRUE);
        $this->set_id("responsive-menu");
        $this->append_to($parent);

        $left = $this->append_div("top-bar-left");

        $this->menu_left = new \k1lib\html\ul("dropdown menu", "k1lib-menu-left");
        $this->menu_left->append_to($left);
        $this->menu_left->set_attrib("data-dropdown-menu", TRUE);

        $li = $this->menu_left->append_li(NULL, "menu-text k1lib-title-container hide-for-small-only");
        $li->append_span("k1lib-title-1");
        $li->append_span("k1lib-title-2");
        $li->append_span("k1lib-title-3");


        $right = $this->append_div("top-bar-right");

        $this->menu_right = new \k1lib\html\ul("dropdown menu", "k1lib-menu-right");
        $this->menu_right->append_to($right);
        $this->menu_right->set_attrib("data-dropdown-menu", TRUE);
    }

    /**
     * @param string $href
     * @param string $label
     * @param string $class
     * @param string $id
     * @return \k1lib\html\a
     */
    function add_button($href, $label, $class = NULL, $id = NULL) {
        $a = new \k1lib\html\a($href, $label, "_self", "button $class", $id);
        $this->menu_right->append_li()->append_child($a);
        return $a;
    }

    /**
     * @param string $href
     * @param string $label
     * @return \k1lib\html\li
     */
    function add_menu_item($href, $label, \k1lib\html\tag $where = NULL) {
        if (empty($where)) {
            $li = $this->menu_left->append_li();
            $li->append_a($href, $label);
        } else {
            $li = $where->append_li();
            $li->append_a($href, $label);
        }
        return $li;
    }

    /**
     * @param string $href
     * @param string $label
     * @return \k1lib\html\li
     */
    function add_sub_menu(\k1lib\html\li $where) {
        $sub_ul = $where->append_ul("menu vertical");
        return $sub_ul;
    }

    function set_title($number, $value, $append = FALSE) {
        $elements = $this->parent->get_elements_by_class("k1lib-title-{$number}");
        foreach ($elements as $element) {
            $element->set_value($value, $append);
        }
    }

    function init_title_bar() {
        $title = $this->parent->append_div("title-bar")
                ->set_attrib("data-responsive-toggle", "responsive-menu")
                ->set_attrib("data-hide-for", "medium");
        $title->append_child((new \k1lib\html\button(NULL, "menu-icon"))->set_attrib("data-toggle", TRUE));

        $title_bar_title = $title->append_h1(NULL, "title-bar-title k1lib-title-container");
        $title_bar_title->set_attrib("style", "font-size:inherit;display:inline");
        $title_bar_title->append_span("k1lib-title-1");
        $title_bar_title->append_span("k1lib-title-2");
        $title_bar_title->append_span("k1lib-title-3");
    }

    /**
     * @return div
     */
    function menu_left() {
        return $this->menu_left;
    }

    /**
     * @return div
     */
    function menu_right() {
        return $this->menu_right;
    }

    /**
     * @return tag
     */
    function get_parent() {
        return $this->parent;
    }

}

class table_from_data extends \k1lib\html\table {

    use foundation_methods;

    /**
     * @var \k1lib\html\tag
     */
    protected $parent;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $fields_to_hide = [];

    /**
     * @var array
     */
    protected $fields_for_key_array_text = [];

    /**
     * @var boolean 
     */
    protected $has_header = TRUE;

    /**
     * @var integer 
     */
    protected $max_text_length_on_cell = NULL;

    function __construct($class = "", $id = "") {

//        $this->parent = $parent;

        parent::__construct($class, $id);
//        $this->append_to($parent);
        $this->set_class($class);
        $this->set_id($id);
    }

    public function set_data(array $data, $has_header = TRUE) {
        $this->data = $data;
        $this->has_header = $has_header;
        return $this;
    }

    public function generate($with_childs = TRUE, $n_childs = 0) {
        $this->use_data();
        return parent::generate($with_childs, $n_childs);
    }

    public function use_data() {
        $num_col = 0;
        $num_row = 0;
        $row = 0;
        foreach ($this->data as $row_index => $row_data) {
            if ($this->has_header && ($row_index === 0)) {
                $thead = $this->append_thead();
                $tr = $thead->append_tr();
            } else {
                $num_row++;
                if (!isset($tbody)) {
                    $tbody = $this->append_tbody();
                }
                $tr = $tbody->append_tr();
            }
            foreach ($row_data as $field => $col_value) {
                if ($this->has_header && $row !== 0) {
                    $col_value = $this->parse_string_value($col_value, $row);
                }
                // FIELD HIDE
                if (array_search($field, $this->fields_to_hide) !== FALSE) {
                    continue;
                }
                if ($this->has_header && ($row_index === 0)) {
                    $tr->append_th($col_value);
                } else {
                    if (!is_object($col_value)) {
                        if (is_numeric($this->max_text_length_on_cell) && strlen($col_value) > $this->max_text_length_on_cell) {
                            $col_value = substr($col_value, 0, $this->max_text_length_on_cell) . "...";
                        }
                    } else {
                        if (is_numeric($this->max_text_length_on_cell) && strlen($col_value->get_value()) > $this->max_text_length_on_cell) {
                            $col_value->set_value(substr($col_value->get_value(), 0, $this->max_text_length_on_cell) . "...");
                        }
                    }
                    $tr->append_td($col_value);
                }
            }
            $row++;
        }
    }

    public function get_fields_for_key_array_text() {
        return $this->fields_for_key_array_text;
    }

    public function set_fields_for_key_array_text(array $fields_for_key_array_text) {
        $this->fields_for_key_array_text = $fields_for_key_array_text;
    }

    public function insert_tag_on_field(\k1lib\html\tag $tag_object, array $fields_to_insert, $tag_attrib_to_use = NULL, $append = FALSE) {
        $row = 0;
        foreach ($this->data as $row_index => $row_data) {
            $row++;
            // NOT on the HEADERS
            if ($this->has_header && $row == 1) {
                continue;
            }
            $col = 0;
            foreach ($row_data as $field => $col_value) {
                $col++;
                // FIELD HIDE, don't waste CPU power ;)
                if (array_search($field, $this->fields_to_hide) !== FALSE) {
                    continue;
                }
                // Field to insert
                if (array_search($field, $fields_to_insert) !== FALSE) {
                    // CLONE the TAG object to apply on each field necessary
                    $tag_object_copy = clone $tag_object;
                    // IF the value is empty, we have to put the field value on it
                    if (empty($tag_attrib_to_use)) {
                        if (empty($tag_object_copy->get_value())) {
                            $tag_object_copy->set_value($this->parse_string_value($col_value, $row_index));
                        } else {
                            $tag_object_copy->set_value($this->parse_string_value($tag_object_copy->get_value(), $row_index));
                        }
                    } else {
                        $tag_object_copy->set_attrib($tag_attrib_to_use, $this->parse_string_value($col_value, $row_index));
                    }
                    foreach ($tag_object_copy->get_attributes_array() as $attribute => $value) {
                        if ($attribute == $tag_attrib_to_use) {
                            continue;
                        }
                        $tag_object_copy->set_attrib($attribute, $this->parse_string_value($value, $row_index));
                    }
                    $this->data[$row_index][$field] = $tag_object_copy;
                }
            }
        }
    }

    protected function parse_string_value($value, $row) {
        foreach ($this->get_fields_on_string($value) as $field) {
            if (isset($this->data[$row][$field])) {
                /**
                 * AUTH-CODE 
                 */
                $key_array_text = implode("--", $this->fields_for_key_array_text);
                if (!empty($key_array_text)) {
                    $auth_code = md5(\k1lib\K1MAGIC::get_value() . $key_array_text);
                } else {
                    $auth_code = NULL;
                }
                if (strstr($value, "--authcode--") !== FALSE) {
                    $value = str_replace("--authcode--", $auth_code, $value);
                }
                /**
                 * {{field:NAME}}
                 */
                $field_tag = "{{field:" . $field . "}}";
                $value = str_replace($field_tag, $this->data[$row][$field], $value);
            }
        }
        return $value;
    }

    protected function get_fields_on_string($value) {
        $pattern = "/{{field:(\w+)}}/";
        $matches = [];
        $fields = [];
        if (preg_match_all($pattern, $value, $matches)) {
            foreach ($matches[1] as $field) {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    public function hide_fields(array $fields) {
        $this->fields_to_hide = $fields;
    }

    public function has_header() {
        return $this->has_header;
    }

    public function get_max_text_length_on_cell() {
        return $this->max_text_length_on_cell;
    }

    public function set_max_text_length_on_cell($max_text_length_on_cell) {
        $this->max_text_length_on_cell = $max_text_length_on_cell;
    }

}

class grid_cell extends \k1lib\html\div {

    use foundation_methods;

    protected $small = NULL;
    protected $medium = NULL;
    protected $large = NULL;
    protected $row_number = 0;

    /**
     * @param integer $col_number
     * @param integer $class
     * @param integer $id
     *  */
    public function __construct($col_number = NULL, $class = NULL, $id = NULL) {
        parent::__construct("column column-{$col_number}" . $class, NULL);
        $this->set_attrib("data-grid-col", $col_number);
    }

    public function set_class($class, $append = TRUE) {
        parent::set_class($class, $append);
        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function small($cols, $clear = FALSE) {
        $this->small = $cols;

        if ($clear) {
            $this->set_attrib("class", "column small-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "small", $cols);
        }

        return $this;
    }

    /**
     * @return grid_cell
     */
    public function small_centered() {
        $this->set_attrib("class", "small-centered", TRUE);
        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function medium($cols, $clear = FALSE) {
        $this->medium = $cols;

        if ($clear) {
            $this->set_attrib("class", "column medium-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "medium", $cols);
        }

        return $this;
    }

    /**
     * @return grid_cell
     */
    public function medium_centered() {
        $this->set_attrib("class", "medium-centered", TRUE);
        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function large($cols, $clear = FALSE) {
        $this->large = $cols;

        if ($clear) {
            $this->set_attrib("class", "column large-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "large", $cols);
        }

        return $this;
    }

    /**
     * @return grid_cell
     */
    public function large_centered() {
        $this->set_attrib("class", "large-centered", TRUE);
        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function end() {
        $this->set_attrib("class", "end", TRUE);
        return $this;
    }

    public function get_small() {
        return $this->small;
    }

    public function get_medium() {
        return $this->medium;
    }

    public function get_large() {
        return $this->large;
    }

    /**
     * @param integer $num_rows
     * @param integer $num_cols
     * @return \k1lib\html\foundation\grid
     */
    public function append_grid($num_rows, $num_cols) {
        $grid = new grid($num_rows, $num_cols, $this);
        return $grid;
    }

    public function append_row($num_cols) {
        $row = new grid_row($num_cols, ++$this->row_number, $this);
        return $row;
    }

}

class grid_row extends \k1lib\html\div {

    use foundation_methods;

    /**
     * @var \k1lib\html\tag
     */
    protected $parent;

    /**
     * @var grid_cell[]
     */
    protected $cols = [];

    function __construct($num_cols, $grid_row = NULL, $parent = NULL) {

        $this->parent = $parent;

        parent::__construct("row row-{$grid_row}", NULL);
        if (!empty($this->parent)) {
            $this->append_to($this->parent);
        }

        if (!empty($grid_row)) {
            $this->set_attrib("data-grid-row", $grid_row);
        }

        for ($col = 1; $col <= $num_cols; $col++) {
            $this->cols[$col] = $this->append_cell($col);
        }
    }

    /**
     * @param integer $col_number
     * @return \k1lib\html\tag
     */
    public function col($col_number) {
        if (isset($this->cols[$col_number])) {
            return $this->cols[$col_number];
        }
    }

    /**
     * @return \k1lib\html\div
     */
    public function expanded() {
        $this->set_attrib("class", "expanded", TRUE);
        return $this;
    }

    /**
     * 
     * @param integer $col_number
     * @param integer $class
     * @param integer $id
     * @return grid_cell
     */
    public function append_cell($col_number = NULL, $class = NULL, $id = NULL) {
        $cell = new grid_cell($col_number, $class, $id);
        $cell->append_to($this);
        return $cell;
    }

}

class grid extends \k1lib\html\div {

    /**
     * @var \k1lib\html\tag
     */
    protected $parent;

    /**
     * @var grid_cell[]
     */
    protected $rows = [];

    public function __construct($num_rows, $num_cols, \k1lib\html\tag $parent = NULL) {
        $this->parent = $parent;

        if (empty($this->parent)) {
            parent::__construct();
            for ($row = 1; $row <= $num_rows; $row++) {
                $this->rows[$row] = $this->append_row($num_cols, $row, $this);
            }
        } else {
//            $this->append_to($this->parent);
            $this->link_value_obj($parent);
            for ($row = 1; $row <= $num_rows; $row++) {
                $this->rows[$row] = $this->append_row($num_cols, $row, $this->parent);
            }
            return $parent;
        }
    }

    /**
     * @param integer $row_number
     * @return \k1lib\html\foundation\grid_row
     */
    public function row($row_number) {
        if (isset($this->rows[$row_number])) {
            return $this->rows[$row_number];
        }
    }

    /**
     * 
     * @param type $num_cols
     * @param type $grid_row
     * @return \k1lib\html\foundation\grid_row
     */
    public function append_row($num_cols = NULL, $grid_row = NULL, $parent = NULL) {
        $row = new grid_row($num_cols, $grid_row, $parent);
        return $row;
    }

}

class label_value_row extends grid_row {

    function __construct($label, $value, $grid_row = 0, $parent = NULL) {
        parent::__construct(2, $grid_row, $parent);

        $this->col(1)->medium(4)->large(3);
        $this->col(2)->medium(8)->large(9)->end();

        $this->col(2)->remove_attribute_text("class", "end");

        $input_name = $this->get_name_attribute($value);

        if (method_exists($label, "generate")) {
            $small_label = clone $label;
            $this->col(1)->append_child($label->set_class("k1lib-label-object right inline hide-for-small-only text-right"));
            $this->col(1)->append_child($small_label->set_class("k1lib-label-object left show-for-small-only"));
        } else {
            $this->col(1)->append_child(new \k1lib\html\label($label, $input_name, "k1lib-label-object right inline hide-for-small-only text-right"));
            $this->col(1)->append_child(new \k1lib\html\label($label, $input_name, "k1lib-label-object left show-for-small-only"));
        }
        $this->col(2)->set_value($value);
    }

    private function get_name_attribute($tag_object) {
        if (\method_exists($tag_object, "get_elements_by_tag")) {
            if (!isset($tag_object)) {
                $tag_object = new \k1lib\html\input("input", "dummy", NULL);
            }
            $elements = $tag_object->get_elements_by_tag("input");
            if (empty($elements)) {
                $elements = $tag_object->get_elements_by_tag("select");
            }
            if (empty($elements)) {
                $elements = $tag_object->get_elements_by_tag("textarea");
            }
            foreach ($elements as $element) {
                $name = $element->get_attribute("name");
                if ($name) {
                    return $name;
                }
            }
        }
        return NULL;
    }

}

class callout extends \k1lib\html\div {

    use foundation_methods;

    /**
     * @var grid_cell[]
     */
    protected $cols = [];
    protected $title = "";
    protected $message = "no message";
    protected $margin = '20px';

    function __construct($message = NULL, $title = NULL, $closable = TRUE, $type = "primary") {
        $this->message = $message;
        $this->title = $title;

        parent::__construct("callout", NULL);
        $this->set_attrib("data-closable", TRUE);
        if ($closable) {
            $close_button = new \k1lib\html\button(NULL, "close-button");
            $close_button->set_attrib("data-close", TRUE);
            $close_button->set_attrib("aria-label", "Close reveal");
            $close_button->append_span()->set_attrib("aria-hidden", TRUE)->set_value("&times;");
            $this->append_child_tail($close_button);
        }

        $this->set_class($type);
    }

    public function set_class($class, $append = FALSE) {
        if ($append === FALSE) {
            $class = "callout {$class}";
        }
        parent::set_class($class, $append);
    }

    public function set_margin($margin) {
        $this->margin = $margin;
    }

    public function get_message() {
        return $this->message;
    }

    public function set_message($message) {
        $this->message = $message;
    }

    function get_title() {
        return $this->title;
    }

    function set_title($title) {
        $this->title = $title;
    }

    public function generate($with_childs = \TRUE, $n_childs = 0) {
        if (!empty($this->title)) {
            $h6 = new \k1lib\html\h6($this->title);
        } else {
            $h6 = "";
        }

        $this->set_value("{$h6}{$this->message}");

        if (!empty($this->margin)) {
            $this->set_attrib("style", "margin: {$this->margin}");
        }

        return parent::generate($with_childs, $n_childs);
    }

}
