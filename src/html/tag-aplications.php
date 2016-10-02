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
        $a = new \k1lib\html\a($href, $label, "_self", $label, "button $class", $id);
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

    function set_title($number, $value) {
        $elements = $this->parent->get_elements_by_class("k1app-title-{$number}");
        foreach ($elements as $element) {
            $element->set_value($value);
        }
    }

    function init_title_bar() {
        $title = $this->parent->append_div("title-bar")
                ->set_attrib("data-responsive-toggle", "responsive-menu")
                ->set_attrib("data-hide-for", "medium");
        $title->append_child((new \k1lib\html\button(null, "menu-icon"))->set_attrib("data-toggle", TRUE));

        $title_bar_title = $title->append_div("title-bar-title k1app-title-container");
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
