<?php
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

require_login($course, true, $cm);

$word_id = required_param('word_id', PARAM_INT);
$word = $DB->get_record('speechcoach_words', array('id' => $word_id));

$fs = get_file_storage();
$file = $fs->get_file_by_id($word->file_id);
$file_address = $CFG->wwwroot . '/pluginfile.php/' . $file->get_contextid()
        . '/' . $file->get_component() . '/' . $file->get_filearea()
        . '/' . $file->get_filepath() . $file->get_itemid()
        . '/' . $file->get_filename();

header('location: ' . $file_address);
?>
