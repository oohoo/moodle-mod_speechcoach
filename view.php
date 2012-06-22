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
 * Prints a particular instance of speechcoach
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage speechcoach
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/locallib.php');
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
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

/// Print the page header
$PAGE->set_url('/mod/speechcoach/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($speechcoach->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

//Javascript

$PAGE->requires->css('/mod/speechcoach/css/MultiSelect/common.css');
$PAGE->requires->css('/mod/speechcoach/css/MultiSelect/ui.multiselect.css');
$PAGE->requires->css('/mod/speechcoach/css/smoothness/jquery-ui.css');
$PAGE->requires->css('/mod/speechcoach/css/comboBox/combobox.css');
$PAGE->requires->css('/mod/speechcoach/css/main.css');

$PAGE->requires->js('/mod/speechcoach/js/jquery.js', true);
$PAGE->requires->js('/mod/speechcoach/js/jquery-ui.js', true);
$PAGE->requires->js('/mod/speechcoach/js/audiosystem.js', true);
$PAGE->requires->js('/mod/speechcoach/js/comboBox/combobox.js', true);

$PAGE->requires->js('/mod/speechcoach/js/main_javascript.php', true);
$PAGE->requires->js('/mod/speechcoach/js/jsTree/jquery.jstree.js', true);

$PAGE->requires->js('/mod/speechcoach/js/MultiSelect/plugins/tmpl/jquery.tmpl.1.1.1.js', true);
$PAGE->requires->js('/mod/speechcoach/js/MultiSelect/ui.multiselect.js', true);
$PAGE->requires->js('/mod/speechcoach/js/locale/ui.multiselect-' . $CFG->lang . '.js', true);
// Output starts here
echo $OUTPUT->header();
?>


<!-- My Content Here -->
<div id="console_output" style="display:none"></div>
<div id="upload_page" value="<?php echo "$CFG->wwwroot/mod/speechcoach/analyze.php?sendsound=true&id=$cm->id" ?>"></div>
<div id="get_history_page" value="<?php echo "$CFG->wwwroot/mod/speechcoach/get.php?id=$cm->id" ?>"></div>
<div id="get_word_audio_page" value="<?php echo "$CFG->wwwroot/mod/speechcoach/get_word_audio.php?id=$cm->id" ?>"></div>
<div id="update_comment_page" value="<?php echo "$CFG->wwwroot/mod/speechcoach/update_comment.php?id=$cm->id" ?>"></div>
<div id="delete_page" value="<?php echo "$CFG->wwwroot/mod/speechcoach/delete.php?id=$cm->id" ?>"></div>
<div id="a_target_score" value ="<?php echo $speechcoach->targetscore; ?>"></div>

<?php
if (has_capability('mod/speechcoach:edit', $context)) {
    ?>

    <div id="add_word_page" value="<?php echo "$CFG->wwwroot/mod/speechcoach/add_words.php?id=$cm->id" ?>"></div>

    <div id="add_remove_word_area" style="display:none;">
        <ul>
            <li><a href="#word_selection" style="font-size:small"><?php echo get_string('select_words', 'speechcoach'); ?></a></li>
            <li><a href="#custom_words" style="font-size:small"><?php echo get_string('custom_words', 'speechcoach'); ?></a></li>
        </ul>
        <div id="word_selection">
            <select id="word_list" style ="width: 515px; height:125px" multiple="multiple" name="word_list[]">
                <?php
                $records = $DB->get_records('speechcoach_words', array('course_module_id' => $cm->id));
                foreach ($records as $record) {
                    $type = $record->base_id == null ? get_string('custom', 'speechcoach') : get_string('base', 'speechcoach');
                    $info_array = array(
                        get_string('name', 'speechcoach') => $record->word,
                        get_string('type', 'speechcoach') => $type,
                        get_string('active', 'speechcoach') => $record->active ? 'true' : 'false'
                    );
                    $info = build_tooltip_table($info_array);
                    $info = str_replace('"', '&quot;', $info);
                    $info = str_replace('\'', '&quot;', $info);

                    $selected = $record->active ? "selected='selected'" : "";
                    echo "<option tags='$record->word' info='$info' value='$record->id' $selected>$record->word</option>";
                }
                ?>
            </select> <br>

            <button id="save_selected_word_buttons"><?php echo get_string('save', 'speechcoach'); ?></button>

        </div>
        <div id='word_selection_page' value="<?php echo "$CFG->wwwroot/mod/speechcoach/get_word_selector.php?id=$id" ?>"></div>

        <div id="custom_words">
            <input type="text" style="margin:auto" id="aWordTile" value="<?php echo get_string('enter_name', 'speechcoach'); ?>"/>
            <br><br>

            <button id="record_word_button"><?php echo get_string('record', 'speechcoach'); ?></button>  <button id="recording_word_play_button" ><?php echo get_string('play', 'speechcoach'); ?></button>
            <br>
            <!--            OR
                        <input type="file" id="aWordFile"/>
                        <br>
                        <br>-->
            <button id="save_added_word_button" href="<?php echo "$CFG->wwwroot/mod/speechcoach/upload_word.php?sendsound=true&id=$cm->id" ?>"><?php echo get_string('add_word', 'speechcoach'); ?></button>
        </div>
    </div>
    <?php
}
?>
<div id="loading_div" style="display:none; text-align:center">
    <p> <?php echo get_string('loading_message', 'speechcoach'); ?> </p><img src='<?php echo "$CFG->wwwroot/mod/speechcoach/pix/load.gif"; ?>'/>
</div>

<div id="outter_div">

    <!-- Content -->
    <div id="content_div">
        <?php
        if (has_capability('mod/speechcoach:edit', $context)) {
            echo "<span id='word_title' class='ui-state-default ui-corner-all'>" . get_string('word_s', 'speechcoach') . "</span>";
        } else {
            echo '<select id = "word_select" class = "word_select" >';

            //Fill in the words.
            $records = $DB->get_records(
                    'speechcoach_words', array('course_module_id' => $id, 'active' => 1)
            );

            foreach ($records as $record) {
                echo "<option word_id='$record->id' file_id='$record->file_id'>$record->word</option>";
            }

            echo '</select>';
            ?>
            <script>
                $("#word_select").combobox().change(function (event) {
                    $.post('visualize.php?id=<?php echo $id ?>' + '&word_id=' + $('#word_select :selected').attr('word_id'), function(data) {

                        drawSelectedWord($.parseJSON(data).data);
                    });
                }); 
                                                                            
                $.post('visualize.php?id=<?php echo $id ?>' + '&word_id=' + $('#word_select :selected').attr('word_id'), function(data) {

                    drawSelectedWord($.parseJSON(data).data);
                });

            </script>
            <?php
        }
        ?>


        <!-- Main content Div -->
        <div id="analysis_div">

            <div class="canvas_container">

                <button class="master_play_button"><?php echo get_string('play', 'speechcoach'); ?></button>
                <canvas class="canvasarea" width="500px"  id="master_file"> </canvas>
            </div>
            </br>
            <div class="canvas_container">
                <button id="recording_play_button"><?php echo get_string('play', 'speechcoach'); ?></button>
                <canvas class="canvasarea droppable" width="500px" id="comparison_file"> </canvas>
            </div>
        </div>

        <div id='audio_button'>
        </div><br><br>

        <object type="application/x-shockwave-flash" data="PlayerRecorderCoach.swf" name="playerRecorder" id="playerRecorder" >
            <param name="allowScriptAccess" value="always" />
            <param name="allowFullScreen" value="true" />
            <param name="wmode" value="transparent"> 
            <param name="movie" value="PlayerRecorderCoach.swf" />
            <param name="quality" value="high" />
        </object>

        <?php
        if (has_capability('mod/speechcoach:edit', $context)) {
            echo "<button id='add_remove_button'>" . get_string('add_remove', 'speechcoach') . "</button>";
        } else {
            ?>
            <select id="difficulty" class="difficulty_select">
                <option value="<?php echo Analysis::DIFFICULTY_EASY ?>"><?php echo get_string('normal', 'speechcoach'); ?></option>

                <option value="<?php echo Analysis::DIFFICULTY_NORMAL ?>"><?php echo get_string('hard', 'speechcoach'); ?></option>

                <option value="<?php echo Analysis::DIFFICULTY_HARD ?>"><?php echo get_string('impossible', 'speechcoach'); ?></option>

            </select>

            <button class="rec_button" id="rec_button" ><?php echo get_string('record', 'speechcoach'); ?></button>
        <?php } ?>

    </div>

    <!-- History Menu -->
    <div id="history_div">
        <?php
        if (has_capability('mod/speechcoach:edit', $context)) {
            ?>
            <h3><a href = "#"> <?php echo get_string('users', 'speechcoach') ?> </a></h3>
            <div id="users_div">
                <?php
                $courseid = $DB->get_record('course_modules', array('id' => $id))->course;
                $course_context = get_context_instance(CONTEXT_COURSE, $courseid, MUST_EXIST);

                $users = $DB->get_records('user', array('deleted' => 0));
                echo "<ul>";
                foreach ($users as $user) {
                    if (is_enrolled($course_context, $user)) {
                        echo "<li><a class='user_list' href='#' value='$user->id'>$user->firstname $user->lastname</a></li>";
                    }
                }
                echo "</ul>";
                ?>

            </div>

        <?php } ?>

        <h3><a id ="history_header" href="#"> <?php echo get_string('history', 'speechcoach') ?> <span id='remove_history_button' class='ui-icon ui-icon-trash'></span></a></h3>
        <div id="history" >
            <?php
//Output the history
            if (has_capability('mod/speechcoach:edit', $context)) {
                $records = array();
            } else {
                $records = $DB->get_records(
                        "speechcoach_history", array('user_id' => $USER->id), 'id DESC'
                );
            }

            foreach ($records as $record) {
                $word = $DB->get_record('speechcoach_words', array('id' => $record->word_id, 'active' => 1));

                //Make sure the word corresponds to this module.
                if ($word && $word->course_module_id == $id) {
                    $checked = ($record->important == 1 ? 'checked="checked"' : '');
                    $comment = $record->comment == get_string('no_comment', 'speechcoach') ? false : true;
                    echo "<div class='draggable ui-state-default'  style='font-size: 10px' history_id='$record->id'>";
                    echo "$word->word [" . Analysis::get_difficulty_abbrv($record->difficulty) . "]";
                    echo "<button class='play_button'>" . get_string('play', 'speechcoach') . "</button>";
                    echo "<div class='progressbar' target='$speechcoach->targetscore' style='width: 50%; height:20px; margin:auto' value='$record->score'></div>";
                    echo "<input type='checkbox' class='important_button' $checked id='history-$record->id'/><label class='important_button_css' for='history-$record->id'>" . get_string("important", 'speechcoach') . "</label>";
                    echo "<button class='download_button'>" . get_string('download', 'speechcoach') . "</button>";
                    echo "<button class='comment_button' comment='$comment'>" . get_string('comments', 'speechcoach') . "</button>";
                    echo "</div>";
                }
            }
            ?>
        </div>

        <h3><a id="comments_header" href="#"> <?php echo get_string('comments', 'speechcoach') ?> </a></h3>
        <div id='comments'>

        </div>
    </div>

</div>


<?php
//Load the flash options menu
echo '
<div id="divPlayerOptions" title="' . get_string('titlePlayerOptions', 'speechcoach') . '" style="position:absolute;top:-1000px;">
	<div id="divPlayerOptionsText" style="width: 400px;">
		' . get_string('playeroptionstxt1', 'speechcoach') . '
		<ol>
			<li>' . get_string('playeroptionstxt2', 'speechcoach', '<img src="' . $CFG->wwwroot . '/mod/speechcoach/pix/privacy-ico.png"/>') . '</li>
			<li>' . get_string('playeroptionstxt3', 'speechcoach', '<img src="' . $CFG->wwwroot . '/mod/speechcoach/pix/allow-ico.png"/>') . '</li>
			<li>' . get_string('playeroptionstxt4', 'speechcoach', '<img src="' . $CFG->wwwroot . '/mod/speechcoach/pix/check-ico.png"/>') . '</li>
			<li>' . get_string('playeroptionstxt5', 'speechcoach') . '</li>
			<li>' . get_string('playeroptionstxt6', 'speechcoach') . '</li>
		</ol>
	</div>
	<div id="divPlayerOptionsObj" style="text-align:center;">
		<object type="application/x-shockwave-flash" data="flash/PlayerOptions.swf" width="250" height="160" name="playerOptions" id="playerOptions">
			<param name="allowScriptAccess" value="always" />
			<param name="allowFullScreen" value="true" />
			<param name="wmode" value="window">
			<param name="movie" value="flash/PlayerOptions.swf" />
			<param name="quality" value="high" />
		</object>
	</div>
	
	<div style="clear:both;"></div>
</div>       
';


// Finish the page
echo $OUTPUT->footer();
?>

