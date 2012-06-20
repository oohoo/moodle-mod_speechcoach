<?php 
$custom_path = '';

if(isset($_REQUEST['downloadfile']) && $_REQUEST['downloadfile'] == true)
{
	header('Content-disposition: attachment; filename='.$_REQUEST['soundfilename']);
	header('Content-type: media/wav');
	readfile($custom_path.$_REQUEST['soundfilename']);
}
else if(isset($_REQUEST['sendsound']) && $_REQUEST['sendsound'] == true)
{
	
	
	//If in mode sendsound, save the file from php://input to $contentSound
	$contentSound = file_get_contents('php://input');
	file_put_contents($custom_path.$_REQUEST['soundfilename'], $contentSound);
	
	//Launch sound process
	//...
	//All Echo  here will be return as parameter of the javascript function soundProcessResult
	//You can return for example JSON to use it in JS
	
	$obj = new stdClass();
	$obj->test = "toto";
	echo json_encode($obj);
}
else
{
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  dir="ltr" lang="fr" xml:lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>test LanguageLag</title>
	<script type="text/javascript">
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
//		eval('var res = '+result);
		//TODO...
		console.log(result);
	}
	
	
	//Run this function when player is ready
	function playerrecorder_ready()
	{
		if (playerRecorder == undefined)
		{
			playerRecorder = getFlashMovieObject('playerRecorder');
		}
	}
		
	//The object Playerrecorder
	var playerRecorder;
	
	</script>
</head>
<body>

<div>
<object type="application/x-shockwave-flash" data="PlayerRecorderCoach.swf" width="215" height="138" name="playerRecorder" id="playerRecorder" style="outline: none;">
	<param name="allowScriptAccess" value="always" />
	<param name="allowFullScreen" value="true" />
	<param name="wmode" value="transparent"> 
	<param name="movie" value="PlayerRecorderCoach.swf" />
	<param name="quality" value="high" />
</object>
</div>
<br />
Server parameter URL: <br />
<input type="text" name="URLLoader" id="URLLoader" size="100" value="http://bretin.csj.ualberta.ca/playerrecorder/speechcoach.php?sendsound=true&courseid=1&activityid=23&soundfilename=filename.wav"/>
<input type="button" onclick="playerRecorder.set_URLLoader(document.getElementById('URLLoader').value);" value="Set the URL of the server"/><br />
<br />

Player : <br />
<input type="button" onclick="playerRecorder.playSound()" value="Play"/>
<input type="button" onclick="playerRecorder.stopSound()" value="Stop"/>
<input type="button" onclick="playerRecorder.recordSound()" value="Record"/>
<input type="button" onclick="window.location=document.getElementById('URLLoader').value+'&downloadfile=true'" value="Save"/> 
<input type="button" onclick="playerRecorder.uploadSound()" value="Upload"/><br />
<br />


</body>
</html>
<?php
}
?>