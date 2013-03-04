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
/**
 * javascript for the main file. It is a php file because I wanted to use getstring quickly.
 */
header('Content-type: text/javascript');
require_once(dirname(dirname(dirname(dirname((__FILE__))))) . '/config.php');
if (false)
{
    ?>
    <script>
    <?php } ?>

    var playerOptions;
    var playeroptionsBtnOk = "<?php echo get_string('playeroptionsBtnOk', 'languagelab'); ?>";

    $(function() {
        /**
         * Loading message dialog box.
         */
        $('#loading_div').dialog({
            'width': 'auto',
            'height': 'auto',
            'modal': true,
            'position': 'center',
            'autoOpen': false
        });
        $('#loading_div').dialog('open');
        init_components();
        setTimeout(function() {
            $('#loading_div').dialog('close');

        }, 450);
    });


    //Used to get the flash component
    function getFlashMovieObject(movieName)
    {
        if (window.document[movieName])
        {
            return window.document[movieName];
        }
        if (navigator.appName.indexOf("Microsoft Internet") == -1)
        {
            if (document.embeds && document.embeds[movieName])
                return document.embeds[movieName];
            else
                return null;
        }
        else // if (navigator.appName.indexOf("Microsoft Internet")!=-1)
        {
            return document.getElementById(movieName);
        }
    }

    //Run this function when player is ready
    function playeroptions_ready()
    {
        if (playerOptions == undefined)
        {
            playerOptions = getFlashMovieObject('playerOptions');
            if (playerOptions.microphoneEnabled())
            {
                //If microphone enabled, do nothing
            }
            else
            {
                playeroptions_open();
            }
        }
    }

    function playeroptions_open()
    {;
        //This is a pathtru for IE, because sometime the flash don't want to shows up in the pop up so create an Iframe with the flash.
        //Well actually an other problem occurs with Firefox so now it is for ALL BROWSERS
        var title = $("#divPlayerOptions").attr("title");
        $('<iframe src="' + M.cfg.wwwroot + '/mod/speechcoach/playeroptions.php" title="' + title + '" style="width:430px !important;min-width:430px !important; padding:10px 0 0 0;"/>').dialog({
            autoOpen: true,
            closeOnEscape: false,
            modal: true,
            width: 450,
            minWidth: 450,
            height: 500,
            zIndex: 4000,
            buttons: [{
                    text: playeroptionsBtnOk,
                    click: function() {
                        window.location.reload();
                    }
                }],
            open: function(event, ui) {
                $(".ui-dialog-titlebar-close", $(this).parent()).hide();
            }
        });
    }

    /**
     * This function analyzes the audio that is currently in the recorder's buffer.
     */
    function analyze_audio() {
        var word_id = $('#word_select :selected').attr('word_id');
        var difficulty = $('.difficulty_select :selected').attr('value');

        //Set the url to the upload page and add parameters for the word and difficulty level.
        playerRecorder.set_URLLoader($('#upload_page').attr('value') + '&word_id=' + word_id + '&difficulty=' + difficulty);

        playerRecorder.uploadSound();

    }

    /**
     * Initialize all the components of the plugin on the main page.
     */
    function init_components() {
        init_history_area();
        init_analysis_area();
        //Make all the draggable things draggable (These will be the history elements.)
        $('.draggable').live('click', function() {
            if (!$(this).hasClass('ui-state-active history_selected_state')) {
                $(this).addClass('ui-state-active history_selected_state');
            } else {
                $(this).removeClass('ui-state-active history_selected_state');
            }
        });

/*
        //This is the options menu for the flash on the page.
        $('#divPlayerOptions').dialog({
            autoOpen: false,
            closeOnEscape: false,
            modal: true,
            width: 450,
            buttons: [{
                    text: "OK",
                    click: function() {
                        window.location.reload();
                    }
                }],
            open: function(event, ui) {
                //When it's open don't let them close it without setting the
                //settings. We don't want people accidently not setting up the
                //settings properly then calling us saying theres a bug.
                $(".ui-dialog-titlebar-close", $(this).parent()).hide();
                $("#divPlayerOptions").css('position', 'relative');
                $("#divPlayerOptions").css('top', 0);
            }
        });*/

        //This is the audio settings button that makes the above dialog show up.
        $('#audio_button').button({
            icons: {
                primary: "ui-icon-volume-on"
            },
            text: false
        }).click(function() {
            $('#divPlayerOptions').dialog('open');
        });


        //This is the user list that a teacher would see. When they select a user
        //it will load the history for that user.
        $('.user_list').click(function() {
            //Get the name
            var name = $(this).clone();
            name.children('ins').remove();

            //Get the word History
            var history = $('#history_header').clone();
            //        history.children('#aName').remove();

            //        $('#history_header').html(history.html() + "<span id='aName'>(" +  name.html() + ")</span>");
            $('#history_header').mouseup();//We use mouse up so that it won't overwrite the delete button in the header.
            $('#history').html('');
            $('#history').append('<p id="history_loading"><?php echo get_string('loading', 'speechcoach');
    ?></p>');
            //Load the histpry for a user when clicked.
            $('#history').load($('#get_history_page').attr('value') + '&history_id=0&user_id=' + $(this).attr('value') + '&type=history_list', function() {
                $('#history').remove('#history_loading');
                init_history_area();
            });
        });

        //Delete a set of history elements that are selected when this button is pressed.
        $('#remove_history_button').click(function() {
            var selected = $('.history_selected_state');
            //Show them the number of thigns to be deleted to limit the number
            //of accidental deletes
            if (confirm("Delete (" + selected.length + ") items?")) {
                selected.each(function() {
                    $.post($('#delete_page').attr('value') + "&history_id=" + $(this).attr('history_id'));
                    $(this).remove();
                });
            }
        });

        //Make the word list that the teacher can use a multiselect Jqury UI object.
        $('#word_list').multiselect();

        //Turn the teacher add/remove words area into a tabbed display.
        $('#add_remove_word_area').tabs();

        //Make the history div an accordian.
        $('#history_div').accordion({
            fillSpace: true,
            event: 'mouseup', //Again we use mouse up so that it won't override the trashbin .click
            animated: false
        });

        //Make the user list that the teacher sees a tree format.
        $('#users_div').jstree({
            plugins: ['html_data']
        });


        $("#difficulty").combobox();

        //When the add_remove button is pressed load the teachers add/remove display.
        //This button and the div it loads will only exist if the user is a taecher.
        $("#add_remove_button").button({
            icons: {
                primary: "ui-icon-pencil"
            },
            text: true
        }).click(function() {
            $('#word_selection').html('');
            //Create a loading screen.
            $('#word_selection').append('<p id="word_loading"><?php echo get_string('loading', 'speechcoach');
    ?></p>');
            //Load all the words.
            $('#word_selection').load($('#word_selection_page').attr('value'), function() {
                $('#word_selection').remove('#word_loading');
            });
            //Show the dialog.
            $('#add_remove_word_area').dialog({
                modal: true,
                width: 575
            });


        });

        //Records audio.
        $("#record_word_button").button({
            icons: {
                primary: "ui-icon-bullet"
            },
            text: true
        }).mousedown(function() {
            playerRecorder.recordSound();
            $(this).attr('time', new Date().getTime());
        }).mouseup(function() {
            playerRecorder.stopSound();
            //Remind the user to hold the button if he just clicks it.
            if (new Date().getTime() - $(this).attr('time') < 1000) {
                alert("<?php echo get_string('hold_record', 'speechcoach');
    ?>");
            }
            alert("<?php echo get_string('recording_complete', 'speechcoach');
    ?>");
        });

        //Add word button that saves it to the system once it's been recorded. (Teacher Only Still)
        $("#save_added_word_button").button({
            icons: {
                primary: "ui-icon-plus"
            },
            text: true
        }).click(function() {
            //Upload the word.
            playerRecorder.set_URLLoader($(this).attr('href') + '&word_name=' + encodeURIComponent($('#aWordTile').val()));
            playerRecorder.uploadSound();

            //Give confrimation.
            alert("'" + $('#aWordTile').val() + "' <?php echo get_string('word_added', 'speechcoach');
    ?>");

            $('#word_selection').html('');
            $('#word_selection').append('<p id="word_loading"><?php echo get_string('loading', 'speechcoach');
    ?></p>');

            //Reload the word selection. Hope that it has updated within .250 seconds otherwise the word lsit 
            //on the first page will not have the newly added word.
            setTimeout(function() {
                $('#word_selection').load($('#word_selection_page').attr('value'), function() {
                    $('#word_selection').remove('#word_loading');
                });
            }, 250);


        });


        $("#save_selected_word_buttons").button({
            icons: {
                primary: "ui-icon-disk"
            },
            text: true
        }).click(function() {
            $('#console_output').load($('#add_word_page').attr('value') + '&' + $('#word_list').serialize());
            $('.ui-dialog-titlebar-close').click();
        });


        $("#recording_word_play_button").button({
            icons: {
                primary: "ui-icon-play"
            },
            text: true
        }).click(function() {
            playerRecorder.playSound();
        });


        $("#rec_button").button({
            icons: {
                primary: "ui-icon-bullet"
            }
        }).mousedown(function() {
            playerRecorder.recordSound();
            $(this).attr('time', new Date().getTime());

        }).mouseup(function() {
            playerRecorder.stopSound();
            if (new Date().getTime() - $(this).attr('time') < 1000) {
                alert("<?php echo get_string('hold_record', 'speechcoach');
    ?>");
            } else {
                //It wil lbe closed in the callback. (See audiosystem.js soundProcessResult)
                $('#loading_div').dialog('open');
                analyze_audio();
            }

        });

    }

    //Initialize the analysis area. (The main view with the two waveform boxes.)
    function init_analysis_area() {
        //Make it possible to drop history elements onto this area to load them.
        $(".droppable").droppable({
            activeClass: "ui-state-hover",
            hoverClass: "ui-state-active",
            drop: function(event, ui) {
                //When an element is dropped load it's data.
                $('#loading_div').dialog('open');

                if ($('#word_select').length) {
                } else if ($('#word_title').length) {
                    $('#word_title').load($('#get_history_page').attr('value') + '&type=word_name&history_id=' + ui.draggable.attr('history_id'));
                }
                $('#analysis_div').load($('#get_history_page').attr('value') + '&type=analysis&history_id=' + ui.draggable.attr('history_id'), function() {
                    $('#loading_div').dialog('close');
                });



            },
            accept: ".draggable"
        });

        //A Play button that plays audio of the master file.
        $(".master_play_button").button({
            icons: {
                primary: "ui-icon-play"
            },
            text: false
        }).click(function() {
            if ($('#word_select').length) {
                playerRecorder.loadExternalSound($('#get_word_audio_page').attr('value') + "&word_id=" + $('#word_select :selected').attr('word_id'));
            } else if ($(this).parent().attr('file')) {
                playerRecorder.loadExternalSound($(this).parent().attr('file'));
            }

            playerRecorder.playSound();
        });

        //A play button that plays the audio of the current recording.
        $("#recording_play_button").button({
            icons: {
                primary: "ui-icon-play"
            },
            text: false
        }).click(function() {
            if ($(this).parent().attr('history_id')) {
                playerRecorder.loadExternalSound($('#get_history_page').attr('value') + '&history_id=' + $(this).parent().attr('history_id') + '&type=user_file');
            }
            playerRecorder.playSound();
        });
    }

    /**
     *  Initialize the history area. (3 boxes on right side.) 
     */
    function init_history_area() {

        //Make all download buttons download files.
        $(".download_button").button({
            icons: {
                primary: "ui-icon-disk"
            },
            text: false
        }).click(function() {
            window.location = $('#get_history_page').attr('value') + '&history_id=' + $(this).parent().attr('history_id') + '&type=user_file';
        });

        $(".comment_button").button({
            icons: {
                primary: "ui-icon-tag"
            },
            text: false
        }).click(function() {
            $('#comments').html('');
            $('#comments').append('<p id="comment_loading">Loading...</p>');
            $('#comments').load($('#get_history_page').attr('value') + '&history_id=' + $(this).parent().attr('history_id') + '&type=comment', function() {
                $('#comments').remove('#comment_loading');
            });
            $('#comments_header').mouseup();

            return false;
        });


        //Make comment buttons disabled if there are not comments for the user.
        //Comment buttons will always be enabled for teachers so they can add
        //comments.
        $(".comment_button").each(function(index, value) {
            //title only exists if user is a teacher,
            if ($(value).attr('comment') || $('#word_title').length) {
                $(value).button('option', 'disabled', false);
            } else {
                $(value).button('option', 'disabled', true);
            }
        });


        $(".play_button").button({
            icons: {
                primary: "ui-icon-play"
            },
            text: false
        }).click(function(event) {
            event.stopPropagation();
            playerRecorder.loadExternalSound($('#get_history_page').attr('value') + '&history_id=' + $(this).parent().attr('history_id') + '&type=user_file');
            playerRecorder.playSound();
        });

        //Mark history element as important.
        $(".important_button").button({
            icons: {
                primary: "ui-icon-star"
            },
            text: false
        }).click(function() {
            $.post($('#update_comment_page').attr('value') + '&history_id=' + $(this).parent().attr('history_id') + '&important=' + (this.checked ? '1' : '0'));
        });

        //Set the progress bar for each element.
        $(".progressbar").each(function() {
            var targetscore = $(this).attr('target');
            var progressbar = $(this).progressbar({
                value: Math.round($(this).attr('value'))
            });

            progressbar.css('position', 'relative');
            var target = $('<div> </div>');
            target.css({
                'position': 'absolute',
                'top': '0px',
                'left': '0px',
                'width': targetscore + '%',
                'height': '100%',
                'border-style': 'solid',
                'border-width': '0px',
                'border-right-width': '1px'
            });

            progressbar.append(target);

            if ($(this).find('img').length == 0) {
                //If we passed add a check else add an x.
                var passfail = '';
                if ($(this).attr('value') >= parseInt(targetscore)) {
                    passfail = $('<img src="pix/tick_green_big.gif"/>');
                } else {
                    passfail = $('<img src="pix/cross_red_big.gif"/>');
                }

                passfail.css({
                    'position': 'absolute',
                    'width': '16px',
                    'height': '16px',
                    'top': progressbar.height() / 2 - 8,
                    'left': progressbar.width() / 2 - 8
                });

                passfail.appendTo(progressbar);
            }
        });

        $(".draggable").draggable({
            helper: function() {

                var aClone = $(this).clone();
                aClone.css('width', '240px')
                        .css('height', '33px')
                        .css('z-index', 20)
                        .css('text-align', 'center')
                        .addClass('ui-widget-shadow')
                        .appendTo('body');

                return aClone;
            }
        });


    }

    /**
     * Draw audio waveform with the given colormap and data.
     * @param canvas The canvas you wish to draw to.
     * @param int[] data an array of datapoints that make up the audio data. (Compressed for drawing)
     * @param color[] A color associated with each datapoint.
     */
    function draw_canvas(canvas, data, colorMap) {
        if (canvas.getContext) {
            var context = canvas.getContext('2d');
            clear(canvas, context);
            context.lineCap = "round";

            var inc = canvas.width / data.length;

            for (var i = 1; i < data.length; i++) {
                //Start the drawing.
                context.beginPath();

                //Set the color
                context.strokeStyle = "rgb(" + colorMap[i].red + "," + colorMap[i].green + "," + colorMap[i].blue + ")";
                context.moveTo((i - 1) * inc, data[i - 1] * canvas.height / 2 + canvas.height / 2);
                //Draw the line.
                context.lineTo(i * inc, data[i] * canvas.height / 2 + canvas.height / 2);
                context.stroke();
            }

        }
    }

    /**
     * Draw the masterfile waveform
     * 
     * @param data The audio data for the master file. (Compressed for drawing)
     */
    function drawSelectedWord(data) {
        draw_canvas(document.getElementById('master_file'), data, generate_master_color_map(data));

        c_canvas = document.getElementById('comparison_file');
        clear(c_canvas, c_canvas.getContext('2d'));
    }

    /**
     * Clears a given canvas.
     * 
     * @param canvas the canvas to be cleared.
     * @param context the context that was genereated from this canvas.
     */
    function clear(canvas, context) {
        context.save();
        context.fillStyle = 'white';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.restore();
    }

    /**
     * Generates a colormap from the fram scores of the data.
     * 
     * @param frame_scores a score between 0-100 that is given to each frame
     * of the audio. In the user file. This is not nessically the same length 
     * as the data object but it does take up the same amount of time so it can be converted.
     * @param data the audio data.
     */
    function generate_color_map(frame_scores, data) {
        var color_map = new Array();
        for (var i = 0; i < data.length; i++) {
            var index = Math.round(i / data.length * frame_scores.length);
            color_map.push(findColor(frame_scores[index]));
        }

        return color_map;

    }

    /**
     * Generate the color map for the master audio sample. -- Always black for now.
     */
    function generate_master_color_map(data) {
        var color_map = new Array();
        for (var i = 0; i < data.length; i++) {
            var color = {};
            color.red = 0;
            color.green = 0;
            color.blue = 0;
            color_map.push(color);
        }
        return color_map;
    }

    /**
     * Generate a color based on the score. Red is bad Green is good Yellow is in the middle.
     * 
     * @param value the score you wish to turn into a color.
     */
    function findColor(value) {
        var red = 255;
        var green = 255;
        var blue = 255;

        if (value < 0.5) {
            red = 255;
            green = 255 - Math.round((2 * (0.5 - value) * 255));
            blue = 0;
        } else if (value <= 1.0) {
            red = Math.round((2 * (1 - value) * 255));
            green = 255;
            blue = 0;
        }

        var color = {};
        color.red = red;
        color.green = green;
        color.blue = blue;

        return color;

    }
