<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Internal library of functions for module speechcoach
 *
 * All the speechcoach specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod
 * @subpackage speechcoach
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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