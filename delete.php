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
 * This page deletes a history element from a users history or a word from the list of
 * words.
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = required_param('id', PARAM_INT); // course_module ID, or
$word_id = optional_param('word_id', false, PARAM_INT);
$history_id = optional_param('history_id', false, PARAM_INT);

$cm = get_coursemodule_from_id('speechcoach', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);


if ($word_id !== false) {
    require_capability('mod/speechcoach:edit', $context);
    $word = $DB->get_record("speechcoach_words", array('id' => $word_id));
    if ($word->course_module_id == $cm->id) {
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($word->file_id);
        $file->delete();
        $DB->delete_records('speechcoach_words', array('id' => $word_id));
    }
} else if ($history_id !== false) {
    $record = $DB->get_record('speechcoach_history', array('id' => $history_id));
    if ($record->user_id == $USER->id || has_capability('mod/speechcoach:edit', $context)) {
        $word = $DB->get_record("speechcoach_words", array('id' => $record->word_id));
        if ($word->course_module_id == $cm->id) {
            $fs = get_file_storage();
            $file = $fs->get_file_by_id($record->file_id);
            $file->delete();
            $DB->delete_records('speechcoach_history', array('id' => $history_id));
        }
    }
}
?>
