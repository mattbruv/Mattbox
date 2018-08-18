<?php
include('connect.php');
include('functions.php');

secureINI();
session_start();
secureSession();

if(isset($_COOKIE['Name']) && isset($_COOKIE['Remember']))
{
	$cookieName = $_COOKIE['Name'];
	$rememberMe = $_COOKIE['Remember'];
	$_SESSION['Name'] = $cookieName;
	$UID = fetch_id($_SESSION['Name']);
}

	$textBar = (isset($_SESSION['Name'])) ? import_styles($_SESSION['Name']) : 0;
	$txtColor = $textBar['Color'];
	$txtBold = $textBar['Bold'];
	$txtItalic = $textBar['Italic'];
	$txtFont = $textBar['Font'];
	$txtUnderline = $textBar['Underline'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo SITENAME; ?></title>
<link rel="stylesheet" href="styles.css" type="text/css">
<link rel="stylesheet" href="forumstyle.css" type="text/css">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/functions.js"></script>
<?php if(isset($_SESSION['Name']) && isApproved($_SESSION['Name'])) { 
	$display = get_display_settings($_SESSION['Name']);
?>
<script type="text/javascript">

$(document).ready(function() 
{
	loadBox();
	// setIdleTimeout();

	$(".submitShout").click(function()
	{
		var textbox = document.getElementById('text');
		var now = new Date().getSeconds();
		
		if(textbox.value.length == 0)
		{
			showNotice('Please enter a message first.');
			return false;
		}
		else if (textbox.value.length >= 1000)
		{
			showNotice('Limit shouts to under 1,000 characters.');
			return false;
		}
		else if (smilies > 4)
		{
			showNotice('Limit 4 smilies per shout.');
			return false;
		}
		else
		{
			if(!spam)
			{
				if(!pm)
				{
					$.post("shouts.php", $("#newquery").serialize());
					$("input[type=text], textarea").val("");
					$('#text').select();
					antiSpam();
					return false;
				}
				else if (pm)
				{
					$.post("newpm.php", $("#newquery").serialize());
					$("input[type=text], textarea").val("");
					$('#text').select();
					antiSpam();
					return false;
				}
				smilies = 0;
				return false;
			}
			else
			{
				showNotice('Wait 3 seconds between shouts.');
				return false;
			}
		}
	});
});

// var idleLength = 5000;
var idleLength = 60000 * 5;
var spam = false;
var pm = false;
var refresh = true;
var smilies = 0;
var sessions = new Array;
var aop_time = -1
var new_aop_time = -1;

// START AOP

function get_aop_time()
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
		xmlhttp = new XMLHttpRequest();
	}
	else
	{
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			new_aop_time = xmlhttp.responseText;
			// aop_time = (aop_time == -1) ? new_aop_time : -1;
		}
	}

	xmlhttp.open("GET","aop.php",true);
	xmlhttp.send();
}

function check_aop()
{
	if (aop_time < new_aop_time)
	{
		aop_time = new_aop_time;
		return true;
	}
	else
	{
		return false;
	}
}

function setIdleTimeout()
{
	setTimeout(function(){
		refresh = false;
		showNotice("You are currently idle. Click <a href='#' onclick='unIdle(); return false;'>here</a> to un-idle.", true);
	}, idleLength)
}

function antiSpam()
{
	spam = true;
	setTimeout(function(){ spam = false; },3000);
}

function addSession(user, array)
{
	array.push(user);
}

function inSession(user, array)
{
	var tester = false;
	for(i = 0; i<= array.length; i++)
	{
		if(array[i] == user)
		{
			tester = true;
		}
	}
	return tester;
}

function removeSession(user, array)
{
	for(i = 0; i <= array.length; i++)
	{
		if(array[i] == user)
		{
			array.splice(i, 1);
		}
	}
}

function showPms(user)
{
	$('.content').load('private.php');
	$.post("private.php", { username: user } );
	pm = true;
	refresh = true;
}

function loadBox()
{
	$('.content').load('showshouts.php');
	
	/* NEW METHOD WIP
	
	clearInterval(looper);

	var looper = setInterval(function(){
		
		get_aop_time();
		
		if (check_aop())
		{
			if (!pm && refresh)
			{
				$('.content').load('showshouts.php');
			}
			else if (pm && refresh)
			{
				$('.content').load('private.php');
			}
		}
		
		// Shitty fix to multiple setintervals
		if (looper > 2)
		{
			clearInterval(looper);
		}
		
	}, 3000);
	*/
	
	setInterval(function()
	{
		if (!pm && refresh)
		{
			$('.content').load('showshouts.php');
		}
		else if (pm && refresh)
		{
			$('.content').load('private.php');
		}
	}, 5000);
}

function updateActiveUsers(number)
{
	users = document.getElementById('activeUsers');
	users.innerHTML = number;
}

function loadPm(pmuser) 
{
	var user = pmuser.id;
	if(!inSession(user, sessions))
	{
		string = "<div id='" + user + "'>&nbsp;&nbsp;<a href='#' onclick='showPms(\"" + user + "\"); return false;'>" + user + "</a>&nbsp;[<a href='#' onclick='closeTab(\"" + user + "\"); return false;'>X</a>]</div>";
		$('#sblinks').append(string);
		addSession(user, sessions);
	}
}

function closeTab(user)
{
	var element = document.getElementById(user);
	element.parentNode.removeChild(element);
	removeSession(user, sessions);
	loadBox();
	pm = false;
}

function viewUsers()
{
	$('.content').load('activeuserlist.php');
	refresh = false;
	pm = false;
}

function editShout(shout)
{
	var id = shout.id;
	
	$.post("editshout.php", { shoutID: id },
		function(data)
		{
			if(data.shout != '')
			{
				editDiv = document.getElementById('editshout');
				editDiv.style.padding = "0px 0px 0px 5px";
				editDiv.innerHTML = '<form onsubmit="updateShout(' + data.id + ')" ><table id="inputTable"><tr><td width="100%"><input type="text" class="text" value="' + data.shout + '" id="updateshout" name="text"></input></td><td width="1%" style="white-space:nowrap"><input type="button" id="button" onclick="updateShout(' + data.id + ')" value="Update" />&nbsp;<input type="button" id="button" onclick="deleteShout(' + data.id + ')" value="Delete" />&nbsp;<input type="button" id="button" onclick="closeDiv()" value="Cancel" /></td></tr></table></form>';
			}
		}, "json");
}

function closeDiv()
{
	editDiv = document.getElementById('editshout');
	editDiv.style.padding = "0px";
	editDiv.innerHTML = '';
}

function deleteShout(id)
{
	closeDiv();
	$.post("updateshout.php", { del: 1, shoutid: id });
}

function updateShout(id)
{
	shout = document.getElementById('updateshout').value;
	
	if(shout.length != 0)
	{
		closeDiv();
		$.post("updateshout.php", { del: 0, shoutid: id, message: shout });
	}
	else
	{
 		showNotice('Please enter a message first.');
	}
}

function showSmilies()
{
	var div = document.getElementById('smilies');
	
	if(div.innerHTML == '')
	{
		$('#smilies').load("smilies.php");
	}
	else
	{
		div.innerHTML = '';
	}
}

function appendSmiley(code)
{
	var textbox = document.getElementById('text');
	
	if(textbox.value == '')
	{
		textbox.value = code;
	}
	else
	{
		textbox.value += ' ' + code;
	}
	smilies++;
}

function showNotice(string, clear)
{
	bool = (clear === undefined) ? 0 : 1;
	noticeDiv = document.getElementById('mattboxNotice');
	actual = document.getElementById('notice');
	padding = (actual == null) ? "5px" : "15px";
	noticeDiv.style.padding = "0px 0px " + padding + " 5px";
	noticeDiv.innerHTML = '<span style="background-color: #fbef8d;"><b>Mattbox Notice:</b></span> ' + string;
	if(!bool)
	{
		setTimeout(function(){
			noticeDiv.innerHTML = '';
			noticeDiv.style.padding = "0px";
			unIdle();
		}, 4000);
	}
}

function unIdle()
{
	noticeDiv.innerHTML = '';
	noticeDiv.style.padding = "0px";
	refresh = true;
	// setIdleTimeout();
}

function clearShout()
{
	textbox = document.getElementById('text');
	textbox.value = '';
	textbox.focus();
	smilies = 0;
}

function changeHeight()
{
	var height = prompt('Enter Height in Pixels:\r\n(Default 220)', '220');
	content = document.getElementById('content');
	if ((height < 10 || height > 1000 || isNaN(height)) && height != null)
	{
		showNotice('You are stupid.');
	}
	else
	{
		content.style.height = height + 'px';
	}
}

function bold()
{
	button = document.getElementsByTagName('input');
	for (i=1; i<=button.length; i++)
	{
		if (button[i].name == "btnBold")
		{
			value = (button[i].value == "B") ? 1 : 0;
			button[i].value = (button[i].value == "B") ? "B*" : "B";
			$.post("style.php", { bold: value } );
			updateStyle();
			updateBar("bold", value);
		}
	}
}

function underline()
{
	button = document.getElementsByTagName('input');
	for (i=1; i<=button.length; i++)
	{
		if (button[i].name == "btnUnderline")
		{
			value = (button[i].value == "U") ? 1 : 0;
			button[i].value = (button[i].value == "U") ? "U*" : "U";
			$.post("style.php", { underline: value } );
			updateStyle();
			updateBar("underline", value);
		}
	}
}

function italic()
{
	button = document.getElementsByTagName('input');
	for (i=1; i<=button.length; i++)
	{
		if (button[i].name == "btnItalic")
		{
			value = (button[i].value == "I") ? 1 : 0;
			button[i].value = (button[i].value == "I") ? "I*" : "I";
			$.post("style.php", { italic: value } );
			updateStyle();
			updateBar("italic", value);
		}
	}
}

function color(color)
{
	value = color.value;
	$.post("style.php", { color: value } );
	updateStyle();
	updateBar("color", value);
}

function font(font)
{
	value = font.value;
	$.post("style.php", { font: value } );
	updateStyle();
	updateBar("font", value);
}

function updateStyle()
{
	showNotice("Your style properties have been updated.");
}

function updateBar(attr, val)
{
	bar = document.getElementById('text');
	
	switch (attr)
	{
		case "bold": 
			bar.style.fontWeight = (val) ? 'bold' : 'normal';
			break;
			
		case "underline":
			bar.style.textDecoration = (val) ? 'underline' : 'none';
			break;
		
		case "italic":
			bar.style.fontStyle = (val) ? 'italic' : 'normal';
			break;
			
		case "color":
			val = (val == 'Default') ? 'inherit' : val;
			bar.style.color = val;
			break;
			
		case "font":
			val = (val == 'Default') ? 'Arial' : val;
			bar.style.fontFamily = val;
			break;
	}
}

function setDefaults()
{
	optionColor = "<?php echo ucfirst($txtColor); ?>";
	optionFont = "<?php echo $txtFont; ?>";
	options = document.getElementsByTagName('option');

	for(i=0;i<=options.length;i++)
	{
		if (options[i].value == optionColor)
		{
			options[i].selected = true;
		}
		if (options[i].value == optionFont)
		{
			options[i].selected = true;
		}
	}
}


</script><?php } ?>
<style type="text/css"><?php if(isset($display)) { ?>

body {
		background-image:url('<?php echo $display['Background']; ?>');
		<?php if ($display['Repeat'] == 0) { ?>
		background-size:100%;
		<?php } else { ?>
		background-repeat:repeat;
		<?php } ?>
}

#text {
	<? if($txtBold) { ?> font-weight: bold; <? } ?>
	<? if($txtItalic) { ?> font-style: italic; <? } ?>
	<? if($txtUnderline) { ?> text-decoration: underline;<? } ?>
	color: <? echo ($txtColor == "Default") ? "Arial" : $txtColor; ?>;
	font-family: <? echo $txtFont; ?>;
}

.shoutbox {
		background-color: rgba(255, 255, 255, <?php echo $display['Opacity'] / 100; ?>);
}
<?php } ?>

#quickLinks img {
	border:solid 1px;
}

.seperator {
	margin-top:20px;
}

</style>
</head>

<body onload="setDefaults()">

<center>
<?php

if(isset($_SESSION['Name']))
{
	$user = $_SESSION['Name'];
	set_login_time($user);
	if(!isBanned($user))
	{
		echo 'Welcome to the ' . SITENAME . ', ' . style_user($user) . '!';
		echo '<br />[<a href="stats.php">Statistics</a>]&nbsp;';
		echo '[<a href="archive.php">Archive</a>]&nbsp;';
		echo '[<a href="faq.php">FAQ</a>]&nbsp;';
		if(can_do_admin($user)) {
			echo '[<a href="admincp">Admin CP</a>]&nbsp;';
		}
		echo '[<a href="logout.php" onclick="return confirm(\'Are you sure you want to log out?\');" title="Log Out">Log Out</a>]<br />';
	}
	else
	{
		echo 'You are no longer welcome to the ' . SITENAME . ', ' . $user . '<br />';
	}
}

else
	echo 'Welcome to the ' . SITENAME . ', Guest<br />' . 'You must be logged in to view the ' . SITENAME . '<br />' . '[<a href="login.php">Log In</a>] [<a href="register.php">Register</a>]';
?>
</center>
<?php if (isset($_SESSION['Name'])) : ?>
<div id="quickLinks">
<center>
<a target="_blank" href="http://www.reddit.com/"><img src="http://www.reddit.com/favicon.ico" /></a>
<a target="_blank" href="http://www.youtube.com/"><img src="http://www.youtube.com/favicon.ico" /></a>
<a target="_blank" href="http://www.facebook.com/"><img src="http://www.facebook.com/favicon.ico" /></a>
<a target="_blank" href="http://forum.bodybuilding.com/forumdisplay.php?f=19"><img src="http://forum.bodybuilding.com/favicon.ico" /></a>
<a target="_blank" href="http://boards.4chan.org/b/"><img src="http://4chan.org/favicon.ico" /></a>
<a target="_blank" href="http://www.nextgenupdate.com/forums"><img src="http://www.nextgenupdate.com/favicon.ico" /></a>
<?php if (isset($UID) && (isset($_SESSION['Name']) && $UID == 1)) : ?>
<a target="_blank" title="Free HD Porn" href="http://www.livestrong.com/myplate/"><b>L</b></a>
<?php endif; ?>
</center>
</div>
<?php else : ?>
<br />
<?php endif; ?>
<div class="sbheader"><div class="cell"><a href="history.html" target="_new" ><? echo SITENAME . ' ' . VERSION; ?></a></div></div>
<div class="shoutbox">
<div id="smilies" align="center"></div>
<div id="editshout"></div>
<div class="newquery">
<form name="newshout" autocomplete="off" id="newquery">
<table id="inputTable">
<tr>
	<td width="100%">
		<input type="text" id="text" name="text"></input>
	</td>
	<td width="1%" style="white-space:nowrap">
		<input type="submit" id="button" name="submit" class="submitShout" value="Shout"/>
		<input type="button" id="button" name="btnClear" onclick="clearShout()" value="Clear"/>
		<input type="button" id="button" name="btnSmilies" onclick="showSmilies()" value="Smilies"/>
		<input type="button" id="button" onclick="window.location.href = 'usercp'" name="btnOptions" value="Options"/>
		<input type="button" id="button" name="btnHeight" onclick="changeHeight()" value="Height"/>
		<input type="button" id="button" onclick="bold()" name="btnBold" style="font-weight: bold;" value="<? echo ($txtBold) ? "B*" : "B" ?>"/>
		<input type="button" id="button" onclick="underline()" name="btnUnderline" style="text-decoration: underline;" value="<? echo ($txtUnderline) ? "U*" : "U" ?>"/>
		<input type="button" id="button" onclick="italic()" name="btnItalic" style="font-style: italic;" value="<? echo ($txtItalic) ? "I*" : "I" ?>"/>
		<select onchange="color(this)">
			<option>Default</option>
			<option style="color:Red;" name="red">Red</option>
			<option style="color:Crimson">Crimson</option>
			<option style="color:Blue">Blue</option>
			<option style="color:Green">Green</option>
			<option style="color:Orange">Orange</option>
			<option style="color:Brown">Brown</option>
			<option style="color:Black">Black</option>
			<option style="color:Purple">Purple</option>
		</select>
		<select onchange="font(this)">
			<option>Default</option>
			<option style="font-family: Arial;">Arial</option>
			<option style="font-family: Arial Narrow;">Arial Narrow</option>
			<option style="font-family: Book Antiqua;">Book Antiqua</option>
			<option style="font-family: Century Gothic;">Century Gothic</option>
			<option style="font-family: Comic Sans MS;">Comic Sans MS</option>
			<option style="font-family: Courier New;">Courier New</option>
			<option style="font-family: Fixedsys;">Fixedsys</option>
			<option style="font-family: Franklin Gothic Medium;">Franklin Gothic Medium</option>
			<option style="font-family: Garamond;">Garamond</option>
			<option style="font-family: Georgia;">Georgia</option>
			<option style="font-family: Lucida Console;">Lucida Console</option>
			<option style="font-family: Microsoft Sans Serif;">Microsoft Sans Serif</option>
			<option style="font-family: Palatino Linotype;">Palatino Linotype</option>
			<option style="font-family: System;">System</option>
			<option style="font-family: Tahoma;">Tahoma</option>
			<option style="font-family: Times New Roman;">Times New Roman</option>
			<option style="font-family: Trebuchet MS;">Trebuchet MS</option>
			<option style="font-family: Verdana;">Verdana</option>
		</select>
	</td>
</tr>
</table>
</form>
</div>
<div id="tabs"><p id="sblinks"><a href="#" onclick="loadBox(); pm = false; refresh = true; return false;">Mattbox</a>  <a href="#" onclick="viewUsers(); return false;">Active Users</a>: <span id="activeUsers"><script type="text/javascript">setActiveUsers()</script></span></p></div>
<div id="mattboxNotice"></div>
<div class="content" id="content">
<p>
<?php
if(!isset($_SESSION['Name'])) {
	echo '<span style="background-color: #fbef8d;"><b>Mattbox Notice:</b></span> You are not logged in. <a href="login.php">Click Here</a> to use the mattbox.';
}
if(isset($_SESSION['Name']) && !isApproved($_SESSION['Name']))
{
	echo '<span style="background-color: #fbef8d;"><b>Mattbox Notice:</b></span> Your account has not been approved yet. An administrator will review it shortly, check back in a few';
}
?>
</p>
</div>
</div>
<?php if (isset($UID) && ($UID == 10 || $UID == 8)) { ?>
<br />
<div align="center" style="font-size:24px; font-weight:bold; color:#FF0000;">Foxkaz, when you get this message, PM Reaper on NGU or talk to him on AIM when you can.</div>
<?php } ?>
</body>
</html>