<?php

$settings->add(new admin_setting_configexecutable('speechcoach_java', 'Java',
                   get_string('javahelp','speechcoach'), 'java'));
$settings->add(new admin_setting_configexecutable('speechcoach_ffmpeg', 'ffmpeg',
                   get_string('ffmpeghelp','speechcoach'), 'ffmpeg'));

?>
