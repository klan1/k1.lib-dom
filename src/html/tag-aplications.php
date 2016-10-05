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

class top_bar extends \k1lib\html\tag {

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

    function __construct(\k1lib\html\tag $parent, $id = "") {

        $this->parent = $parent;
        $this->init_title_bar();

        parent::__construct("div", FALSE);
        $this->set_class("top-bar", TRUE);
        $this->set_id("responsive-menu");
        $this->append_to($parent);

        $left = $this->append_div("top-bar-left");

        $this->menu_left = new \k1lib\html\ul("dropdown menu", "k1app-menu-left");
        $this->menu_left->append_to($left);
        $this->menu_left->set_attrib("data-dropdown-menu", TRUE);

        $li = $this->menu_left->append_li(null, "menu-text k1app-title-container");
        $li->append_span("k1app-title-1");
        $li->append_span("k1app-title-2");
        $li->append_span("k1app-title-3");


        $right = $this->append_div("top-bar-right");

        $this->menu_right = new \k1lib\html\ul("dropdown menu", "k1app-menu-right");
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
    function add_button($href, $label, $class = null, $id = null) {
        $a = new \k1lib\html\a($href, $label, "_self", "button $class", $id);
        $this->menu_right->append_li()->append_child($a);
        return $a;
    }

    /**
     * @param string $href
     * @param string $label
     * @return \k1lib\html\li
     */
    function add_menu_item($href, $label, \k1lib\html\tag $where = null) {
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
        $elements = $this->parent->get_elements_by_class("k1app-title-{$number}");
        foreach ($elements as $element) {
            $element->set_value($value, $append);
        }
    }

    function init_title_bar() {
        $title = $this->parent->append_div("title-bar")
                ->set_attrib("data-responsive-toggle", "responsive-menu")
                ->set_attrib("data-hide-for", "medium");
        $title->append_child((new \k1lib\html\button(null, "menu-icon"))->set_attrib("data-toggle", TRUE));

        $title_bar_title = $title->append_h1(null, "title-bar-title k1app-title-container");
        $title_bar_title->set_attrib("style", "font-size:inherit;display:inline");
        $title_bar_title->append_span("k1app-title-1");
        $title_bar_title->append_span("k1app-title-2");
        $title_bar_title->append_span("k1app-title-3");
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
//    use \k1lib\html\append_shotcuts;

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
     * @var boolean 
     */
    protected $has_header = TRUE;

    /**
     * @var integer 
     */
    protected $max_text_length_on_cell = null;

    function __construct(\k1lib\html\tag $parent, $class = "", $id = "") {

        $this->parent = $parent;

        parent::__construct($class, $id);
        $this->append_to($parent);
        $this->set_class($class);
        $this->set_id($id);
    }

    public function set_data(array $data, $has_header = TRUE) {
        $this->data = $data;
        $this->has_header = $has_header;
    }

    public function generate($with_childs = TRUE, $n_childs = 0) {
        $this->use_data();
        return parent::generate($with_childs, $n_childs);
    }

    public function use_data() {
        $num_col = 0;
        $num_row = 0;
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
                // FIELD HIDE
                if (array_search($field, $this->fields_to_hide) !== FALSE) {
                    continue;
                }
                if ($this->has_header && ($row_index === 0)) {
                    $tr->append_th($col_value);
                } else {
                    if (!is_object($col_value)) {
                        if (is_numeric($col_value)) {
                            if (is_float($col_value)) {
                                $col_value = number_format($col_value, 2);
                            } else {
                                $col_value = number_format($col_value);
                            }
                        }
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
        }
    }

    public function insert_tag_on_field(\k1lib\html\tag $tag_object, array $fields_to_insert, array $tag_attribs_to_check = ['href', 'src', 'alt']) {
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
                    if (empty($tag_object_copy->get_value())) {
                        $tag_object_copy->set_value($col_value);
                    }
                    $attribs = [];
                    foreach ($tag_attribs_to_check as $attrib) {
                        $attribs[$attrib] = $tag_object_copy->get_attribute($attrib);
                        if (!empty($attribs[$attrib])) {
                            $tag_object_copy->set_attrib($attrib, $this->parse_string_value($attribs[$attrib], $row_index));
                        }
                    }
                    $tag_object_copy->set_value($this->parse_string_value($tag_object_copy->get_value(), $row_index));
                    $this->data[$row_index][$field] = $tag_object_copy;
                }
            }
        }
    }

    protected function parse_string_value($value, $row) {
        foreach ($this->get_fields_on_string($value) as $field) {
            if (isset($this->data[$row][$field])) {
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
