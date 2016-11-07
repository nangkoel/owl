<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$TICKETNO=$_POST['TICKETNO'];
$TICKETNOPECAH=$_POST['TICKETNOPECAH'];
$pecahtiket=$_POST['pecahtiket'];
$jjg1st=$_POST['jjg1st'];

$SPBNO=$_POST['SPBNO'];
$SPBNO2=$_POST['SPBNO2'];
//convert first segment to 7 char
$tx=explode("/",$SPBNO);
$tx2=explode("/",$SPBNO2);
$tx[0]=str_pad($tx[0], 7, "0", STR_PAD_LEFT);
$tx2[0]=str_pad($tx2[0], 7, "0", STR_PAD_LEFT);

//ambil millcode dan idwb
$str="select millcode, IDWB from ".$dbname.".mssystem";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $MILLCODE=$bar->millcode;
    $IDWB=$bar->IDWB;
}
if($MILLCODE=='')
    $MILLCODE=$_POST['MILLCODE'];
	

$proses = $_GET['proses'];
if ($proses=='getFormPecahTiket'){
	$str2="select TICKETNO from ".$dbname.".mstrxtbs where IDWB='".$IDWB."' order by TICKETNO desc limit 1";
	$res2=mysql_query($str2);
	if (mysql_num_rows($res2)>0){
	while ($bar=mysql_fetch_array($res2))
		{
			$ticketno=$bar[0];
			$no_1=intval($ticketno)+1;
			$no=str_pad($no_1,6,"0",STR_PAD_LEFT);
		}
	}
	else {
		$no3=1;
		$no=str_pad($no3,6,"0",STR_PAD_LEFT);
	}

        $form="<fieldset style=float: left;>
        <legend>PECAH TIKET</legend>
                NO TIKET : ".$_POST['IDWB'].$_POST['TICKETNO']."<br>
                NO SPB   : ".$_POST['SPBNO']."<br>
                TOTAL JJG : ".$_POST['JJG']."<br>
                Timbang 1  : ".$_POST['WEIGH1']."<br>
                Timbang 2  : ".$_POST['WEIGH2']."<br>
                Potongan   : ".$_POST['POTONGAN']."<br><br>
                Jumlah JJG SPB Pertama : <input type=text id=jjg1st size=5 maxlength=5 tabindex=1 onkeypress='return angka_doang(event);' onkeyup='hitungpecah()' style='text-align:right;' value='".$_POST['JJG']."'><br>
                Persentase Pembagian : &nbsp;&nbsp;&nbsp;<input type=text id=pct size=5 maxlength=2 tabindex=1 style='text-align:right;' value=0 disabled>&nbsp;(%)<br><br>
                <u>Setelah pembagian menjadi:</u><br>
                <b>NO TIKET PERTAMA : ".$_POST['IDWB'].$_POST['TICKETNO']."<br>
                NO SPB PERTAMA : ".$_POST['SPBNO']."</b><br>
                JJG &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input type=text style='text-align:right;' id=jjg1 size=7 disabled value='".$_POST['JJG']."'/><br>
                Timbang 1  : <input type=text style='text-align:right;' id=weigh11 size=7 disabled value='".$_POST['WEIGH1']."'/><br>
                Timbang 2  : <input type=text style='text-align:right;' id=weigh21 size=7 disabled value='".$_POST['WEIGH2']."'/><br>
                Potongan &nbsp;&nbsp;: <input type=text style='text-align:right;' id=POTONGAN1 size=7 disabled value='".$_POST['POTONGAN']."'/><br><br>
                <b>NO TIKET KEDUA  : ".$_POST['IDWB'].$no."<input type=text id=TICKETNOPECAH hidden value='".$no."'/><br>
                NO SPB KEDUA    : <input type=text style='text-align:right;' id=SPBNO2 size=5 maxlength=7/>&nbsp;<i>* Wajib diisi (cukup hanya angka Surat Jalan, akan otomatis terbentuk XXXXXXX".substr($_POST['SPBNO'],7).")</i></b><br>
                JJG &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input type=text style='text-align:right;' id=jjg2 size=7 disabled value=0><br>
                Timbang 1  : <input type=text style='text-align:right;' id=weigh12 size=7 disabled value=0><br>
                Timbang 2  : <input type=text style='text-align:right;' id=weigh22 size=7 disabled value=0><br>
                Potongan &nbsp;&nbsp;: <input type=text style='text-align:right;' id=POTONGAN2 size=7 disabled value=0><br><br>
                <button class=mybutton onclick=\"saveTbsInt(0,1,event)\">Simpan</button>
                <button class=mybutton onclick=\"closeDialog3()\">Batal</button><br>
               </fieldset>";
        echo $form;
        exit();
}

if(intval($tx[0])==0)
{
    exit (" Error, No.Spb harus di isi");
}

$SPBNO=implode("/", $tx);
$SPBNO2=implode("/", $tx2);

if($pecahtiket==1)
{
    if (intval($tx2[0])==0){
        exit (" Error, No.Spb kedua harus di isi");
    } else {
        //periksa dulu di table apakah no spb sudah ada
         $strd="select * from ".$dbname.".mstrxtbs where SPBNO='".$SPBNO2."'";
         $rg=mysql_query($strd);
         if(mysql_num_rows($rg)>0)
         {
             exit(" Error: No.SPB/DO sudah pernah ditimbang");
         }    
    }
}

$VEHNO=strtoupper(trim($_POST['VEHNOCODE']));$TRPCODE=$_POST['TRPCODE'];
$DRIVER=$_POST['DRIVER'];$UNIT=$_POST['UNITCODE'];
$DIVISI=$_POST['DIVISI'];$JJG=$_POST['JJG'];
$TAHUNTANAM=$_POST['TAHUNTANAM'];$BRONDOLAN=$_POST['BRONDOLAN'];
$BERATKIRIM=$_POST['BERATKIRIM'];$DATEIN=tanggalsystem($_POST['DATEIN']);
$WEIGH1=$_POST['WEIGH1'];$USERID=$_SESSION['standard']['uname'];
$USERLEVEL=$_SESSION['standard']['access_level'];

$OUTIN=$_POST['OUTIN'];$PRODUCTCODE=$_POST['PRODUCTCODE'];
$JENIS=$_POST['JENIS'];
$WEIGH2=$_POST['WEIGH2'];$SLOC=$_POST['SLOC'];
$DATEOUT=tanggalsystem($_POST['DATEOUT']);$NETTO=$_POST['NETTO'];
$IDWB=$_POST['IDWB'];$TICKETNO2=$_POST['TICKETNO2'];
$POTONGAN=$_POST['POTONGAN'];
if($POTONGAN=='') $POTONGAN=0;

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
        //periksa dulu di table apakah no spb sudah ada
         $strd="select * from ".$dbname.".mstrxtbs where SPBNO='".$SPBNO."'";
         $rg=mysql_query($strd);
         if(mysql_num_rows($rg)>0)
         {
             exit(" Error: No.SPB/DO sudah pernah ditimbang");
         }    
          $str="insert into ".$dbname.".mstrxtbs
             (IDWB,TICKETNO,OUTIN,SPBNO,PRODUCTCODE,DATEIN,WEI1ST,MILLCODE,VEHNOCODE,
                     TRPCODE,UNITCODE,DIVCODE,TAHUNTANAM,JMLHJJG,BRONDOLAN,DRIVER,BERATKIRIM,USERID,JENISSPB,TICKETNO2,KGPOTSORTASI)
                      values('".$IDWB."',
                     '".$TICKETNO."',".$OUTIN.",
                      '".$SPBNO."',
                      '".$PRODUCTCODE."','".$DATEIN."',".$WEIGH1.",'".$MILLCODE."',
                      '".$VEHNO."','".$TRPCODE."','".$UNIT."',
                      '".$DIVISI."','".$TAHUNTANAM."',".$JJG.",".$BRONDOLAN.",
                      '".$DRIVER."',
                      '".$BERATKIRIM."',
                     '".$_SESSION['standard']['username']."',
                      '".$JENIS."',
                      '".$TICKETNO2."',
                      ".$POTONGAN."                     
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
        if ($pecahtiket==1 && $jjg1st>0){
                $persenPecah=100;
                if ($JJG-$jjg1st>0){
                    $persenPecah=($JJG-$jjg1st)/$JJG*100;
                }
                $jjgkedua=$JJG*$persenPecah/100;
                $w1=$WEIGH1*$persenPecah/100;
                $w2=$WEIGH2*$persenPecah/100;
                $pot=$POTONGAN*$persenPecah/100;
                $JJG=$JJG-($JJG*$persenPecah/100);
                $WEIGH1=$WEIGH1-($WEIGH1*$persenPecah/100);
                $WEIGH2=$WEIGH2-($WEIGH2*$persenPecah/100);
                $POTONGAN=$POTONGAN-($POTONGAN*$persenPecah/100);
                $net=$w1-$w2;
            }    

        $NETTO=$WEIGH1-$WEIGH2;

            if ($USERLEVEL!=16){#user normal
                        if (($tarmin<=$WEIGH2) && ($tarmax>=$WEIGH2)){ //validasi range tarra kendaraan
                             $str="insert into ".$dbname.".mstrxtbs
                             (IDWB,TICKETNO,OUTIN,SPBNO,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,MILLCODE,VEHNOCODE,
                             TRPCODE,UNITCODE,DIVCODE,TAHUNTANAM,JMLHJJG,BRONDOLAN,DRIVER,NETTO,BERATKIRIM,USERID,TICKETNO2,KGPOTSORTASI,
                             buahbusuk, buahkrgmatang, buahsakit, janjangkosong, lwtmatang,mentah,tkpanjang,tigakilo)
                              values('".$IDWB."',
                             '".$TICKETNO."',".$OUTIN.",
                              '".$SPBNO."',
                              '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".round($WEIGH1).",".round($WEIGH2).",'".$MILLCODE."',
                              '".$VEHNO."','".$TRPCODE."','".$UNIT."','".$DIVISI."','".$TAHUNTANAM."',
                              ".$JJG.",".$BRONDOLAN.",'".$DRIVER."',".round($NETTO).",'".$BERATKIRIM."',
                              '".$_SESSION['standard']['username']."','".$TICKETNO2."',".$POTONGAN.",
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
                    else{#user admin
                        $str="insert into ".$dbname.".mstrxtbs
                         (IDWB,TICKETNO,OUTIN,SPBNO,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,MILLCODE,VEHNOCODE,
                             TRPCODE,UNITCODE,DIVCODE,TAHUNTANAM,JMLHJJG,BRONDOLAN,DRIVER,NETTO,BERATKIRIM,USERID,TICKETNO2,KGPOTSORTASI,
                             buahbusuk, buahkrgmatang, buahsakit, janjangkosong, lwtmatang,mentah,tkpanjang,tigakilo)
                              values('".$IDWB."',
                             '".$TICKETNO."',".$OUTIN.",'".$SPBNO."',
                              '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".round($WEIGH1).",".round($WEIGH2).",'".$MILLCODE."',
                              '".$VEHNO."','".$TRPCODE."','".$UNIT."','".$DIVISI."','".$TAHUNTANAM."',
                              ".$JJG.",".$BRONDOLAN.",'".$DRIVER."',
                              ".round($NETTO).",'".$BERATKIRIM."',
                              '".$_SESSION['standard']['username']."','".$TICKETNO2."',".$POTONGAN.",
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


        if ($pecahtiket==1 && $persenPecah>0){
                // update WEI1ST di tiket pertama
                $str="UPDATE ".$dbname.".mstrxtbs SET WEI1ST=".$WEIGH1.",JMLHJJG=".$JJG." WHERE IDWB='".$IDWB."' AND TICKETNO=".$TICKETNO." AND OUTIN=1";
                mysql_query($str);
                
                // Insert tiket kedua
                $str="insert into ".$dbname.".mstrxtbs
                   (IDWB,TICKETNO,OUTIN,SPBNO,PRODUCTCODE,DATEIN,WEI1ST,MILLCODE,VEHNOCODE,
                           TRPCODE,UNITCODE,DIVCODE,TAHUNTANAM,JMLHJJG,BRONDOLAN,DRIVER,BERATKIRIM,USERID,JENISSPB,TICKETNO2,KGPOTSORTASI)
                            values('".$IDWB."',
                           '".$TICKETNOPECAH."',1,
                            '".$SPBNO2."',
                            '".$PRODUCTCODE."','".$DATEIN."',".round($w1).",'".$MILLCODE."',
                            '".$VEHNO."','".$TRPCODE."','".$UNIT."',
                            '".$DIVISI."','".$TAHUNTANAM."',".$jjgkedua.",".$BRONDOLAN.",
                            '".$DRIVER."',
                            '".$BERATKIRIM."',
                           '".$_SESSION['standard']['username']."',
                            '".$JENIS."',
                            '".$IDWB.$TICKETNOPECAH."',0
                            )";
                  if(mysql_query($str)){
                          $xc="update ".$dbname.".msvehicle set FLAG='Y' where VEHNOCODE='".$VEHNO."'";
                          $reg=mysql_query($xc);
                          echo"0";
                  }
                  else{
                          echo "Error: ".addslashes(mysql_error($conn));
                  }

                if ($USERLEVEL!=16){#user normal
                            if (($tarmin<=$w2) && ($tarmax>=$w2)){ //validasi range tarra kendaraan
                                 $str="insert into ".$dbname.".mstrxtbs
                                 (IDWB,TICKETNO,OUTIN,SPBNO,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,MILLCODE,VEHNOCODE,
                                 TRPCODE,UNITCODE,DIVCODE,TAHUNTANAM,JMLHJJG,BRONDOLAN,DRIVER,NETTO,BERATKIRIM,USERID,TICKETNO2,KGPOTSORTASI,
                                 buahbusuk, buahkrgmatang, buahsakit, janjangkosong, lwtmatang,mentah,tkpanjang,tigakilo)
                                  values('".$IDWB."',
                                 '".$TICKETNOPECAH."',0,
                                  '".$SPBNO2."',
                                  '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".round($w1).",".round($w2).",'".$MILLCODE."',
                                  '".$VEHNO."','".$TRPCODE."','".$UNIT."','".$DIVISI."','".$TAHUNTANAM."',
                                  ".$jjgkedua.",".$BRONDOLAN.",'".$DRIVER."',".round($net).",'".$BERATKIRIM."',
                                  '".$_SESSION['standard']['username']."','".$IDWB.$TICKETNOPECAH."',".round($pot).",
                                  ".$buahbusuk.",".$buahkrgmatang.", ".$buahsakit.",".$janjangkosong.",".$lwtmatang.",".$mentah.",".$tkpanjang.",
                                  ".$tigakilo.")";
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
                        else{#user admin
                            $str="insert into ".$dbname.".mstrxtbs
                             (IDWB,TICKETNO,OUTIN,SPBNO,PRODUCTCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,MILLCODE,VEHNOCODE,
                                 TRPCODE,UNITCODE,DIVCODE,TAHUNTANAM,JMLHJJG,BRONDOLAN,DRIVER,NETTO,BERATKIRIM,USERID,TICKETNO2,KGPOTSORTASI,
                                 buahbusuk, buahkrgmatang, buahsakit, janjangkosong, lwtmatang,mentah,tkpanjang,tigakilo)
                                  values('".$IDWB."',
                                 '".$TICKETNOPECAH."',".$OUTIN.",'".$SPBNO2."',
                                  '".$PRODUCTCODE."','".$DATEIN."','".$DATEOUT."',".round($w1).",".round($w2).",'".$MILLCODE."',
                                  '".$VEHNO."','".$TRPCODE."','".$UNIT."','".$DIVISI."','".$TAHUNTANAM."',
                                  ".$jjgkedua.",".$BRONDOLAN.",'".$DRIVER."',
                                  ".round($net).",'".$BERATKIRIM."',
                                  '".$_SESSION['standard']['username']."','".$IDWB.$TICKETNOPECAH."',".round($pot).",
                                  ".$buahbusuk.",".$buahkrgmatang.", ".$buahsakit.",".$janjangkosong.",".$lwtmatang.",".$mentah.",".$tkpanjang.", 
                                  ".$tigakilo.")";

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
                  
            } // Selesai insert tiket pecahan
    }              
}
else
{
	echo "Error: Kendaraan tidak terdaftar";
}
?>
