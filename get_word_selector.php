<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/locallib.php');

$id = required_param('id', PARAM_INT); // course_module ID, or

$cm = get_coursemodule_from_id('speechcoach', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/speechcoach:edit', $context);
?>

<html>
    <head></head>
    <body>
        <select id="word_list" style ="width: 505px; height:125px" multiple="multiple" name="word_list[]">
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
        <script>
            $("#word_list").multiselect();
            $("#save_selected_word_buttons").button({
                icons: {
                    primary: "ui-icon-disk"
                },
                text: true
            }).click(function(){
                $('#console_output').load($('#add_word_page').attr('value') + '&' + $('#word_list').serialize());
                $('.ui-dialog-titlebar-close').click();
            });
        </script>
    </body>
</html>