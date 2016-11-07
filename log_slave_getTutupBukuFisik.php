<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$gudang=$_POST['gudang'];

#==================integrity Check=======================================================
if($_SESSION['empl']['tipelokasitugas']=='KEBUN'){   #hanya berlaku untuk kebun 
        #ambil kodePT:
        $str="select induk from ".$dbname.".organisasi where kodeorganisasi='".substr($gudang,0,4)."'";
        $res=mysql_query($str);
        $kodept='';
        while($bar=mysql_fetch_object($res)){
            $kodept=$bar->induk;
        }
        if($kodept==''){
            exit(' Error: Org code is missing');
        }
        #ambil periode akunting
        $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where kodeorg='".substr($gudang,0,4)."' and periode='".$_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan']."'";
        $res=mysql_query($str);
        $mulai='';
        $sampai='';
        while($bar=mysql_fetch_object($res)){
            $mulai=$bar->tanggalmulai;
             $sampai=$bar->tanggalsampai;
        }
        if($mulai=='' or $sampai==''){
            exit(" Error: periode akuntansi unit ".substr($gudang,0,4)." belum terdaftar");
        }else{   
        #ambil transaksi material
            $bkmMat=Array();
            $str="select a.*,b.jurnal,b.tanggal,c.kodekegiatan from ".$dbname.".kebun_pakaimaterial a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi
                     left join ".$dbname.".kebun_prestasi c on a.notransaksi=c.notransaksi
                      where b.tanggal>='".$mulai."' and b.tanggal<='".$sampai."' and b.jurnal=1 and b.kodeorg='".substr($gudang,0,4)."'";
            $res=mysql_query($str);
            //echo mysql_error($conn);
            while($bar=mysql_fetch_array($res)){
                     $bkmMat[]=$bar;
                     $bkmLast[]=$bar;
            }

         #ambil transaksi gudang
            $str="select notransaksireferensi from ".$dbname.".log_transaksiht where kodegudang like '".substr($gudang,0,4)."%' and tanggal>='".$mulai."' and tanggal<='".$sampai."'
                      and tipetransaksi=5 and notransaksireferensi is not null and notransaksireferensi!=''";
            $res=mysql_query($str);
            while($bar=mysql_fetch_object($res)){
                $log[]=$bar->notransaksireferensi;
            }

          if(count($log)>0){
            foreach($log as $key =>$val){
                    foreach($bkmMat as $key1 => $val1){
                             if($val1['notransaksi']==$val){
                                     unset($bkmLast[$key1]);
                    }
                }
            }
          }
          #material BKMyg tidak ada di log_transasi
                    if(count($bkmLast)==0){
                        //do nothing
                    }  
                    else{
                              exit(" Error: Please check data integrity via menu: Pengadaan->Proses->Integrity Check");
                    }
        }
}
#=======================================================================================

#ambil daftar gudang:
if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
    $str="select a.kodeorganisasi,a.namaorganisasi,b.periode,b.tanggalmulai,b.tanggalsampai
    from ".$dbname.".organisasi a left join ".$dbname.".setup_periodeakuntansi b on a.kodeorganisasi=b.kodeorg
    where a.kodeorganisasi ='".$gudang."' and a.tipe like 'GUDANG%' and b.tutupbuku=0";   
}
else
{
    $str="select a.kodeorganisasi,a.namaorganisasi,b.periode,b.tanggalmulai,b.tanggalsampai
    from ".$dbname.".organisasi a left join ".$dbname.".setup_periodeakuntansi b on a.kodeorganisasi=b.kodeorg
    where a.kodeorganisasi like '".$gudang."%' and a.tipe like 'GUDANG%' and b.tutupbuku=0";    
}
if(($_SESSION['empl']['lokasitugas']==('MRKE'))or($_SESSION['empl']['lokasitugas']==('SKSE'))){
    $str="select a.kodeorganisasi,a.namaorganisasi,b.periode,b.tanggalmulai,b.tanggalsampai
    from ".$dbname.".organisasi a left join ".$dbname.".setup_periodeakuntansi b on a.kodeorganisasi=b.kodeorg
    where a.kodeorganisasi like '".$gudang."%' and a.tipe like 'GUDANGTEMP%' and b.tutupbuku=0";    
}

//$res=mysql_query($str);
//$maxRow=  mysql_num_rows($res);
//while($bar=mysql_fetch_object($res))
//{
//    $key=$bar->kodeorganisasi.$bar->periode;
//    $arkey[$key]=$key;
//    $argud[$bar->kodeorganisasi]=$bar->kodeorganisasi;
//    $arper[$bar->kodeorganisasi]=$bar->periode;
//}
//
//$dagud="('";
//if(!empty($argud))foreach($argud as $key=>$gdg){
//    $dagud.=$gdg."', '";
//    echo $gdg;
//}
//$dagud=substr($dagud,0,-3).')';
//
//$daper="('";
//if(!empty($arper))foreach($arper as $key=>$gdg){
//    $daper.=$gdg."', '";
//    echo $gdg;
//}
//$daper=substr($daper,0,-3).')';
//
//$esteer="SELECT *
//FROM ".$dbname.".`log_5saldobulanan`
//WHERE kodegudang in ".$dagud." and periode in ".$daper."
//AND saldoakhirqty != ( saldoawalqty + qtymasuk - qtykeluar )";
////echo $esteer.'<br>';
//$erees=mysql_query($esteer);
//$erowe=  mysql_num_rows($erees);
//while($beaer=mysql_fetch_object($erowe))
//{
//    $key=$bar->kodegudang.$bar->periode;
//    
//}

$stream="Please choose storage location(warehouse):
              <table class=sortable cellspacing=1 border=0>
              <thead>
               <tr class=rowheader>
               <td>".$_SESSION['lang']['kodegudang']."</td>
               <td>".$_SESSION['lang']['namaorganisasi']."</td>
               <td>".$_SESSION['lang']['periode']."</td>
               <td>".$_SESSION['lang']['tanggalmulai']."</td>
               <td>".$_SESSION['lang']['tanggalsampai']."</td>
               <td>".$_SESSION['lang']['pilih']."</td>     
               </tr>    
               </thead>";
$stream2="Please recalculate(material):
              <table class=sortable cellspacing=1 border=0>
              <thead>
               <tr class=rowheader>
               <td>".$_SESSION['lang']['gudang']."</td>
               <td>".$_SESSION['lang']['kodebarang']."</td>
               <td>".$_SESSION['lang']['periode']."</td>
               <td>".$_SESSION['lang']['saldoawal']."</td>
               <td>".$_SESSION['lang']['masuk']."</td>
               <td>".$_SESSION['lang']['keluar']."</td>
               <td>".$_SESSION['lang']['saldoakhir']."</td>
               <td>".$_SESSION['lang']['action']."</td>     
               </tr>    
               </thead>";
$no=0;
$no2=0;
$res=mysql_query($str);
$maxRow=  mysql_num_rows($res);
$adaerror=0;
while($bar=mysql_fetch_object($res))
{
        $str2="SELECT *, (saldoawalqty+qtymasuk-qtykeluar) as pembanding
        FROM ".$dbname.".log_5saldobulanan
        WHERE kodegudang = '".$bar->kodeorganisasi."' and periode = '".$bar->periode."'
        AND ( saldoawalqty + qtymasuk - qtykeluar - saldoakhirqty) != 0";
//        echo $str2.'<br>';
        $res2=mysql_query($str2);
//        $row2=  mysql_num_rows($res2);
        while($bar2=mysql_fetch_object($res2))
        {
            if((number_format($bar2->pembanding,2))!=(number_format($bar2->saldoakhirqty,2))){
            $adaerror=1;
            $no2+=1;  
          $stream2.="<tr class=rowcontent  id=guaikutaja_".$no2.">
               <td id=kodegud".$no2.">".$bar2->kodegudang."</td>
               <td id=kodebar".$no2.">".$bar2->kodebarang."</td>
               <td id=kodeper".$no2.">".$bar2->periode."</td>
               <td id=sawal_".$no2.">".$bar2->saldoawalqty."</td>
               <td id=qtymsk_".$no2.">".$bar2->qtymasuk."</td>
               <td id=qtyklr_".$no2.">".$bar2->qtykeluar."</td>
               <td id=salak_".$no2.">".$bar2->saldoakhirqty."</td>
               <td><button class=mybutton onclick=reklasDt('".$bar2->kodebarang."','".$bar2->kodegudang."','".$bar2->periode."','".$no2."') >".$_SESSION['lang']['rekalkulasi']."</button></td>    
               </tr>";  
            }
        }    
  $no+=1;  
  $stream.="<tr class=rowcontent  id=row".$no.">
               <td id=kodeorg".$no.">".$bar->kodeorganisasi."</td>
               <td>".$bar->namaorganisasi."</td>
               <td id=periode".$no.">".$bar->periode."</td>
               <td id=tanggalmulai".$no.">".$bar->tanggalmulai."</td>
               <td id=tanggalsampai".$no.">".$bar->tanggalsampai."</td>
               <td><input type=checkbox  id=pilihan".$no." checked></td>    
               </tr>";  
}
$stream.="</tbody><tfoot></tfoot></table>
<button onclick=saveSaldoFisik(".$maxRow.",this)>Proses</button>";
$stream2.="</tbody><tfoot></tfoot></table>
Please refresh after all material has been recalculated correctly (green).<br/><br/>
<button onclick=setSloc('simpan') class=mybutton id=btnsloc>Refresh</button>
";
if($adaerror==1){
    echo $stream2;    
}else{
    echo $stream;    
}
/*script lama

if(isTransactionPeriod())//check if transaction period is normal
{
//========================
  $gudang=$_POST['gudang'];
  $user  =$_SESSION['standard']['userid'];
  $awal  =$_POST['awal'];
  $akhir =$_POST['akhir'];
  $period=$_SESSION['gudang'][$gudang]['tahun']."-".$_SESSION['gudang'][$gudang]['bulan'];
//=============================
//next period is
  $tg=mktime(0,0,0,$_SESSION['gudang'][$gudang]['bulan']+1,15,$_SESSION['gudang'][$gudang]['tahun']);
  $nextPeriod=date('Y-m',$tg);
  $tg=mktime(0,0,0,substr($akhir,4,2),intval(substr($akhir,6,2)+1),$_SESSION['gudang'][$gudang]['bulan']);
  $nextAwal=date('Ymd',$tg);
  $tg=mktime(0,0,0,intval(substr($akhir,4,2)+1),substr($akhir,6,2),$_SESSION['gudang'][$gudang]['bulan']);
  $nextAkhir=date('Ymd',$tg); 
//================================================
//periksa periode
$str="select tutupbuku from ".$dbname.".setup_periodeakuntansi where periode='".$period."'
      and kodeorg='".$gudang."'";

$res=mysql_query($str);
$periode='benar';
if(mysql_num_rows($res)>0)
{
	while($bar=mysql_fetch_object($res))
	{
		if($bar->tutupbuku==0)
		{
			$periode='benar';
		}
		else
		{
			$periode='salah';
		}
	}
}
else
{
	$periode='salah';
}
//==========================================  
  
//cel apakah sudah posting semua pada periode tersebut;
$str="select count(tanggal) as tgl from ".$dbname.".log_transaksiht
      where kodegudang='".$gudang."' and tanggal>=".$awal." and tanggal<=".$akhir."
	  and post=0";  
$res=mysql_query($str);
$jlhNotPost=0;
while($bar=mysql_fetch_object($res))
{
	$jlhNotPost=$bar->tgl;
}

if($jlhNotPost>0)
{
	echo " Error: ".$_SESSION['lang']['belumposting']." > 0";
}  
else if($periode=='salah')
{
	echo " Error: Transaction period not defined or closed";
} 
else
{
   //ambil semua daftar barang dari log5_masterbarangdt berdasarkan gudang
   $str="select a.*,b.namabarang,b.satuan from ".$dbname.".log_5masterbarangdt a left join
         ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang 
          where kodegudang='".$gudang."' order by namabarang";
   $res=mysql_query($str);
   $r=mysql_num_rows($res);
   if($r>0)
   {	
   echo "<button class=mybutton onclick=saveSaldoFisik(".$r.");>".$_SESSION['lang']['proses']."</button>
         <button style='display:none;' onclick=lanjut(); id=lanjut>Lanjut</button>
         <table class=sortable cellspacing=1 border=0>
         <thead>
		   <tr class=rowheader>
		     <td>No</td>
			 <td>".$_SESSION['lang']['periode']."</td>
			 <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
			 <td>".$_SESSION['lang']['sloc']."</td>
			 <td>".$_SESSION['lang']['kodebarang']."</td>
			 <td>".$_SESSION['lang']['namabarang']."</td>
			 <td>".$_SESSION['lang']['satuan']."</td>
		   </tr>
		 </thead>
		 <tbody>
		";

   $no=0;
   while($bar=mysql_fetch_object($res))
   {
 	$no+=1;
	echo"<tr class=rowcontent id=row".$no.">
		     <td>".$no."</td>
			 <td id=period".$no.">".$period."</td>
			 <td id=pt".$no.">".$bar->kodeorg."</td>
			 <td id=gudang".$no.">".$gudang."</td>
			 <td id=kodebarang".$no.">".$bar->kodebarang."</td>
			 <td>".$bar->namabarang."</td>
			 <td>".$bar->satuan."</td>
		   </tr>";   
   }
	echo"</tbody><tfoot></tfoot></table>";
   }
   else
   {
   	echo "No data";
   }
 }
}
else
{
	echo " Error: Transaction Period missing";
}
 
 */
?>