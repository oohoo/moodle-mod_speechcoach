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
 * Library of interface functions and constants for module speechcoach
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the speechcoach specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod
 * @subpackage speechcoach
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/** example constant */
//define('speechcoach_ULTIMATE_ANSWER', 42);
////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function speechcoach_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO: return true;
        default: return null;
    }
}

/**
 * Saves a new instance of the speechcoach into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $speechcoach An object from the form in mod_form.php
 * @param mod_speechcoach_mod_form $mform
 * @return int The id of the newly inserted speechcoach record
 */
function speechcoach_add_instance(stdClass $speechcoach, mod_speechcoach_mod_form $mform = null) {
    global $DB;

    $speechcoach->timecreated = time();

    # You may have to add extra stuff in here #

    return $DB->insert_record('speechcoach', $speechcoach);
}

/**
 * Updates an instance of the speechcoach in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $speechcoach An object from the form in mod_form.php
 * @param mod_speechcoach_mod_form $mform
 * @return boolean Success/Fail
 */
function speechcoach_update_instance(stdClass $speechcoach, mod_speechcoach_mod_form $mform = null) {
    global $DB;

    $speechcoach->timemodified = time();
    $speechcoach->id = $speechcoach->instance;

    # You may have to add extra stuff in here #

    return $DB->update_record('speechcoach', $speechcoach);
}

/**
 * Removes an instance of the speechcoach from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function speechcoach_delete_instance($id) {
    global $DB;

    if (!$speechcoach = $DB->get_record('speechcoach', array('id' => $id))) {
        return false;
    }

    # Delete any dependent records here #

    $DB->delete_records('speechcoach', array('id' => $speechcoach->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function speechcoach_user_outline($course, $user, $mod, $speechcoach) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $speechcoach the module instance record
 * @return void, is supposed to echp directly
 */
function speechcoach_user_complete($course, $user, $mod, $speechcoach) {
    
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in speechcoach activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function speechcoach_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link speechcoach_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function speechcoach_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid = 0, $groupid = 0) {
    
}

/**
 * Prints single activity item prepared by {@see speechcoach_get_recent_mod_activity()}

 * @return void
 */
function speechcoach_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
    
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 * */
function speechcoach_cron() {
    return true;
}

/**
 * Returns an array of users who are participanting in this speechcoach
 *
 * Must return an array of users who are participants for a given instance
 * of speechcoach. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $speechcoachid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function speechcoach_get_participants($speechcoachid) {
    return false;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function speechcoach_get_extra_capabilities() {
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of speechcoach?
 *
 * This function returns if a scale is being used by one speechcoach
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $speechcoachid ID of an instance of this module
 * @return bool true if the scale is used by the given speechcoach instance
 */
function speechcoach_scale_used($speechcoachid, $scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('speechcoach', array('id' => $speechcoachid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of speechcoach.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any speechcoach instance
 */
function speechcoach_scale_used_anywhere($scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('speechcoach', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the give speechcoach instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $speechcoach instance object with extra cmidnumber and modname property
 * @return void
 */
function speechcoach_grade_item_update(stdClass $speechcoach) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    /** @example */
    $item = array();
    $item['itemname'] = clean_param($speechcoach->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax'] = $speechcoach->grade;
    $item['grademin'] = 0;

    grade_update('mod/speechcoach', $speechcoach->course, 'mod', 'speechcoach', $speechcoach->id, 0, null, $item);
}

/**
 * Update speechcoach grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $speechcoach instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function speechcoach_update_grades(stdClass $speechcoach, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir . '/gradelib.php');

    /** @example */
    $grades = array(); // populate array of grade objects indexed by userid

    grade_update('mod/speechcoach', $speechcoach->course, 'mod', 'speechcoach', $speechcoach->id, 0, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function speechcoach_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * Serves the files from the speechcoach file areas
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return void this should never return to the caller
 */
function speechcoach_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    $fileinfo = array(
        'component' => 'mod_speechcoach', // usually = table name
        'filearea' => $filearea, // usually = table name
        'itemid' => $args[1], // usually = ID of row in table
        'contextid' => $context->id, // ID of context
        'filepath' => '/' . $args[0] . '/', // any path beginning and ending in /
        'filename' => $args[2]); // any filename

    $fs = get_file_storage();
    $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'], $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);
    //Onlt creating temp so that we know where to put the mp3 file.
    $temp = tempnam(sys_get_temp_dir(), 'sch');
    $temp2 =  tempnam(sys_get_temp_dir(), 'sch');
    $file->copy_content_to($temp2);
    exec("$CFG->speechcoach_ffmpeg -y -i $temp2 -f mp3 $temp", $output);
    clearstatcache();
//    echo "ffmpeg -i {$file->get_filepath()} $temp.mp3";
    header('Content-Type: audio/mpeg');
    header("Content-Disposition: attachment; filename='{$fileinfo['filename']}'");
    header('Content-length: ' . filesize("$temp"));
    header('Cache-Control: no-cache');
    header("Content-Transfer-Encoding: binary");

    $contents = file_get_contents($temp);

    echo $contents;

    unlink($temp2);
    unlink($temp);
//    
    die();
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding speechcoach nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the speechcoach module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function speechcoach_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
    
}

/**
 * Extends the settings navigation with the speechcoach settings
 *
 * This function is called when the context for the page is a speechcoach module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $speechcoachnode {@link navigation_node}
 */
function speechcoach_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $speechcoachnode = null) {
    
}
