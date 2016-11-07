<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once ('lib/zLib.php');

//# Make Query
//$opt = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',"tipe='AFDELING' and induk='".$_POST['kebun']."'");
//#echo "error";
//#print_r($opt);
//
//
//# Isi Options
//echo "var afdeling = document.getElementById('".$_POST['afdelingId']."');";
//foreach($opt as $key=>$row) {
//	echo"<option value=''>".$_SESSION['lang']['all']."</option>";
//    echo "afdeling.options[afdeling.options.length] = new Option('".$row."','".$key."');";
//}
//$opt[''] = $_SESSION['lang']['all'];

$proses=$_POST['proses'];
$kebun=$_POST['kebun'];
$afdeling=$_POST['afdeling'];
$thnTnmId=$_POST['thnTnmId'];
switch($proses)
{
	case'getAfd':
	if($_POST['kebun']=='')
	{
	$optAfd="<option value=''>".$_SESSION['lang']['all']."</option>";
	echo $optAfd;
	}
	else
	{
	$sOpt="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='AFDELING' and induk='".$_POST['kebun']."'";
	$qOpt=mysql_query($sOpt) or die(mysql_error());
	$optAfd="<option value=''>".$_SESSION['lang']['all']."</option>";
	while($rOpt=mysql_fetch_assoc($qOpt))
	{
	$optAfd.="<option value=".$rOpt['kodeorganisasi'].">".$rOpt['namaorganisasi']."</option>";
	}
	//$optAfd.="</select>";
	echo $optAfd;
	}
	break;
	case'getThn':
	
		$sOpt="select distinct tahuntanam from ".$dbname.".setup_blok where left(kodeorg,6)='".$afdeling."'";
		//echo"warning:masuk".$sOpt;exit();
		$qOpt=mysql_query($sOpt) or die(mysql_error());
		$optThn="<option value=''>".$_SESSION['lang']['all']."</option>";
		while($rOpt=mysql_fetch_assoc($qOpt))
		{
		$optThn.="<option value=".$rOpt['tahuntanam'].">".$rOpt['tahuntanam']."</option>";
		}
	//$optAfd.="</select>";
	
	echo $optThn;
	break;
	
	default:
	break;
}



?>