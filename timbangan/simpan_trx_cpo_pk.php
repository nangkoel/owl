<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$TICKETNO=$_POST['TICKETNO'];
//$TICKETNO2=$_POST['TICKETNO2'];
//$IDWB=$_POST['IDWB'];
$OUTIN=$_POST['OUTIN'];
$CTRNO=$_POST['CTRNO'];
$SIPBNO=$_POST['SIPBNO'];
$SPNO=$_POST['SPNO'];
$PRODUCTCODE=$_POST['PRODUCTCODE'];
$DATEIN=tanggalsystem($_POST['DATEIN']);
$DATEOUT=tanggalsystem($_POST['DATEOUT']);
$WEIGH1=$_POST['WEIGH1'];
$WEIGH2=$_POST['WEIGH2'];
$SLOC=$_POST['SLOC'];
$VEHNO=strtoupper($_POST['VEHNO']);
$TRPCODE=$_POST['TRPCODE'];
$DRIVER=$_POST['DRIVER'];
$NETTO=$_POST['NETTO'];
$NODO=$_POST['NODO'];
$NOSEGEL=$_POST['NOSEGEL'];
$TIPE=$_POST['TIPE'];
$USERID=$_SESSION['standard']['uname'];
$USERLEVEL=$_SESSION['standard']['access_level'];

//ambil millcode
$str="select millcode from ".$dbname.".mssystem";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $MILLCODE=$bar->millcode;
}
if($MILLCODE=='')
    $MILLCODE=$_POST['MILLCODE'];

$IDWB=$_POST['IDWB'];
$TICKETNO2=$_POST['TICKETNO2'];

//pastikan kendaraan ada;
$strx="select VEHNOCODE from ".$dbname.".msvehicle where VEHNOCODE='".$VEHNO."'";
$resx=mysql_query($strx);
if(mysql_num_rows($resx)>0)
{
	
$str5="select * from ".$dbname.".mssystem";
$res5=mysql_query($str5);
	while($bar3=mysql_fetch_object($res5)){
		$timeveh=$bar3->TIMEVEH;
	}

$waktu=mktime(date("H"),date("i")-$timeveh,0,date("m"),date("d"),date("Y"));
$_now=date('Y-m-d H:i:s',$waktu);


$str2="select VEHTARMIN,VEHTARMAX from ".$dbname.".msvehicle where ".$dbname.".msvehicle.VEHNOCODE='".$_POST['VEHNO']."'";
$res2=mysql_query($str2);
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
		$wei2=$bar2[12];
	}
if($OUTIN==1){
    if ($USERLEVEL!=16){
        if (($tarmin<=$WEIGH1) && ($tarmax>=$WEIGH1)){

            $str="insert into ".$dbname.".mstrxtbs
             (IDWB,TICKETNO,OUTIN,CTRNO,SIPBNO,SPNO,PRODUCTCODE,DATEIN,WEI1ST,MILLCODE,VEHNOCODE,
                     TRPCODE,DRIVER,USERID,NODOTRP,TRANSACTIONTYPE,TICKETNO2,NOSEGEL)
                      values('".$IDWB."',
                      '".$TICKETNO."',".$OUTIN.",'".$CTRNO."',
                      '".$SIPBNO."','".$SPNO."',
                      '".$PRODUCTCODE."','".$DATEIN."',".$WEIGH1.",'".$MILLCODE."',
                      '".$VEHNO."',
                      '".$TRPCODE."',
                      '".$DRIVER."',
                       '".$_SESSION['standard']['username']."',
                      '".$NODO."',".$TIPE.",'".$TICKETNO2."',
                      '".$NOSEGEL."'
                      )";
                      if(mysql_query($str)){
                              $xc="update ".$dbname.".msvehicle set FLAG='Y' where VEHNOCODE='".$VEHNO."'";
                              $reg=mysql_query($xc);
                                    echo"0";
                            }
                     else{
                                    echo "Error: ".addslashes(mysql_error($conn));
                    }
            }
            else{
                    echo "Error : Berat Tarra Kendaraan melewati batas toleransi, diperlukan proses Authorisasi!!!.";
            }
        }
        else{
            $str="insert into ".$dbname.".mstrxtbs
                 (IDWB,TICKETNO,OUTIN,CTRNO,SIPBNO,SPNO,PRODUCTCODE,DATEIN,WEI1ST,MILLCODE,VEHNOCODE,
                         TRPCODE,DRIVER,USERID,NODOTRP,TRANSACTIONTYPE,TICKETNO2,NOSEGEL)
                          values('".$IDWB."',
                          '".$TICKETNO."',".$OUTIN.",'".$CTRNO."',
                          '".$SIPBNO."','".$SPNO."',
                          '".$PRODUCTCODE."','".$DATEIN."',".$WEIGH1.",'".$MILLCODE."',
                          '".$VEHNO."',
                          '".$TRPCODE."',
                          '".$DRIVER."',
                           '".$_SESSION['standard']['username']."',
                          '".$NODO."',".$TIPE.",'".$TICKETNO2."',
                          '".$NOSEGEL."'
                          )";
                if(mysql_query($str)){
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
	$str9="select sum(NETTO) as SUMNETTO from ".$dbname.".mstrxtbs where mstrxtbs.SIPBNO='".$_POST['SIPBNO']."'";
	$res9=mysql_query($str9);
	while ($bar9=mysql_fetch_object($res9)){
			$SUMNETTO=$bar9->SUMNETTO;
	}
	if($CTRNO=='')
	{
	  $strc="select CTRNO from ".$dbname.".mssipb where SIPBNO='".$_POST['SIPBNO']."'";
	  $resc=mysql_query($strc);
	  //echo mysql_error($conn);
	  while($barc=mysql_fetch_object($resc)){
	     $CTRNO=$barc->CTRNO;
	  }
	}
	
	$str10="select * from ".$dbname.".mscontract where mscontract.CTRNO='".$CTRNO."'";
	$res10=mysql_query($str10);
	while ($bar10=mysql_fetch_object($res10)){
			$QTYCTR=$bar10->CTRQTY;
	}
	$TOTALNETTO=$SUMNETTO+$NETTO;
       if ($USERLEVEL!=16){
           if($TOTALNETTO<=$QTYCTR){
              if (($wei2!==$WEIGH2)){
                $str7="select SIPBQTY from ".$dbname.".mssipb where SIPBNO='".$_POST['SIPBNO']."' order by SIPBNO";
                $res7=mysql_query($str7);
                        while($bar7=mysql_fetch_object($res7)){
                                $sipbqty=$bar7->SIPBQTY;
                        }
                $str9="select * from ".$dbname.".mstrxtbs where ".$dbname.".mstrxtbs.OUTIN='0' and ".$dbname.".mstrxtbs.PRODUCTCODE='".$_POST['PRODUCTCODE']."' and ".$dbname.".mstrxtbs.SIPBNO='".$_POST['SIPBNO']."' order by id desc limit 1";
                $res9=mysql_query($str9);
                $sipbqty2='';
                        while ($bar9=mysql_fetch_object($res9)){
                                $sipbqty2=$bar9->SIPBQTY;
                        }
                if ($sipbqty2==''){
                        $sipbqty-=$NETTO;
                        $str="insert into ".$dbname.".mstrxtbs
                         (IDWB,TICKETNO,OUTIN,CTRNO,SIPBNO,SIPBQTY,SPNO,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,MILLCODE,SLOC,VEHNOCODE,
                              TRPCODE,DRIVER,NETTO,USERID,NODOTRP,TRANSACTIONTYPE,TICKETNO2,NOSEGEL)
                              values('".$IDWB."',
                              '".$TICKETNO."',".$OUTIN.",'".$CTRNO."',
                              '".$SIPBNO."',".$sipbqty.",'".$SPNO."',
                              '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".$WEIGH1.",".$WEIGH2.",
                              '".$MILLCODE."','".$SLOC."',
                              '".$VEHNO."',
                              '".$TRPCODE."',
                              '".$DRIVER."',
                              ".$NETTO.",
                               '".$_SESSION['standard']['username']."',
                              '".$NODO."',
                              ".$TIPE.",'".$TICKETNO2."',
                              '".$NOSEGEL."'
                              )";
                        //echo "Gagal ".$str;
                        if(mysql_query($str))
                        {
                                $xc="update ".$dbname.".msvehicle set FLAG='T' where VEHNOCODE='".$VEHNO."'";
                                $reg=mysql_query($xc);
                                echo"0";
                                $str="update ".$dbname.".mssipb set SIPBQTY=".$sipbqty." where CTRNO='".$CTRNO."' and SIPBNO='".$SIPBNO."'";
                                $res=mysql_query($str);
                        }
                        else
                        {
                                echo "Error: ".addslashes(mysql_error($conn));
                        }
                }
                else{
                        $sipbqty3=$sipbqty2-$NETTO;
                        $str="insert into ".$dbname.".mstrxtbs
                         (IDWB,TICKETNO,OUTIN,CTRNO,SIPBNO,SIPBQTY,SPNO,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,MILLCODE,SLOC,VEHNOCODE,
                              TRPCODE,DRIVER,NETTO,USERID,NODOTRP,TRANSACTIONTYPE,TICKETNO2,NOSEGEL)
                              values('".$IDWB."',
                              '".$TICKETNO."',".$OUTIN.",'".$CTRNO."',
                              '".$SIPBNO."',".$sipbqty3.",'".$SPNO."',
                              '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".$WEIGH1.",".$WEIGH2.",
                              '".$MILLCODE."','".$SLOC."',
                              '".$VEHNO."',
                              '".$TRPCODE."',
                              '".$DRIVER."',
                              ".$NETTO.",
                              '".$_SESSION['standard']['username']."',
                              '".$NODO."',
                              ".$TIPE.",'".$TICKETNO2."',
                              '".$NOSEGEL."'
                              )";
                        //echo "Gagal ".$str;
                        if(mysql_query($str))
                        {
                                $xc="update ".$dbname.".msvehicle set FLAG='T' where VEHNOCODE='".$VEHNO."'";
                                $reg=mysql_query($xc);
                                echo"0";
                                $str="update ".$dbname.".mssipb set SIPBQTY=".$sipbqty3." where CTRNO='".$CTRNO."' and SIPBNO='".$SIPBNO."'";
                                $res=mysql_query($str);
                        }
                        else
                        {
                                echo "Error: ".addslashes(mysql_error($conn));
                        }
                }
        }
        else {
                        echo "Error : Berat Timbang I sama dengan Record berat Timbang terakhir di database, Diperlukan Proses Authorisasi.";
                }
      }
      else{
            echo "Error : Quantity Yang Ditimbang Melebihi Nilai Kontrak, Silahkan Kurangi Quantity  Di Dalam Truk!!! ";
            $str11="update wbridge.mssipb set mssipb.SIPBSTATUS='Tidak Aktif' where mssipb.SIPBNO='".$SIPBNO."'";
            $res11=mysql_query($str11);
         }
      }
else{
        $str7="select SIPBQTY from ".$dbname.".mssipb where SIPBNO='".$_POST['SIPBNO']."' order by SIPBNO";
                $res7=mysql_query($str7);
                        while($bar7=mysql_fetch_object($res7)){
                                $sipbqty=$bar7->SIPBQTY;
                        }
                $str9="select * from ".$dbname.".mstrxtbs where ".$dbname.".mstrxtbs.OUTIN='0'and ".$dbname.".mstrxtbs.PRODUCTCODE='".$_POST['PRODUCTCODE']."' and ".$dbname.".mstrxtbs.SIPBNO='".$_POST['SIPBNO']."' order by id desc limit 1";
                $res9=mysql_query($str9);
                        $sipbqty2='';
                        while ($bar9=mysql_fetch_object($res9)){
                                $sipbqty2=$bar9->SIPBQTY;
                        }
                if ($sipbqty2==''){
                        $sipbqty-=$NETTO;
                        $str="insert into ".$dbname.".mstrxtbs
                         (IDWB,TICKETNO,OUTIN,CTRNO,SIPBNO,SIPBQTY,SPNO,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,MILLCODE,SLOC,VEHNOCODE,
                              TRPCODE,DRIVER,NETTO,USERID,NODOTRP,TRANSACTIONTYPE,TICKETNO2,NOSEGEL)
                              values('".$IDWB."',
                              '".$TICKETNO."',".$OUTIN.",'".$CTRNO."',
                              '".$SIPBNO."',".$sipbqty.",'".$SPNO."',
                              '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".$WEIGH1.",".$WEIGH2.",
                              '".$MILLCODE."','".$SLOC."',
                              '".$VEHNO."',
                              '".$TRPCODE."',
                              '".$DRIVER."',
                              ".$NETTO.",
                               '".$_SESSION['standard']['username']."',
                              '".$NODO."',
                              ".$TIPE.",'".$TICKETNO2."',
                              '".$NOSEGEL."'
                              )";
                        if(mysql_query($str))
                        {
                                $xc="update ".$dbname.".msvehicle set FLAG='T' where VEHNOCODE='".$VEHNO."'";
                                $reg=mysql_query($xc);
                                echo"0";
                                $str="update ".$dbname.".mssipb set SIPBQTY=".$sipbqty." where CTRNO='".$CTRNO."' and SIPBNO='".$SIPBNO."'";
                                $res=mysql_query($str);
                        }
                        else
                        {
                                echo "Error: ".addslashes(mysql_error($conn));
                        }
                }
                else{
                        $sipbqty3=$sipbqty2-$NETTO;
                        $str="insert into ".$dbname.".mstrxtbs
                         (IDWB,TICKETNO,OUTIN,CTRNO,SIPBNO,SIPBQTY,SPNO,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,MILLCODE,SLOC,VEHNOCODE,
                          TRPCODE,DRIVER,NETTO,USERID,NODOTRP,TRANSACTIONTYPE,TICKETNO2,NOSEGEL)
                          values('".$IDWB."',
                          '".$TICKETNO."',".$OUTIN.",'".$CTRNO."',
                          '".$SIPBNO."',".$sipbqty3.",'".$SPNO."',
                          '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".$WEIGH1.",".$WEIGH2.",
                          '".$MILLCODE."','".$SLOC."',
                          '".$VEHNO."',
                          '".$TRPCODE."',
                          '".$DRIVER."',
                          ".$NETTO.",
                           '".$_SESSION['standard']['username']."',
                          '".$NODO."',
                          ".$TIPE.",'".$TICKETNO2."',
                          '".$NOSEGEL."'
                          )";
                        //echo "Gagal ".$str;
                        if(mysql_query($str))
                        {
                                $xc="update ".$dbname.".msvehicle set FLAG='T' where VEHNOCODE='".$VEHNO."'";
                                $reg=mysql_query($xc);
                                echo"0";
                                $str="update ".$dbname.".mssipb set SIPBQTY=".$sipbqty3." where CTRNO='".$CTRNO."' and SIPBNO='".$SIPBNO."'";
                                $res=mysql_query($str);
                        }
                        else
                        {
                                echo "Error: ".addslashes(mysql_error($conn));
                        }
                }
        }

  }
}
else
{
	echo "Error: Kendaraan tidak terdaftar";
}
?>

