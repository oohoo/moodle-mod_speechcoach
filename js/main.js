$(function () {

    $('#loading_div').dialog({
        'width' : 'auto',
        'height' : 'auto',
        'modal' : true,
        'position' : 'center',
        'autoOpen' : false
    });
    $('#loading_div').dialog('open');
    init_components();
    setTimeout(function() {
        $('#loading_div').dialog('close');

    }, 450);
});

function analyze_audio() {
    var word_id = $('#word_select :selected').attr('word_id');
    var difficulty = $('.difficulty_select :selected').attr('value');
    
    playerRecorder.set_URLLoader($('#upload_page').attr('value') + '&word_id=' + word_id + '&difficulty=' + difficulty);    

    playerRecorder.uploadSound();
    
}

function init_components() {
    init_history_area();
    init_analysis_area();
	
	$('#divPlayerOptions').dialog({
		autoOpen: false,
		closeOnEscape: false,
        modal: true,
        width: 450,
        buttons: [ {
            text:"OK", 
            click: function() {
                window.location.reload();
            }
        }],
        open: function(event, ui){
            $(".ui-dialog-titlebar-close", $(this).parent()).hide();
            $("#divPlayerOptions").css('position', 'relative');
            $("#divPlayerOptions").css('top',0);
        }
    });
	
		
	$('#audio_button').button({
		 icons: {
            primary: "ui-icon-volume-on"
        },
        text: false
	}).click(function() {
		$('#divPlayerOptions').dialog('open');
	});
    

	
    $(".multiselect_node").live('click', function() {
        
        });
    
    $('.user_list').click(function() {
        //Get the na,e
        var name = $(this).clone();
        name.children('ins').remove();
        
        //Get the word History
        var history = $('#history_header').clone();
        //        history.children('#aName').remove();
        
        //        $('#history_header').html(history.html() + "<span id='aName'>(" +  name.html() + ")</span>");
        $('#history_header').mouseup();
        $('#history').html('');
        $('#history').append('<p id="history_loading">Loading...</p>');
        $('#history').load($('#get_history_page').attr('value') + '&history_id=0&user_id=' + $(this).attr('value') + '&type=history_list', function() {
            $('#history').remove('#history_loading');
            init_history_area();
        });
    });
    
    $('#remove_history_button').click(function() {
        var selected = $('.history_selected_state');
        if(confirm("Delete (" + selected.length + ") items?")) {
            selected.each(function () {
               console.log($('#delete_page').attr('value') + "&history_id=" + $(this).attr('history_id'));
               $.post($('#delete_page').attr('value') + "&history_id=" + $(this).attr('history_id'));
               $(this).remove(); 
            });
        }
    });
    
    $('#word_list').multiselect();
    
    $('#add_remove_word_area').tabs();
    
    $('#history_div').accordion({
        fillSpace: true,
        event: 'mouseup'
    });    
    
    $('#users_div').jstree({
        plugins: ['html_data']
    });
    
    
    
     
    $("#difficulty").combobox();           
  
    $("#add_remove_button").button({
        icons: {
            primary: "ui-icon-pencil"
        },
        text: true
    }).click(function(){
        $('#word_selection').html('');
        $('#word_selection').append('<p id="word_loading">Loading...</p>');

        $('#word_selection').load($('#word_selection_page').attr('value'), function() {
            $('#word_selection').remove('#word_loading');
        });
        
        $('#add_remove_word_area').dialog({
            modal:true,
            width: 500
        });
        
        
    });
  
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
        if(new Date().getTime() - $(this).attr('time') < 1000) {
            alert("You must hold the record button to record.");
        }
    });
    
    $("#save_added_word_button").button({
        icons: {
            primary: "ui-icon-plus"
        },
        text: true
    }).click(function(){
        playerRecorder.set_URLLoader($(this).attr('href') + '&word_name=' + $('#aWordTile').val());    
        playerRecorder.uploadSound();
        alert("'" + $('#aWordTile').val() + "' added!");
        $('#word_selection').html('');
        $('#word_selection').append('<p id="word_loading">Loading...</p>');

        $('#word_selection').load($('#word_selection_page').attr('value'), function() {
            $('#word_selection').remove('#word_loading');
        });
       
    });
    
    $("#save_selected_word_buttons").button({
        icons: {
            primary: "ui-icon-disk"
        },
        text: true
    }).click(function(){
        $('#console_output').load($('#add_word_page').attr('value') + '&' + $('#word_list').serialize());
        $('.ui-dialog-titlebar-close').click();
    });
    
    
    $("#recording_word_play_button").button({
        icons: {
            primary: "ui-icon-play"
        },
        text: true
    }).click(function(){
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
        if(new Date().getTime() - $(this).attr('time') < 1000) {
            alert("You must hold the record button to record.");
        } else {
            //It wil lbe closed in the callback. (See audiosystem.js soundProcessResult)
            $('#loading_div').dialog('open');
            analyze_audio();
        }
        
    });
    
}

function init_analysis_area() {
    $(".droppable").droppable({
        activeClass: "ui-state-hover",
        hoverClass: "ui-state-active",
        drop: function( event, ui ) {
            $('#loading_div').dialog('open');

            if($('#word_select').length) {
            } else if($('#word_title').length){
                $('#word_title').load($('#get_history_page').attr('value') + '&type=word_name&history_id=' + ui.draggable.attr('history_id'));
            }
            $('#analysis_div').load($('#get_history_page').attr('value') + '&type=analysis&history_id=' + ui.draggable.attr('history_id'), function() {
                $('#loading_div').dialog('close');
            });
            
            
            
        },
        accept: ".draggable"
    });
    
    $(".master_play_button").button({
        icons: {
            primary: "ui-icon-play"
        },
        text: false
    }).click(function(){
        if($('#word_select').length) {
            playerRecorder.loadExternalSound($('#get_word_audio_page').attr('value')  +  "&word_id=" + $('#word_select :selected').attr('word_id'));
        } else if ($(this).parent().attr('file')) {
            playerRecorder.loadExternalSound($(this).parent().attr('file'));
        }
       
        playerRecorder.playSound();
    });
    
    $("#recording_play_button").button({
        icons: {
            primary: "ui-icon-play"
        },
        text: false
    }).click(function(){
        if($(this).parent().attr('history_id')) {
            playerRecorder.loadExternalSound($('#get_history_page').attr('value') + '&history_id=' + $(this).parent().attr('history_id') + '&type=user_file');
        }
        playerRecorder.playSound();
    });
}

function init_history_area() {
                 
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
    
    $(".comment_button").each(function(index, value) {
        //title only exists if editor.
        if($(value).attr('comment') || $('#word_title').length) {
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
    }).click(function(event){
		event.stopPropagation();
        playerRecorder.loadExternalSound($('#get_history_page').attr('value') + '&history_id=' + $(this).parent().attr('history_id') + '&type=user_file');
        playerRecorder.playSound();
    });

    $(".important_button").button({
        icons: {
            primary: "ui-icon-star"
        },
        text: false
    }).click(function() {
        console.log($('#update_comment_page').attr('value') + '&history_id=' + $(this).parent().attr('history_id') + '&important=' + (this.checked ? '1' : '0'));
        $.post($('#update_comment_page').attr('value') + '&history_id=' + $(this).parent().attr('history_id') + '&important=' + (this.checked ? '1' : '0'));
    });
    
                      
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
    });
    
    $(".draggable").draggable({
        helper: function() {

            var aClone = $(this).clone();
            aClone.css('width','240px')
            .css('height','33px')
            .css('z-index', 20)
            .css('text-align', 'center')
            .addClass('ui-widget-shadow')
            .appendTo('body');
            
            return aClone;
        }
    }).click(function() {
        if(!$(this).hasClass('ui-state-active history_selected_state')) {
            $(this).addClass('ui-state-active history_selected_state');
        } else {
            $(this).removeClass('ui-state-active history_selected_state');
        }

    });
    
}

function draw_canvas(canvas, data, colorMap) {
    if(canvas.getContext) {
        var context = canvas.getContext('2d');
        clear(canvas, context);
        context.lineCap = "round";
        
        var inc = canvas.width / data.length;
                                
        for(var i = 1; i < data.length; i++) {
            //Start the drawing.
            context.beginPath();
                        
            //Set the color
            context.strokeStyle = "rgb(" + colorMap[i].red + "," + colorMap[i].green + "," + colorMap[i].blue + ")";
            context.moveTo((i-1) * inc , data[i-1] * canvas.height/2 + canvas.height/2);
            //Draw the line.
            context.lineTo(i * inc , data[i] * canvas.height/2 + canvas.height/2);
            context.stroke();
        }
            
    }
}

function drawSelectedWord(data) {
    draw_canvas(document.getElementById('master_file'), data, generate_master_color_map(data));
    
    c_canvas = document.getElementById('comparison_file');
    clear(c_canvas, c_canvas.getContext('2d'));
}

function clear(canvas, context) {
    context.save();
    context.fillStyle = 'white';
    context.fillRect(0, 0, canvas.width, canvas.height);
    context.restore();
}

function generate_color_map(frame_scores, data) {
    var color_map = new Array();
    for(var i = 0; i < data.length; i++) {
        var index = Math.round(i / data.length * frame_scores.length);
        color_map.push(findColor(frame_scores[index]));
    }
                
    return color_map;
                
}
            
function generate_master_color_map(data) {
    var color_map = new Array();
    for(var i = 0; i < data.length; i++) {
        var color = {};
        color.red = 0;
        color.green = 0;
        color.blue = 0;
        color_map.push(color);
    }
    return color_map;
}
            
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

function wait_for_signal() {
    
}