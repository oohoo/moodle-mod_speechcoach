<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/classes/analysis.php');

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

$type = required_param('type', PARAM_TEXT);
$history_id = required_param('history_id', PARAM_INT);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if ($history_id == 0 && has_capability('mod/speechcoach:edit', $context) && $type == 'history_list') {
    $userid = required_param('user_id', PARAM_INT);

    $records = $DB->get_records(
            "speechcoach_history", array('user_id' => $userid), 'id DESC'
    );

    foreach ($records as $record) {
        $word = $DB->get_record('speechcoach_words', array('id' => $record->word_id, 'active' => 1));

        //Make sure the word corresponds to this module.
        if ($word && $word->course_module_id == $id) {
            $checked = ($record->important == 1 ? 'checked="checked"' : '');
            $comment = $record->comment == get_string('no_comment', 'speechcoach') ? false : true;
            echo "<div class='draggable ui-state-default' style='font-size: 10px' history_id='$record->id'>";
            echo "$word->word [" . Analysis::get_difficulty_abbrv($record->difficulty) . "]";
            echo "<button class='play_button'>" . get_string('play', 'speechcoach') . "</button>";
            echo "<div class='progressbar' target='$speechcoach->targetscore' style='width: 50%; height:20px; margin:auto' value='$record->score'></div>";
            echo "<input type='checkbox' class='important_button' $checked id='history-$record->id'/><label class='important_button_css' for='history-$record->id'>" . get_string("important", 'speechcoach') . "</label>";
            echo "<button class='download_button'>" . get_string('download', 'speechcoach') . "</button>";
            echo "<button class='comment_button' comment='$comment'>" . get_string('comments', 'speechcoach') . "</button>";

            echo "</div>";
        }
    }
} else {
    $history = $DB->get_record('speechcoach_history', array('id' => $history_id));

    if ($history->user_id == $USER->id || has_capability('mod/speechcoach:edit', $context)) {
        if ($type == 'master_file') {
            $fs = get_file_storage();

            $word = $DB->get_record('speechcoach_words', array('id' => $history->word_id));

            $file = $fs->get_file_by_id($word->file_id);

            $file_address = $CFG->wwwroot . '/pluginfile.php/' . $file->get_contextid()
                    . '/' . $file->get_component() . '/' . $file->get_filearea()
                    . '/' . $file->get_filepath() . $file->get_itemid()
                    . '/' . $file->get_filename();

            header('location: ' . $file_address);
        } else if ($type == 'user_file') {
            $fs = get_file_storage();
            $file = $fs->get_file_by_id($history->file_id);
            $file_address = $CFG->wwwroot . '/pluginfile.php/' . $file->get_contextid()
                    . '/' . $file->get_component() . '/' . $file->get_filearea()
                    . '/' . $file->get_filepath() . $file->get_itemid()
                    . '/' . $file->get_filename();

            header('location: ' . $file_address);
        } else if ($type == 'comment') {
            if (has_capability('mod/speechcoach:edit', $context)) {
                echo "<textarea style='width:75%; height:65%' id='comment_area'>$history->comment</textarea><br>";
                echo "<button id='comment_submit' history_id='$history_id'>" . get_string('submit') . "</button>";
                echo <<<SCRIPT
                    <script>
                    $('#comment_submit').button({
                    icons: {
                        primary: "ui-icon-pencil"
                    }
                    }).click( function() {
                                $('#console_output').load($('#update_comment_page').attr('value') + '&history_id=' + $(this).attr('history_id') + '&comment=' + encodeURIComponent($('#comment_area').val()), function() {
                                alert('Changed!');
                                });
                            });
                    </script>
SCRIPT;
            } else {
                echo $history->comment;
            }
        } else if ($type == 'word_name') {
            $word = $DB->get_record('speechcoach_words', array('id' => $history->word_id));
            echo $word->word;
        } else if ($type == 'analysis') {
            $word = $DB->get_record('speechcoach_words', array('id' => $history->word_id));
            $masterfile = tempnam(sys_get_temp_dir(), 'sch');
            $comparisonfile = tempnam(sys_get_temp_dir(), 'sch');

            $fs = get_file_storage();

            $cfile = $fs->get_file_by_id($history->file_id);
            $mfile = $fs->get_file_by_id($word->file_id);

            $file_address = $CFG->wwwroot . '/pluginfile.php/' . $mfile->get_contextid()
                    . '/' . $mfile->get_component() . '/' . $mfile->get_filearea()
                    . '/' . $mfile->get_filepath() . $mfile->get_itemid()
                    . '/' . $mfile->get_filename();
            ?>

            <div class="canvas_container" file="<?php echo $file_address ?>">
                <button class="master_play_button"><?php echo get_string('play', 'speechcoach'); ?></button>
                <canvas class="canvasarea" width="500px"  id="master_file"> </canvas>
            </div>
            </br>
            <div class="canvas_container" history_id='<?php echo $history->id; ?>'>
                <button id="recording_play_button"><?php echo get_string('play', 'speechcoach'); ?></button>
                <canvas class="canvasarea droppable" width="500px" id="comparison_file"> </canvas>
            </div>

            <?php
            $cfile->copy_content_to($comparisonfile);
            $mfile->copy_content_to($masterfile);

            $output = '';
            exec("java -cp \"Oohoo Acoustic Suite - Server.jar\" oohoo.acoustic.suite.server.SentanceCompare $masterfile $comparisonfile $history->difficulty", $output);
            //Insert into database.
            $data = json_decode($output[0]);

            $data = json_encode($data);
            echo <<<ANALYSISSCRIPT
           
                <script>
                    data = $data;
                    console.log(data);
                    draw_canvas(document.getElementById('master_file'), data.master_data, generate_master_color_map(data.master_data));
                    draw_canvas(document.getElementById('comparison_file'), data.comparison_data, generate_color_map(data.frame_score, data.comparison_data));
                    init_analysis_area();
                </script>
ANALYSISSCRIPT;

            unlink($masterfile);
            unlink($comparisonfile);
        }
    }
}
?>
