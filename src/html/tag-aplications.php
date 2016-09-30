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

namespace k1lib\html;

class top_bar extends tag {

    use append_shotcuts;

    /**
     * @var tag
     */
    protected $parent;

    /**
     * @var ul
     */
    protected $menu_left;

    /**
     * @var ul
     */
    protected $menu_right;

    function __construct(tag $parent, $id = "") {

        $this->parent = $parent;
        $this->init_title_bar();

        parent::__construct("div", FALSE);
        $this->set_class("top-bar", TRUE);
        $this->set_id("responsive-menu");
        $this->append_to($parent);

        $left = $this->append_div("top-bar-left");

        $this->menu_left = new ul("dropdown menu", "k1app-menu-left");
        $this->menu_left->append_to($left);
        $this->menu_left->set_attrib("data-dropdown-menu", TRUE);

        $li = $this->menu_left->append_li(null, "menu-text k1app-title-container");
        $li->append_span("k1app-title-1");
        $li->append_span("k1app-title-2");
        $li->append_span("k1app-title-3");


        $right = $this->append_div("top-bar-right");

        $this->menu_right = new ul("menu", "k1app-menu-right");
        $this->menu_right->append_to($right);
    }

    function add_button($href, $label, $class = null, $id = null) {
        $this->menu_right->append_li()->append_child(new a($href, $label, "_self", $label, "button $class", $id));
//        $this->menu_right->append_li()->append_child(new button("Salir", "button alert", "btn-logout"));
    }

    /**
     * @param string $href
     * @param string $label
     * @return li
     */
    function add_menu_item($href, $label, tag $where = null) {
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
     * @return li
     */
    function add_sub_menu(li $where, $href, $label) {
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
        $title->append_child((new button(null, "menu-icon"))->set_attrib("data-toggle", TRUE));

        $title_bar_title = $title->append_div("title-bar-title k1app-title-container");
        $title_bar_title->append_span("k1app-title-1");
        $title_bar_title->append_span("k1app-title-2");
        $title_bar_title->append_span("k1app-title-3");
    }

}
