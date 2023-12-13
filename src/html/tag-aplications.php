<?php
// V1.0


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

    protected $small = NULL;
    protected $medium = NULL;
    protected $large = NULL;

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

    /**
     * @return \k1lib\html\div
     */
    public function align_center() {
        $this->set_attrib("class", "align-center", TRUE);
        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function align_left() {
        $this->set_attrib("class", "align-left", TRUE);
        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function align_right() {
        $this->set_attrib("class", "align-right", TRUE);
        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function align_justify() {
        $this->set_attrib("class", "align-justify", TRUE);
        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function small($cols, $clear = FALSE) {
        $this->small = $cols;

        if ($clear) {
            $this->set_attrib("class", "cell small-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "small", $cols);
        }

        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function medium($cols, $clear = FALSE) {
        $this->medium = $cols;

        if ($clear) {
            $this->set_attrib("class", "cell medium-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "medium", $cols);
        }

        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function large($cols, $clear = FALSE) {
        $this->large = $cols;

        if ($clear) {
            $this->set_attrib("class", "cell large-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "large", $cols);
        }

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
}

class bar extends \k1lib\html\div {

    /**
     * @var string
     */
    protected $type;

    /**
     * @var \k1lib\html\div
     */
    protected $left = null;

    /**
     * @var \k1lib\html\div
     */
    protected $right = null;

    function __construct($type, $id = NULL) {
        $this->type = $type;
        parent::__construct("{$type}-bar", $id);
        $this->left = new \k1lib\html\div("{$type}-bar-left");
        $this->right = new \k1lib\html\div("{$type}-bar-right");

        $this->left->append_to($this);
        $this->right->append_to($this);
    }

    /**
     * @return \k1lib\html\div
     */
    public function left() {
        return $this->left;
    }

    /**
     * @return \k1lib\html\div
     */
    public function right() {
        if (empty($this->right)) {
            $this->right = new \k1lib\html\div("{$this->type}-bar-right");
        }
        return $this->right;
    }
}

class title_bar extends bar {

    /**
     * @var \k1lib\html\button
     */
    protected $left_button = null;

    /**
     * @var \k1lib\html\span
     */
    protected $title = null;

    function __construct($id = NULL) {
        parent::__construct('title', $id);
        $this->left_button = new \k1lib\html\button(NULL, "menu-icon");
        $this->left_button->append_to($this->left);

        $this->title = new \k1lib\html\span("title-bar-title");
        $this->title->append_to($this->left);
    }

    /**
     * @return \k1lib\html\span
     */
    public function title() {
        return $this->title;
    }

    /**
     * @return \k1lib\html\button
     */
    public function left_button() {
        return $this->left_button;
    }
}

class top_bar extends bar {

    /**
     * @var menu
     */
    protected $menu_left = null;

    /**
     * @var \k1lib\html\span
     */
    protected $title = null;

    function __construct($id = NULL) {
        parent::__construct('top', $id);

        $this->menu_left = new menu('dropdown');
        $this->menu_left->append_to($this->left);
        $this->title = $this->menu_left->add_menu_item(NULL, NULL);
        $this->title->set_class('menu-text');
    }

    /**
     * @return \k1lib\html\span
     */
    public function title() {
        return $this->title;
    }

    /**
     * @return menu
     */
    public function menu_left() {
        return $this->menu_left;
    }
}

class menu extends \k1lib\html\ul {

    protected $type = '';
    protected $is_vertical = false;
    protected $menu_class = '';
    protected $nested_class = '';
    protected $data_attribute = '';

    function __construct($type = 'menu', $sub_class = NULL, $vertical = FALSE) {
        $this->type = $type;
        $this->is_vertical = $vertical;
        switch ($type) {
            case 'menu':
                if ($vertical) {
                    $this->menu_class = 'menu vertical';
                } else {
                    $this->menu_class = 'menu';
                }
                $this->nested_class = '';
                $this->data_attribute = '';

                break;
            case 'dropdown':
                if ($vertical) {
                    $this->menu_class = 'dropdown menu vertical';
                } else {
                    $this->menu_class = 'dropdown menu';
                }
                $this->nested_class = 'menu';
                $this->data_attribute = 'data-dropdown-menu';

                break;
            case 'drilldown':
                $vertical = TRUE;
                $this->menu_class = 'vertical menu drilldown';
                $this->nested_class = 'menu vertical nested';
                $this->data_attribute = 'data-drilldown';

                break;
            case 'accordion':
                $vertical = TRUE;
                $this->menu_class = 'vertical menu accordion-menu';
                $this->nested_class = 'menu vertical nested';
                $this->data_attribute = 'data-accordion-menu';

                break;

            default:
                break;
        }
        if (!empty($sub_class)) {
            $this->menu_class = $sub_class;
        }
        parent::__construct($this->menu_class, NULL);
        if (empty($sub_class)) {
            $this->set_attrib($this->data_attribute, TRUE);
        }
    }

    /**
     * @param string $href
     * @param string $label
     * @param string $id
     * @param string $where
     * @return \k1lib\html\li
     */
    function add_menu_item($href, $label, $id = NULL, $where_id = NULL) {
        if (!empty($where_id)) {
            $parent = $this->get_element_by_id($where_id);
//            d($parent);
        }
        if (empty($parent)) {
            $li = $this->append_li();
            $li->set_id($id);
            if (!empty($href)) {
                $a = $li->append_a($href, $label);
                $li->link_value_obj($a);
            } else {
                $li->set_value($label);
            }
        } else {
            $ul = new menu($this->type, $this->nested_class, $this->is_vertical);
            $parent->append_child($ul);
            $li = $ul->add_menu_item($href, $label, $id);
        }
        return $li;
    }

    /**
     * @param string $href
     * @param string $label
     * @return menu
     */
    function add_sub_menu($href, $label, $id = NULL, $where_id = NULL) {
        if (!empty($where_id)) {
            $parent = $this->get_element_by_id($where_id);
        }
        if (empty($parent)) {
            $li = $this->add_menu_item($href, $label, $id);
            $li->unlink_value_obj();
        } else {
            $ul = new menu($this->type, $this->nested_class, $this->is_vertical);
            $parent->append_child($ul);
            $li = $ul->add_menu_item($href, $label, $id);
        }
        $li->set_class("has-submenu", TRUE);
        $ul = new menu($this->type, $this->nested_class, $this->is_vertical);
        $li->append_child($ul);
        return $ul;
    }

    function set_active($id) {
        $tag = $this->get_element_by_id($id);
        if (!empty($tag)) {
            $tag->unlink_value_obj();
            $tag->set_class('active', TRUE);
        }
    }
}

class off_canvas extends \k1lib\html\tag {

    use foundation_methods;
    use \k1lib\html\append_shotcuts;

    /**
     * @var \k1lib\html\tag
     */
    protected $parent;

    /**
     * @var \k1lib\html\div
     */
    protected $left = null;

    /**
     * @var menu
     */
    protected $menu_left = null;

    /**
     * @var menu
     */
    protected $menu_left_head = null;

    /**
     * @var menu
     */
    protected $menu_left_tail = null;

    /**
     * @var \k1lib\html\div
     */
    protected $right = null;

    /**
     * @var menu
     */
    protected $rigth_menu = null;

    /**
     * @var \k1lib\html\div
     */
    public $content = null;

    public function __construct(\k1lib\html\body $parent = NULL) {
        $this->parent = $parent;
    }

    /**
     * @return \k1lib\html\div
     */
    public function left() {
        if (empty($this->left)) {
            $this->left = new \k1lib\html\div("off-canvas position-left", 'offCanvasLeft');
            $this->left->set_attrib('data-off-canvas', TRUE);
            $this->left->append_to($this->parent);
        }
        return $this->left;
    }

    /**
     * @return menu
     */
    public function menu_left() {
        if (empty($this->menu_left)) {
            $this->menu_left = new menu('accordion');
            $this->menu_left->set_id('menu-left');
            $this->left->append_child($this->menu_left);
        }
        return $this->menu_left;
    }

    /**
     * @return menu
     */
    public function menu_left_head() {
        if (empty($this->menu_left_head)) {
            $this->menu_left_head = new menu('accordion');
            $this->menu_left_head->set_id('menu-left-head');
            $this->menu_left_head->set_class('head', TRUE);
            $this->left->append_child_head($this->menu_left_head);
        }
        return $this->menu_left_head;
    }

    /**
     * @return menu
     */
    public function menu_left_tail() {
        if (empty($this->menu_left_tail)) {
            $this->menu_left_tail = new menu('accordion');
            $this->menu_left_tail->set_id('menu-left-tail');
            $this->menu_left_tail->set_class('tail', TRUE);
            $this->left->append_child_tail($this->menu_left_tail);
        }
        return $this->menu_left_tail;
    }

    /**
     * @return \k1lib\html\div
     */
    public function right() {
        if (empty($this->right)) {
            $this->right = new \k1lib\html\div("off-canvas position-right", 'offCanvasRight');
            $this->right->set_attrib('data-off-canvas', TRUE);
            $this->right->set_attrib('data-position', 'right');
            $this->right->append_to($this->parent);
        }
        return $this->right;
    }

    /**
     * @return \k1lib\html\div
     */
    public function content() {
        if (empty($this->content)) {
            $this->content = new \k1lib\html\div("off-canvas-content");
            $this->content->set_attrib('data-off-canvas-content', TRUE);
            $this->content->append_to($this->parent);
        }
        return $this->content;
    }
}

class top_bar_ extends \k1lib\html\tag {

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

    static public $float_round_default = NULL;

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
    protected $data_original = [];

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

    /**
     * @var int
     */
    protected $float_round = NULL;

    function __construct($class = "", $id = "") {

//        $this->parent = $parent;

        parent::__construct($class, $id);
//        $this->append_to($parent);
        $this->set_class($class);
        $this->set_id($id);

        $this->float_round = self::$float_round_default;
    }

    public function set_data(array $data, $has_header = TRUE) {
        $this->data = $data;
        $this->data_original = $data;
        $this->has_header = $has_header;
        return $this;
    }

    public function generate($with_childs = TRUE, $n_childs = 0) {
        $this->use_data();
        return parent::generate($with_childs, $n_childs);
    }

    public function set_fields_to_hide($fields) {
        $this->fields_to_hide = $fields;
    }

    public function use_data() {
        $num_col = 0;
        $num_row = 0;
        $row = 0;
        foreach ($this->data as $row_index => $row_data) {
//            print_r($this->data);
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
                        if (($this->float_round !== NULL) && is_numeric($col_value) && is_float($col_value + 0)) {
                            $col_value = round($col_value + 0, $this->float_round);
                        } else {
                            if (is_numeric($this->max_text_length_on_cell) && strlen($col_value) > $this->max_text_length_on_cell) {
                                $col_value = substr($col_value, 0, $this->max_text_length_on_cell) . "...";
                            } else {
                                
                            }
                        }
                    } else {
                        if (is_numeric($this->max_text_length_on_cell) && strlen($col_value->get_value()) > $this->max_text_length_on_cell) {
                            $col_value->set_value(substr($col_value->get_value(), 0, $this->max_text_length_on_cell) . "...");
                        } else {
                            
                        }
                    }
                    $last_td = $tr->append_td($col_value);
//                    if ($this->has_header && $row !== 0) {
//                        $last_td->set_attrib('data-label', trim($this->data[0][$field]->value), TRUE);
//                    }
                }
            }
            $row++;
        }
        return $this;
    }

    public function get_fields_for_key_array_text() {
        return $this->fields_for_key_array_text;
    }

    public function set_fields_for_key_array_text(array $fields_for_key_array_text) {
        $this->fields_for_key_array_text = $fields_for_key_array_text;
    }

    public function insert_tag_on_field(\k1lib\html\tag $tag_object, array $fields_to_insert, $tag_attrib_to_use = NULL, $append = FALSE, $respect_blanks = FALSE, $just_replace_attribs = FALSE, $just_this_row = NULL) {
        $row = 0;
//        if ($just_replace_attribs) {
//            echo "child call - row_key:$just_this_row<br>";
//        } else {
//            echo "normal call <br>";
//        }
        foreach ($this->data_original as $row_index => $row_data) {
            $row++;
            if ($just_this_row !== NULL && $just_this_row != $row_index) {
//                echo "child: $row_index:$just_this_row <br>";
                continue;
            }
//            else {
//                if ($just_this_row !== NULL) {
//                    echo "this is the ROW: $row_index:$just_this_row <br>";
//                }
//            }
            // NOT on the HEADERS
            if ($this->has_header && $row == 1) {
                continue;
            }
            $col = 0;
            foreach ($row_data as $field => $col_value) {
                $col++;
                if (empty($this->data_original[$row_index][$field]) && $respect_blanks) {
                    continue;
                }
                // FIELD HIDE, don't waste CPU power ;)
                if (array_search($field, $this->fields_to_hide) !== FALSE) {
                    continue;
                }
                // Field to insert
                if (array_search($field, $fields_to_insert) !== FALSE) {
                    // CLONE the TAG object to apply on each field necessary
                    if (!$just_replace_attribs) {
                        $tag_object_copy = clone $tag_object;
                    } else {
                        $tag_object_copy = $tag_object;
                    }

                    // IF the value is empty, we have to put the field value on it
                    if (empty($tag_attrib_to_use)) {
                        if (empty($tag_object_copy->get_value())) {
                            $tag_object_childs = $tag_object_copy->get_childs();
                            if (!empty($tag_object_childs)) {
//                                echo "childs!  [$row_index][$field] <br>";
                                foreach ($tag_object_childs as $child_key => $tag_object_child) {
//                                    echo "$tag_object $tag_object_child <br>";
                                    $tag_object_child_copy = clone $tag_object_child;
                                    $this->insert_tag_on_field($tag_object_child_copy, $fields_to_insert, $tag_attrib_to_use, $append, $respect_blanks, TRUE, $row_index);
                                    $tag_object_copy->replace_child($child_key, $tag_object_child_copy);
//                                    echo "$tag_object_copy $tag_object_child_copy <br>";
                                }
                            } else {
                                $tag_object_copy->set_value($this->parse_string_value($col_value, $row_index));
                            }
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
                    if (!$just_replace_attribs) {
                        $this->data[$row_index][$field] = $tag_object_copy;
                    }
                }
            }
            if ($just_this_row !== NULL && $just_this_row == $row_index) {
//                echo "END child: $just_this_row <br>";
                break;
            }
        }
        return $this;
    }

    protected function parse_string_value($value, $row) {
        foreach ($this->get_fields_on_string($value) as $field) {
            if (array_key_exists($field, $this->data_original[$row])) {
                /**
                 * AUTH-CODE 
                 */
                $key_array = [];
                foreach ($this->fields_for_key_array_text as $field_for_key_array_text) {
                    $key_array[] = $this->data_original[$row][$field_for_key_array_text];
                }
                $key_array_text = implode("--", $key_array);
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
                $value = str_replace($field_tag, rawurlencode($this->data_original[$row][$field]), $value);
            }
        }
        foreach ($this->get_raw_fields_on_string($value) as $field) {
            if (array_key_exists($field, $this->data_original[$row])) {
                /**
                 * AUTH-CODE 
                 */
                $key_array = [];
                foreach ($this->fields_for_key_array_text as $field_for_key_array_text) {
                    $key_array[] = $this->data_original[$row][$field_for_key_array_text];
                }
                $key_array_text = implode("--", $key_array);
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
                $field_tag = "{{field-raw:" . $field . "}}";
                $value = str_replace($field_tag, $this->data_original[$row][$field], $value);
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

    protected function get_raw_fields_on_string($value) {
        $pattern = "/{{field-raw:(\w+)}}/";
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
        return $this;
    }

    public function has_header() {
        return $this->has_header;
    }

    public function get_max_text_length_on_cell() {
        return $this->max_text_length_on_cell;
    }

    public function set_max_text_length_on_cell($max_text_length_on_cell) {
        $this->max_text_length_on_cell = $max_text_length_on_cell;
        return $this;
    }

    public function set_float_round($round_places) {
//        if (is_int($round_places)) {
        $this->float_round = $round_places;
//        }
    }

    public function get_float_round() {
        return $this->float_round;
    }
}

class grid_cell extends \k1lib\html\div {

    use foundation_methods;

    protected $row_number = 0;

    /**
     * @param integer $col_number
     * @param integer $class
     * @param integer $id
     *  */
    public function __construct($col_number = NULL, $class = NULL, $id = NULL) {
        parent::__construct("cell cell-{$col_number}" . $class, NULL);
        $this->set_attrib("data-grid-cell", $col_number);
    }

    // change the default behaivor of append from FALSE to TRUE
    public function set_class($class, $append = TRUE) {
        parent::set_class($class, $append);
        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function end() {
        $this->set_attrib("class", "end", TRUE);
        return $this;
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

        parent::__construct("grid-x row-{$grid_row}", NULL);
        if (!empty($this->parent)) {
            $this->append_to($this->parent);
        }

        if (!empty($grid_row)) {
            $this->set_attrib("data-grid-row", $grid_row);
        }

        for ($col = 1; $col <= $num_cols; $col++) {
            $this->num_cols[$col] = $this->append_cell($col);
        }
    }

    /**
     * @param integer $col_number
     * @return \k1lib\html\foundation\grid_cell
     */
    public function col($col_number) {
        return $this->cell($col_number);
    }

    /**
     * @param integer $col_number
     * @return \k1lib\html\foundation\grid_cell
     */
    public function cell($col_number) {
        if (isset($this->num_cols[$col_number])) {
            return $this->num_cols[$col_number];
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

    use foundation_methods;

    /**
     * @var \k1lib\html\tag
     */
    protected $parent;

    /**
     * @var grid_cell[]
     */
    protected $rows = [];
    protected $num_rows;
    protected $num_cols;

    public function __construct($num_rows, $num_cols, \k1lib\html\tag $parent = NULL) {
        $this->parent = $parent;

        $this->num_rows = 0;
        $this->num_cols = $num_cols;

        if (empty($this->parent)) {
            parent::__construct();
            for ($row = 1; $row <= $num_rows; $row++) {
                $this->append_row($num_cols, $row, $this);
            }
        } else {
//            $this->append_to($this->parent);
            $this->link_value_obj($parent);
            for ($row = 1; $row <= $num_rows; $row++) {
                $this->append_row($num_cols, $row, $this->parent);
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
     * @param int $num_cols
     * @param int $grid_row
     * @return \k1lib\html\foundation\grid_row
     */
    public function append_row($num_cols = NULL, $grid_row = NULL, $parent = NULL) {
        if ($num_cols === NULL) {
            $num_cols = $this->num_cols;
        }
        $row = new grid_row($num_cols, $grid_row, $parent);
        $this->rows[++$this->num_rows] = $row;
        return $row;
    }
}

class label_value_row extends grid_row {

    function __construct($label, $value, $grid_row = 0, $parent = NULL) {
        parent::__construct(2, $grid_row, $parent);
        $this->set_class('grid-margin-x', TRUE);
        $this->cell(1)->medium(3)->large(3);
        $this->cell(2)->medium(9)->large(9)->end();

        $this->cell(2)->remove_attribute_text("class", "end");

        $input_name = $this->get_name_attribute($value);

        if (method_exists($label, "generate")) {
            $small_label = clone $label;
            $this->cell(1)->append_child($label->set_class("k1lib-label-object right inline hide-for-small-only text-right"));
            $this->cell(1)->append_child($small_label->set_class("k1lib-label-object left show-for-small-only"));
        } else {
            $this->cell(1)->append_child(new \k1lib\html\label($label, $input_name, "k1lib-label-object right inline hide-for-small-only text-right"));
            $this->cell(1)->append_child(new \k1lib\html\label($label, $input_name, "k1lib-label-object left show-for-small-only"));
        }
        $this->cell(2)->set_value($value);
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
    protected $margin = '';

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

/**
 * 
 */
class accordion extends \k1lib\html\ul {

    use foundation_methods;

    /**
     *
     * @var array($tab_id, $tab_label) 
     */
    private $config_array = [];

    /**
     * @var \k1lib\html\div
     */
    private $tabs_content_container;

    /**
     * @var \k1lib\html\div
     */
    private $tabs_content = [];
    private $active_tab = 1;
    private $tab_count = 0;

    /**
     * generates an Foundation accordion
     * @param type $class
     * @param type $id
     */
    function __construct($class = null, $id = 'accordion', $class_config = 'small-accordion medium-tabs large-tabs') {

        parent::__construct('tabs', 'accordion-' . $id);
        $this->set_attrib('data-responsive-accordion-tabs', 'tabs ' . $class_config);
//        $this->append_to($content);
        $this->tabs_content_container = new \k1lib\html\div('tabs-content ' . $class, 'tabs-content-' . $id);
        $this->tabs_content_container->set_attrib('data-tabs-content', 'accordion-' . $id);
    }

    function set_config($confing_array) {
        $this->config_array = $confing_array;
        $c = 0;
        foreach ($this->config_array as $tab_id => $tab_label) {
            $c++;
            $this->new_li_tab($tab_id, $tab_label, $c);
//            $this->new_content_tab($tab_id, $c);
        }
    }

    /**
     * 
     * @param string $tab_id
     * @param string $value
     * @param int $c
     * @return \k1lib\html\div
     */
    function new_li_tab($tab_id, $value, $c = 0) {
        $this->tab_count++;
        if ($c == 0) {
            $c = $this->tab_count;
        }
        $this->append_li(null, ($c == $this->active_tab) ? 'tabs-title is-active' : 'tabs-title', 'acordeon-li-' . $tab_id)
                ->append_a('#' . 'accordion-content-' . urlencode($tab_id), strtoupper($value));
        return $this->new_content_tab($tab_id, $c);
    }

    /**
     * 
     * @param string $tab_id
     * @param int $c
     * @return \k1lib\html\div
     */
    private function new_content_tab($tab_id, $c = 0) {
        if ($c == 0) {
            $c = $this->tab_count;
        }
        $this->tabs_content[$tab_id] = new \k1lib\html\div(($c == $this->active_tab) ? 'tabs-panel is-active' : 'tabs-panel', 'accordion-content-' . urlencode($tab_id));
        $this->tabs_content[$tab_id]->append_to($this->tabs_content_container);
        return $this->tabs_content[$tab_id];
    }

    /**
     * @param string $tab_id
     * @return \k1lib\html\div
     */
    function tab_content($tab_id) {
        return $this->tabs_content[$tab_id];
    }

    /**
     * @return \k1lib\html\div
     */
    function tabs_content_container() {
        return $this->tabs_content_container;
    }

    function set_active_tab($active_tab): void {
        $this->active_tab = $active_tab;
    }

    /**
     * @param \k1lib\html
     * @return accordion
     */
    function append_to($html_object) {
        parent::append_to($html_object);
        $this->tabs_content_container->append_to($html_object);
        return $this;
    }
}
