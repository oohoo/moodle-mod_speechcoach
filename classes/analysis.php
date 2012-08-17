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

class Analysis {

    const DIFFICULTY_EASY = 30;
    const DIFFICULTY_NORMAL = 45;
    const DIFFICULTY_HARD = 55;

    public static function get_difficulty_name($difficulty) {
        switch ($difficulty) {
            case Analysis::DIFFICULTY_EASY:
                return get_string('normal', 'speechcoach');
            case Analysis::DIFFICULTY_NORMAL:
                return get_string('hard', 'speechcoach');
            case Analysis::DIFFICULTY_HARD:
                return get_string('impossible', 'speechcoach');
            default:
                return get_string('diff_not_available', 'speechcoach');
        }
    }

    public static function get_difficulty_abbrv($difficulty) {
        switch ($difficulty) {
            case Analysis::DIFFICULTY_EASY:
                return "*";
            case Analysis::DIFFICULTY_NORMAL:
                return "**";
            case Analysis::DIFFICULTY_HARD:
                return "***";
            default:
                return "?";
        }
    }

    //More should be in here but I had a deadline and did some reallllllllly bad
    //coding.
}

?>
