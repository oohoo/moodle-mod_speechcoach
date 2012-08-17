<?php
/**
 * ************************************************************************
 * *                           Speech Coach                              **
 * ************************************************************************
 * @package     mod                                                      **
 * @subpackage  Speech Coach                                             **
 * @name        Speech Coach                                             **
 * @copyright   oohoo.biz                                                **
 * @link        http://oohoo.biz                                         **
 * @author      Andrew McCann                                            **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * ************************************************************************
 * ************************************************************************ */

/**
 * Internal library of functions for module speechcoach
 *
 * All the speechcoach specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 */
//function speechcoach_do_something_useful(array $things) {
//    return new stdClass();
//}

/**
 * Builds a tree from an array of arrays.
 * 
 * @param array $tree  is of the format
 * $tree = array( 
 *      array('1',
 *          array( //Substree
 *              '1-1', //Title of subtree
 *              '1-1-1', //Leeafs
 *              '1-1-2',
 *              '1-1-3'
 *          ),
 *          array(
 *              '1-2'
 *          )
 *      ),
 *      array('2',
 *          array('2-1'),
 *          array('2-2'),
 *          array('2-3')
 *      ),
 *      array('3',
 *          array('3-1')
 *      )
 *  );
 * 
 * If the first element is a string then it will use that element as the 
 * title for that tree, otherwise it just builds the tree from that level 
 * down. (Other than the top level you should always have a title on your 
 * subtrees. Or it could do something you don't expect.)
 * @return string 
 */
function build_tree($tree) {

    $output = '';

    if (!is_array($tree)) {
        $output .= "<li>$tree</li>\n";
    } else {

        if (!is_array($tree[0])) {
            $output .= "<li>$tree[0]\n";
            $start = 1;
        } else {
            $start = 0;
        }

        $output .= "<ul>\n";

        for ($i = $start; $i < count($tree); $i++) {
            $output .= HTML::build_tree($tree[$i]) . "\n";
        }

        $output .= "</ul>\n";

        if (!is_array($tree[0])) {
            $output .= "</li>\n";
        }
    }

    return $output;
}

function build_tooltip_table($list) {
    $output = "<table class='ui-widget-shadow'>";

    foreach ($list as $title => $value) {
        $output .= "<tr><td>$title</td><td>$value</td></tr>";
    }
    return $output . '</td>';
}