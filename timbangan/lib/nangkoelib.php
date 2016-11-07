<?php
if (isset($_SESSION['theme']))
   $theme=$_SESSION['theme'];
else
   $theme='skyblue';

function OPEN_BODY($title='NewWbridge')
{
echo"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
<html>
	<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
		<meta http-equiv='Cache-Control' CONTENT='no-cache'>
		<meta http-equiv='Pragma' CONTENT='no-cache'>
		<title>".$title."</title>";
require_once('js/header_menu.php');
echo"
    <script language=JavaScript1.2 src=js/menuscript.js></script>
    <script language=JavaScript1.2 src=js/calendar.exe></script>
    <script language=JavaScript1.2 src=js/drag.exe></script>
    <script language=JavaScript1.2 src=js/generic.js></script>
    <script language=JavaScript1.2 src=js/nangkoelsort.exe></script>
	<link rel=stylesheet type=text/css href=style/menu.css>
	<link rel=stylesheet type=text/css href=style/generic.css>
	<link rel=stylesheet type=text/css href=style/calendarblue.css>
    </head>
<body background=images/bg.jpg onload=verify()>
<!--<body background=images/bg3.jpg onload=verify()>-->
<noscript>
	<span style='font-size:13px;font-family:arial;'>
		<span style='color:#dd3300'>Warning!</span>
			&nbsp&nbsp; QuickMenu may have been blocked by IE-SP2's active
			content option. This browser feature blocks JavaScript from running
			locally on your computer.<br>
			<br>This warning will not display once the menu is on-line.
			To enable the menu locally, click the yellow bar above, and select
			<span style='color:#0033dd;'>'Allow Blocked Content'
		</span>.
	<br><br>To permanently enable active content locally...
		<div style=padding:0px 0px 30px 10px;color:#0033dd;'>
			<br>1: Select 'Tools' --> 'Internet Options' from the IE menu.
			<br>2: Click the 'Advanced' tab.
			<br>3: Check the 2nd option under 'Security' in the tree
			(Allow active content to run in files on my computer.)
		</div>
	</span>
</noscript>
";
}

function CLOSE_BODY()
{
	@require_once('master_footer.php');
	echo "</body></html>";
}

function OPEN_BOX($style='',$title='',$id='',$contentId='')
{
	echo"<div  id='".$id."' class=\"x-box-blue\" style='".$style."'>
		<div class=\"x-box-tl\"><div class=\"x-box-tr\">
		<div class=\"x-box-tc\"></div>
		</div>
		</div>
		<div class=\"x-box-ml\"><div class=\"x-box-mr\">
		<div class=\"x-box-mc\" id='contentBox".$contentId."' style='overflow:auto;'>
		".$title;
}
function CLOSE_BOX()
{
	echo"</div></div></div>
        <div class=\"x-box-bl\"><div class=\"x-box-br\"><div class=\"x-box-bc\"></div></div></div>
        </div>";
}
function drawTab($tabId='T',$arrHeader,$arrContent,$tabLength,$contentLength='300')
{
//if you use more than one tab group on one page you must throw/fill the $tabID var
//array header must the same size as array content of the tab

$tabLength=str_replace("px","",$tabLength);
$tabLength=str_replace(";","",$tabLength);
$contentLength=str_replace("px","",$contentLength);
$contentLength=str_replace(";","",$contentLength);
$stream="
<table border=0 cellspacing=0>
<tr class=tab>";
 for($x=0;$x<count($arrHeader);$x++)
 {
	if($x==0)
	  $stream.="<td id=tab".$tabId.$x." onclick=tabAction(this,".$x.",'".$tabId."',".(count($arrHeader)-1)."); onmouseover=chgBackgroundImg(this,'./images/tab3.png','#d0d0d0');  onmouseout=chgBackgroundImg(this,'./images/tab1.png','#333333');  style=\"background-image:url('./images/tab2.png');color:#FFFFFF;font-weight:bolder;border-right:#dedede solid 1px;width:".$tabLength."px;height:20px\">".$arrHeader[$x]."</td>";
	else
      $stream.="<td id=tab".$tabId.$x." style='border-right:#dedede solid 1px; height:20px; width:".$tabLength."px;' onclick=tabAction(this,".$x.",'".$tabId."',".(count($arrHeader)-1)."); onmouseover=chgBackgroundImg(this,'./images/tab3.png','#d0d0d0');  onmouseout=chgBackgroundImg(this,'./images/tab1.png','#333333'); >".$arrHeader[$x]."</td>";
 }
$stream.="</tr></table>";
 for($x=0;$x<count($arrContent);$x++)
 {
	if($x==0)
       $stream.="<fieldset style='display:\"\";border-color:#2368B0; border-style:solid;border-width:2px; border-top:#1E5896 solid 8px; background-color:#E0ECFF;margin-left:0px;width:".$contentLength."px;' id=content".$tabId.$x.">".$arrContent[$x]."</fieldset>";
	else
	   $stream.="<fieldset style='display:none;border-color:#2368B0; border-style:solid;border-width:2px; border-top:#1E5896 solid 8px; background-color:#E0ECFF;margin-left:0px;width:".$contentLength."px;' id=content".$tabId.$x.">".$arrContent[$x]."</fieldset>";
 }
 echo $stream;
}

function OPEN_THEME($caption='',$width='',$text_align='left')
{
if (isset($_SESSION['theme']))
   $theme=$_SESSION['theme'];
else
   $theme='skyblue';

   if($theme=='black')
      $capcolor='#FFFFFF';
   else
      $capcolor='#333333';

   if(isset($width))
      $lebar=" width=".$width."px";
   else
      $lebar='';

	$text="<table class='boundary' ".$lebar." cellspacing='0' border='0' padding='0' style='font-family:Tahoma;font-size:11px;'>
	<tr class='trheader' style='height:22px;'>

	<td class='headleft' style='width:7px;height:22px;background: url(images/".$theme."/a1.gif);background-repeat:no-repeat;'></td>
	<td class='headtop' align='".$text_align."' style='color:".$capcolor.";height:22px;background: url(images/".$theme."/a2.gif);'><b>".$caption."</b></td>
	<td class='headright' style='width:13px;height:22px;background: url(images/".$theme."/a3.gif);background-repeat:no-repeat;'></td>
	</tr>

	<tr>
	<td class='bodyleft' style='background: url(images/".$theme."/a4.gif);'></td>
	<td class='content' style='padding:0px 0px 0px 0px;background-color:#FFFFFF;'>";
	return $text;
}

function CLOSE_THEME()
{
if (isset($_SESSION['theme']))
   $theme=$_SESSION['theme'];
else
   $theme='skyblue';
	$text="</td>
	<td class='bodyright' style='background: url(images/".$theme."/a5.gif);background-repeat:repeat-y;'></td>
	</tr>

	<tr class='trbottom' style='height:7px;'>
	<td class='bottomleft' style='background: url(images/".$theme."/a6.gif);background-repeat:no-repeat;'></td>
	<td class='bottom' style='background: url(images/".$theme."/a7.gif);background-repeat:repeat-x;'></td>
	<td class='bottomright' style='background: url(images/".$theme."/a8.gif);background-repeat:no-repeat;'></td></tr>
	</table>";
	return $text;
}

function OPEN_THEME2($caption='',$width='',$text_align='left')
{
if (isset($_SESSION['theme']))
   $theme=$_SESSION['theme'];
else
   $theme='skyblue';

   if($theme=='black')
      $capcolor='#FFFFFF';
   else
      $capcolor='#333333';

   if(isset($width))
      $lebar=" width=".$width."px";
   else
      $lebar='';

	$text="<table class='boundary' ".$lebar." cellspacing='0' border='0' padding='0' style='font-family:Tahoma;font-size:11px;'>
	<tr class='trheader' style='height:22px;'>

	<td class='headleft' style='width:7px;height:22px;background: url(images/".$theme."/a1-1.gif);background-repeat:no-repeat;'></td>
	<td class='headtop' align='".$text_align."' style='color:".$capcolor.";height:22px;background: url(images/".$theme."/a2-1.gif);'><b>".$caption."</b></td>
	<td class='headright' style='width:13px;height:22px;background: url(images/".$theme."/a3-1.gif);background-repeat:no-repeat;'></td>
	</tr>

	<tr>
	<td class='bodyleft' style='background: url(images/".$theme."/a4.gif);'></td>
	<td class='content' style='padding:0px 0px 0px 0px;background-color:#FFFFFF;'>";
	return $text;
}

function CLOSE_THEME2()
{
if (isset($_SESSION['theme']))
   $theme=$_SESSION['theme'];
else
   $theme='skyblue';
	$text="</td>
	<td class='bodyright' style='background: url(images/".$theme."/a5.gif);background-repeat:repeat-y;'></td>
	</tr>

	<tr class='trbottom' style='height:7px;'>
	<td class='bottomleft' style='background: url(images/".$theme."/a6.gif);background-repeat:no-repeat;'></td>
	<td class='bottom' style='background: url(images/".$theme."/a7.gif);background-repeat:repeat-x;'></td>
	<td class='bottomright' style='background: url(images/".$theme."/a8.gif);background-repeat:no-repeat;'></td></tr>
	</table>";
	return $text;
}

function tanggalnormal($_q)
{
 //$_q=str_replace("-","",$_q);
 //$_retval=substr($_q,6,2)."-".substr($_q,4,2)."-".substr($_q,0,4);
 $_retval=substr($_q,8,2)."-".substr($_q,5,2)."-".substr($_q,0,4)." ".substr($_q,11,2).":".substr($_q,14,2).":".substr($_q,17,2);
 return($_retval);
}

function tanggalsystem($_q)//from format dd-mm-YYYY
{
 //$_retval=substr($_q,6,4).substr($_q,3,2).substr($_q,0,2);
 $_retval=substr($_q,6,4)."-".substr($_q,3,2)."-".substr($_q,0,2)." ".substr($_q,11,2).":".substr($_q,14,2).":".substr($_q,17,2);
 return($_retval);//return 8 chardate format eg.20090804
}

function hari($tgl)
{
//return name of days in Indonesia
	$bln=substr($tgl,5,2);
	$thn=substr($tgl,0,4);
	$tgl=substr($tgl,8,2);
	$ha=date("w", mktime(0, 0, 0, $bln,$tgl,$thn));
	$x=array ("Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu");
	return($x[$ha]);
}

function getUserEmail($uname='',$userid=0,$conn)
{
	//find user email address on user_empl table
	global $dbname;
	$email='';
	$strAv="select email from ".$dbname.".user_empl
	        where  userid=".$userid;
	$resAv=mysql_query($strAv);
	while($barAv=mysql_fetch_object($resAv))
	{
		$email=$barAv->email;
	}
	if(strlen($email)>5)
	{
		if(strpos($email,'@')>0 and strpos($email,'.')>2)
		{
			return $email;
		}
		else
		return '';
	}
	else
      return '';
}
function sendMail($subject,$content,$from='',$to,$cc='',$bcc='',$replyTo='')
{
	//$subject,$content and $to is obligatory
	$headers  ='MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: ".$from. "\r\n";
    $headers .= "Cc: ".$cc. "\r\n";
    $headers .= 'Bcc: '.$bcc. "\r\n";
	$headers .= 'Reply-To: '.$replyTo. "\r\n";
    if(mail($to,$subject,$content,$headers))
	    return true;
	else
	  {
	    return false;
	  }
}
function readCountry($file)
{
$comment = "#";

$fp = fopen($file, "r");
$lin=-1;
while (!feof($fp)) {
$line = fgets($fp, 4096); // Read a line.
    if(!ereg("^#",$line) AND $line!='')
	{
    $lin+=1;
	$pieces = explode("=", $line);
    $name = trim($pieces[0]);
    $code = trim($pieces[1]);
	$curr = trim($pieces[2]);
	$cont = trim($pieces[3]);
    $country[$lin][0] = $name;
	$country[$lin][1] = $code;
	$country[$lin][2] = $curr;
	$country[$lin][3] = $cont;
	}
  }

fclose($fp);
return $country;
}
?>