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

$settings->add(new admin_setting_configexecutable('speechcoach_java', 'Java',
                   get_string('javahelp','speechcoach'), 'java'));
$settings->add(new admin_setting_configexecutable('speechcoach_ffmpeg', 'ffmpeg',
                   get_string('ffmpeghelp','speechcoach'), 'ffmpeg'));

?>
