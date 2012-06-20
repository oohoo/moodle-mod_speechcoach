<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = required_param('id', PARAM_INT); // course_module ID, or
$wordname = required_param('word_name', PARAM_TEXT);

$cm = get_coursemodule_from_id('speechcoach', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/speechcoach:edit', $context);

if (isset($_REQUEST['sendsound']) && $_REQUEST['sendsound'] == true) {

    //If in mode sendsound, save the file from php://input to $contentSound'
    $contentSound = file_get_contents('php://input');
} else if (isset($_FILES['uploadsound']) && $_FILES['uploadsound'] == true) {
//    $contentSound = $_FILES['']
}

$tempname = tempnam(sys_get_temp_dir(), 'sch');
clearstatcache();
$bytes = file_put_contents($tempname, $contentSound);

$filepath = uniqid();
while (filepath_exists($filepath)) {
    $filepath = uniqid();
}

$file_record = array(
    'contextid' => $context->id,
    'component' => 'mod_speechcoach',
    'filearea' => 'words_audio',
    'itemid' => 0,
    'filepath' => '/' . $filepath . '/',
    'filename' => uniqid(),
    'mimetype' => 'audio/wav'
);

$fs = get_file_storage();

$myfile = $fs->create_file_from_pathname($file_record, $tempname);

$record = new stdClass();
$record->course_module_id = $id;
$record->word = $wordname;
$record->file_id = $myfile->get_id();
$record->active = 1;

$DB->insert_record("speechcoach_words", $record);

unlink($tempname);

function filepath_exists($filepath) {

    global $context;
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_speechcoach', 'history_audio');

    foreach ($files as $file) {
        if ($file->get_filepath() == $filepath) {
            return true;
        }
    }

    return false;
}

?>
