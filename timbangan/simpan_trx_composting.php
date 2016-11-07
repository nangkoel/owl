<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$TICKETNO=$_POST['TICKETNO'];
$PENERIMA=$_POST['PENERIMA'];
$VEHNO=$_POST['VEHNO'];
$TRPCODE=$_POST['TRPCODE'];
$DRIVER=$_POST['DRIVER'];
$DATEIN=tanggalsystem($_POST['DATEIN']);
$WEIGH1=$_POST['WEIGH1'];
$USERID=$_SESSION['standard']['uname'];
$USERLEVEL=$_SESSION['standard']['access_level'];
//$MILLCODE=$_SESSION['MILLCODE'];
$OUTIN=$_POST['OUTIN'];
$PRODUCTCODE=$_POST['PRODUCTCODE'];
$WEIGH2=$_POST['WEIGH2'];
$SLOC=$_POST['SLOC'];
$DATEOUT=tanggalsystem($_POST['DATEOUT']);
$NETTO=$_POST['NETTO'];
$IDWB=$_POST['IDWB'];
$TICKETNO2=$_POST['TICKETNO2'];
//print_r($_POST);
//$CREATEDATE=$_POST['CREATEDATE'];
//print_r($_POST);

$str5="select * from ".$dbname.".mssystem";
$res5=mysql_query($str5);
	while($bar3=mysql_fetch_object($res5)){
		$timeveh=$bar3->TIMEVEH;
	}

$waktu=mktime(date("H"),date("i")-$timeveh,0,date("m"),date("d"),date("Y"));
$_now=date('Y-m-d H:i:s',$waktu);

$str2="select VEHTARMIN,VEHTARMAX from ".$dbname.".msvehicle where ".$dbname.".msvehicle.VEHNOCODE='".$_POST['VEHNO']."'";
$res2=mysql_query($str2);
	//print_r($res2);
		while($bar=mysql_fetch_object($res2)){
			$tarmin=$bar->VEHTARMIN;$tarmax=$bar->VEHTARMAX;
		}
$str3="select * from ".$dbname.".mstrxtbs where".$dbname.".mstrxtbs.DATEIN>='".$_now."' and ".$dbname.".mstrxtbs.OUTIN='1' and VEHNOCODE like '".$_POST['VEHNO']."' order by id desc limit 1";
$res3=mysql_query($str3);

$str6="select * from ".$dbname.".mstrxtbs where ".$dbname.".mstrxtbs.SPNO='".$_POST['SPNO']."' and OUTIN='1'";
$res6=mysql_query($str6);

$str4="select * from ".$dbname.".mstrxtbs where PRODUCTCODE='".$_POST['PRODUCTCODE']."' order by id desc limit 1";
$res4=mysql_query($str4);
	while($bar2=mysql_fetch_array($res4)){
		$wei1=$bar2[11];
	}

if($OUTIN==1){
	//if ((($tarmin<=$WEIGH2) && ($tarmax>=$WEIGH2)) || (($WEIGH2==0)) ){
		if ($USERLEVEL!=16){
						$str="insert into ".$dbname.".mstrxtbs
					     	 (IDWB,TICKETNO,OUTIN,PRODUCTCODE,DATEIN,WEI1ST,VEHNOCODE,
						  	 TRPCODE,DRIVER,USERID,PENERIMA,TICKETNO2)
							  values('".$IDWB."',
						 	  '".$TICKETNO."',".$OUTIN.",
							  '".$PRODUCTCODE."','".$DATEIN."',".$WEIGH1.",
							  '".$VEHNO."','".$TRPCODE."','".$DRIVER."',
							  '".$_SESSION['standard']['username']."',
							  '".$PENERIMA."',
							  '".$TICKETNO2."'
							  )";
						//echo "Gagal ".$str;
						if(mysql_query($str)){
							//printf("Updated Records: %d\n", mysql_affected_rows());
							//if($rc>0)
							$xc="update ".$dbname.".msvehicle set FLAG='Y' where VEHNOCODE='".$VEHNO."'";
							$reg=mysql_query($xc);
							echo"0";
						}
						else{
				  			echo "Error: ".addslashes(mysql_error($conn));
						}
		}
		else {
			$str="insert into ".$dbname.".mstrxtbs
					     	 (IDWB,TICKETNO,OUTIN,PRODUCTCODE,DATEIN,WEI1ST,VEHNOCODE,
						  	 TRPCODE,DRIVER,USERID,PENERIMA,TICKETNO2)
							  values('".$IDWB."',
						 	  '".$TICKETNO."',".$OUTIN.",
							  '".$PRODUCTCODE."','".$DATEIN."',".$WEIGH1.",
							  '".$VEHNO."','".$TRPCODE."','".$DRIVER."',
							  '".$_SESSION['standard']['username']."',
							  '".$PENERIMA."',
							  '".$TICKETNO2."'
							  )";
						//echo "Gagal ".$str;
						if(mysql_query($str)){
							//printf("Updated Records: %d\n", mysql_affected_rows());
							//if($rc>0)
							$xc="update ".$dbname.".msvehicle set FLAG='Y' where VEHNOCODE='".$VEHNO."'";
							$reg=mysql_query($xc);
							echo"0";
						}
						else{
				  			echo "Error: ".addslashes(mysql_error($conn));
						}
		}
}
if ($OUTIN==0){
	$str8="select DATEIN,NODOTRP from ".$dbname.".mstrxtbs where ".$dbname.".mstrxtbs.TICKETNO='".$_POST['TICKETNO']."'";
	$res8=mysql_query($str8);
	while($bar8=mysql_fetch_object($res8)){
		$datein2=$bar8->DATEIN;$nodo2=$bar8->NODOTRP;
	}
	if ($USERLEVEL!=16){
			if (($wei2!==$WEIGH2)) {
				$str="insert into ".$dbname.".mstrxtbs
			     	 (IDWB,TICKETNO,OUTIN,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,VEHNOCODE,
				  	 TRPCODE,DRIVER,NETTO,USERID,PENERIMA,TICKETNO2)
					  values('".$IDWB."',
				 	 '".$TICKETNO."',".$OUTIN.",
					  '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".$WEIGH1.",".$WEIGH2.",
					  '".$VEHNO."','".$TRPCODE."','".$DRIVER."',".$NETTO.",'".$_SESSION['standard']['username']."',
					  '".$PENERIMA."','".$TICKETNO2."'
					  )";
				//echo "Gagal ".$str;
				if(mysql_query($str))
				{
					//printf("Updated Records: %d\n", mysql_affected_rows());
					//if($rc>0)
					$xc="update ".$dbname.".msvehicle set FLAG='T' where VEHNOCODE='".$VEHNO."'";
					$reg=mysql_query($xc);
					echo"0";
				}
				else
				{
		  			echo "Error: ".addslashes(mysql_error($conn));	
				}
			}
			
			else{
				echo "Error : Berat Timbang I sama dengan Record berat Timbang terakhir di database, Diperlukan Proses Authorisasi.";
			}
	}
	else {
		$str="insert into ".$dbname.".mstrxtbs
			     	 (IDWB,TICKETNO,OUTIN,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,VEHNOCODE,
				  	 TRPCODE,DRIVER,NETTO,USERID,PENERIMA,TICKETNO2)
					  values('".$IDWB."',
				 	 '".$TICKETNO."',".$OUTIN.",
					  '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".$WEIGH1.",".$WEIGH2.",
					  '".$VEHNO."','".$TRPCODE."','".$DRIVER."',
					  ".$NETTO.",'".$_SESSION['standard']['username']."','".$PENERIMA."','".$TICKETNO2."'
					  )";
				//echo "Gagal ".$str;
				if(mysql_query($str))
				{
					//printf("Updated Records: %d\n", mysql_affected_rows());
					//if($rc>0)
					$xc="update ".$dbname.".msvehicle set FLAG='T' where VEHNOCODE='".$VEHNO."'";
					$reg=mysql_query($xc);
					echo"0";
				}
				else
				{
		  			echo "Error: ".addslashes(mysql_error($conn));	
				}
	}	
}
?>

