<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$history = optional_param('history', false, PARAM_BOOL);

if($history) {
    
    echo "<html><head></head><body>";
    
    get_history();
    
    echo "</body></html>";
    
}

function get_history() {
    global $DB;
    echo "<div class='ui-widget-header'> " . get_string('history', 'speechcoach') . " </div>";
    //Output the history
    $records = $DB->get_records(
            "speechcoach_history", array('user_id' => $USER->id)
    );
    echo "<div class='draggable ui-state-default'>";
    echo "Word";
    echo "<button class='play_button'>" . get_string('play', 'speechcoach') . "</button>";
    echo "<div id='progressbar' style='width: 50%; height:20px; margin:auto'></div>";
    echo "<input type='checkbox' class='important_button' id='history-'/><label class='important_button_css' for='history-'>" . get_string("important", 'speechcoach') . "</label>";
    echo "<button class='download_button'>" . get_string('download', 'speechcoach') . "</button>";
    echo "<button class='comment_button'>" . get_string('comments', 'speechcoach') . "</button>";
    echo "</div>";
    foreach ($records as $record) {
        $word = $DB->get_record('speechcoach_word', array('id' => $record->word_id));
        //Make sure the word corresponds to this module.
        if ($word->course_module_id == $id) {
            echo "<div class='draggable ui-state-default'>";
            echo "$record->word";
            echo "<button class='play_button'>" . get_string('play', 'speechcoach') . "</button>";
            echo "<div id='progressbar' style='width: 50%; height:20px; margin:auto'></div>";
            echo "<input type='checkbox' class='important_button' id='history-$record->id'/><label class='important_button_css' for='history-$record->id'>" . get_string("important", 'speechcoach') . "</label>";
            echo "<button class='download_button'>" . get_string('download', 'speechcoach') . "</button>";
            echo "<button class='comment_button'>" . get_string('comments', 'speechcoach') . "</button>";
            echo "</div>";
        }
    }
    echo "  <div class='comments'>";
    echo "  </div>";

}

?>
