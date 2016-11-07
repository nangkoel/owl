<?php
	require_once('master_validation.php');
	require_once('config/connection.php');
	include('lib/nangkoelib.php');
	include_once('lib/zLib.php');
	$org_code=$_POST['code_org'];
	$code_block=$_POST['kode_blok'];
	$no_rmh=$_POST['rmh_no'];
	$method=$_POST['method'];
	
	
	
	switch($method)
	{
	case'get_blok':
	$optOrg='';
	$sql="select blok from ".$dbname.".sdm_perumahanht where kodeorg='".$org_code."' group by blok";
	//echo"warning:".$sql;
	$query=mysql_query($sql) or die(mysql_error());
	$optOrg.="<option value=></option>";
	while($res=mysql_fetch_assoc($query))
	{
		$optOrg.="<option value=".$res['blok'].">".$res['blok']."</option>";
		
	}
	
	
	echo $optOrg;
	break;	
	case'get_normh':
	$optNormh='';
	//$optNormh.="<option value=></option>";
	//echo"warning:".$no_rmh."---".$code_block;
	if(($no_rmh!=0)&&($code_block!=0))
	{
		$where.=" kodeorg='".$org_code."' and blok='".$code_block."' and norumah='".$no_rmh."'";
	}
	elseif($code_block!='')
	{
		$where.=" kodeorg='".$org_code."' and blok='".$code_block."'";
	}
	$sql="select norumah from ".$dbname.".sdm_perumahanht where".$where;
	//echo "warning:".$sql;
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		
		$optNormh.="<option value=".$res['norumah'].">".$res['norumah']."</option>";
	}
	echo $optNormh;
	break;	
		default:
		break;
	}
?>