<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$TICKETNO=$_POST['TICKETNO'];
$SPBNO=$_POST['SPBNO'];
$VEHNO=strtoupper(trim($_POST['VEHNOCODE']));
$TRPCODE=$_POST['TRPCODE'];
$DRIVER=$_POST['DRIVER'];
$JJG=$_POST['JJG'];$JJG2=$_POST['JJG2'];$JJG3=$_POST['JJG3'];
$TAHUNTANAM=$_POST['TAHUNTANAM'];$TAHUNTANAM2=$_POST['TAHUNTANAM2'];$TAHUNTANAM3=$_POST['TAHUNTANAM3'];
$BRONDOLAN=$_POST['BRONDOLAN'];$BRONDOLAN2=$_POST['BRONDOLAN2'];$BRONDOLAN3=$_POST['BRONDOLAN3'];
$BERATKIRIM=$_POST['BERATKIRIM'];
$DATEIN=tanggalsystem($_POST['DATEIN']);
$WEIGH1=$_POST['WEIGH1'];
$OUTIN=$_POST['OUTIN'];
$PRODUCTCODE=$_POST['PRODUCTCODE'];
$CEKBOX=$_POST['CEKBOX'];
$WEIGH2=$_POST['WEIGH2'];
$SLOC=$_POST['SLOC'];
$DATEOUT=tanggalsystem($_POST['DATEOUT']);
$NETTO=$_POST['NETTO'];
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
$POTONGAN=$_POST['POTONGAN'];
if($POTONGAN=='')
    $POTONGAN=0;

$buahbusuk=$_POST['buahbusuk'];
$buahkrgmatang=$_POST['buahkrgmatang'];
$buahsakit=$_POST['buahsakit'];
$janjangkosong=$_POST['janjangkosong'];
$lwtmatang=$_POST['lwtmatang'];
$mentah=$_POST['mentah'];
$tkpanjang=$_POST['tkpanjang'];
$tigakilo=$_POST['tigakilo']; 
 
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
$str2="select VEHTARMIN,VEHTARMAX from ".$dbname.".msvehicle where ".$dbname.".msvehicle.VEHNOCODE='".$VEHNO."'";
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
		$wei1=$bar2[11];
	}

if($OUTIN==1){
    $str="insert into ".$dbname.".mstrxtbs
             (IDWB,TICKETNO,OUTIN,SPBNO,PRODUCTCODE,DATEIN,WEI1ST,MILLCODE,VEHNOCODE,
              TRPCODE,TAHUNTANAM,JMLHJJG,BRONDOLAN,DRIVER,BERATKIRIM,USERID,TAHUNTANAM2,
              JMLHJJG2,BRONDOLAN2,TAHUNTANAM3,JMLHJJG3,BRONDOLAN3,TICKETNO2,KGPOTSORTASI)
              values('".$IDWB."',
              '".$TICKETNO."',".$OUTIN.",
              '".$SPBNO."',
              '".$PRODUCTCODE."','".$DATEIN."',".$WEIGH1.",'".$MILLCODE."',
              '".$VEHNO."','".$TRPCODE."',
              '".$TAHUNTANAM."',".$JJG.",".$BRONDOLAN.",
              '".$DRIVER."',
              '".$BERATKIRIM."',
              '".$_SESSION['standard']['username']."',
              '".$TAHUNTANAM2."',".$JJG2.",".$BRONDOLAN2.",
              '".$TAHUNTANAM3."',".$JJG3.",".$BRONDOLAN3.",
              '".$TICKETNO2."',".$POTONGAN."
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
if($OUTIN==0){
    
    $NETTO=$WEIGH1-$WEIGH2;    
    
        if ($USERLEVEL!=16){
            if (($tarmin<=$WEIGH2) && ($tarmax>=$WEIGH2)){ //validasi range tarra kendaraan
                $str="insert into ".$dbname.".mstrxtbs
                     (IDWB,TICKETNO,OUTIN,SPBNO,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,MILLCODE,VEHNOCODE,
                      TRPCODE,TAHUNTANAM,JMLHJJG,BRONDOLAN,DRIVER,NETTO,BERATKIRIM,USERID,TAHUNTANAM2,
                      JMLHJJG2,BRONDOLAN2,TAHUNTANAM3,JMLHJJG3,BRONDOLAN3,TICKETNO2,KGPOTSORTASI,
                         buahbusuk, buahkrgmatang, buahsakit, janjangkosong, lwtmatang,mentah,tkpanjang,tigakilo)
                      values('".$IDWB."',
                              '".$TICKETNO."',".$OUTIN.",
                              '".$SPBNO."',
                              '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".$WEIGH1.",".$WEIGH2.",'".$MILLCODE."',
                              '".$VEHNO."','".$TRPCODE."',
                              '".$TAHUNTANAM."',".$JJG.",".$BRONDOLAN.",
                              '".$DRIVER."',".$NETTO.",
                              '".$BERATKIRIM."',
                              '".$_SESSION['standard']['username']."',
                              '".$TAHUNTANAM2."',".$JJG2.",".$BRONDOLAN2.",
                              '".$TAHUNTANAM3."',".$JJG3.",".$BRONDOLAN3.",
                              '".$TICKETNO2."',".$POTONGAN.",
                          ".$buahbusuk.",".$buahkrgmatang.", ".$buahsakit.",".$janjangkosong.",".$lwtmatang.",".$mentah.",".$tkpanjang."
						  ,".$tigakilo." 	
                      )";

                if(mysql_query($str))
                {
                    $xc="update ".$dbname.".msvehicle set FLAG='T' where VEHNOCODE='".$VEHNO."'";
                    $reg=mysql_query($xc);
                    echo"0";
                }
                else
                {
                        echo "Error: ".addslashes(mysql_error($conn));
                }
            }
            else {
                    echo "Error : Berat Tarra Kendaraan melewati batas toleransi, diperlukan proses Authorisasi!!!.";
            }
        }
        else{
           $str="insert into ".$dbname.".mstrxtbs
             (IDWB,TICKETNO,OUTIN,SPBNO,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,MILLCODE,VEHNOCODE,
                  TRPCODE,TAHUNTANAM,JMLHJJG,BRONDOLAN,DRIVER,NETTO,BERATKIRIM,USERID,TAHUNTANAM2,
                  JMLHJJG2,BRONDOLAN2,TAHUNTANAM3,JMLHJJG3,BRONDOLAN3,TICKETNO2,KGPOTSORTASI,
                         buahbusuk, buahkrgmatang, buahsakit, janjangkosong, lwtmatang,mentah,tkpanjang,tigakilo)
                  values('".$IDWB."',
                  '".$TICKETNO."',".$OUTIN.",
                  '".$SPBNO."',
                  '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".$WEIGH1.",".$WEIGH2.",'".$MILLCODE."',
                  '".$VEHNO."','".$TRPCODE."',
                  '".$TAHUNTANAM."',".$JJG.",".$BRONDOLAN.",
                  '".$DRIVER."',".$NETTO.",
                  '".$BERATKIRIM."',
                  '".$_SESSION['standard']['username']."',
                  '".$TAHUNTANAM2."',".$JJG2.",".$BRONDOLAN2.",
                  '".$TAHUNTANAM3."',".$JJG3.",".$BRONDOLAN3.",
                  '".$TICKETNO2."',".$POTONGAN.",
                          ".$buahbusuk.",".$buahkrgmatang.", ".$buahsakit.",".$janjangkosong.",".$lwtmatang.",".$mentah.",".$tkpanjang.",".$tigakilo."
                  )";
            if(mysql_query($str))
            {
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