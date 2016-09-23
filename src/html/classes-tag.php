<?php

/**
 * HTML Classes for general purposes use
 */

namespace k1lib\html;

class tag_log {

    static protected $log;

    static function get_log() {
        return self::$log;
    }

    static function log($log) {
        self::$log .= $log . "\n";
    }

}

class tag_catalog {

    static protected $index = 0;
    static protected $catalog = [];

    static function get_index() {
        return self::$index;
    }

    static function index_exist($index) {
        if (isset(self::$catalog[$index])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function increase(tag $tag_object) {
        self::$index++;
        self::$catalog[self::$index] = $tag_object;
        return self::$index;
    }

    static function decatalog($tag_index) {
        if (isset(self::$catalog[$tag_index])) {
//            self::$catalog[$tag_index] = null;
            unset(self::$catalog[$tag_index]);
        }
    }

    static function get_catalog() {
        return self::$catalog;
    }

}

/**
 * HTML Tag abstraction
 */
class tag {

    use append_shotcuts;

    /** @var String */
    protected $tag_id = 0;

    /** @var String */
    protected $tag_name = NULL;

    /** @var Boolean */
    protected $is_selfclosed = FALSE;

    /** @var Array */
    protected $attributes = array();

    /** @var String */
    protected $attributes_code = "";

    /** @var String */
    protected $tag_code = "";

    /** @var String */
    protected $pre_code = "";

    /** @var String */
    protected $post_code = "";

    /** @var String */
    protected $value = "";

    /** @var Boolean */
    protected $has_child = FALSE;

    /** @var Array */
    protected $childs_head = array();

    /** @var Array */
    protected $childs = array();

    /** @var Array */
    protected $childs_tail = array();

    /** @var Integer */
    protected $child_level = 0;

    /** @var tag */
    protected $parent = null;

    /** @var boolean */
    static protected $use_log = false;

    /** @var tag; */
    protected $linked_html_obj = null;

    /**
     * Constructor with $tag_name and $selfclosed options for beginning
     * @param String $tag_name
     * @param Boolean $selfclosed Is self closed as <tag /> or tag closed one <tag></tag>
     */
    function __construct($tag_name, $selfclosed = TRUE) {
        if (!empty($tag_name) && is_string($tag_name)) {
            $this->tag_name = $tag_name;
        } else {
            trigger_error("TAG has to be string", E_USER_WARNING);
        }

        if (is_bool($selfclosed)) {
            $this->is_selfclosed = $selfclosed;
        } else {
            trigger_error("Self closed value has to be boolean", E_USER_WARNING);
        }
//            $this->set_attrib("class", "k1-{$tag_name}-object");
        // GET the global tag ID and catalog the object
        $this->tag_id = tag_catalog::increase($this);
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] id:{$this->tag_id} was created");
        }
    }

    function decatalog() {
        tag_catalog::decatalog($this->tag_id);
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] id:{$this->tag_id} was decataloged");
        }
        if ($this->has_child) {
            foreach ($this->childs as $child_object) {
                $child_object->decatalog();
            }
        }
    }

    function get_tag_id() {
        return $this->tag_id;
    }

    static function get_use_log() {
        return self::$use_log;
    }

    static function set_use_log($use_log) {
        self::$use_log = $use_log;
    }

    /**
     * @return tag 
     */
    function get_parent() {
        return $this->parent;
    }

    function set_parent(tag $parent) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] is child of [{$parent->get_tag_name()}]");
        }
        $this->parent = $parent;
    }

    public function __toString() {
        return $this->generate();
    }

    /**
     * Chains an HTML tag into the actual HTML tag on MAIN collection, by default will put on last 
     * position but with $put_last_position = FALSE will be the on first position
     * @param tag $child_object
     * @return tag 
     */
    public function append_child(tag $child_object, $put_last_position = TRUE) {
        $child_object->set_parent($this);
        if ($put_last_position) {
            $this->childs[$child_object->get_tag_id()] = $child_object;
        } else {
            array_unshift($this->childs, $child_object);
        }
        $this->has_child = TRUE;
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] appends [{$child_object->get_tag_name()}]");
        }
        return $child_object;
    }

    /**
     * Chains an HTML tag into the actual HTML tag on TAIL collection, by default will put on last 
     * position but with $put_last_position = FALSE will be the on first position
     * @param tag $child_object
     * @return tag 
     */
    public function append_child_tail(tag $child_object, $put_last_position = TRUE) {
        $child_object->set_parent($this);
        if ($put_last_position) {
            $this->childs_tail[$child_object->get_tag_id()] = $child_object;
        } else {
            array_unshift($this->childs_tail, $child_object);
        }
        $this->has_child = TRUE;
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] appends on tail of [{$child_object->get_tag_name()}]");
        }
        return $child_object;
    }

    /**
     * Chains an HTML tag into the actual HTML tag on HEAD collection, by default will put on last 
     * position but with $put_last_position = FALSE will be the on first position
     * @param tag $child_object
     * @return tag 
     */
    public function append_child_head(tag $child_object, $put_last_position = TRUE) {
        $child_object->set_parent($this);
        if ($put_last_position) {
            $this->childs_head[$child_object->get_tag_id()] = $child_object;
        } else {
            array_unshift($this->childs_head, $child_object);
        }
        $this->has_child = TRUE;
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] appends on head of [{$child_object->get_tag_name()}]");
        }
        return $child_object;
    }

    /**
     * Chains THIS HTML tag to a another HTML tag
     * @param tag $child_object
     * @return tag 
     */
    public function append_to($html_object) {
        if (html::get_use_log()) {
            tag_log::log("[{$html_object->get_tag_name()} will receive {$this->get_tag_name()}]");
            html::set_use_log(FALSE);
        }
        $html_object->append_child($this);
        if (html::get_use_log()) {
            html::set_use_log(TRUE);
        }
        return $this;
    }

    /**
     * Add free TEXT before the generated TAG
     * @param String $pre_code
     */
    function pre_code($pre_code) {
        $this->pre_code = $pre_code;
    }

    /**
     * Add free TEXT after the generated TAG
     * @param String $post_code
     */
    function post_code($post_code) {
        $this->post_code = $post_code;
    }

    /**
     * Set the VALUE for the TAG, as <TAG value="$value" /> or <TAG>$value</TAG>
     * @param String $value
     * @return tag
     */
    public function set_value($value, $append = false) {
        if (is_object($value)) {
            if (is_object($this->value)) {
                $this->value .= $value;
            } elseif (!empty($this->value)) {
                $this->value .= $value;
            } else {
                $this->value = $value;
            }
        } else {
            if (empty($this->linked_html_obj)) {
                $this->value = (($append === TRUE) && (!empty($this->value)) ) ? ($this->value . " " . $value) : ($value);
            } else {
                $this->linked_html_obj->value = (($append === TRUE) && (!empty($this->linked_html_obj->value)) ) ? ($this->linked_html_obj->value . " " . $value) : ($value);
            }
        }

        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] set value to: {$value}");
        }
        return $this;
    }

    /**
     * Links the value of the current object to a child one. The current WON't be used and the value will be placed on the link object.
     * @param tag $obj_to_link
     */
    public function link_value_obj(tag $obj_to_link) {
        $this->linked_html_obj = $obj_to_link;
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] is linked to [{$obj_to_link->get_tag_name()}]");
        }
    }

    /**
     * Return the reference for chained HTML tag object
     * @param Int $n Index beginning from 0
     * @return tag Returns FALSE if is not set
     */
    public function &get_child($n) {
        if (isset($this->childs[$n])) {
            return $this->childs[$n];
        } else {
            return FALSE;
        }
    }

    /**
     * Set an attribute with its value always overwriting if $append is not set TRUE to append old value with the recieved one.
     * @param String $attribute
     * @param String $value
     * @param Boolean $append
     * @return tag
     */
    public function set_attrib($attribute, $value, $append = FALSE) {
        if (!empty($attribute) && is_string($attribute)) {
            if (empty($this->linked_html_obj)) {
                $this->attributes[$attribute] = (($append === TRUE) && (!empty($this->attributes[$attribute])) ) ? ($this->attributes[$attribute] . " " . $value) : ($value);
            } else {
                $this->linked_html_obj->set_attrib($attribute, $value, $append);
            }
        } else {
            trigger_error("HTML ATTRIBUTE has to be string", E_USER_WARNING);
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] new attrib: {$attribute}={$value}");
        }

        return $this;
    }

    /**
     * Shortcut for $html->set_attrib("id",$id);
     * @param string $id
     * @return tag
     */
    public function set_id($id) {
        if (!empty($id)) {
            $this->set_attrib("id", $id);
        }
        return $this;
    }

    /**
     * Shortcut for $html->set_attrib("class",$class);
     * @param string $class
     * @return tag
     */
    public function set_class($class) {
        if (!empty($class)) {
            $this->set_attrib("class", $class);
        }
        return $this;
    }

    /**
     * If the attribute was set returns its value
     * @param String $attribute
     * @return String Returns FALSE if is not set
     */
    public function get_attribute($attribute) {
        if (isset($this->attributes[$attribute])) {
            return $this->attributes[$attribute];
        } else {
            return FALSE;
        }
    }

    /**
     * Gets the VALUE for the TAG, as <TAG value="$value" /> or <TAG>$value</TAG>
     * @return String
     */
    public function get_value() {
        if (is_object($this->value)) {
            return $this->value->generate();
        } else {
            return $this->value;
        }
    }

    /**
     * VALUE for the TAG, as <TAG attribute1="value1" .. attributeN="valueN" /> or <TAG attribute1="value1" .. attributeN="valueN">$value</TAG>
     * @param Boolean $do_echo
     * @return string Returns FALSE if is not attributes to generate
     */
    protected function generate_attributes_code() {
        if ($this->is_selfclosed && !empty($this->value)) {
            $this->set_attrib("value", $this->value);
        }

        $attributes_count = count($this->attributes);
        $current_attribute = 0;
        $attributes_code = "";

        if ($attributes_count != 0) {
            foreach ($this->attributes as $attribute => $value) {
                $current_attribute++;
                if ($value !== TRUE && $value !== FALSE) {
                    $attributes_code .= "{$attribute}=\"{$value}\"";
                } else {
                    if ($value === TRUE) {
                        $attributes_code .= "{$attribute}";
                    }
                }
                $attributes_code .= ($current_attribute < $attributes_count) ? " " : "";
            }
            $this->attributes_code = $attributes_code;
            return " " . $this->attributes_code;
        } else {
            return "";
        }
    }

    /**
     * This will generate the HTML TAG with ALL his childs by default. If the TAG is not SELF CLOSED will generate all as <TAG attributeN="valueN">$value</TAG>
     * @param Boolean $do_echo Do ECHO action or RETURN HTML
     * @param Boolean $with_childs
     * @param Int $n_childs
     * @return string Won't return any if is set $do_echo = TRUE
     */
    public function generate($do_echo = \FALSE, $with_childs = \TRUE, $n_childs = 0) {
        /**
         * Merge the child arrays HEAD, MAIN and TAIL collections
         */
        $this->childs = $this->get_all_childs();

        $object_childs = count($this->childs);

        /**
         * TAB constructor
         */
        $tabs = str_repeat("\t", $this->child_level);

        $new_line = ($this->child_level >= 1) ? "\n" : "";

        $html_code = "{$new_line}{$tabs}<{$this->tag_name}";
        $html_code .= $this->generate_attributes_code();
        if ($this->is_selfclosed) {
//                $html_code .= " /";
        }
        $html_code .= ">";

        $has_childs = FALSE;
        if (!$this->is_selfclosed) {
            if ($has_childs && !empty($this->value)) {
                $html_code .= "\n{$tabs}\t";
            }
            $html_code .= $this->get_value();
            if (($with_childs) && ($object_childs >= 1)) {
                $has_childs = TRUE;
                foreach ($this->childs as $child_object) {
                    if (tag_catalog::index_exist($child_object->get_tag_id())) {
                        $child_object->child_level = $this->child_level + 1;
                        $html_code .= $child_object->generate();
                    }
                }
            }
            if ($has_childs) {
                $html_code .= "\n";
            }
            $html_code .= $this->generate_close();
        }

        $this->tag_code = $this->pre_code . $html_code . $this->post_code;
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] is generated");
        }

        if ($do_echo) {
            echo $this->tag_code;
        } else {
            return $this->tag_code;
        }
    }

    /**
     * This will generate the HTML CLOSE TAG 
     * @param Boolean $do_echo Do ECHO action or RETURN HTML
     * @return string Won't return any if is set $do_echo = TRUE
     */
    protected function generate_close() {
        /**
         * TAB constructor
         */
        if (count($this->childs)) {
            $tabs = str_repeat("\t", $this->child_level);
        } else {
            $tabs = '';
        }
        $html_code = "{$tabs}</{$this->tag_name}>";
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] generated close tag");
        }

        return $html_code;
    }

    public function get_code() {
        return $this->tag_code;
    }

    public function get_tag_name() {
        return $this->tag_name;
    }

    /**
     * Return the FIRST object found with the $id
     * @param string $id
     * @return tag
     */
    public function get_element_by_id($id, $deep = 0) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] will SEARCH by ID='$id' on deep {$deep}");
        }
        if ($this->has_childs()) {
            $all_childs = $this->get_all_childs();
//            $child = new tag("dummy");
            /** @var tag */
            foreach ($all_childs as $child) {
                if ($child->get_attribute("id") == $id) {
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] has child [{$child->get_tag_name()}] with the ID='$id' and is returned");
                    }
                    return $child;
                } else {
                    if ($child->has_childs()) {
                        $child_get_by_id_result = $child->get_element_by_id($id, $deep + 1);
                        if (!empty($child_get_by_id_result)) {
                            return $child_get_by_id_result;
                        }
                    }
                }
            }
        }
        return FALSE;
    }

    /**
     * Return an Array with all the objects that TAG is $tag_name
     * @param string $tag_name
     * @return array|tag
     */
    public function get_elements_by_tag($tag_name, $deep = 0) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] will SEARCH by TAG='$tag_name' on deep {$deep}");
        }
        if ($this->has_childs()) {
            $all_childs = $this->get_all_childs();
//            $child = new tag("dummy");
            $tags = array();
            /** @var tag */
            foreach ($all_childs as $child) {
                if ($child->get_tag_name() == $tag_name) {
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] has child [{$child->get_tag_name()}] with the TAG='$tag_name' and is stored on array \$tags[] on deep={$deep}");
                    }
                    $tags[] = $child;
                } else {
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}]: [{$child->get_tag_name()}] IS NOT TAG='$tag_name'");
                    }
                }
                if ($child->has_childs()) {
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] will look on childs of [{$child->get_tag_name()}] childs for TAG='$tag_name'");
                    }

                    $child_get_by_tag_result = $child->get_elements_by_tag($tag_name, $deep + 1);
                    if (!empty($child_get_by_tag_result)) {
                        if (html::get_use_log()) {
                            tag_log::log("[{$this->get_tag_name()}] has child [{$child->get_tag_name()}] where FOUND " . count($child_get_by_tag_result) . " tags with name='$tag_name'");
                        }
                        $tags = array_merge($tags, $child_get_by_tag_result);
                    }
                }
            }
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] will return " . count($tags) . " tags with name='$tag_name'");
        }
        return $tags;
    }

    /**
     * Return an Array with all the objects that CLASS is $class_name
     * @param string $class_name
     * @return array|class
     */
    public function get_elements_by_class($class_name, $deep = 0) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] will SEARCH by CLASS='$class_name' on deep {$deep}");
        }
        if ($this->has_childs()) {
            $all_childs = $this->get_all_childs();
            $child = new tag("dummy");
            $classs = array();
            /** @var class */
            foreach ($all_childs as $child) {
                if ($child->get_attribute("class") == $class_name) {
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] has child [{$child->get_tag_name()}] with the CLASS='$class_name' and is stored on array \$classs[] on deep={$deep}");
                    }
                    $classs[] = $child;
                } else {
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}]: [{$child->get_tag_name()}] IS NOT CLASS='$class_name'");
                    }
                }
                if ($child->has_childs()) {
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] will look on childs of [{$child->get_tag_name()}] childs for CLASS='$class_name'");
                    }

                    $child_get_by_class_result = $child->get_elements_by_class($class_name, $deep + 1);
                    if (!empty($child_get_by_class_result)) {
                        if (html::get_use_log()) {
                            tag_log::log("[{$this->get_tag_name()}] has child [{$child->get_tag_name()}] where FOUND " . count($child_get_by_class_result) . " classs with name='$class_name'");
                        }
                        $classs = array_merge($classs, $child_get_by_class_result);
                    }
                }
            }
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] will return " . count($classs) . " classs with name='$class_name'");
        }
        return $classs;
    }

    function has_childs() {
        return $this->has_child;
    }

    /**
     * Merge and return the $childs_head, $childs and $childs_tail
     * @return array
     */
    protected function get_all_childs() {
        /**
         * Merge the child arrays HEAD, MAIN and TAIL collections
         */
        $merged_childs = [];
        if (!empty($this->childs_head)) {
            foreach ($this->childs_head as $child) {
                $merged_childs[] = $child;
            }
        }
        if (!empty($this->childs)) {
            foreach ($this->childs as $child) {
                $merged_childs[] = $child;
            }
        }
        if (!empty($this->childs_tail)) {
            foreach ($this->childs_tail as $child) {
                $merged_childs[] = $child;
            }
        }
        return $merged_childs;
    }

}

trait append_shotcuts {

    /**
     * 
     * @param string $class
     * @param string $id
     * @return div
     */
    function append_div($class = "", $id = "") {
        $new = new div($class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * 
     * @param string $class
     * @param string $id
     * @return span
     */
    function append_span($class = "", $id = "") {
        $new = new span($class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * 
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_p($value = "", $class = "", $id = "") {
        $new = new p($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * 
     * @param string $href
     * @param string $label
     * @param string $target
     * @param string $alt
     * @param string $class
     * @param string $id
     * @return a
     */
    function append_a($href = "", $label = "", $target = "", $alt = "", $class = "", $id = "") {
        $new = new a($href, $label, $target, $alt, $class, $id);
        $this->append_child($new);
        return $new;
    }

}

/**
 * HTML Classes for general propouses use
 */
class DOM {
//    use append_shotcuts;

    /**
     * @var html
     */
    static protected $html = null;

    static function start($lang = "en") {
        self::$html = new html($lang);
    }

    /**
     * @return html
     */
    static function html() {
        return self::$html;
    }

    static function generate() {
        return self::$html->generate();
    }

}

class html extends tag {

    use append_shotcuts;

    /**
     * @var head
     */
    protected $head = null;

    /**
     * @var body
     */
    private $body = null;

    function __construct($lang = "en") {
        parent::__construct("html", FALSE);
        $this->set_attrib("lang", $lang);
        $this->append_head();
        $this->append_body();
    }

    function append_head() {
        $this->head = new head();
        $this->append_child($this->head);
    }

    function append_body() {
        $this->body = new body();
        $this->append_child($this->body);
    }

    /**
     * @return head
     */
    function head() {
        return $this->head;
    }

    /**
     * @return body
     */
    function body() {
        return $this->body;
    }

}

class head extends tag {

    use append_shotcuts;

    /**
     * @var title
     */
    protected $title;

    function __construct() {
        parent::__construct("head", FALSE);
        $this->append_title();
    }

    /**
     * @return title
     */
    function append_title() {
        $this->title = new title();
        $this->append_child_head($this->title);
        return $this->title;
    }

    function set_title($document_title) {
        $this->title->set_value($document_title);
    }

    /**
     * @return link
     */
    function link_css($href = "") {
        $new = new link($href);
        $this->append_child_tail($new);
        return $new;
    }

    /**
     * 
     * @return meta
     */
    function append_meta($name = "", $content = "") {
        $new = new meta($name, $content);
        $this->append_child_tail($new);
        return $new;
    }

}

class title extends tag {

    use append_shotcuts;

    function __construct() {
        parent::__construct("title", FALSE);
    }

}

class section extends tag {

    use append_shotcuts;

    function __construct($id = "", $class = "") {
        parent::__construct("section", FALSE);
        if (!empty($id)) {
            $this->set_attrib("id", $id);
        }
        if (!empty($class)) {
            $this->set_attrib("class", $class);
        }
    }

}

class meta extends tag {

    use append_shotcuts;

    function __construct($name = "", $content = "") {
        parent::__construct("meta", FALSE);
        if (!empty($name)) {
            $this->set_attrib("name", $name);
        }
        if (!empty($content)) {
            $this->set_attrib("content", $content);
        }
    }

}

class body extends tag {

    use append_shotcuts;

    /**
     * @var section
     */
    protected $section_header = null;

    /**
     * @var section
     */
    protected $section_content = null;

    /**
     * @var section
     */
    protected $section_footer = null;

    function __construct() {
        parent::__construct("body", FALSE);
    }

    function init_sections() {
        $this->section_header = new section("k1app-header");
        $this->section_header->append_to($this);
        $this->section_content = new section("k1app-content");
        $this->section_content->append_to($this);
        $this->section_footer = new section("k1app-footer");
        $this->section_footer->append_to($this);
    }

    /**
     * return section
     */
    function header() {
        return $this->section_header;
    }

    /**
     * return section
     */
    function content() {
        return $this->section_content;
    }

    /**
     * return section
     */
    function footer() {
        return $this->section_footer;
    }

}

class a extends tag {

    use append_shotcuts;

    function __construct($href, $label, $target = "", $alt = "", $class = "", $id = "") {
        parent::__construct("a", FALSE);
        $this->set_attrib("href", $href);
        $this->set_value($label);
        $this->set_attrib("target", $target);
        $this->set_attrib("alt", $alt);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

class img extends tag {

    use append_shotcuts;

    function __construct($src = "", $alt = "", $class = "", $id = "") {
        parent::__construct("img", FALSE);
        $this->set_attrib("src", $src);
        $this->set_attrib("alt", $alt);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    function set_value($value, $append = FALSE) {
        $this->set_attrib("alt", $value, $append);
    }

}

class div extends tag {

    use append_shotcuts;

    /**
     * Create a DIV html tag with VALUE as data. Use $div->set_value($data)
     * @param String $class
     * @param String $id
     */
    function __construct($class = "", $id = "") {
        parent::__construct("div", FALSE);
//        $this->data_array &= $data_array;
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

class ul extends tag {

    use append_shotcuts;

    /**
     * Create a UL html tag.
     * @param String $class
     * @param String $id
     */
    function __construct($class = "", $id = "") {
        parent::__construct("ul", FALSE);
//        $this->data_array &= $data_array;
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    function set_value($value, $append = false) {
//            parent::set_value($value, $append);
    }

    /**
     * 
     * @param string $class
     * @param string $id
     * @return li
     */
    function &append_li($class = "", $id = "") {
        $new = new li($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

}

class ol extends tag {

    use append_shotcuts;

    /**
     * Create a UL html tag.
     * @param String $class
     * @param String $id
     */
    function __construct($class = "", $id = "") {
        parent::__construct("ol", FALSE);
//        $this->data_array &= $data_array;
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    function set_value($value, $append = false) {
//            parent::set_value($value, $append);
    }

    /**
     * 
     * @param string $class
     * @param string $id
     * @return li
     */
    function &append_li($value = "", $class = "", $id = "") {
        $new = new li($value, $class, $id);
        $this->set_value($value);
        $this->append_child($new);
        return $new;
    }

}

class link extends tag {

    use append_shotcuts;

    function __construct($href) {
        parent::__construct("link");
        if (!empty($href)) {
            $this->set_attrib("rel", "stylesheet");
            $this->set_attrib("type", "text/css");
            $this->set_attrib("href", $href);
        }
    }

}

class li extends tag {

    use append_shotcuts;

    /**
     * Create a LI html tag with VALUE as data. Use $div->set_value($data)
     * @param String $class
     * @param String $id
     */
    function __construct($value = "", $class = "", $id = "") {
        parent::__construct("li", FALSE);
//        $this->data_array &= $data_array;
        $this->set_value($value);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    /**
     * 
     * @param string $class
     * @param string $id
     * @return ul
     */
    function &append_ul($class = "", $id = "") {
        $new = new ul($class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * 
     * @param string $class
     * @param string $id
     * @return div
     */
    function &append_ol($class = "", $id = "") {
        $new = new ol($class, $id);
        $this->append_child($new);
        return $new;
    }

}

class script extends tag {

    use append_shotcuts;

    /**
     * Create a SCRIPT html tag with VALUE as data. Use $script->set_value($data)
     * @param String $class
     * @param String $id
     */
    function __construct($src = "") {
        parent::__construct("script", FALSE);
        if (!empty($src)) {
            $this->set_attrib("src", $src);
        }
    }

}

class small extends tag {

    use append_shotcuts;

    /**
     * Create a SMALL html tag with VALUE as data. Use $small->set_value($data)
     * @param String $class
     * @param String $id
     */
    function __construct($class = "", $id = "") {
        parent::__construct("small", FALSE);
//        $this->data_array &= $data_array;
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

class span extends tag {

    use append_shotcuts;

    /**
     * Create a SPAN html tag with VALUE as data. Use $span->set_value($data)
     * @param String $class
     * @param String $id
     */
    function __construct($class = "", $id = "") {
        parent::__construct("span", FALSE);
//        $this->data_array &= $data_array;
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

class table extends tag {

    use append_shotcuts;

//    private $data_array = array();
    /**
     * @param String $class
     * @param String $id
     */
    function __construct($class = "", $id = "") {
        parent::__construct("table", FALSE);
//        $this->data_array &= $data_array;
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    /**
     * Chains a new <THEAD> HTML TAG
     * @param String $class
     * @param String $id
     * @return thead
     */
    function &append_thead($class = "", $id = "") {
        $child_object = new thead($class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

    /**
     * Chains a new <TBODY> HTML TAG
     * @param String $class
     * @param String $id
     * @return tbody
     */
    function &append_tbody($class = "", $id = "") {
        $child_object = new tbody($class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

}

class thead extends tag {

    use append_shotcuts;

    /**
     * @param String $class
     * @param String $id
     */
    function __construct($class = "", $id = "") {
        parent::__construct("thead", FALSE);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    /**
     * Chains a new <TR> HTML TAG
     * @param String $class
     * @param String $id
     * @return tr
     */
    function append_tr($class = "", $id = "") {
        $child_object = new tr($class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

}

class tbody extends tag {

    use append_shotcuts;

    /**
     * @param String $class
     * @param String $id
     */
    function __construct($class = "", $id = "") {
        parent::__construct("tbody", FALSE);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    /**
     * Chains a new <TR> HTML TAG
     * @param String $class
     * @param String $id
     * @return tr
     */
    function append_tr($class = "", $id = "") {
        $child_object = new tr($class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

}

class tr extends tag {

    use append_shotcuts;

    /**
     * @param String $class
     * @param String $id
     */
    function __construct($class = "", $id = "") {
        parent::__construct("tr", FALSE);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    /**
     * Chains a new <TH> HTML TAG
     * @param String $value <TAG>$value</TAG>
     * @param String $class
     * @param String $id
     * @return th
     */
    function append_th($value, $class = "", $id = "") {
        $child_object = new th($value, $class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

    /**
     * Chains a new <TD> HTML TAG
     * @param String $value <TAG>$value</TAG>
     * @param String $class
     * @param String $id
     * @return td
     */
    function append_td($value, $class = "", $id = "") {
        $child_object = new td($value, $class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

}

class th extends tag {

    use append_shotcuts;

    /**
     * @param String $value <TAG>$value</TAG>
     * @param String $class
     * @param String $id
     */
    function __construct($value, $class = "", $id = "") {
        parent::__construct("th", FALSE);
        $this->set_value($value);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

class td extends tag {

    use append_shotcuts;

    /**
     * @param String $value <TAG>$value</TAG>
     * @param String $class
     * @param String $id
     */
    function __construct($value, $class = "", $id = "") {
        parent::__construct("td", FALSE);
        $this->set_value($value);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

class input extends tag {

    use append_shotcuts;

    /**
     * @param String $type Should be HTML standars: text, button.... 
     * @param String $name
     * @param String $value <TAG value='$value' />
     * @param String $class
     * @param String $id
     */
    function __construct($type, $name, $value, $class = "", $id = "") {
        parent::__construct("input", TRUE);
        $this->set_attrib("type", $type);
        $this->set_attrib("name", $name);
        $this->set_value($value);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

class textarea extends tag {

    use append_shotcuts;

    /**
     * 
     * @param string $name
     * @param string $class
     * @param string $id
     */
    function __construct($name, $class = "", $id = "") {
        parent::__construct("textarea", FALSE);
        $this->set_attrib("name", $name);
        $this->set_class($class, TRUE);
        $this->set_id($id);
        $this->set_attrib("rows", 10);
    }

}

class label extends tag {

    use append_shotcuts;

    /**
     * @param String $label <TAG>$value</TAG>
     * @param String $for
     * @param String $class
     * @param String $id
     */
    function __construct($label, $for, $class = "", $id = "") {
        parent::__construct("label", FALSE);
        $this->set_value($label);
        $this->set_attrib("for", $for);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

class select extends tag {

    use append_shotcuts;

    /**
     * @param String $name
     * @param String $class
     * @param String $id
     */
    function __construct($name, $class = "", $id = "") {
        parent::__construct("select", FALSE);
        $this->set_attrib("name", $name);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    /**
     * Chains a new <OPTION> HTML TAG
     * @param String $value
     * @param String $label
     * @param Boolean $selected
     * @param String $class
     * @param String $id
     * @return option
     */
    function append_option($value, $label, $selected = FALSE, $class = "", $id = "") {
        $child_object = new option($value, $label, $selected, $class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

}

class option extends tag {

    use append_shotcuts;

    /**
     * @param String $value <TAG value='$value' />
     * @param String $label <TAG>$label</TAG>
     * @param Boolean $selected
     * @param String $class
     * @param String $id
     */
    function __construct($value, $label, $selected = FALSE, $class = "", $id = "") {
        parent::__construct("option", FALSE);
        $this->set_value($label);
        $this->set_attrib("value", $value);
        $this->set_attrib("selected", $selected);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

/**
 * FORM
 */
class form extends tag {

    use append_shotcuts;

    function __construct($id = "k1-form") {
        parent::__construct("form", FALSE);
        $this->set_id($id);
        $this->set_attrib("name", "k1-form");
        $this->set_attrib("method", "post");
        $this->set_attrib("autocomplete", "yes");
        $this->set_attrib("enctype", "multipart/form-data");
        $this->set_attrib("novalidate", FALSE);
        $this->set_attrib("target", "_self");
    }

    /**
     * 
     * @param string $label
     * @param boolean $just_return
     * @return input
     */
    function append_submit_button($label = "Enviar", $just_return = FALSE) {
        $button = new input("submit", "submit-it", $label, "button success");
        if (!$just_return) {
            $this->append_child($button);
        }
        return $button;
    }

}

/**
 * P
 */
class p extends tag {

    use append_shotcuts;

    function __construct($value = "", $class = "", $id = "") {
        parent::__construct("p", FALSE);
        $this->set_value($value);
        $this->set_class($class);
        $this->set_id($id);
    }

}

/**
 * h1
 */
class h1 extends tag {

    use append_shotcuts;

    function __construct($value = "", $class = "") {
        parent::__construct("h1", FALSE);
        $this->set_value($value);
        $this->set_class($class);
    }

}

/**
 * h2
 */
class h2 extends tag {

    use append_shotcuts;

    function __construct($value = "", $class = "") {
        parent::__construct("h2", FALSE);
        $this->set_value($value);
        $this->set_class($class);
    }

}

/**
 * h3
 */
class h3 extends tag {

    use append_shotcuts;

    function __construct($value = "", $class = "") {
        parent::__construct("h3", FALSE);
        $this->set_value($value);
        $this->set_class($class);
    }

}

/**
 * h4
 */
class h4 extends tag {

    use append_shotcuts;

    function __construct($value = "", $class = "") {
        parent::__construct("h4", FALSE);
        $this->set_value($value);
        $this->set_class($class);
    }

}

/**
 * h5
 */
class h5 extends tag {

    use append_shotcuts;

    function __construct($value = "", $class = "") {
        parent::__construct("h5", FALSE);
        $this->set_value($value);
        $this->set_class($class);
    }

}

/**
 * P
 */
class fieldset extends tag {

    use append_shotcuts;

    function __construct($legend) {
        parent::__construct("fieldset", FALSE);
        $this->set_class("fieldset");
        $legend = new legend($legend);
        $this->append_child($legend);
    }

}

class legend extends tag {

    use append_shotcuts;

    function __construct($value) {
        parent::__construct("legend", FALSE);
        $this->set_value($value);
    }

}

class pre extends tag {

    use append_shotcuts;

    function __construct($value) {
        parent::__construct("pre", FALSE);
        $this->set_value($value);
    }

}
