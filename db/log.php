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

defined('MOODLE_INTERNAL') || die();

global $DB;

$logs = array(
    array('module'=>'speechcoach', 'action'=>'add', 'mtable'=>'speechcoach', 'field'=>'name'),
    array('module'=>'speechcoach', 'action'=>'update', 'mtable'=>'speechcoach', 'field'=>'name'),
    array('module'=>'speechcoach', 'action'=>'view', 'mtable'=>'speechcoach', 'field'=>'name'),
    array('module'=>'speechcoach', 'action'=>'view all', 'mtable'=>'speechcoach', 'field'=>'name')
);
