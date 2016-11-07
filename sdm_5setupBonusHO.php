<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/sdm_payrollHO.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?php
include('master_mainMenu.php');
//+++++++++++++++++++++++++++++++++++++++++++++
	OPEN_BOX('','<b>'.$_SESSION['lang']['setupbonus'].':</b>');
		echo"<div id=EList>";
		
		//get current
		$arrCurr=Array();
		$stra="select * from ".$dbname.".sdm_ho_thr_setup";
		$resa=mysql_query($stra);
		while($bara=mysql_fetch_object($resa))
		{
			array_push($arrCurr,$bara->component);
		}
		
		//get component
		$str="select * from ".$dbname.".sdm_ho_component where type='basic'";
		$res=mysql_query($str);
		echo"<fieldset>
		      <legend>".$_SESSION['lang']['komponenbonus']."</legend>
			 ";
		while($bar=mysql_fetch_object($res))
		{
			if($bar->id==1)
			  $s=' disabled ';
			else
			  $s='';
			    
			if (in_array($bar->id, $arrCurr)) {
    			echo"<input type=checkbox ".$s." checked onclick=bonusSetup(this,this.value) value=".$bar->id." id=com".$bar->id.">".$bar->name."<br>";
			}
            else
			{
			echo"<input type=checkbox ".$s."  onclick=bonusSetup(this,this.value) value=".$bar->id." id=com".$bar->id.">".$bar->name."<br>";
			}
		}	 
		echo"</fieldet>"; 
		echo"</div>";
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>