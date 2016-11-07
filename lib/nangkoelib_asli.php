<?php
if (isset($_SESSION['theme']))
   $theme=$_SESSION['theme'];
else
   $theme='skyblue';
   
function OPEN_BODY($title='OWL System')
{
echo"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
<html>
	<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
		<meta http-equiv='Cache-Control' CONTENT='no-cache'>
		<meta http-equiv='Pragma' CONTENT='no-cache'>
		<title>".$title."</title>";
echo" 
    <script language=JavaScript1.2 src=js/menuscript.js></script>
    <script language=JavaScript1.2 src=js/calendar.js></script>
    <script language=JavaScript1.2 src=js/drag.js></script>
    <script language=JavaScript1.2 src=js/generic.js></script>
    <script language=JavaScript1.2 src=js/nangkoelsort.js></script>
	<link rel=stylesheet type=text/css href=style/menu.css>
	<link rel=stylesheet type=text/css href=style/generic.css>	
	<link rel=stylesheet type=text/css href=style/calendarblue.css>
    </head>
<body  style='margin:30px;background-color:#E8F4F4;' onload=verify()>
<img src='images/owl.jpg' style='position:fixed;right:0;bottom:0;z-index:-999'>
<img id='smallOwl' src='images/owl1.png' width='300px'
	style='position:absolute;top:20%;left:35%;z-index:-998'>
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
	echo"<div  id='".$id."' class=\"x-box-blue\" style='".$style."'><div class=\"x-box-tl\"><div class=\"x-box-tr\">
		<div class=\"x-box-tc\"></div></div></div><div class=\"x-box-ml\"><div class=\"x-box-mr\">
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

function tanggalnormal($_q)
{
 $_q=str_replace("-","",$_q);
 $_retval=substr($_q,6,2)."-".substr($_q,4,2)."-".substr($_q,0,4);
 return($_retval);
}

function tanggalsystem($_q)//from format dd-mm-YYYY
{
 $_retval=substr($_q,6,4).substr($_q,3,2).substr($_q,0,2);
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
	$strAv="select email from ".$dbname.".organisasi
	        where  karyawanid=".$userid;		
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

function getFieldName($TABLENAME,$output)
{
//get Fieldname on the table mentioned
//this is general purposed
//return value available on array or string like <option value...>
	global $dbname;
	global $conn;
	$option='';
	$arrReturn=Array();
	$strUx="select * from ".$dbname.".".$TABLENAME." limit 1";
	$resUx=mysql_query($strUx);
	while($PxUx=mysql_fetch_field($resUx))
	{
		array_push($arrReturn, $PxUx->name);
		$option.="<option value='".$PxUx->name."'>".$PxUx->name."</option>"; 
	}
	if($output=='array')
	  return $arrReturn;
	else
	  return $option; 
}

function printTableController($TABLENAME)
{
//seacrh controller for table query
//javascript function stated in tablebrowser.js	
$field=getFieldName($TABLENAME,'option');
echo"<br>".$_SESSION['lang']['find']." <input type=text class=myinputtext id=txtcari onkeypress=\"return tanpa_kutip(event);\" size=15 maxlength=20 onblur=checkThis(this) value=All> ".$_SESSION['lang']['oncolumn'].":<select id=field>".$field."</select>
    ".$_SESSION['lang']['order']." <select id=order1>".$field."</select>,<select id=order2>".$field."</select>
	 ";
echo"<button class=mybutton onclick=\"browseTable('".$TABLENAME."');\">".$_SESSION['lang']['find']."</button>";
	
}

function printSearchOnTable($TABLENAME,$fieldname,$texttofind,$order='',$curpage=0,$MAX_ROW=1000)
{
 	//$MAX_ROW plese change this if required
	global $dbname;
	global $conn;
	$offset   =$curpage*$MAX_ROW;//
	$disp_page=$curpage+1;
	$field=getFieldName($TABLENAME,'array'); 
	if($texttofind!='')
	{
		$where=" where ".$fieldname." like '%".$texttofind."%'"; 
	}	
	else
	{
		$where='';
	}
	$strXu="select * from ".$dbname.".".$TABLENAME." ".$where."  order by ".$order." limit ".$offset.",".$MAX_ROW;
	$resXu=mysql_query($strXu);
	//get num rows of the query
	//and create page navigator
	$strXur="select * from ".$dbname.".".$TABLENAME." ".$where;
	$resXur=mysql_query($strXur);
	$numrows=mysql_num_rows($resXur);
	if($numrows>$MAX_ROW)
	{
		if(($numrows%$MAX_ROW)!=0)
		    $page=(floor($numrows/$MAX_ROW))+1;
		else
		    $page=$numrows/$MAX_ROW;	
	}	
	else
	{
		$page=1;
	}
	echo $_SESSION['lang']['page']." ".$disp_page." ".$_SESSION['lang']['of']." ".$page." (Max.".$MAX_ROW."/".$_SESSION['lang']['page'].")";
	echo" [ ".$_SESSION['lang']['gotopage'].":<select id=page>";
	for($y=0;$y<$page;$y++)
	{
		echo"<option value=".$y.">".($y+1)."</option>";
	}
	echo "</select> <button onclick=\"navigatepage('".$TABLENAME."');\" class=mybutton>Go</button> ]";
		
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead><tr class=rowheader>";
	for($x=0;$x<count($field);$x++)
	{
		echo"<td>".$field[$x]."</td>";
	}	 
	echo"</tr></thead><tbody>";
	$num=0;
	while($barXu=mysql_fetch_array($resXu))
	{
		echo"<tr class=rowcontent>";
		for($x=0;$x<count($field);$x++)
		{
			echo"<td>".$barXu[$x]."</td>";
		}	
		echo"</tr>";		
	}
	echo"</tbody><tfoot></tfoot></table>";
}
function sendMail($subject,$content,$from='',$to,$cc='',$bcc='',$replyTo='')//for win
{
	//FOR WINDOW SERVER ONLY
	//$subject,$content and $to is obligatory
	$headers  ='MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    if($from!='')
    $headers .= "From: ".$from. "\r\n";
    if($cc!='')
    $headers .= "Cc: ".$cc. "\r\n";
    if($bcc!='')
	$headers .= 'Bcc: '.$bcc. "\r\n";
	if($replyTo!='')
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

function readOrgType($file)
{
$comment = "#";

$fp = fopen($file, "r");
$lin=0;
while (!feof($fp)) {
$line = fgets($fp, 4096); // Read a line.
    if(!ereg("^#",$line) AND $line!='')
	{
    $lin+=1;
	$pieces = explode("=", $line);
    $code = trim($pieces[0]);
    $name = trim($pieces[1]);
    $orgtype[$lin][0] = $code;
	$orgtype[$lin][1] = $name;
	}
  }

fclose($fp);
return $country;
}

function numToMonth($int,$lang='E',$format='short')
{
	$arr=Array();
	$arr[1]['E']['short']='Jan';
	$arr[1]['I']['short']='Jan';
	$arr[1]['E']['long']='January';
	$arr[1]['I']['long']='Januari';
		$arr[2]['E']['short']='Feb';
		$arr[2]['I']['short']='Peb';
		$arr[2]['E']['long']='February';
		$arr[2]['I']['long']='Feberuari';	
	$arr[3]['E']['short']='Mar';
	$arr[3]['I']['short']='Mar';
	$arr[3]['E']['long']='Maret';
	$arr[3]['I']['long']='Maret';	
		$arr[4]['E']['short']='Apr';
		$arr[4]['I']['short']='Apr';
		$arr[4]['E']['long']='April';
		$arr[4]['I']['long']='April';			
	$arr[5]['E']['short']='May';
	$arr[5]['I']['short']='Mei';
	$arr[5]['E']['long']='May';
	$arr[5]['I']['long']='Mei';	
		$arr[6]['E']['short']='Jun';
		$arr[6]['I']['short']='Jun';
		$arr[6]['E']['long']='Juni';
		$arr[6]['I']['long']='Juni';
	$arr[7]['E']['short']='Jul';
	$arr[7]['I']['short']='Jul';
	$arr[7]['E']['long']='July';
	$arr[7]['I']['long']='Juli';	
		$arr[8]['E']['short']='Aug';
		$arr[8]['I']['short']='Agu';
		$arr[8]['E']['long']='August';
		$arr[8]['I']['long']='Agustus';
	$arr[9]['E']['short']='Sep';
	$arr[9]['I']['short']='Sep';
	$arr[9]['E']['long']='September';
	$arr[9]['I']['long']='September';	
		$arr[10]['E']['short']='Oct';
		$arr[10]['I']['short']='Okt';
		$arr[10]['E']['long']='October';
		$arr[10]['I']['long']='Oktober';
	$arr[11]['E']['short']='Nov';
	$arr[11]['I']['short']='Nop';
	$arr[11]['E']['long']='November';
	$arr[11]['I']['long']='Nopember';	
		$arr[12]['E']['short']='Dec';
		$arr[12]['I']['short']='Des';
		$arr[12]['E']['long']='December';
		$arr[12]['I']['long']='Desember';
		
//find and return		
	$int=intval($int);
    return $arr[$int][$lang][$format];
}
?>