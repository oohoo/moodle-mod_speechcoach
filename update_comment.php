<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = required_param('id', PARAM_INT); // course_module ID, or
$comment = optional_param('comment', false, PARAM_TEXT);
$important = optional_param('important', false, PARAM_INT);
$history_id = required_param('history_id', PARAM_INT);

$cm = get_coursemodule_from_id('speechcoach', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);


if ($comment) {
    require_capability('mod/speechcoach:edit', $context);
    $record = $DB->get_record('speechcoach_history', array('id' => $history_id));
    $word = $DB->get_record("speechcoach_words", array('id' => $record->word_id));
    if ($word->course_module_id == $cm->id) {
        $record->comment = $comment;
        $DB->update_record('speechcoach_history', $record);
    }
} else if ($important !== false) {
    $record = $DB->get_record('speechcoach_history', array('id' => $history_id));
    if ($record->user_id == $USER->id || has_capability('mod/speechcoach:edit', $context)) {
        $word = $DB->get_record("speechcoach_words", array('id' => $record->word_id));
        if ($word->course_module_id == $cm->id) {
            $record->important = $important;
            $DB->update_record('speechcoach_history', $record);
        }
    }
}
?>
