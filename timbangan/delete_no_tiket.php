<?php
require_once('master_validation.php');
require_once('config/connection.php');

$TICKETNO=strtoupper($_POST['TICKETNO']);

$str="SELECT * from ".$dbname.".mstrxtbs where TICKETNO2='".$TICKETNO."' and (OUTIN=1 or OUTIN=0) ";
$res=mysql_query($str);
if(mysql_num_rows($res)>1)
{
 exit("Error: Kendaraan sudah keluar, data tidak capat di hapus");
}

while ($bar=mysql_fetch_object($res)){
         $IDWB=$bar->IDWB;
	$id=$bar->id;$TICKETNO=$bar->TICKETNO;$OUTIN=$bar->OUTIN;$SPBNO=$bar->SPBNO;$CTRNO=$bar->CTRNO;$SIPBNO=$bar->SIPBNO;
	$SIPBQTY=$bar->SIPBQTY;$SPNO=$bar->SPNO;$PRODUCTCODE=$bar->PRODUCTCODE;$DATEIN=$bar->DATEIN;$DATEOUT=$bar->DATEOUT;
	$WEI1ST=$bar->WEI1ST;$WEI2ND=$bar->WEI2ND;$MILLCODE=$bar->MILLCODE;$SLOC=$bar->SLOC;$VEHNOCODE=$bar->VEHNOCODE;
	$TRPCODE=$bar->TRPCODE;$UNITCODE=$bar->UNITCODE;$DIVCODE=$bar->DIVCODE;$TAHUNTANAM=$bar->TAHUNTANAM;
	$JMLHJJG=$bar->JMLHJJG;$BRONDOLAN=$bar->BRONDOLAN;$DRIVER=$bar->DRIVER;$NETTO=$bar->NETTO;$BERATKIRIM=$bar->BERATKIRIM;
	$USERID=$bar->USERID;$NODOTRP=$bar->NODOTRP;$SATUANBERAT=$bar->SATUANBERAT;$PENERIMA=$bar->PENERIMA;
	$APPROVTARRA=$bar->APPROVTARRA;$GI=$bar->GI;$GR=$bar->GR;$TF=$bar->TF;$TRANSACTIONTYPE=$bar->TRANSACTIONTYPE;
	$JENISSPB=$bar->JENISSPB;$SPNOBULKING=$bar->SPNOBULKING;$SLOCBULKING=$bar->SLOCBULKING;$DOCNO=$bar->DOCNO;
	$DOCQTY=$bar->DOCQTY;$BPS=$bar->BPS;$PENGIRIM=$bar->PENGIRIM;$KETERANGAN=$bar->KETERANGAN;$TAHUNTANAM2=$bar->TAHUNTANAM2;
	$JMLHJJG2=$bar->JMLHJJG2;$BRONDOLAN2=$bar->BRONDOLAN2;$TAHUNTANAM3=$bar->TAHUNTANAM3;$JMLHJJG3=$bar->JMLHJJG3;
	$BRONDOLAN3=$bar->BRONDOLAN3;$TICKETNO2=$bar->TICKETNO2;$NOSEGEL=$bar->NOSEGEL;
	if ($SIPBQTY==''){
		$SIPBQTY=0;
	}
	if ($WEI2ND==''){
		$WEI2ND=0;
	}
	if ($JMLHJJG==''){
		$JMLHJJG=0;
	}
	if ($BRONDOLAN==''){
		$BRONDOLAN=0;
	}
	if ($NETTO==''){
		$NETTO=0;
	}
	if ($NETTO==''){
		$NETTO=0;
	}
	if ($TRANSACTIONTYPE==''){
		$TRANSACTIONTYPE=0;
	}
	if ($DOCQTY==''){
		$DOCQTY=0;
	}
	if ($JMLHJJG2==''){
		$JMLHJJG2=0;
	}
	if ($JMLHJJG3==''){
		$JMLHJJG3=0;
	}
	if ($BRONDOLAN2==''){
		$BRONDOLAN2=0;
	}
	if ($BRONDOLAN3==''){
		$BRONDOLAN3=0;
	}
	$str2="insert into ".$dbname.".mstrxtbslogdel
		  (IDWB,id,TICKETNO,OUTIN,SPBNO,CTRNO,SIPBNO,SIPBQTY,SPNO,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,MILLCODE,SLOC,
		   VEHNOCODE,TRPCODE,UNITCODE,DIVCODE,TAHUNTANAM,JMLHJJG,BRONDOLAN,DRIVER,NETTO,BERATKIRIM,USERID,NODOTRP,
		   SATUANBERAT,PENERIMA,APPROVTARRA,GI,GR,TF,TRANSACTIONTYPE,JENISSPB,SPNOBULKING,SLOCBULKING,DOCNO,DOCQTY,
		   BPS,PENGIRIM,KETERANGAN,TAHUNTANAM2,JMLHJJG2,BRONDOLAN2,TAHUNTANAM3,JMLHJJG3,BRONDOLAN3,TICKETNO2,
		   NOSEGEL)
		   values('".$IDWB."',
		   ".$id.",".$TICKETNO.",".$OUTIN.",'".$SPBNO."','".$CTRNO."','".$SIPBNO."',".$SIPBQTY.",'".$SPNO."','".$PRODUCTCODE."',
		   '".$DATEIN."','".$DATEOUT."',".$WEI1ST.",".$WEI2ND.",'".$MILLCODE."','".$SLOC."','".$VEHNOCODE."','".$TRPCODE."',
		   '".$UNITCODE."','".$DIVCODE."','".$TAHUNTANAM."','".$JMLHJJG."','".$BRONDOLAN."','".$DRIVER."',".$NETTO.",
		   '".$BERATKIRIM."','".$USERID."','".$NODOTRP."','".$SATUANBERAT."','".$PENERIMA."','".$APPROVTARRA."',
		   '".$GI."','".$GR."','".$TF."','".$TRANSACTIONTYPE."','".$JENISSPB."','".$SPNOBULKING."','".$SLOCBULKING."',
		   '".$DOCNO."','".$DOCQTY."','".$BPS."','".$PENGIRIM."','".$KETERANGAN."','".$TAHUNTANAM2."',
		   '".$JMLHJJG2."','".$BRONDOLAN2."','".$TAHUNTANAM3."','".$JMLHJJG3."','".$BRONDOLAN3."','".$TICKETNO2."',
		   '".$NOSEGEL."'
		   )";
		   //echo $str2;

	if(mysql_query($str2))
	{
			//echo"0";
		$str3="delete from ".$dbname.".mstrxtbs where TICKETNO2='".$TICKETNO2."' and (OUTIN=1 or OUTIN=0)";
		//echo $str3;
		$res3=mysql_query($str3);
	}
	else
	{
 		 echo "Error: ".addslashes(mysql_error($conn));
	}
}
?>
