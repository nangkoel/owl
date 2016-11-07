<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$TICKETNO=$_POST['TICKETNO'];
$SPBNO=$_POST['SPBNO'];
$VEHNO=strtoupper($_POST['VEHNO']);
$TRPCODE=$_POST['TRPCODE'];
$DRIVER=$_POST['DRIVER'];
$DATEIN=tanggalsystem($_POST['DATEIN']);
$WEIGH1=$_POST['WEIGH1'];
$OUTIN=$_POST['OUTIN'];
$PRODUCTCODE=$_POST['PRODUCTCODE'];
$CEKBOX=$_POST['CEKBOX'];
$WEIGH2=$_POST['WEIGH2'];
$SLOC=$_POST['SLOC'];
$DATEOUT=tanggalsystem($_POST['DATEOUT']);
$NETTO=$_POST['NETTO'];
$PENERIMA=$_POST['PENERIMA'];
$IDWB=$_POST['IDWB'];
$TICKETNO2=$_POST['TICKETNO2'];
$USERLEVEL=$_SESSION['standard']['access_level'];
//ambil millcode
$str="select millcode from ".$dbname.".mssystem";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $MILLCODE=$bar->millcode;
}
if($MILLCODE=='')
    $MILLCODE=$_POST['MILLCODE'];
	
$USERID=$_SESSION['standard']['uname'];
$str5="select * from ".$dbname.".mssystem";
$res5=mysql_query($str5);
	while($bar3=mysql_fetch_object($res5)){
		$timeveh=$bar3->TIMEVEH;
	}
//pastikan kendaraan ada;
$strx="select VEHNOCODE from ".$dbname.".msvehicle where VEHNOCODE='".$VEHNO."'";
$resx=mysql_query($strx);
if(mysql_num_rows($resx)>0)
{

$waktu=mktime(date("H"),date("i")-$timeveh,0,date("m"),date("d"),date("Y"));
$_now=date('Y-m-d H:i:s',$waktu);

$str2="select VEHTARMIN,VEHTARMAX from ".$dbname.".msvehicle where ".$dbname.".msvehicle.VEHNOCODE='".$_POST['VEHNO']."'";
$res2=mysql_query($str2);
	//print_r($res2);
		while($bar=mysql_fetch_object($res2)){
			$tarmin=$bar->VEHTARMIN;$tarmax=$bar->VEHTARMAX;
		}
$str3="select * from ".$dbname.".mstrxtbs where ".$dbname.".mstrxtbs.DATEIN>='".$_now."' and ".$dbname.".mstrxtbs.OUTIN='1' and VEHNOCODE like '".$_POST['VEHNO']."' order by id desc limit 1";
$res3=mysql_query($str3);
$str6="select * from ".$dbname.".mstrxtbs where ".$dbname.".mstrxtbs.SPBNO='".$_POST['SPBNO']."' and OUTIN='1'";
$res6=mysql_query($str6);
$str4="select * from ".$dbname.".mstrxtbs where PRODUCTCODE='".$_POST['PRODUCTCODE']."' order by id desc limit 1";
$res4=mysql_query($str4);
	while($bar2=mysql_fetch_array($res4)){
		$wei1=$bar2[11];
	}

if($OUTIN==1){
	//if ((($tarmin<=$WEIGH2) && ($tarmax>=$WEIGH2)) || (($WEIGH2==0)) ){
			if ($USERLEVEL!=16){
					//if (mysql_num_rows($res3)==0){

							$str="insert into ".$dbname.".mstrxtbs
						     	 (IDWB,TICKETNO,OUTIN,SPNO,PRODUCTCODE,DATEIN,WEI1ST,MILLCODE,VEHNOCODE,
							  	 TRPCODE,DRIVER,USERID,PENERIMA,TICKETNO2)
								  values('".$IDWB."',
							 	 '".$TICKETNO."',".$OUTIN.",
								  '".$SPBNO."',
								  '".$PRODUCTCODE."','".$DATEIN."',".$WEIGH1.",'".$MILLCODE."',
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


				//	}
				//else{
				//	echo "Error : Berat Timbang I sama dengan Record berat Timbang terakhir di database, Diperlukan Proses Authorisasi.";
				//}
			}
			else{
				$str="insert into ".$dbname.".mstrxtbs
						     	 (IDWB,TICKETNO,OUTIN,SPNO,PRODUCTCODE,DATEIN,WEI1ST,MILLCODE,VEHNOCODE,
							  	 TRPCODE,DRIVER,USERID,PENERIMA,TICKETNO2)
								  values('".$IDWB."',
							 	 '".$TICKETNO."',".$OUTIN.",
								  '".$SPBNO."',
								  '".$PRODUCTCODE."','".$DATEIN."',".$WEIGH1.",'".$MILLCODE."',
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
	if ($USERLEVEL!=16){
					$str="insert into ".$dbname.".mstrxtbs
				     	 (IDWB,TICKETNO,OUTIN,SPNO,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,MILLCODE,VEHNOCODE,
					  	 TRPCODE,DRIVER,NETTO,USERID,PENERIMA,TICKETNO2)
						  values('".$IDWB."',
					 	 '".$TICKETNO."',".$OUTIN.",'".$SPBNO."',
						  '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".$WEIGH1.",".$WEIGH2.",'".$MILLCODE."',
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
		$str="insert into ".$dbname.".mstrxtbs
				     	 (IDWB,TICKETNO,OUTIN,SPNO,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,MILLCODE,VEHNOCODE,
					  	 TRPCODE,DRIVER,NETTO,USERID,PENERIMA,TICKETNO2)
						  values('".$IDWB."',
					 	 '".$TICKETNO."',".$OUTIN.",'".$SPBNO."',
						  '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".$WEIGH1.",".$WEIGH2.",'".$MILLCODE."',
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

}
}
else
{
	echo "Error: Kendaraan tidak terdaftar";
}
?>
