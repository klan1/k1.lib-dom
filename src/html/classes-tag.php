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

/**
 * Static Class to log all the Class tag actions 
 */
class tag_log {

    /**
     * @var string A simple log, each line is an action.
     */
    static protected $log;

    /**
     * Return the Log as string
     * @return string
     */
    static function get_log() {
        return htmlspecialchars(self::$log);
    }

    /**
     * Receive 1 action, do not need New Line at end.
     * @param string $log 
     */
    static function log($log) {
        self::$log .= $log . "\n";
    }

}

/**
 * Holds all the tags created with the tag Class.
 */
class tag_catalog {

    /**
     * @var integer 
     */
    static protected $catalog = [];

    /**
     * @var array 
     */
    static protected $index = 0;

    /**
     * Gets the actual index position.
     * @return integer
     */
    static function get_index() {
        return self::$index;
    }

    /**
     * Get a tag Object form catalog using the ID to search on Catalog index
     * @param integer $index
     * @return tag|NULL
     */
    static function get_by_index($index) {
        if (self::index_exist($index)) {
            return self::$catalog[$index];
        } else {
            return NULL;
        }
    }

    /**
     * Checks if and index exist. If the tag have been decataloged wont be found.
     * @param integer $index
     * @return boolean
     */
    static function index_exist($index) {
        if (isset(self::$catalog[$index])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Increase the index value and returns the new index value.
     * @param \k1lib\html\tag $tag_object
     * @return integer
     */
    static function increase(tag $tag_object) {
        self::$index++;
        self::$catalog[self::$index] = $tag_object;
        return self::$index;
    }

    /**
     * Remove the tag Object from the Array catalog, this will disable the 
     * Object to be found or generated on chain actions.
     * @param integer|\k1lib\html\tag $tag_index
     */
    static function decatalog($tag_index) {
        if (is_object($tag_index) && method_exists($tag_index, "get_tag_id")) {
            $tag_index = $tag_index->get_tag_id();
        }
        if (isset(self::$catalog[$tag_index])) {
//            self::$catalog[$tag_index] = NULL;
            unset(self::$catalog[$tag_index]);
        }
    }

    /**
     * Returns all the tag Object Catalog Array
     * @return tag[]
     */
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
    protected $is_self_closed = FALSE;

    /** @var Boolean */
    protected $is_inline = FALSE;

    /** @var Boolean */
    protected $inside_inline = FALSE;

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

    /** @var tag[] */
    protected $childs = array();

    /** @var Array */
    protected $childs_tail = array();

    /** @var Integer */
    protected $child_level = 0;

    /** @var tag */
    protected $parent = NULL;

    /** @var boolean */
    static protected $use_log = FALSE;

    /** @var tag; */
    protected $this_link = NULL;

    /**
     * Constructor with $tag_name and $self_closed options for beginning
     * @param String $tag_name
     * @param Boolean $self_closed Is self closed as <tag> or tag closed one <tag></tag>
     */
    function __construct($tag_name, $self_closed = IS_SELF_CLOSED) {
        if (!empty($tag_name) && is_string($tag_name)) {
            $this->tag_name = $tag_name;
        } else {
            trigger_error("TAG has to be string", E_USER_WARNING);
        }

        if (is_bool($self_closed)) {
            $this->is_self_closed = $self_closed;
        } else {
            trigger_error("Self closed value has to be boolean", E_USER_WARNING);
        }
//            $this->set_attrib("class", "k1lib-{$tag_name}-object");
// GET the global tag ID and catalog the object
        $this->tag_id = tag_catalog::increase($this);
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} was created");
        }
    }

    function __clone() {
        $this->tag_id = tag_catalog::increase($this);
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} was cloned");
        }
    }

    /**
     * Remove the tag Object from the Array catalog, this will disable the 
     * Object to be found or generated on chain actions.     
     */
    function decatalog() {
// Itself from Catalog
        tag_catalog::decatalog($this->tag_id);
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} was decataloged");
        }
// His childs
        if ($this->has_child) {
            foreach ($this->childs as $child_object) {
                $child_object->decatalog();
            }
        }
// Inline objects
        foreach ($this->get_inline_tags() as $tag) {
            $tag->decatalog();
        }
    }

    /**
     * Get the catalog index (an unique id) for this tag Object or NULL if the 
     * Object has been decataloged
     * @return integer|NULL
     */
    function get_tag_id() {
        if (tag_catalog::index_exist($this->tag_id)) {
            return $this->tag_id;
        } else {
            NULL;
        }
    }

    /**
     * Whatever or not EVERY tag Object created will use the log system
     * @return boolean
     */
    static function get_use_log() {
        return self::$use_log;
    }

    static function set_use_log($use_log) {
        self::$use_log = $use_log;
    }

    /**
     * Return the parent tag Object.
     * @return \k1lib\html\tag|NULL
     */
    function get_parent() {
        return $this->parent;
    }

    /**
     * Chains the parent tag Object
     * @param \k1lib\html\tag $parent
     */
    function set_parent(tag $parent) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is child of [{$parent->get_tag_name()}] ID:{$parent->tag_id} ");
        }
        $this->parent = $parent;
    }

    /**
     * When the tag Object is used as string, maybe as inline on text it
     * will be returned as {{ID:1..}} to converted when the container Object is
     * generated 
     * @return string
     */
    public function __toString() {
        if ($this->get_tag_id()) {
            if (html::get_use_log()) {
                tag_log::log("[{$this->get_tag_name()}] is returned for inline use");
            }
            return "{{ID:" . $this->get_tag_id() . "}}";
        } else {
            return "";
        }
    }

//    /**
//     * Wherever the tag Object is used as string, will be returned as 
//     * the generated tag
//     * @return string
//     */
//    public function __toString() {
//        return $this->generate();
//    }

    /**
     * Chains an HTML tag into the actual HTML tag on MAIN collection, by default will put on last 
     * position but with $put_last_position = FALSE will be the on first position
     * @param tag $child_object
     * @return tag 
     */
    public function append_child(tag $child_object, $put_last_position = TRUE, $tag_position = APPEND_ON_MAIN) {
        $child_object->set_parent($this);
        if ($put_last_position) {
            switch ($tag_position) {
                case APPEND_ON_HEAD:
                    $this->childs_head[] = $child_object;
                    break;
                case APPEND_ON_TAIL:
                    $this->childs_tail[] = $child_object;
                    break;
                default:
                    $this->childs[] = $child_object;
                    break;
            }
        } else {
            switch ($tag_position) {
                case APPEND_ON_HEAD:
                    array_unshift($this->childs_head, $child_object);
                    break;
                case APPEND_ON_TAIL:
                    array_unshift($this->childs_tail, $child_object);
                    break;
                default:
                    array_unshift($this->childs, $child_object);
                    break;
            }
        }
        $this->has_child = TRUE;
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} appends [{$child_object->get_tag_name()}] ID:{$child_object->tag_id} ");
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
        $this->append_child($child_object, $put_last_position, APPEND_ON_HEAD);
        return $child_object;
    }

    /**
     * Chains an HTML tag into the actual HTML tag on TAIL collection, by default will put on last 
     * position but with $put_last_position = FALSE will be the on first position
     * @param tag $child_object
     * @return tag 
     */
    public function append_child_tail(tag $child_object, $put_last_position = TRUE) {
        $this->append_child($child_object, $put_last_position, APPEND_ON_TAIL);
        return $child_object;
    }

    /**
     * Chains THIS HTML tag to a another HTML tag
     * @param tag $child_object
     * @return tag 
     */
    public function append_to($html_object) {
        $html_object->append_child($this);
        return $this;
    }

    /**
     * @return tag
     */
    public function remove_childs() {
        foreach ($this->childs as $key => $child) {
            unset($this->childs[$key]);
            $child->decatalog();
        }
        foreach ($this->childs_head as $key => $child) {
            unset($this->childs_head[$key]);
            $child->decatalog();
        }
        foreach ($this->childs_tail as $key => $child) {
            unset($this->childs_tail[$key]);
            $child->decatalog();
        }
        $this->has_child = FALSE;
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
    public function set_value($value, $append = FALSE) {

        if (empty($this->this_link)) {
            if (!empty($value)) {
                $this->value = ($append === TRUE) ? ($this->value . " " . $value) : ("$value");
            }
        } else {
            $this->this_link->set_value((($append === TRUE) && (!empty($this->this_link->get_value())) ) ? ($this->this_link->get_value() . " " . $value) : ("$value"));
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} set value to: {$value}");
        }
        return $this;
    }

    /**
     * Links the value of the current object to a child one. The current WON't be used and the value will be placed on the link object.
     * @param tag $obj_to_link
     */
    public function link_value_obj(tag $obj_to_link) {
        $this->this_link = $obj_to_link;
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is linked to [{$obj_to_link->get_tag_name()}]");
        }
    }

    /**
     * Return the reference for chained HTML tag object
     * @param Int $n Index beginning from 0
     * @return tag Returns FALSE if is not set
     */
    public function get_child($n) {
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
            if (empty($this->this_link)) {
                if ($value !== NULL) {
                    if (($append === TRUE) && (!empty($this->attributes[$attribute]))) {
                        $this->attributes[$attribute] = $this->attributes[$attribute] . " " . $value;
                    } else {
                        $this->attributes[$attribute] = $value;
                    }
                }
            } else {
                $this->this_link->set_attrib($attribute, $value, $append);
            }
        } else {
            trigger_error("HTML ATTRIBUTE has to be string", E_USER_WARNING);
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} new attrib: {$attribute}={$value}");
        }
        return $this;
    }

    public function remove_attrib($attribute) {
        if (isset($this->attributes[$attribute])) {
            unset($this->attributes[$attribute]);
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

    /**
     * Shortcut for $html->set_attrib("id",$id);
     * @param string $id
     * @return tag
     */
    public function set_id($id, $append = FALSE) {
        if (!empty($id)) {
            $this->set_attrib("id", $id, $append);
        }
        return $this;
    }

    /**
     * Shortcut for $html->set_attrib("class",$class);
     * @param string $class
     * @return tag
     */
    public function set_class($class, $append = FALSE) {
        if (!empty($class)) {
            $this->set_attrib("class", $class, $append);
        }
        return $this;
    }

    /**
     * Shortcut for $html->set_attrib("style",$style);
     * @param string $style
     * @return tag
     */
    public function set_style($style, $append = FALSE) {
        if (!empty($style)) {
            $this->set_attrib("style", $style, $append);
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

    public function get_attributes_array() {
        return $this->attributes;
    }

    /**
     * Gets the VALUE for the TAG, as <TAG value="$value" /> or <TAG>$value</TAG>
     * @return String
     */
    public function get_value($current_child_level = 0) {
        if (is_object($this->value)) {
            trigger_error("This shouldn't be used more", E_USER_NOTICE);
            return $this->get_value();
        } else {
            $this->parse_value($current_child_level);
            return $this->value;
        }
    }

    /**
     * Generate inline tag Objects on the value property
     */
    public function parse_value($current_child_level = 0) {
        $value_original = $this->value;
        foreach ($this->get_inline_ids() as $tag_id) {
            if (tag_catalog::index_exist($tag_id)) {
                $tag_string = "{{ID:" . $tag_id . "}}";
                tag_catalog::get_by_index($tag_id)->child_level = $current_child_level + 1;
                tag_catalog::get_by_index($tag_id)->is_inline = TRUE;
                $this->value = str_replace($tag_string, tag_catalog::get_by_index($tag_id)->generate(), $this->value);
            }
        }
        if ($value_original !== $this->value) {
            $this->has_child = TRUE;
        }
    }

    /**
     * Returns an Array with the ID list found on $this->value
     * @return integer[]
     */
    public function get_inline_ids() {
        $regexp = "/\{\{ID:(\d*)\}\}/";
        $matches = [];
        $cataloged = [];
        if (preg_match_all($regexp, $this->value, $matches)) {
            foreach ($matches[1] as $tag_id) {
                if (tag_catalog::index_exist($tag_id)) {
                    $cataloged[] = $tag_id;
                }
            }
        }
        return $cataloged;
    }

    /**
     * Returns an Array with the tag Objects found on $this->value
     * 
     * @return tag[]
     */
// TODO: Fix the error!
    public function get_inline_tags() {
        $regexp = "/\{\{ID:(\d*)\}\}/";
        $matches = [];
        $tags = [];
        if (preg_match_all($regexp, $this->value, $matches)) {
            foreach ($matches[1] as $tag_id) {
                if (tag_catalog::index_exist($tag_id)) {
                    $tags[] = tag_catalog::get_by_index($tag_id);
                }
            }
        }
        return $tags;
    }

    /**
     * VALUE for the TAG, as <TAG attribute1="value1" .. attributeN="valueN" /> or <TAG attribute1="value1" .. attributeN="valueN">$value</TAG>
     * @param Boolean $do_echo
     * @return string Returns FALSE if is not attributes to generate
     */
    protected function generate_attributes_code() {
        if ($this->is_self_closed && !empty($this->value)) {
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
    public function generate($with_childs = \TRUE, $n_childs = 0) {
        /**
         * Merge the child arrays HEAD, MAIN and TAIL collections
         */
        $this->childs = $this->get_all_childs();

        $object_childs = count($this->childs);

        /**
         * TAB constructor
         */
        $tabs = str_repeat("\t", $this->child_level);
        /**
         * NL manager :)
         */
        $new_line = ($this->child_level >= 1) ? "\n" : "";

        $html_code = "{$new_line}{$tabs}<{$this->tag_name}";
        $html_code .= $this->generate_attributes_code();
        $html_code .= ">";

        $has_childs = FALSE;
        if (!$this->is_self_closed) {
//            if ($has_childs && empty($this->value)) {
//                $html_code .= "\n{$tabs}\t";
//            }
            // VALUE first, then child objects
            $html_code .= $this->get_value($this->child_level);
            // Child objetcs generation
            if (($with_childs) && ($object_childs >= 1)) {
                $has_childs = TRUE;
                foreach ($this->childs as $child_object) {
                    if ($child_object->get_tag_id()) {
                        $child_object->child_level = $this->child_level + 1;
                        $html_code .= $child_object->generate();
                    }
                }
            }
            if ($has_childs || $this->has_child) {
                $html_code .= "\n";
            }
            $html_code .= $this->generate_close();
        }
        // TODO: Fix this!! please no more pre_code and post_code
        $this->tag_code = $this->pre_code . $html_code . $this->post_code;
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] is generated");
        }

        return $this->tag_code;
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
        if (($this->child_level > 0) && $this->has_child) {
            $tabs = str_repeat("\t", $this->child_level);
        } else {
            $tabs = '';
        }
        $html_code = "{$tabs}</{$this->tag_name}>";
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] close tag generated");
        }

        return $html_code;
    }

    /**
     * Returns the tag name. <tag name> or <tag name></tag name>
     * @return string
     */
    public function get_tag_name() {
        return $this->tag_name;
    }

    /**
     * Return the FIRST object found with the $id
     * @param string $id
     * @return tag|NULL
     */
    public function get_element_by_id($id) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will SEARCH by ID='$id'");
        }
        if ($this->get_tag_id()) {
            if ($this->get_attribute("id") == $id) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} has the ID='$id' and is returned");
                }
                return $this;
            } else {
                $inline_tags = $this->get_inline_tags();
                $all_childs = $this->get_all_childs();
                $all_childs = array_merge($inline_tags, $all_childs);
                foreach ($all_childs as $child) {
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will SEARCH by ID='$id' on child [{$child->get_tag_name()}] ID:{$child->tag_id}");
                    }
                    $child_search_result = $child->get_element_by_id($id);
                    if (!empty($child_search_result)) {
                        if (html::get_use_log()) {
                            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} has child [{$child->get_tag_name()}] ID:{$child->tag_id} with the ID='$id' and is returned");
                        }
                        return $child_search_result;
                    }
                }
            }
        } else {
            return NULL;
        }
    }

    /**
     * Return an Array with all the objects that TAG is $tag_name
     * @param string $tag_name
     * @return tag[]
     */
    public function get_elements_by_tag($tag_name) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will SEARCH by TAG='$tag_name'");
        }
        $tags = [];
        if ($this->get_tag_id()) {
            if ($this->get_tag_name() == $tag_name) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned");
                }
                $tags[] = $this;
            }
            /**
             * Child and inline tags
             */
            $inline_tags = $this->get_inline_tags();
            $all_childs = $this->get_all_childs();
            $all_childs = array_merge($inline_tags, $all_childs);
            foreach ($all_childs as $child) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} looking on child [{$child->get_tag_name()}] ID:{$child->tag_id}");
                }
                $child_search_result = $child->get_elements_by_tag($tag_name);
                if (!empty($child_search_result)) {
//                        print_r($child_search_result);
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return child [{$child->get_tag_name()}] ID:{$child->tag_id} results");
                    }
                    $tags = array_merge($tags, $child_search_result);
                }
            }
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return " . count($tags) . " '$tag_name' tags");
        }
        return $tags;
    }

    /**
     * Return an Array with all the objects that has ATTRIBUTE as $attribute_name
     * @param string $attribute_name
     * @param boolean $partial_text_search
     * @return tag[]
     */
    public function get_elements_by_attrib($attribute_name, $partial_text_search = FALSE) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will SEARCH by ATTRIB='$attribute_name'");
        }
        $tags = [];
        if ($this->get_tag_id()) {
            if (array_key_exists($attribute_name, $this->attributes) && !$partial_text_search) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned by exact match");
                }
                $tags[] = $this;
            } elseif ($partial_text_search) {
                foreach ($this->attributes as $attribute => $value) {
                    if (strstr($attribute, $attribute_name) !== FALSE) {
                        if (html::get_use_log()) {
                            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned by partial match");
                        }
                        $tags[] = $this;
                    }
                }
            }
            /**
             * Child and inline tags
             */
            $inline_tags = $this->get_inline_tags();
            $all_childs = $this->get_all_childs();
            $all_childs = array_merge($inline_tags, $all_childs);
            foreach ($all_childs as $child) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} looking on child [{$child->get_tag_name()}] ID:{$child->tag_id}");
                }
                $child_search_result = $child->get_elements_by_attrib($attribute_name);
                if (!empty($child_search_result)) {
//                        print_r($child_search_result);
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return child [{$child->get_tag_name()}] ID:{$child->tag_id} results");
                    }
                    $tags = array_merge($tags, $child_search_result);
                }
            }
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return " . count($tags) . " '$attribute_name' attribute");
        }
        return $tags;
    }

    /**
     * Return an Array with all the objects that has ATTRIBUTE as $attribute_name
     * @param string $attribute_name
     * @param boolean $partial_text_search
     * @return tag[]
     */
    public function get_elements_by_attrib_value($attribute_name, $attribute_value, $partial_attribute_text_search = FALSE, $partial_value_text_search = FALSE) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will SEARCH by ATTRIB='$attribute_name' and VALUE='$attribute_value'");
        }
        $tags = [];
        if ($this->get_tag_id()) {
            $tag_has_attribute = $this->get_elements_by_attrib($attribute_name, $partial_attribute_text_search);
            if (!empty($tag_has_attribute) && $partial_attribute_text_search) {
                foreach ($tag_has_attribute as $tag_to_look) {
                    $tag_attributes = $tag_to_look->get_attributes_array();
                    foreach ($tag_attributes as $attribute => $value) {
                        if (strstr($attribute, $attribute_name) !== FALSE) {
                            if ($partial_value_text_search && strstr($value, $attribute_value)) {
                                if (html::get_use_log()) {
                                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned by partial text and partial attrib match");
                                }
                                $tags[] = $tag_to_look;
                            } elseif ($attribute_value == $value) {
                                if (html::get_use_log()) {
                                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned by exact text and partial attrib match");
                                }
                                $tags[] = $tag_to_look;
                            }
                        }
                    }
                }
            } else if (!empty($tag_has_attribute) && !$partial_attribute_text_search) {
                foreach ($tag_has_attribute as $tag_to_look) {
                    $tag_attribute_value = $tag_to_look->get_attribute($attribute_name);
                    if ($partial_value_text_search && (strstr($tag_attribute_value, $attribute_value) !== FALSE)) {
                        if (html::get_use_log()) {
                            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned by partial text and exact attrib match");
                        }
                        $tags[] = $tag_to_look;
                    } elseif ($tag_attribute_value == $attribute_value) {
                        if (html::get_use_log()) {
                            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned by exact text and exact attrib match");
                        }
                        $tags[] = $tag_to_look;
                    }
                }
            }

            /**
             * Child and inline tags
             */
            $inline_tags = $this->get_inline_tags();
            $all_childs = $this->get_all_childs();
            $all_childs = array_merge($inline_tags, $all_childs);
            foreach ($all_childs as $child) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} looking on child [{$child->get_tag_name()}] ID:{$child->tag_id}");
                }
                $child_search_result = [];
                $child_search_result = $child->get_elements_by_attrib_value($attribute_name, $attribute_value, $partial_attribute_text_search, $partial_value_text_search);
                if (!empty($child_search_result)) {
//                        print_r($child_search_result);
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return child [{$child->get_tag_name()}] ID:{$child->tag_id} results");
                    }
                    $tags = array_merge($tags, $child_search_result);
                }
            }
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return " . count($tags) . " '$attribute_name' attribute");
        }
        return $tags;
    }

    /**
     * Return an Array with all the objects that CLASS is $class_name. 
     * NOTE: This will work ONLY with 1 class at time, or multiple in exact order.
     * @param string $class_name
     * @return tag[]
     */
    public function get_elements_by_class($class_name) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will SEARCH by CLASS='$class_name'");
        }
        $classes = [];
        if ($this->get_tag_id()) {
//            if ($this->get_attribute("class") == $class_name) {
            if (strstr($this->get_attribute("class"), $class_name) !== FALSE) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned");
                }
                $classes[] = $this;
            }
            /**
             * Child and inline tags
             */
            $inline_tags = $this->get_inline_tags();
            $all_childs = $this->get_all_childs();
            $all_childs = array_merge($inline_tags, $all_childs);
            foreach ($all_childs as $child) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} looking on child [{$child->get_tag_name()}] ID:{$child->tag_id}");
                }
                $child_search_result = $child->get_elements_by_class($class_name);
                if (!empty($child_search_result)) {
//                        print_r($child_search_result);
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return child [{$child->get_tag_name()}] ID:{$child->tag_id} results");
                    }
                    $classes = array_merge($classes, $child_search_result);
                }
            }
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return " . count($classes) . " tags with CLASS='$class_name'");
        }
        return $classes;
    }

    /**
     * TRUE if this Objects have child and FALSE if not.
     * @return boolean
     */
    function has_childs() {
        return $this->has_child;
    }

    /**
     * Merge and return the $childs_head, $childs and $childs_tail
     * @return \k1lib\html\tag[]
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

/**
 * Common tag Objects append operations
 */
trait append_shotcuts {

    /**
     * 
     * @param string $class
     * @param string $id
     * @return div
     */
    function append_div($class = NULL, $id = NULL) {
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
    function append_span($class = NULL, $id = NULL) {
        $new = new span($class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_p($value = NULL, $class = NULL, $id = NULL) {
        $new = new p($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return pre
     */
    function append_pre($value = NULL, $class = NULL, $id = NULL) {
        $new = new pre($value, $class, $id);
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
    function append_a($href = NULL, $label = NULL, $target = NULL, $class = NULL, $id = NULL) {
        $new = new a($href, $label, $target, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_h1($value = NULL, $class = NULL, $id = NULL) {
        $new = new h1($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_h2($value = NULL, $class = NULL, $id = NULL) {
        $new = new h2($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_h3($value = NULL, $class = NULL, $id = NULL) {
        $new = new h3($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_h4($value = NULL, $class = NULL, $id = NULL) {
        $new = new h4($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_h5($value = NULL, $class = NULL, $id = NULL) {
        $new = new h5($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * @param string $class
     * @param string $id
     * @return p
     */
    function append_h6($value = NULL, $class = NULL, $id = NULL) {
        $new = new h6($value, $class, $id);
        $this->append_child($new);
        return $new;
    }

}

/**
 * Static Class that holds the first tag Object <html></html>. 
 */
class DOM {
//    use append_shotcuts;

    /**
     * @var html
     */
    static protected $html = NULL;

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
        self::$html->pre_code("<!DOCTYPE html>\n");
        return self::$html->generate();
    }

}

/**
 * This is the main object that will holds all the HTML document.
 * <html>  
 *  <head>
 *      <title></title>
 *  </head>
 *  <body>
 *  </body>
 * </html> 

 */
class html extends tag {

    use append_shotcuts;

    /**
     * @var head
     */
    protected $head = NULL;

    /**
     * @var body
     */
    private $body = NULL;

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

/**
 *  <head>
 *      <title></title>
 *  </head>

 */
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

    public function get_title() {
        return $this->title->get_value();
    }

    /**
     * @return link
     */
    function link_css($href = NULL) {
        $new = new link($href);
        $this->append_child_tail($new);
        return $new;
    }

    /**
     * 
     * @return meta
     */
    function append_meta($name = NULL, $content = NULL) {
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

    function __construct($id = NULL, $class = NULL) {
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

    function __construct($name = NULL, $content = NULL) {
        parent::__construct("meta", TRUE);
        if (!empty($name)) {
            $this->set_attrib("name", $name);
        }
        if (!empty($content)) {
            $this->set_attrib("content", $content);
        }
    }

}

/**
 * This is the body of HTML document 
 *  <body>
 *      <section id='k1lib-header'></section>
 *      <section id='k1lib-content'></section>
 *      <section id='k1lib-footer'></section>
 *  </body>
 */
class body extends tag {

    use append_shotcuts;

    /**
     * @var section
     */
    protected $section_header = NULL;

    /**
     * @var section
     */
    protected $section_content = NULL;

    /**
     * @var section
     */
    protected $section_footer = NULL;

    function __construct() {
        parent::__construct("body", FALSE);
    }

    function init_sections() {
        $this->section_header = new section("k1lib-header", "hide-for-print");
        $this->section_header->append_to($this);
        $this->section_content = new section("k1lib-content");
        $this->section_content->set_attrib("style", "overflow-x: auto;");
        $this->section_content->append_to($this);
        $this->section_footer = new section("k1lib-footer", "hide-for-print");
        $this->section_footer->append_to($this);
    }

    /**
     * return section
     */
    function header() {
        if (!empty($this->section_header)) {
            return $this->section_header;
        } else {
            return FALSE;
        }
    }

    /**
     * return section
     */
    function content() {
        if (!empty($this->section_content)) {
            return $this->section_content;
        } else {
            return FALSE;
        }
    }

    /**
     * return section
     */
    function footer() {
        if (!empty($this->section_footer)) {
            return $this->section_footer;
        } else {
            return FALSE;
        }
    }

}

class a extends tag {

    use append_shotcuts;

    function __construct($href, $label, $target = NULL, $class = NULL, $id = NULL) {
        parent::__construct("a", FALSE);
        if (!empty($href)) {
            $this->set_attrib("href", $href);
        }
        if (!empty($label)) {
            $this->set_value($label);
        }
        if (!empty($target)) {
            $this->set_attrib("target", $target);
        }
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

class img extends tag {

    use append_shotcuts;

    function __construct($src = NULL, $alt = "Image", $class = NULL, $id = NULL) {
        parent::__construct("img", TRUE);
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
    function __construct($class = NULL, $id = NULL) {
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
    function __construct($class = NULL, $id = NULL) {
        parent::__construct("ul", FALSE);
//        $this->data_array &= $data_array;
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    function set_value($value, $append = FALSE) {
//            parent::set_value($value, $append);
    }

    /**
     * 
     * @param string $class
     * @param string $id
     * @return li
     */
    function append_li($value = NULL, $class = NULL, $id = NULL) {
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
    function __construct($class = NULL, $id = NULL) {
        parent::__construct("ol", FALSE);
//        $this->data_array &= $data_array;
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    function set_value($value, $append = FALSE) {
//            parent::set_value($value, $append);
    }

    /**
     * 
     * @param string $class
     * @param string $id
     * @return li
     */
    function append_li($value = NULL, $class = NULL, $id = NULL) {
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
    function __construct($value = NULL, $class = NULL, $id = NULL) {
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
    function append_ul($class = NULL, $id = NULL) {
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
    function append_ol($class = NULL, $id = NULL) {
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
    function __construct($src = NULL) {
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
    function __construct($class = NULL, $id = NULL) {
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
    function __construct($class = NULL, $id = NULL) {
        parent::__construct("span", FALSE);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

class strong extends tag {

    use append_shotcuts;

    /**
     * Create a STRONG html tag with VALUE as data. Use $strong->set_value($data)
     * @param String $class
     * @param String $id
     */
    function __construct($value = '', $class = NULL, $id = NULL) {
        parent::__construct("strong", FALSE);
        $this->set_value($value);
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
    function __construct($class = NULL, $id = NULL) {
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
    function append_thead($class = NULL, $id = NULL) {
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
    function append_tbody($class = NULL, $id = NULL) {
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
    function __construct($class = NULL, $id = NULL) {
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
    function append_tr($class = NULL, $id = NULL) {
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
    function __construct($class = NULL, $id = NULL) {
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
    function append_tr($class = NULL, $id = NULL) {
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
    function __construct($class = NULL, $id = NULL) {
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
    function append_th($value, $class = NULL, $id = NULL) {
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
    function append_td($value, $class = NULL, $id = NULL) {
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
    function __construct($value, $class = NULL, $id = NULL) {
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
    function __construct($value, $class = NULL, $id = NULL) {
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
    function __construct($type, $name, $value, $class = NULL, $id = NULL) {
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
    function __construct($name, $class = NULL, $id = NULL) {
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
    function __construct($label, $for, $class = NULL, $id = NULL) {
        parent::__construct("label", FALSE);
        $this->set_value($label);
        if (!empty($for)) {
            $this->set_attrib("for", $for);
        }
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
    function __construct($name, $class = NULL, $id = NULL) {
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
    function append_option($value, $label, $selected = FALSE, $class = NULL, $id = NULL) {
        $child_object = new option($value, $label, $selected, $class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

    function set_value($value, $append = FALSE) {
        $selected = $this->get_elements_by_attrib("selected");
        if (!empty($selected)) {
            $selected[0]->remove_attrib("selected");
        }
        $targuet_tag = $this->get_elements_by_attrib_value("value", $value);
        if (isset($targuet_tag[0])) {
            $targuet_tag[0]->set_attrib("selected", TRUE);
        }
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
    function __construct($value, $label, $selected = FALSE, $class = NULL, $id = NULL) {
        parent::__construct("option", FALSE);
        $this->set_value($label);
        $this->set_attrib("value", $value);
        if ($selected) {
            $this->set_attrib("selected", $selected);
        }
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

/**
 * FORM
 */
class form extends tag {

    use append_shotcuts;

    function __construct($id = "k1lib-form") {
        parent::__construct("form", FALSE);
        $this->set_id($id);
        $this->set_attrib("name", "k1lib-form");
        $this->set_attrib("method", "post");
        $this->set_attrib("autocomplete", "on");
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

    function __construct($value = NULL, $class = NULL, $id = NULL) {
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

    function __construct($value = NULL, $class = NULL, $id = NULL) {
        parent::__construct("h1", FALSE);
        $this->set_value($value);
        $this->set_class($class);
        $this->set_id($id);
    }

}

/**
 * h2
 */
class h2 extends tag {

    use append_shotcuts;

    function __construct($value = NULL, $class = NULL, $id = NULL) {
        parent::__construct("h2", FALSE);
        $this->set_value($value);
        $this->set_class($class);
        $this->set_id($id);
    }

}

/**
 * h3
 */
class h3 extends tag {

    use append_shotcuts;

    function __construct($value = NULL, $class = NULL, $id = NULL) {
        parent::__construct("h3", FALSE);
        $this->set_value($value);
        $this->set_class($class);
        $this->set_id($id);
    }

}

/**
 * h4
 */
class h4 extends tag {

    use append_shotcuts;

    function __construct($value = NULL, $class = NULL, $id = NULL) {
        parent::__construct("h4", FALSE);
        $this->set_value($value);
        $this->set_class($class);
        $this->set_id($id);
    }

}

/**
 * h5
 */
class h5 extends tag {

    use append_shotcuts;

    function __construct($value = NULL, $class = NULL, $id = NULL) {
        parent::__construct("h5", FALSE);
        $this->set_value($value);
        $this->set_class($class);
        $this->set_id($id);
    }

}

/**
 * h6
 */
class h6 extends tag {

    use append_shotcuts;

    function __construct($value = NULL, $class = NULL, $id = NULL) {
        parent::__construct("h6", FALSE);
        $this->set_value($value);
        $this->set_class($class);
        $this->set_id($id);
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

    function __construct($value, $class = NULL, $id = NULL) {
        parent::__construct("pre", FALSE);
        $this->set_value($value);
        $this->set_class($class);
        $this->set_id($id);
    }

}

class button extends tag {

    use append_shotcuts;

    function __construct($value = NULL, $class = NULL, $id = NULL, $type = "button") {
        parent::__construct("button", FALSE);
        $this->set_value($value);
        $this->set_class($class);
        $this->set_id($id);
        if (!empty($type)) {
            $this->set_attrib("type", $type);
        }
    }

}
