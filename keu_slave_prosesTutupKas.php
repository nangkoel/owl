<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;
$tmpPeriod = explode('-',$param['periode']);
$tahunbulan = implode("",$tmpPeriod);
$kodeorg=$param['kodeorg'];
$dariperiode=$param['periode'];
#==================== Prep Periode ====================================
# Prep Tahun Bulan untuk periode selanjutnya
if($tmpPeriod[1]==12) {
    $bulanLanjut = 1;
    $tahunLanjut = $tmpPeriod[0]+1;
} else {
    $bulanLanjut = $tmpPeriod[1]+1;
    $tahunLanjut = $tmpPeriod[0];
}
$keperiode=$tahunLanjut.'-'.addZero($bulanLanjut,2);
$sawal=Array();
$mtdebet=Array();
$mtkredit=Array();
$salak=Array();
#ambil saldoawal bulan berjalan
$str="select awal".substr($dariperiode,5,2).",noakun from ".$dbname.".keu_saldobulanankas
      where periode='".str_replace("-", "", $dariperiode)."' and kodeorg='".$kodeorg."'";
$res=mysql_query($str);
while($bar=mysql_fetch_array($res))
{
    $sawal[$bar[1]]=$bar[0];
    $mtdebet[$bar[1]]=0;
    $mtkredit[$bar[1]]=0;
    $salak[$bar[1]]=$bar[0];
}
#ambil transaksi transaksi bln berjalan
$str="select noakun,tipetransaksi,sum(jumlah) as jumlah from ".$dbname.".keu_kasbankht where noakun like '1110%'
      AND posting=1 AND tanggalposting like'".$dariperiode."%' and kodeorg='".$kodeorg."' group by noakun,tipetransaksi";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    if ($bar->tipetransaksi=='M'){
        $mtdebet[$bar->noakun]=$bar->jumlah;
    } else {
        $mtkredit[$bar->noakun]=$bar->jumlah;
    }
}
if ($param['proses']=='listing'){
        echo"<button class=mybutton onclick=prosesTutupBank(1) id=btnproses>Process</button>
            <table class=sortable cellspacing=1 border=0>
            <thead>
            <tr class=rowheader>
            <td>No</td>
            <td>No Akun</td>
            <td>Saldo Awal</td>
            <td>Debit</td>
            <td>Kredit</td>
            <td>Saldo Akhir</td>
            </tr>
            </thead>
            <tbody>";

    $no=0;
    foreach($sawal as $key=>$val)
    { 
        $salak=$mtdebet[$key]+$sawal[$key]-$mtkredit[$key];
            $no+=1;

            echo"<tr class=rowcontent id='row".$no."'>
            <td>".$no."</td>
            <td id='noakun".$no."'>".$key."</td>
            <td align=right id='sawal".$no."'>".$sawal[$key]."</td>    
            <td align=right id='debit".$no."'>".$mtdebet[$key]."</td>
            <td align=right id='kredit".$no."'>".$mtkredit[$key]."</td>
            <td align=right id='salak".$no."'>".$salak."</td>
            </tr>";
    }
    echo"</tbody><tfoot></tfoot></table>";
} else if ($param['proses']=='insert'){
    #delete saldo awal bulan selanjutnya;
    $str="delete from ".$dbname.".keu_saldobulanankas where periode='".str_replace("-", "", $keperiode)."'
          and kodeorg='".$kodeorg."' and noakun=".$param['noakun'];
    if(mysql_query($str)) {
           $temp="insert into  ".$dbname.".keu_saldobulanankas (kodeorg,periode,noakun,
                  awal".substr($keperiode,5,2).")values('". 
                   $kodeorg."','".str_replace("-", "", $keperiode)."','".$param['noakun']."',".$param['sakhir'].")";
           if(substr($keperiode,5,2)!='01')#jika bukan awal tahun
           {      
               if(!mysql_query($temp))
               {
                   exit("Error insert saldo awal ".mysql_error($conn).":".$temp);
               }  
           }
           else #jika bulan 12
           {
                $temp1="insert into  ".$dbname.".keu_saldobulanankas (kodeorg,periode,noakun,
                      awal".substr($keperiode,5,2).")values('". 
                       $kodeorg."','".str_replace("-", "", $keperiode)."','".$param['noakun']."',".$param['sakhir'].")";

               if(!mysql_query($temp1))
               {
                   exit("Error insert saldo awal ".mysql_error($conn));
               } 
           }
    }   
} else {
#ambil semua nomor akun kas bank
$str="select noakun from ".$dbname.".keu_5akun where length(noakun)=7 and noakun like '1110%'";
$res=mysql_query($str);
$temp='';
while($bar=mysql_fetch_object($res))
{
    $nmakun[$bar->noakun]=$bar->namaakun;
    if($sawal[$bar->noakun]!='')
    {  
     #jika sudah ada di database maka update
        if($mtdebet[$bar->noakun]=='')
            $mtdebet[$bar->noakun]=0;
       if($mtkredit[$bar->noakun]=='')
            $mtkredit[$bar->noakun]=0;

//       $temp="update ".$dbname.".keu_saldobulanankas
//            set debet".substr($dariperiode,5,2)."=".$mtdebet[$bar->noakun].",
//            kredit".substr($dariperiode,5,2)."=".$mtkredit[$bar->noakun]."
//            where periode='".str_replace("-", "", $dariperiode)."'
//            and kodeorg='".$kodeorg."' and noakun='".$bar->noakun."';";
//       if(!mysql_query($temp))
//       {
//           exit("Error update mutasi bulanan ".mysql_error($conn));
//       }   
    }
    else
    {
       #jika belum ada maka insert
     if($sawal[$bar->noakun]!='' or $mtdebet[$bar->noakun]!='' or  $mtkredit[$bar->noakun]!=''){
        if($mtdebet[$bar->noakun]=='')
            $mtdebet[$bar->noakun]=0;
       if($mtkredit[$bar->noakun]=='')
            $mtkredit[$bar->noakun]=0;
       $temp="insert into  ".$dbname.".keu_saldobulanankas (kodeorg,periode,noakun,
              awal".substr($dariperiode,5,2).")values('". 
               $kodeorg."','".str_replace("-", "", $dariperiode)."','".$bar->noakun."',0);";
       if(!mysql_query($temp))
       {
           exit("Error insert mutasi bulanan ".mysql_error($conn));
       }  
     }
    }   
} 
#delete saldo awal bulan selanjutnya;
$str="delete from ".$dbname.".keu_saldobulanankas where periode='".str_replace("-", "", $keperiode)."'
      and kodeorg='".$kodeorg."';";
if(mysql_query($str))
{
    $saldoditahan=0;
    foreach($salak as $key=>$val){
        if($salak[$key]!=''){

            $temp="insert into  ".$dbname.".keu_saldobulanankas (kodeorg,periode,noakun,
                  awal".substr($keperiode,5,2).")values('". 
                   $kodeorg."','".str_replace("-", "", $keperiode)."','".$key."',".$salak[$key].")";
           if(substr($keperiode,5,2)!='01')#jika bukan awal tahun
           {      
               if(!mysql_query($temp))
               {
                   exit("Error insert saldo awal ".mysql_error($conn).":".$temp);
               }  
           }
           else #jika bulan 12
           {
                $temp1="insert into  ".$dbname.".keu_saldobulanankas (kodeorg,periode,noakun,
                      awal".substr($keperiode,5,2).")values('". 
                       $kodeorg."','".str_replace("-", "", $keperiode)."','".$key."',".$salak[$key].")";

               if(!mysql_query($temp1))
               {
                   exit("Error insert saldo awal ".mysql_error($conn));
               } 
           }
        }   
    }
}   
}
?>