//Used to get the flash object 
function getFlashMovieObject(movieName)
{
    if (window.document[movieName]) 
    {
        return window.document[movieName];
    }
    if (navigator.appName.indexOf("Microsoft Internet")==-1)
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

//This function is called after the upload of the file by flash. Result is the content returned by the php process
function soundProcessResult(result)
{


//    console.log(result);
    var data = $.parseJSON(result);
    //Make sure this was after an analysis.
//    console.log(data);

    if(data != null && 'error' in data) {
        if(data.error == 1) {
            alert("Error");
            $('#loading_div').dialog('close');
            return;
        }
                
        draw_canvas(document.getElementById('master_file'), data.master_data, generate_master_color_map(data.master_data));
        draw_canvas(document.getElementById('comparison_file'), data.comparison_data, generate_color_map(data.frame_score, data.comparison_data));
        $('#comparison_file').parent().attr('history_id', data.history_id);
        
        var history_element = "";
        history_element += "<div class='draggable ui-state-default'  style='font-size: 10px' history_id='" + data.history_id + "'>";

        history_element += $("#word_select :selected").html() + $("#difficulty :selected").html().substr($("#difficulty :selected").html().indexOf("["));
        history_element += "<button class='play_button'>" + $('.play_button :first').html() + "</button>";
        history_element += "<div class='progressbar' target='" + $('.progressbar').first().attr('target') + "' style='width: 50%; height:20px; margin:auto' value='" + Math.round(100 * data.score) + "'></div>";
        history_element += "<input type='checkbox' class='important_button' id='history-" + data.history_id + "'/><label class='important_button_css' for='history-" + data.history_id + "'>" + $('.important_button_css :first').html() + "</label>";
        history_element += "<button class='download_button'>" + $('.download_button :first').html() + "</button>";
        history_element += "<button class='comment_button'>" + $('.comment_button :first').html() + "</button>";
        history_element += "</div>";
        $('#history').prepend(history_element);
        
        //Initialize the elements.
        init_history_area();
    } else {
        alert("Error");
    }
    $('#loading_div').dialog('close');

    
   
    
}
	
	
//Run this function when player is ready
function playerrecorder_ready()
{
    //    if (playerRecorder == undefined)
    //    {
    playerRecorder = getFlashMovieObject('playerRecorder');
//    }
}
		
//The object Playerrecorder
var playerRecorder;
