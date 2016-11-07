<?php
session_start();//to make sure all session is destroyed
session_destroy();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><title>New Wbridge</title></head>
<link rel=stylesheet type=text/css href='style/generic.css'>
<style>
	body{
		margin:30px 15px 5px 15px;
	}
</style>
<script language=JavaScript1.2 src='js/generic.exe'></script>
<script language=JavaScript1.2 src='js/drag.exe'></script>
<script language=JavaScript1.2 src='js/login.exe'></script>
<script language=JavaScript1.2>
    function onloadFocus(id){
        document.getElementById(id).focus();
    }
</script>
<!--<body background=images/bg.jpg>-->
<!--<body background=images/bg-beon01.png>-->
<!--<body background=images/11.jpg>-->
<body background=images/scale-weights1.jpg onload=onloadFocus('name')>
<!--<div class=drag style='background-color:#CFE9FA;width:228px;text-align:center;border-bottom:#3E71B2 solid 2px;'>-->
<div class=drag style='background-color:#000000;width:228px;text-align:center;border-bottom:#3E71B2 solid 2px;'>
 <!--<div style='padding-top:2px;padding-left:2px;height:22px;width:25px;font-weight:bolder;color:#FFFFFF;'>NewWbridge</div>-->
 <marquee style='width:70px;height:24px;color:#FFFFFF;solid 2px' title='Drag to move' scrolldelay='400'>NewWBRIDGE</marquee>
	<fieldset class=box style='width:209px;margin-left:1px;margin-right:1px;'>
	<div style="background-image:url('./images/key_64.png');background-repeat:no-repeat;">
    <br>
	<b>LOGIN</b>
	<table>
	<tr><td>Username</td><td>:<input  type=text class=myinputtext size=15 id=name onkeypress="return enter(event);" title='Case-sensitif'></td></tr>
	<tr><td>Password</td><td>:<input  type=password class=myinputtext size=15  id=pwd onkeypress="return enter(event);" title='Case-sensitif'></td></tr></tr>
	</table>
	</div>
	<input type=button class=mybutton value='Login' onclick=login()><br>
	<span id=msg></span>
	</fieldset>
	<span class=power>Powered By : <a href=http://http://bga target=new>Minanga IT-DEPT</a></span>
</div>

<?php
//echo CLOSE_THEME();
?>

<div id='progress' style='display:none;border:orange solid 1px;width:150px;position:fixed;right:20px;top:65px;color:#ff0000;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;'>
Please wait.....! <br>
<img src='images/progress.gif'>
</div>
<div id='screenlocker' style='display:none; width:100%;height:0px;color:#666666;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;'>
</div>
<div id='locker' style='display:none; width:100%;height:0px;color:#666666;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;'>
</div>
</body></html>
