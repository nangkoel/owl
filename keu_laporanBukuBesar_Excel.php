<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$pt=$_GET['pt'];
$gudang=$_GET['gudang'];
$periode=$_GET['periode'];
$periode1=$_GET['periode1'];
$revisi=$_GET['revisi'];

//cek periode dan periode1
if($periode1<$periode)
{  #ditukar
    $z=$periode;
    $periode=$periode1;
    $periode1=$z;
}

/*$str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
      periode='".$periode."' and kodeorg='".$gudang."'";
$res=mysql_query($str);
$fromstart='';
$fromend='';
while($bar=mysql_fetch_object($res))
{
    $fromstart=$bar->tanggalmulai;
    $fromend=$bar->tanggalsampai;
}
$str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
      periode='".$periode1."' and kodeorg='".$gudang."'";
$res=mysql_query($str);
$tostart='';
$toend='';
while($bar=mysql_fetch_object($res))
{
    $tostart=$bar->tanggalmulai;
    $toend=$bar->tanggalsampai;
}*/

if ($gudang==''){
	#penambahan jamhari
	#mengambil kodeorganisasi dengan induk=pt dan tipe holding agar mendapatkan tanggalmulai dan tanggalsampai
	#tanggal : 06-02-2015
	$whrHed="induk='".$pt."' and tipe='HOLDING'";
	$sHold="select kodeorganisasi from ".$dbname.".organisasi where ".$whrHed."";
	$qHold=mysql_query($sHold) or die(mysql_error($conn));
	$rHold=mysql_fetch_assoc($qHold);
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
          periode='".$periode."' and kodeorg='".$rHold['kodeorganisasi']."'";
    $res=mysql_query($str);
    $fromstart='';
    $fromend='';
    while($bar=mysql_fetch_object($res))
    {
        $fromstart=$bar->tanggalmulai;
        $fromend=$bar->tanggalsampai;
    }
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
          periode='".$periode1."' and kodeorg='".$rHold['kodeorganisasi']."'";
    $res=mysql_query($str);
    $tostart='';
    $toend='';
    while($bar=mysql_fetch_object($res))
    {
        $tostart=$bar->tanggalmulai;
        $toend=$bar->tanggalsampai;
    }
} else {
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
          periode='".$periode."' and kodeorg='".$gudang."'";
    $res=mysql_query($str);
    $fromstart='';
    $fromend='';
    while($bar=mysql_fetch_object($res))
    {
        $fromstart=$bar->tanggalmulai;
        $fromend=$bar->tanggalsampai;
    }
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
          periode='".$periode1."' and kodeorg='".$gudang."'";
    $res=mysql_query($str);
    $tostart='';
    $toend='';
    while($bar=mysql_fetch_object($res))
    {
        $tostart=$bar->tanggalmulai;
        $toend=$bar->tanggalsampai;
    }
}

//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namapt=strtoupper($bar->namaorganisasi);
}

//ambil namagudang
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$gudang."'";
$namagudang='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namagudang=strtoupper($bar->namaorganisasi);
}

//ambil akun laba rugi tahun berjalan:
$CLM='';
$str="select noakundebet from ".$dbname.".keu_5parameterjurnal where kodeaplikasi='CLM'";
$res=mysql_query($str);
while($bar=  mysql_fetch_object($res))
{
    $CLM=$bar->noakundebet;
}

//ambil semua noakun dari bulan lalu dan bulan ini
$lmperiode=mktime(0,0,0,substr($periode,5,2)-1,4,substr($periode,0,4));
$lmperiode=date('Y-m',$lmperiode);
if($_SESSION['language']=='ID'){
    $str="select distinct noakun,namaakun from ".$dbname.".keu_5akun where  noakun!='".$CLM."' order by noakun";
}
else{
    $str="select distinct noakun,namaakun1 as namaakun from ".$dbname.".keu_5akun where  noakun!='".$CLM."' order by noakun";
}
$res=mysql_query($str);
$TAB=Array();
while($bar=mysql_fetch_object($res))
{
    $TAB[$bar->noakun]['noakun']=$bar->noakun;
    $TAB[$bar->noakun]['namaakun']=$bar->namaakun;
    $TAB[$bar->noakun]['sawal']=0;
    $TAB[$bar->noakun]['salak']=0;
}
//ambil saldo awal
if($gudang=='' and $pt!='')
{
    $where =" and kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."')";
}
else if($gudang!='')
{
    $where =" and kodeorg ='".$gudang."'";
}
else
{
    $where='';  
}       
//$str="select sum(awal".substr(str_replace("-","",$periode),4,2).") as sawal,noakun from ".$dbname.".keu_saldobulanan 
//      where periode ='".str_replace("-","",$periode)."' ".$where." 
//       and noakun!='3110400' group by noakun order by noakun";
$str="select sum(awal".substr(str_replace("-","",$periode),4,2).") as sawal,noakun from ".$dbname.".keu_saldobulanan 
      where periode ='".str_replace("-","",$periode)."' and   noakun!='".$CLM."' ".$where." group by noakun order by noakun";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $TAB[$bar->noakun]['sawal']=$bar->sawal;
    $TAB[$bar->noakun]['salak']=$bar->sawal;
}

//ambil mutasi-----------------------
if($gudang=='' and $pt=='')
{
//    $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnalsum_vw
//        where periode>='".$periode."' and periode<='".$periode1."'
//        and noakun!='".$CLM."' group by noakun"; #tidak sama dengan laba/rugi berjalan
    $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnaldt_vw
        where tanggal between '".$fromstart."' and '".$toend."'
        and noakun!='".$CLM."' and revisi <= '".$revisi."' group by noakun"; #tidak sama dengan laba/rugi berjalan
}
else if($gudang=='' and $pt!='')
{
//    $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnalsum_vw
//        where periode>='".$periode."' and periode<='".$periode1."' and kodeorg in(select kodeorganisasi 
//        from ".$dbname.".organisasi where induk='".$pt."' and length(kodeorganisasi)=4)
//        and noakun!='".$CLM."' group by noakun"; #tidak sama dengan laba/rugi berjalan
    $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnaldt_vw
        where tanggal between '".$fromstart."' and '".$toend."' and kodeorg in(select kodeorganisasi 
        from ".$dbname.".organisasi where induk='".$pt."' and length(kodeorganisasi)=4)
        and noakun!='".$CLM."' and revisi <= '".$revisi."' group by noakun"; #tidak sama dengan laba/rugi berjalan
} 
else
{
//    $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnalsum_vw
//        where periode>='".$periode."' and periode<='".$periode1."' and kodeorg ='".$gudang."'
//        and noakun!='".$CLM."' group by noakun"; #tidak sama dengan laba/rugi berjalan 
    $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnaldt_vw
        where tanggal between '".$fromstart."' and '".$toend."' and kodeorg ='".$gudang."'
        and noakun!='".$CLM."' and revisi <= '".$revisi."' group by noakun"; #tidak sama dengan laba/rugi berjalan 
} 
//=================================================
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $TAB[$bar->noakun]['debet']=$bar->debet;
    $TAB[$bar->noakun]['kredit']=$bar->kredit;
    $TAB[$bar->noakun]['salak']=$TAB[$bar->noakun]['sawal']+$bar->debet-$bar->kredit;
} 
$no=0;
$stream=strtoupper($_SESSION['lang']['neracasaldo'])." : ".$namapt." ".$namagudang."<br>".$periode." s/d ".$periode1."<table border=1>
    <thead>
    <tr bgcolor='#dedede'>
        <td>".$_SESSION['lang']['nourut']."</td>
        <td>".$_SESSION['lang']['noakun']."</td>
        <td width=60px>".$_SESSION['lang']['namaakun']."</td>
        <td>".$_SESSION['lang']['saldoawal']."</td>
        <td>".$_SESSION['lang']['debet']."</td>
        <td>".$_SESSION['lang']['kredit']."</td>
        <td>".$_SESSION['lang']['saldoakhir']."</td>
    </tr>  
    </thead>
    <tbody id=container>";
$sal_awal=0;
$sal_debet=0;
$sal_kredit=0;
$sal_salak=0;    
foreach($TAB as $baris => $data)
{
 /*   if($data['sawal']==0 && $data['debet']==0 && $data['kredit']==0 && $data['salak']==0)
    {
        
    }
    else
    {*/    
    $no+=1;
    $stream.="<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=\"lihatDetail('".$data['noakun']."','".$periode."','".$lmperiode."','".$pt."','".$gudang."',event);\">
            <td>".$no."</td>
            <td>".$data['noakun']."</td>    
            <td>".$data['namaakun']."</td>
            <td align=right>".$data['sawal']."</td>
            <td align=right>".$data['debet']."</td>
            <td align=right>".$data['kredit']."</td>   
            <td align=right>".$data['salak']."</td>    
        </tr>";
   // }   
    $sal_awal+=$data['sawal'];
    $sal_debet+=$data['debet'];
    $sal_kredit+=$data['kredit'];
    $sal_salak+=$data['salak'];
} 

$stream.="<tr class=rowcontent>
        <td colspan=3 align=center>TOTAL</td>
        <td align=right>".$sal_awal."</td>
        <td align=right>".$sal_debet."</td>
        <td align=right>".$sal_kredit."</td>   
        <td align=right>".$sal_salak."</td> 
    </tr>
    </tbody>
    <tfoot>
    </tfoot>		 
    </table>";
$qwe=date("YmdHms");

$nop_="NeracaSaldo_".$gudang.$periode." ".$qwe;
if(strlen($stream)>0)
{
    $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
    gzwrite($gztralala, $stream);
    gzclose($gztralala);
    echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}    
?>