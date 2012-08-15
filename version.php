<?php
/**
 * Defines the version of speechcoach
 *
 * This code fragment is called by moodle_needs_upgrading() and
 * /admin/index.php
 *
 * @package    mod
 * @subpackage speechcoach
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2012081500;
$plugin->requires = 2010031900;
$plugin->maturity = MATURITY_RC;
$plugin->release = '1.0.0';
$module->component = 'mod_speechcoach';
$module->cron      = 0;               // Period for cron to check this module (secs)
