<?php
include('config/connection.php');
//include('function/functions.php');
?>
<script language=javascript1.2 src=js/HttpRequest.js></script>
<center>
<style>
	input{
		height:17px;
		font-size:11px;
		font-family:Tahoma,Arial Narrow;
	}
	select{
	height:17px;
	font-size:11px;
	font-family:Tahoma,Arial Narrow;
	}
</style>
<script language=javascript1.2>
	function load()
	{
		_wilayah=document.getElementById('wilayah').options[document.getElementById('wilayah').selectedIndex].text;
		frmbps.location='previewuploaddata.php?wilayah='+_wilayah;
	}
</script>
<body>
<b>UPLOAD DATA WB KE BPS</b>
<?php
$opt='';
$str="select distinct wilcode from ".$dbname.".mscompany order by wilcode";
$res=mysql_query($str);
while($bar=mysql_fetch_array($res))
{
	$opt.="<option>".$bar[0]."</option>";
}
?>
<fieldset style='width:350px;font-family:Tahoma,Arial Narrow; font-size:11px;'>
	<legend>PILIH WILAYAH:</legend>
	<select id=wilayah><?echo $opt;?></select> <input type=button value=Preview onclick=load() style='height:20px;'>
</fieldset><br>
<div id=pro style='width:200px;background-color:white;border:black solid 1px;display:none;text-align:left'>
<div id=pro_in style='width:0px;background-color:blue;text-align:center;'>&nbsp</div>
</center>
<center>
<IFRAME FRAMEBORDER=yes style='border:darkgreen solid 1px;' HEIGHT=450px WIDTH=98% ID=frmbps name=frmbps NORESIZE=NORESIZE SCROLLING=AUTO SRC=''>

</IFRAME>
</center>
</body>