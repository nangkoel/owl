<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$tanggalmulai=$_GET['tanggalmulai'];
$tanggalsampai=$_GET['tanggalsampai']; 
$noakun=$_GET['noakun'];
$kodeo=$_GET['kodeorg'];

$pt=makeOption($dbname,'organisasi','kodeorganisasi,induk');

//exit("Error:$kodeorg");

if($kodeo=='') {
	$ind='';
} else {
	$ind="AND a.kodeorg IN (SELECT kodeorganisasi FROM ".$dbname.".organisasi WHERE induk ='".$kodeo."')";
}

$qwe=explode("-",$tanggalmulai); $tanggalmulai=$qwe[2]."-".$qwe[1]."-".$qwe[0];
$qwe=explode("-",$tanggalsampai); $tanggalsampai=$qwe[2]."-".$qwe[1]."-".$qwe[0];

$cekData="select count(*) as jumlah from ".$dbname.".keu_jurnaldt_vw a
  where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' and noakun='".$noakun."' ".$ind;
$rescek=fetchData($cekData);
if ($rescek[0]['jumlah']>32000){
    exit('Error: Range tanggal terlalu besar. Harap memilih range yang lebih kecil.');
}

$str="select distinct nik from ".$dbname.".keu_jurnaldt_vw a
  where tanggal between '".$tanggalmulai."' and '".$tanggalsampai."'  and noakun = '".$noakun."' and nik!='' and nik is not null ".$ind;
$res=mysql_query($str);
$whrKary="''";
while($bar=mysql_fetch_object($res)){
    $whrKary.=",'".$bar->nik."'";
}
$whrKary="karyawanid in (".$whrKary.")";
$nmKar=  makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan',$whrKary);
$nmSup=makeOption($dbname,'log_5supplier','supplierid,namasupplier');
//=================================================
$stream="<table border=1>
             <thead>
                    <tr>
                          <td align=center width=50>".$_SESSION['lang']['nourut']."</td>
                          <td align=center>".$_SESSION['lang']['organisasi']."</td>
                          <td align=center>".$_SESSION['lang']['tanggal']."</td>
                          <td align=center>".$_SESSION['lang']['nojurnal']."</td>
                          <td align=center>".$_SESSION['lang']['noreferensi']."</td>
                          <td align=center>".$_SESSION['lang']['nodok']."</td>
                          <td align=center>".$_SESSION['lang']['noakun']."</td>
                          <td align=center>".$_SESSION['lang']['keterangan']."</td>
                           <td align=center>".$_SESSION['lang']['karyawanid']."/".$_SESSION['lang']['kodesupplier']."</td>
                           <td align=center>".$_SESSION['lang']['karyawan']."/".$_SESSION['lang']['supplier']."</td>
                          <td align=center>".$_SESSION['lang']['saldoawal']."</td>                             
                          <td align=center>".$_SESSION['lang']['debet']."</td>
                          <td align=center>".$_SESSION['lang']['kredit']."</td>
                          <td align=center>".$_SESSION['lang']['saldoakhir']."</td>                               
                        </tr>  
                 </thead>
                 <tbody id=container>";


    
     #ambil saldo awal  karyawan
    $str="select sum(a.debet-a.kredit) as sawal,a.noakun, b.namaakun,a.nik,a.kodeorg from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
      where a.tanggal<'".$tanggalmulai."'  and a.noakun = '".$noakun."' and a.nik!='' and a.nik is not null AND a.nik!=0
       ".$ind." group by a.nik";//,a.kodeorg
    //echo $str;

    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $no+=1;
        if(strlen($bar->nik)<10)
        {
            $bar->nik=  addZero($bar->nik, 10);
        }
        
        
        if($bar->nik==0)
        {
             $sawal['lain']+=$bar->sawal;
        }
        else
        {
            $sawal[$bar->nik]=$bar->sawal;
            $supplier[$bar->nik]=$nmKar[$bar->nik];   
            $akun[$bar->noakun]=$bar->namaakun;  
        }
        $kodeorg[$no]=$bar->kodeorg;    
    }


    $str="select sum(a.debet) as debet,sum(a.kredit) as kredit,a.noakun, b.namaakun,a.nik,a.kodeorg from ".$dbname.".keu_jurnaldt_vw a
         left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
         where a.tanggal between'".$tanggalmulai."' and '".$tanggalsampai."'  
         and a.noakun = '".$noakun."' and a.nik!='' and a.nik is not null AND a.nik!=0
         ".$ind." group by a.nik ";//,a.kodeorg
  
   $res=mysql_query($str);
   while($bar=mysql_fetch_object($res))
   {
       if(strlen($bar->nik)<10)
       {
           $bar->nik=  addZero($bar->nik, 10);
       }
        $no+=1;
        
       if($bar->nik==0)
       {
           $debet['lain']+=$bar->debet;
           $kredit['lain']+=$bar->kredit; 
       } 
       else
       {
            $debet[$bar->nik]=$bar->debet;
            $kredit[$bar->nik]=$bar->kredit;
            $supplier[$bar->nik]=$nmKar[$bar->nik];   
       }
        
             
       $akun[$bar->noakun]=$bar->namaakun;

           $kodeorg[$no]=$bar->kodeorg;
   }


####################################################################################################################
####################################################################################################################   

    #ambil saldo awal supplier
     $str="select sum(a.debet-a.kredit) as sawal,a.noakun, b.namaakun,a.kodesupplier as nik,a.kodeorg from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
      where a.tanggal<'".$tanggalmulai."'  and a.noakun = '".$noakun."' and (a.kodesupplier!='' and (a.kodesupplier IS NOT NULL AND (a.nik=0 OR a.nik=''))) 
       ".$ind." group by a.kodesupplier";//,a.kodeorg
    //echo $str;

    
     
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $no+=1;
        if($bar->nik==''  or $bar->nik=='0')
        {
           $sawal['lain']+=$bar->sawal;
        }
       
        $sawal[$bar->nik]=$bar->sawal;
        $supplier[$bar->nik]=$nmSup[$bar->nik];
        $akun[$bar->noakun]=$bar->namaakun;

            $kodeorg[$no]=$bar->kodeorg;
    }
    
    #ambil  transaksi dalam periode supplier
   $str="select sum(a.debet) as debet,sum(a.kredit) as kredit,a.noakun, b.namaakun,a.kodesupplier as nik,a.kodeorg from ".$dbname.".keu_jurnaldt_vw a
         left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
         where a.tanggal between'".$tanggalmulai."' and '".$tanggalsampai."'  
         and a.noakun = '".$noakun."' and (a.kodesupplier!='' or (a.kodesupplier IS NOT NULL AND (a.nik=0 OR a.nik='')))  
         ".$ind." group by a.kodesupplier ";//,a.kodeorg
    
  
   $res=mysql_query($str) or die (mysql_error($conn));
   while($bar=mysql_fetch_object($res))
   {
       if($bar->nik=='' or $bar->nik=='0')
        {
           $debet['lain']+=$bar->debet;
           $kredit['lain']+=$bar->kredit;
        }
           $no+=1;
       $debet[$bar->nik]=$bar->debet;
       $kredit[$bar->nik]=$bar->kredit;
       $supplier[$bar->nik]=$nmSup[$bar->nik];      
       $akun[$bar->noakun]=$bar->namaakun;

           $kodeorg[$no]=$bar->kodeorg;
   }
    
/*echo"<pre>";
print_r($sawal);
echo"</pre>";*/

//=================================================
$no=0;
if($supplier<1)
{
        $stream.="<tr class=rowcontent><td colspan=9>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
}
else
{
   if(!empty($supplier)){
        $str2="";
        foreach($supplier as $kdsupp =>$val){
           if ($kdsupp!='') $str2.="'".$kdsupp."',";
        }
        $str2=substr($str2,0,-1);
        $str="select a.* from ".$dbname.".keu_jurnaldt_vw a
              where a.tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' 
              and a.noakun = '".$noakun."' and (a.kodesupplier in (".$str2.") or a.nik in (".$str2."))
              ".$ind." order by a.kodesupplier,a.nik,tanggal";
        $res=mysql_query($str) or die (mysql_error($conn));
        while($bar=mysql_fetch_object($res)) {
            $detail[$bar->nojurnal.$bar->nourut]=$bar->nojurnal;
            $detorg[$bar->nojurnal.$bar->nourut]=$bar->kodeorg;
            $detref[$bar->nojurnal.$bar->nourut]=$bar->noreferensi;
            $dettgl[$bar->nojurnal.$bar->nourut]=$bar->tanggal;
            $detket[$bar->nojurnal.$bar->nourut]=$bar->keterangan;
            $detnodok[$bar->nojurnal.$bar->nourut]=$bar->nodok;
            $detsupp[$bar->nojurnal.$bar->nourut]=$bar->kodesupplier;
            $detnik[$bar->nojurnal.$bar->nourut]=$bar->nik;
            $detdebet[$bar->nojurnal.$bar->nourut]=$bar->debet;
            $detkredit[$bar->nojurnal.$bar->nourut]=$bar->kredit;
        }
        $str="select a.* from ".$dbname.".keu_jurnaldt_vw a
              where a.tanggal between '".$tanggalmulai."' and '".$tanggalsampai."' 
              and a.noakun = '".$noakun."' and (kodesupplier='' OR kodesupplier IS NULL) AND (a.nik='' OR a.nik IS NULL OR nik=0)
              ".$ind." order by a.kodesupplier,a.nik,tanggal";
        //exit("Error:".$str);
        $res=mysql_query($str) or die (mysql_error($conn));
        while($bar=mysql_fetch_object($res)) {
            $detail['lain'][$bar->nojurnal.$bar->nourut]=$bar->nojurnal;
            $detorg['lain'][$bar->nojurnal.$bar->nourut]=$bar->kodeorg;
            $detref['lain'][$bar->nojurnal.$bar->nourut]=$bar->noreferensi;
            $dettgl['lain'][$bar->nojurnal.$bar->nourut]=$bar->tanggal;
            $detket['lain'][$bar->nojurnal.$bar->nourut]=$bar->keterangan;
            $detnodok['lain'][$bar->nojurnal.$bar->nourut]=$bar->nodok;
            $detsupp['lain'][$bar->nojurnal.$bar->nourut]=$bar->kodesupplier;
            $detnik['lain'][$bar->nojurnal.$bar->nourut]=$bar->nik;
            $detdebet['lain'][$bar->nojurnal.$bar->nourut]=$bar->debet;
            $detkredit['lain'][$bar->nojurnal.$bar->nourut]=$bar->kredit;
        }
        
        $no=0;
        foreach($detail as $nojurnal=>$val2){
            if ($nojurnal!='lain'){
            $nik=($detsupp[$nojurnal]!='')?$detsupp[$nojurnal]:$detnik[$nojurnal];
            if ($nik1!=$nik) {
                $detsawal=$sawal[$nik];
                $nik1=$nik;
            }
            $niks=(substr($nik,0,1)=='0')?"'".$nik:$nik;
            $nodok=($detnodok[$nojurnal]!='0')?$detnodok[$nojurnal]:"";

            $no++;
            $stream.="<tr>
                  <td align=center width=20>".$no."</td>
                  <td align=center>".$detorg[$nojurnal]."</td>
                  <td>".$dettgl[$nojurnal]."</td>
                  <td>".$val2."</td>
                  <td>".$detref[$nojurnal]."</td>
                  <td>".$nodok."</td>
                  <td>".$noakun."</td>
                  <td>".$detket[$nojurnal]."</td>
                  <td>".$niks."</td>
                  <td nowrap>".$supplier[$nik]."</td>
                  <td align=right width=100>".number_format($detsawal,2)."</td>   
                  <td align=right width=100>".number_format($detdebet[$nojurnal],2)."</td>
                  <td align=right width=100>".number_format($detkredit[$nojurnal],2)."</td>
                  <td align=right width=100>".number_format($detsawal+$detdebet[$nojurnal]-$detkredit[$nojurnal],2)."</td>
                 </tr>"; 
            $detsawal+=$detdebet[$nojurnal]-$detkredit[$nojurnal];
            }
        }
        $nolain=0;
        foreach($detail['lain'] as $nojurnal=>$val2){
            $no++;$nolain++;
            if ($nolain==1) $detsawal=$sawal['lain'];
            $nodok=($detnodok['lain'][$nojurnal]!='0')?$detnodok['lain'][$nojurnal]:"";
            $stream.="<tr>
                <td align=center width=20>".$no."</td>
                <td align=center>".$detorg['lain'][$nojurnal]."</td>
                <td>".$dettgl['lain'][$nojurnal]."</td>
                <td>".$val2."</td>
                <td>".$detref['lain'][$nojurnal]."</td>
                <td>".$nodok."</td>
                <td>".$noakun."</td>
                <td>".$detket['lain'][$nojurnal]."</td>
                <td></td>
                <td>Lain-Lain</td>
                <td align=right width=100>".number_format($detsawal,2)."</td>   
                <td align=right width=100>".number_format($detdebet['lain'][$nojurnal],2)."</td>
                <td align=right width=100>".number_format($detkredit['lain'][$nojurnal],2)."</td>
                <td align=right width=100>".number_format($detsawal+$detdebet['lain'][$nojurnal]-$detkredit['lain'][$nojurnal],2)."</td>
               </tr>"; 
          $detsawal+=$detdebet['lain'][$nojurnal]-$detkredit['lain'][$nojurnal];
        }
   }
} 
//$stream.="<tr class=rowcontent>
//      <td align=center colspan=6>Total</td>
//       <td align=right width=100>".number_format($tsa,2)."</td>   
//      <td align=right width=100>".number_format($td,2)."</td>
//      <td align=right width=100>".number_format($tk,2)."</td>
//      <td align=right width=100>".number_format($tak,2)."</td>
//     </tr>"; 

//exit("Error:$stream");
$stream.="</tbody></table>";
$qwe=date("YmdHms");
$nop_="LP_JRNL_Detail_".$noakun."_".$qwe;
if(strlen($stream)>0)
{
    if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
        if ($handle = opendir('tempExcel')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                @unlink('tempExcel/'.$file);
                }
            }	
            closedir($handle);
        }
        $handle=fopen("tempExcel/".$nop_.".xls",'w');
        if(!fwrite($handle,$stream)) {
            echo "<script language=javascript1.2>
            parent.window.alert('Can't convert to excel format');
            </script>";
            exit;
        } else {
            echo "<script language=javascript1.2>
            window.location='tempExcel/".$nop_.".xls';
            </script>";
        }
        closedir($handle);
    } else {
        $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
        gzwrite($gztralala, $stream);
        gzclose($gztralala);
        echo "<script language=javascript1.2>
           window.location='tempExcel/".$nop_.".xls.gz';
           </script>";
    }
}
?>