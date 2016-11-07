<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
//========================
  $gudang=$_POST['gudang'];
  $user  =$_SESSION['standard']['userid'];
  $period=$_POST['periode'];
  $awal  =$_POST['tanggalmulai'];
  $akhir =$_POST['tanggalsampai'];

  //==============
  $x=str_replace("-","",$period);
  $x=str_replace("/","",$x);
  $x=mktime(0,0,0,intval(substr($x,4,2))+1,15,substr($x,0,4));
  $prefper=$period;
  $period=date('Y-m',$x);  
  

$kodeOrg=substr($gudang,0,4);


$namaTrans=array("0"=>"Koreksi","1"=>"Penerimaan Barang dari Supplier","2"=>"Retur","3"=>"Penerimaan Mutasi","5"=>"Pengeluaran/Pemakaian",
        "6"=>"Pengembalian penerimaan supplier","7"=>"Pengeluaran Mutasi");

$nmGudang=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "tipe like 'gudang%'");

#cek pengeluaran untuk unit ini
$iJur="select * from ".$dbname.".log_transaksiht 
         where tipetransaksi=5 and untukunit='".$kodeOrg."' and tanggal between '".$awal."' and '".$akhir."' and post=0";
$nJur=mysql_query($iJur) or die (mysql_error($conn));
$hasil="Masih ada transaksi pengeluaran dari unit lain yang belum diposting: \n";
while($dJur=mysql_fetch_assoc($nJur))
{
    $noJur+=1;
    $hasil.=" ".$noJur.". ".$nmGudang[$dJur['kodegudang']]." : ".$dJur['notransaksi']." \n "; //- ".tanggalnormal($dJur['tanggal'])."
}
    if($noJur>0)
    {
       echo $hasil;
        exit("Error");
    }



#cek nilai jurnal 0
$iJur="select a.nojurnal,b.notransaksi,b.tipetransaksi,b.tanggal,b.kodegudang from ".$dbname.".keu_jurnaldt_vw a left join ".$dbname.".log_transaksiht b
         on a.noreferensi=b.notransaksi where b.kodegudang='".$gudang."' and a.noreferensi like '%".$gudang."%' and 
         a.nojurnal like '%".$kodeOrg."/inv%' and jumlah=0 and b.tanggal between '".$awal."' and '".$akhir."' and b.post=1 ";
$nJur=mysql_query($iJur) or die (mysql_error($conn));
$hasil="Masih ada transaksi yang nilai jurnalnya 0 \n";
while($dJur=mysql_fetch_assoc($nJur))
{
    $noJur+=1;
    $hasil.=" ".$noJur.". ".$namaTrans[$dJur['tipetransaksi']]." : ".$dJur['notransaksi']." \n "; //- ".tanggalnormal($dJur['tanggal'])."
}
    if($noJur>0)
    {
       echo $hasil;
        exit("Error");
    }




#untuk mengecek tidak terbentuk jurnal
$iTipe="select  distinct tipetransaksi from ".$dbname.".log_transaksiht where post=1  and tanggal between '".$awal."' and '".$akhir."' "
        . " and kodegudang='".$gudang."' and tipeTransaksi=3 order by tipetransaksi asc";
$nTipe=mysql_query($iTipe) or die (mysql_error($conn));
while($dTipe=mysql_fetch_assoc($nTipe))
{
    $tipeTransaksi[$dTipe['tipetransaksi']]=$dTipe['tipetransaksi'];  
}

$arrNyala=array("0"=>false,"1"=>false,"2"=>false,"3"=>false,"5"=>false,"6"=>false,"7"=>false);
foreach($tipeTransaksi as $tipeTran)
{
switch($tipeTran){
	case'5':
		$sJurnal="select count(distinct noreferensi) as totData,sum(jumlah*-1) as totRp from ".$dbname.".keu_jurnaldt_vw where 
		      noreferensi in (select distinct notransaksi from ".$dbname.".log_transaksi_vw where tipetransaksi='".$tipeTran."' and statusjurnal=1 
			  and tanggal like '".substr($awal,0,7)."%' and notransaksireferensi is null and kodegudang='".$gudang."') and tanggal like '".substr($awal,0,7)."%' 
		      and left(noakun,3)='115' and jumlah<0 order by noreferensi,sum(jumlah*-1) asc";
	    $sTrans="SELECT count(distinct notransaksi) as totDtTr,sum(hartot) as rupiahTr
             FROM ".$dbname.".`log_transaksi_vw` where tipetransaksi='".$tipeTran."' and statusjurnal=1 
			  and tanggal like '".substr($awal,0,7)."%' and notransaksireferensi is null and kodegudang='".$gudang."'";
	break;
	case'7':
		$sJurnal="select count(distinct noreferensi) as totData,sum(jumlah*-1) as totRp from ".$dbname.".keu_jurnaldt_vw where 
		      noreferensi in (select distinct notransaksi from ".$dbname.".log_transaksi_vw where tipetransaksi='".$tipeTran."' and statusjurnal=1 
			  and tanggal like '".substr($awal,0,7)."%' and notransaksireferensi is null and kodegudang='".$gudang."') and tanggal like '".substr($awal,0,7)."%' 
		      and left(noakun,3)='115' and jumlah<0 order by noreferensi,sum(jumlah*-1) asc";
	    $sTrans="SELECT count(distinct notransaksi) as totDtTr,sum(hartot) as rupiahTr
             FROM ".$dbname.".`log_transaksi_vw` where tipetransaksi='".$tipeTran."' and statusjurnal=1 
			  and tanggal like '".substr($awal,0,7)."%' and notransaksireferensi is null and kodegudang='".$gudang."'";
	break;
	case'3':
	$sJurnal="select count(noreferensi) as totData,sum(totaldebet) as totRp from ".$dbname.".keu_jurnalht where 
		    noreferensi in (select distinct notransaksi from ".$dbname.".log_transaksiht where tipetransaksi='".$tipeTran."' and statusjurnal=1 
			and tanggal like '".substr($awal,0,7)."%' and kodegudang='".$gudang."') and tanggal like '".substr($awal,0,7)."%'";
	$sTrans="SELECT count(notransaksi) as totDtTr,sum(hartot) as rupiahTr
             FROM ".$dbname.".`log_transaksi_vw` where  tipetransaksi='".$tipeTran."' and statusjurnal=1 and tanggal like '".substr($awal,0,7)."%' and kodegudang='".$gudang."'";
    break;
	case'1':
		$sJurnal="select count(noreferensi) as totData,sum(totaldebet) as totRp from ".$dbname.".keu_jurnalht where 
		    noreferensi in (select distinct notransaksi from ".$dbname.".log_transaksiht where tipetransaksi='".$tipeTran."' and statusjurnal=1 
			and tanggal like '".substr($awal,0,7)."%' and kodegudang='".$gudang."') and tanggal like '".substr($awal,0,7)."%'";
	   $sTrans="SELECT count(notransaksi) as totDtTr,sum(hartot) as rupiahTr
             FROM ".$dbname.".`log_transaksi_vw` where  tipetransaksi='".$tipeTran."' and statusjurnal=1 and tanggal like '".substr($awal,0,7)."%' and kodegudang='".$gudang."'";
	break;

}

//exit("error: \n" .$sJurnal."___\n".$sTrans);
$nTran=mysql_query($sJurnal) or die (mysql_error($conn));
$rJr=mysql_fetch_assoc($nTran);
$jurnal=$rJr['totData'];
$rpJurnal=$rJr['totData'];

$qTran=mysql_query($sTrans) or die(mysql_error($conn));
$rTr=mysql_fetch_assoc($qTran);
$tran=$rTr['totDtTr'];
$rpTrans=$rTr['rupiahTr'];

		if($tipeTran==7){
			$iTran="select count(notransaksi) as transaksi,kodegudang from ".$dbname.".log_transaksi_vw where tipetransaksi='".$tipeTran."' and post=0"
						. " and tanggal between '".$awal."' and '".$akhir."' and gudangx='".$gudang."' group by kodegudang ";
			$nTran=mysql_query($iTran) or die (mysql_error($conn));
			while($dTran=mysql_fetch_assoc($nTran)){
				$gdngx[$dTran['kodegudang']]=$dTran['kodegudang'];
			}
			$itung=count($gdngx);
			if($itung>0){
				$arrNyala[$tipeTran]=true;
				$hslError[$tipeTran]=" \n $namaTrans[$tipeTran] \n  Transaksi : $tran \n Jurnal : $jurnal";
			}
			//gudangx
		}
	if(($tran!=$jurnal)&&($rpTrans!=$rpJurnal)){
        $arrNyala[$tipeTran]=true;
        $hslError[$tipeTran]="$namaTrans[$tipeTran] \n  Transaksi : $tran \n Jurnal : $jurnal";
    }
/* 
    if ($tipeTran=='1' || $tipeTran=='3')
    {
        $iTran="select count(notransaksi) as transaksi from ".$dbname.".log_transaksi_vw where tipetransaksi='".$tipeTran."' and post=1"
                . " and tanggal between '".$awal."' and '".$akhir."' and kodegudang='".$gudang."' group by kodebarang";
        $nTran=mysql_query($iTran) or die (mysql_error($conn));
        while($dTran=mysql_fetch_assoc($nTran)){
			$tran+=$dTran['transaksi'];
		}
            
        $iJurnal="select count(noreferensi) as jurnal from ".$dbname.".keu_jurnalht where noreferensi in (select notransaksi "
                . " from ".$dbname.".log_transaksiht where tipetransaksi='".$tipeTran."' and post=1 and tanggal between "
                . " '".$awal."' and '".$akhir."' and kodegudang='".$gudang."') ";
        $nJurnal=mysql_query($iJurnal) or die (mysql_error($conn));
        $dJurnal=mysql_fetch_assoc($nJurnal);
            $jurnal=$dJurnal['jurnal'];
         
    }  
    else {
	if($t)
	$sJurnal="select count(noreferensi) as totData,sum(jumlah*-1) as totRp from ".$dbname.".keu_jurnaldt_vw where 
		      noreferensi in (select distinct notransaksi from ".$dbname.".log_transaksi_vw where tipetransaksi='".$tipeTran."' and statusjurnal=1 
			  and tanggal like '".substr($awal,0,7)."%' and notransaksireferensi is null and kodegudang='".$gudang."') and tanggal like '".substr($awal,0,7)."%' 
		      and left(noakun,3)='115' and jumlah<0 order by noreferensi,sum(jumlah*-1) asc";
	$sTrans="SELECT sum(hartot) as jumlah,count(notransaksi) as totDtTr
             FROM ".$dbname.".`log_transaksi_vw` where statusjurnal=1 
			 and kodegudang='".$gudang."'
             and tanggal like '".$substr($awal,0,7)."%'  and notransaksireferensi is null and tipetransaksi=".$tipeTran." 
		     group by kodegudang order by substr(notransaksi,16,4),notransaksi,sum(hartot) asc";
	   /*  if(($_SESSION['empl']['tipelokasitugas']=='KEBUN')&&($tipeTran==5)){
			$whrt=" and notransaksi not like '%M%'";
		}
        $iTran="select count(notransaksi) as transaksi from ".$dbname.".log_transaksi_vw where tipetransaksi='".$tipeTran."' and post=1"
                    . " and tanggal between '".$awal."' and '".$akhir."' and kodegudang='".$gudang."' ".$whrt."";
        $nTran=mysql_query($iTran) or die (mysql_error($conn));
        $dTran=mysql_fetch_assoc($nTran);
            $tran=$dTran['transaksi'];
            
        $jurnal=0;
        $iJurnal="select nojurnal from ".$dbname.".keu_jurnalht where  noreferensi in (select distinct notransaksi "
                . " from ".$dbname.".log_transaksiht where tipetransaksi='".$tipeTran."' and post=1 and tanggal between "
                . " '".$awal."' and '".$akhir."' and kodegudang='".$gudang."')";
        $nJurnal=mysql_query($iJurnal) or die (mysql_error($conn));
        while($dJurnal=mysql_fetch_assoc($nJurnal))
        {
            $jurnal+=1;
        }  
		
		
    }*/

  
    
       
}
$cekAda=0;
foreach($tipeTransaksi as $tipeTran)
{
    if($arrNyala[$tipeTran]==true){
        $cekAda+=1;
    }
}
//if($cekAda!=0){
//    echo"<pre>";
//	if($itung>0){
//		print_r($gdngx);
//	}
//    print_r($hslError);
//    echo"</pre>";
//    exit("Error");
//}

#######################################tutup jurnal cek


  
#periksa apakah sudah pernah tutup buku pada periode tersebut:
$str="select distinct(periode)  from `".$dbname."`.`log_5saldobulanan` where periode='".$period."' and kodegudang='".$gudang."'";
$res=mysql_query($str);
if(mysql_num_rows($res)>0){
    exit('Error: gudang '.$gudang.' sudah tutup buku pada periode tersebut ('.$prefper.'), mohon hubungi IT');
}


#ambil PT:
$str="select induk from ".$dbname.".organisasi where kodeorganisasi='".substr($gudang,0,4)."'";
$res=mysql_query($str);
$pt='';
while($bar=mysql_fetch_object($res))
{
    $pt=$bar->induk;
}
if($pt=='')
{
    exit(' Error: Gudang belum memiliki PT');
}

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
    exit(" Error: ".$_SESSION['lang']['belumposting']." > 0");
}  

//=============================
//ambil saldo akhir bulan lalu termasuk rupiah
    $str="select kodebarang,saldoakhirqty,nilaisaldoakhir,hargarata 
            from ".$dbname.".log_5saldobulanan
            where kodeorg='".$pt."' and kodegudang='".$gudang."' and periode='".$prefper."'";

    $res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
 //insert new line
  $str1="INSERT INTO `".$dbname."`.`log_5saldobulanan`
        (`kodeorg`,
        `kodebarang`,
        `saldoakhirqty`,
        `hargarata`,
        `lastuser`,
        `periode`,
        `nilaisaldoakhir`,
        `kodegudang`,
        `qtymasuk`,
        `qtykeluar`,
        `qtymasukxharga`,
        `qtykeluarxharga`,
        `saldoawalqty`,
        `hargaratasaldoawal`,
        `nilaisaldoawal`)
        VALUES
        (
            '".$pt."',
            '".$bar->kodebarang."',
            ".$bar->saldoakhirqty.",
            ".$bar->hargarata.",
            ".$user.",
            '".$period."',
            ".$bar->nilaisaldoakhir.",
            '".$gudang."',
            0,
            0,
            0,
            0,
            ".$bar->saldoakhirqty.",
            ".$bar->hargarata.",
            ".$bar->nilaisaldoakhir."
        )";
  if(!mysql_query($str1))
  {
      $err= addslashes(mysql_error($conn))."(".$str1.")"; 
      break;
  }
}

if($err=='')
{
  //next period is
  $nextPeriod=$period;
  $tg=mktime(0,0,0,substr($akhir,5,2),intval(substr($akhir,8,2)+1),intval(substr($prefper,0,4)));
  $nextAwal=date('Ymd',$tg);
//  $tg=mktime(0,0,0,intval(substr($akhir,5,2))+1,date('t',$tg),intval(substr($prefper,0,4)));
  $tglAkhir=(intval(substr($akhir,5,2))+1<12)?intval(substr($akhir,8,2)):31;
  $tg=mktime(0,0,0,intval(substr($akhir,5,2))+1,$tglAkhir,intval(substr($prefper,0,4)));
  $nextAkhir=date('Ymd',$tg);  
 //update setup_periodeakuntansi
   $str="update ".$dbname.".setup_periodeakuntansi set tutupbuku=1
          where kodeorg='".$gudang."' and periode='".$prefper."'";
   if(mysql_query($str))
   {
    $str="INSERT INTO `".$dbname."`.`setup_periodeakuntansi`
            (`kodeorg`,
            `periode`,
            `tanggalmulai`,
            `tanggalsampai`,
            `tutupbuku`)
            VALUES
            ('".$gudang."',
                '".$nextPeriod."',
                ".$nextAwal.",
                ".$nextAkhir.",
                0
                )";
        if(mysql_query($str))
        {
            $str="delete from ".$dbname.".keu_setup_watu_tutup where periode='".$prefper."'. and kodeorg='".$gudang."'";
            mysql_query($str);
            $str="insert into ".$dbname.".keu_setup_watu_tutup(kodeorg,periode,username) values(
                  '".$gudang."','".$prefper."','".$_SESSION['standard']['username']."')";
            mysql_query($str);                        
        }
        else
        {
        $err= addslashes(mysql_error($conn))."(".$str.")";
        //buka kembali periodeakuntansi
           $str="update ".$dbname.".setup_periodeakuntansi set tutupbuku=0
          where kodeorg='".$gudang."' and periode='".$period."'";
        mysql_query($str);            
        //==========================================
        //delete jika sudah terdaftar pada saldo bulanan
        $str="delete from ".$dbname.".log_5saldobulanan where kodeorg='".$pt."' and kodegudang='".$gudang."'  and periode='".$period."'";
        mysql_query($str);   
        exit("Error: data ".$err);        
        }
   }
   else
   {
      $err= addslashes(mysql_error($conn))."(".$str.")";  
        //==========================================
        //delete jika sudah terdaftar pada saldo bulanan
        $str="delete from ".$dbname.".log_5saldobulanan where kodeorg='".$pt."' and kodegudang='".$gudang."'  and periode='".$period."'";
        mysql_query($str);   
        exit("Error: data ".$err);      
   }   
  
}
else
{
    //==========================================
    //delete jika sudah terdaftar pada saldo bulanan
    $str="delete from ".$dbname.".log_5saldobulanan where kodeorg='".$pt."' and kodegudang='".$gudang."'  and periode='".$period."'";
    mysql_query($str);   
    exit("Error: data ".$err);
}  
?>