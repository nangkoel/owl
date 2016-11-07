<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><title>NangkoelFramework</title></head>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<style>
	body{
		margin:30px 15px 5px 15px;
		background-color:#E8F4F4;
	}
	#smallOwl {
		position:absolute;
		top:-80px;
		right:-80px;
		z-index:1;
	}
</style>
<body>
::Production Server    
<img src="images/OWL_OV.png" style="position:fixed;right:0;bottom:0;z-index:-999;">
<div class=drag style='background-color:#CFE9FA;width:228px;text-align:center;border-bottom:#3E71B2 solid 2px;margin:15% 0 0 40%;border:orange solid 1px; '>
    <br><marquee style='width:150px;height:24px;color:#ADADAD' title='Drag to move' scrolldelay='400'><b>OWL-Plantation System</b></marquee>	

	<div style="background-image:url('./images/key_64.png');background-repeat:no-repeat;">
    <br>
	<br>
	<b style='font-size:14px;'>L O G I N</b>	
	<table align="center">
	<tr><td>Username</td><td>:</td><td align="left"><input  type=text  class=myinputtext size=20 id=name onkeypress="return enter(event);" title='Case-sensitif'></td></tr>
	<tr><td>Password</td><td>:</td><td align="left"><input  type=password   class=myinputtext   size=20  id=pwd onkeypress="return enter(event);" title='Case-sensitif'></td></tr>
	<tr><td>Language
	</td><td>:</td><td align="left"><select id=language>
	</select></td></tr>
	</table>
	</div>
	<input type=button class=mybutton value='Login' onclick=login()><br>
	<div id=msg></div>
	<span class=power>Powered By : <a href=http://www.owl-plantation.com target=new>Blasius Cosa</a></span> 
</div>

<div id='progress' style='display:none;border:orange solid 1px;width:150px;position:absolute;right:20px;top:65px;color:#ff0000;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;'>
Please wait.....! <br>
<img src='images/progress.gif'>
</div>
</body></html>
