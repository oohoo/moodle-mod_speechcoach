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
 * This file handles the updating of comment information and allows users to mark
 * words as important.
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = required_param('id', PARAM_INT); // course_module ID, or
$comment = optional_param('comment', false, PARAM_TEXT); //Either this or important will be set.
$important = optional_param('important', false, PARAM_INT);
$history_id = required_param('history_id', PARAM_INT);

$cm = get_coursemodule_from_id('speechcoach', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);


//If we are updating a comment
if ($comment) {
    
    //Make sure the user is a teacher for this course
    require_capability('mod/speechcoach:edit', $context);
    
    //Get old comment info and replace it with the new comment.
    $record = $DB->get_record('speechcoach_history', array('id' => $history_id));
    $word = $DB->get_record("speechcoach_words", array('id' => $record->word_id));
    if ($word->course_module_id == $cm->id) {
        $record->comment = $comment;
        $DB->update_record('speechcoach_history', $record);
    }
    //If we are marking as important
} else if ($important !== false) {
    $record = $DB->get_record('speechcoach_history', array('id' => $history_id));
    //Make sure the user is the owner or a teacher for that course
    if ($record->user_id == $USER->id || has_capability('mod/speechcoach:edit', $context)) {
        //Changes the old status to important.
        $word = $DB->get_record("speechcoach_words", array('id' => $record->word_id));
        if ($word->course_module_id == $cm->id) {
            $record->important = $important;
            $DB->update_record('speechcoach_history', $record);
        }
    }
}
?>
