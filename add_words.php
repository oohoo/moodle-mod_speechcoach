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
 * This page deals with the adding and removal of words from the users view.
 * When a teacher edits the words that he wants to show his users and then saves
 * that change this is the page that is called.
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // speechcoach instance ID - it should be named as the first character of the module

if ($id) {
    $cm = get_coursemodule_from_id('speechcoach', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $speechcoach = $DB->get_record('speechcoach', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $speechcoach = $DB->get_record('speechcoach', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $speechcoach->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('speechcoach', $speechcoach->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/speechcoach:edit', $context);

$word_list = optional_param_array('word_list', array(), PARAM_INT);

$records = $DB->get_records('speechcoach_words', array('course_module_id' => $cm->id));

foreach ($records as $record) {
    in_array($record->id, $word_list) ? $record->active = 1 : $record->active = 0;
    $DB->update_record('speechcoach_words', $record);
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
?>
