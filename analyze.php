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

$word_id = required_param('word_id', PARAM_INT);
$difficulty = required_param('difficulty', PARAM_INT);


require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if (isset($_REQUEST['downloadfile']) && $_REQUEST['downloadfile'] == true) {
    header('Content-disposition: attachment; filename=' . $_REQUEST['soundfilename']);
    header('Content-type: media/wav');
    readfile($custom_path . $_REQUEST['soundfilename']);
} else if (isset($_REQUEST['sendsound']) && $_REQUEST['sendsound'] == true) {

    //If in mode sendsound, save the file from php://input to $contentSound
    $contentSound = file_get_contents('php://input');

    $tempname = tempnam(sys_get_temp_dir(), 'sch');

    //44 is the length of an empty file. Don't ask me why.
    if (strlen($contentSound) > 44) {
        file_put_contents($tempname, $contentSound);
    }
   

    $filepath = uniqid();
    while (filepath_exists($filepath)) {
        $filepath = uniqid();
    }

    $file_record = array(
        'contextid' => $context->id,
        'component' => 'mod_speechcoach',
        'filearea' => 'history_audio',
        'itemid' => 0,
        'filepath' => '/' . $filepath . '/',
        'filename' => uniqid(),
        'mimetype' => 'audio/wav'
    );

    $fs = get_file_storage();

    $myfile = $fs->create_file_from_pathname($file_record, $tempname);

    //Perform Analysis here.
    $word = $DB->get_record('speechcoach_words', array('id' => $word_id));

    $masterfile = tempnam(sys_get_temp_dir(), 'sch');

    if ($word->file_id) {
        $file = $fs->get_file_by_id($word->file_id);
        $file->copy_content_to($masterfile);
    } else {
        $file = $DB->get_record('speechcoach_base_words', array('id' => $word->base_id));
        $contents = file_get_contents($file->file_address);
        file_put_contents($masterfile, $contents);
    }


    $comparisonfile = $tempname;

    //Tell them the file really does exist =D
    clearstatcache();

    $output = shell_exec("$CFG->speechcoach_java" . ' -cp "Oohoo Acoustic Suite - Server.jar" oohoo.acoustic.suite.server.SentanceCompare ' . $masterfile . ' ' . $comparisonfile . ' ' . $difficulty);

    //Insert into database.
    $data = json_decode($output);
//    $data->debugging_info = array(
//          "x" => $contentSound
//        "m-location" => urlencode($masterfile),
//        "c-location" => urlencode($comparisonfile),
//        'difficulty' => $difficulty,
//        'command' => urlencode('java -cp "Oohoo Acoustic Suite - Server.jar" oohoo.acoustic.suite.server.SentanceCompare ' . $masterfile . ' ' . $comparisonfile . ' ' . $difficulty),
//        "m-filesize(actual)" => urlencode($file->get_filesize()),
//        "m-filesize" => filesize($masterfile),
//        "c-filesize" => filesize($comparisonfile),
//        "file-id" => $word->file_id,
//    );
//    $data->debuginfo = "oohoo.acoustic.suite.server.SentanceCompare $masterfile $comparisonfile $difficulty";
    if ($data->error == 0) {
        $record = new stdClass();
        $record->user_id = $USER->id;
        $record->word_id = $word_id;
        $record->file_id = $myfile->get_id();
        $record->score = 100 * $data->score;
        $record->difficulty = $difficulty;
        $record->important = 0;
        $record->comment = get_string('no_comment', 'speechcoach');

        $data->history_id = $DB->insert_record('speechcoach_history', $record);

//        print_object($data);
//        $data->debugging_info = "java -cp 'Oohoo Acoustic Suite - Server.jar' oohoo.acoustic.suite.server.SentanceCompare $masterfile $comparisonfile $difficulty";

        echo json_encode($data);
    } else {
        echo json_encode($data);
    }
    unlink($masterfile);
    unlink($tempname);
}

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
