<?php

require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
require_once('lib/zLib.php');

$pt=$_POST['pt'];
$unit=$_POST['unit']; //kebun
$periode=$_POST['periode'];

$qwe=explode('-',$periode);
$tahun=$qwe[0];
$tahunlalu=$tahun-1;
$bulan=$qwe[1];

$bulanlalu=$bulan-1; 
if($bulanlalu<10)$bulanlalu='0'.$bulanlalu; // bulan lalu dia digit
$periodelalu=$tahun.'-'.$bulanlalu; // periode lalu
if($bulan==1)$periodelalu=$tahunlalu.'-12';

$desemberlalu=$tahunlalu.'-12'; // periode desember tahun lalu

//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namapt=strtoupper($bar->namaorganisasi);
}

$kodelaporan='LAPORAN KEUANGAN';

$periodesaldo=str_replace("-", "", $periode);

//periode db
$periodeCUR=str_replace("-", "", $periode);
$periodePRF=str_replace("-", "", $periodelalu);
$periodeLSD=str_replace("-", "", $desemberlalu);

//kolom db
$kolomCUR='awal'.$bulan;
$kolomPRF='awal'.$bulanlalu;
$kolomLSD='awal12';

//title table
$t=mktime(0,0,0,substr($periodeCUR,4,2),15,substr($periodeCUR,0,4));
$captionCUR=date('M-Y',$t);
$t=mktime(0,0,0,substr($periodePRF,4,2),15,substr($periodePRF,0,4));
$captionPRF=date('M-Y',$t);
$t=mktime(0,0,0,substr($periodeLSD,4,2),15,substr($periodeLSD,0,4));
$captionLSD=date('M-Y',$t);

//involving units
if($unit=='')$where=" kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."')";
else $where=" kodeorg='".$unit."'";

//report format
$str="select * from ".$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $dzArr[$bar->nourut]['nourut']=$bar->nourut;
    $dzArr[$bar->nourut]['tipe']=$bar->tipe;
    if($_SESSION['language']=='ID'){
        $dzArr[$bar->nourut]['keterangan']=$bar->keterangandisplay;}
    else{
        $dzArr[$bar->nourut]['keterangan']=$bar->keterangandisplay1;
    }
    $dzArr[$bar->nourut]['noakundari']=$bar->noakundari;
    $dzArr[$bar->nourut]['noakunsampai']=$bar->noakunsampai;
}

$stream="<table class=sortable border=0 cellspacing=0>
    <thead>
        <tr class=rowheader>
            <td style='width:520px' align=center colspan=3 rowspan=2>Description</td>
            <td style='width:120px' align=center rowspan=2>".$captionCUR."</td>
            <td style='width:120px' align=center rowspan=2>".$captionPRF."</td>    
            <td style='width:120px' align=center rowspan=2>".$captionLSD."</td>    
            <td align=center colspan=2>Increase/Decrease</td>    
        </tr>
        <tr class=rowheader>
            <td style='width:120px' align=center>Rupiah</td>
            <td style='width:50px' align=center>%</td>
        </tr>
    </thead><tbody>";

//saldo awal
if(!empty($dzArr))foreach($dzArr as $data){
    $st12="select sum(".$kolomPRF.") as jumlah
        from ".$dbname.".keu_saldobulanan where noakun between '".$data['noakundari']."' 
        and '".$data['noakunsampai']."' and (periode='".$periodePRF."') and ".$where;  
    $res12=mysql_query($st12);
    $jlhlalu=0;
    while($ba12=mysql_fetch_object($res12))
    {
        $dzArr[$data['nourut']]['PRF']=$ba12->jumlah;
    }     
    
    $st12="select sum(".$kolomCUR.") as jumlah
        from ".$dbname.".keu_saldobulanan where noakun between '".$data['noakundari']."' 
        and '".$data['noakunsampai']."' and (periode='".$periodeCUR."') and ".$where;
    $res12=mysql_query($st12);
    $jlhsekarang=0;
    while($ba12=mysql_fetch_object($res12))
    {
        $dzArr[$data['nourut']]['CUR']=$ba12->jumlah;
    }      
    
    $st12="select sum(".$kolomLSD.") as jumlah
        from ".$dbname.".keu_saldobulanan where noakun between '".$data['noakundari']."' 
        and '".$data['noakunsampai']."' and (periode='".$periodeLSD."') and ".$where;
    $res12=mysql_query($st12);
    $jlhsekarang=0;
    while($ba12=mysql_fetch_object($res12))
    {
        $dzArr[$data['nourut']]['LSD']=$ba12->jumlah;
    }      
}

$st12="select noakun, namaakun, namaakun1
    from ".$dbname.".keu_5akun where level=5";
$res12=mysql_query($st12);
while($ba12=mysql_fetch_object($res12))
{
    if($_SESSION['language']=='ID'){
        $akun[$ba12->noakun]=$ba12->namaakun;}
    else{
        $akun[$ba12->noakun]=$ba12->namaakun1;
    }
}      

$st12="select noakun, ".$kolomCUR." as jumlah
    from ".$dbname.".keu_saldobulanan where (periode='".$periodeCUR."') and ".$where;
$res12=mysql_query($st12);
while($ba12=mysql_fetch_object($res12))
{
    $dzArr2[$ba12->noakun]['CUR']+=$ba12->jumlah;
    $dzArr2[$ba12->noakun]['noakun']=$ba12->noakun;
}      
$st12="select noakun, ".$kolomPRF." as jumlah
    from ".$dbname.".keu_saldobulanan where (periode='".$periodePRF."') and ".$where;
$res12=mysql_query($st12);
while($ba12=mysql_fetch_object($res12))
{
    $dzArr2[$ba12->noakun]['PRF']+=$ba12->jumlah;
    $dzArr2[$ba12->noakun]['noakun']=$ba12->noakun;
}      
$st12="select noakun, ".$kolomLSD." as jumlah
    from ".$dbname.".keu_saldobulanan where (periode='".$periodeLSD."') and ".$where;
$res12=mysql_query($st12);
while($ba12=mysql_fetch_object($res12))
{
    $dzArr2[$ba12->noakun]['LSD']+=$ba12->jumlah;
    $dzArr2[$ba12->noakun]['noakun']=$ba12->noakun;
}      

//echo "<pre>";
//print_r($dzArr);
//echo "</pre>";
//exit;

//ambil format mesinlaporan
if(!empty($dzArr))foreach($dzArr as $data){
    if($data['tipe']=='Header')
    {
        $stream.="<tr class=rowcontent>
            <td colspan=8><b>".$data['keterangan']."</b></td>
        </tr>"; 
    }
    else
    if($data['tipe']=='Total'){
        if($totallagi==1){
            $subtotal['CUR']=$subtotal2['CUR'];
            $subtotal['PRF']=$subtotal2['PRF'];
            $subtotal['LSD']=$subtotal2['LSD'];
            $subtotal2['CUR']=0;
            $subtotal2['PRF']=0;
            $subtotal2['LSD']=0;  
        }
        @$subtotalPER=($subtotal['CUR']-$subtotal['PRF'])/$subtotal['PRF']*100;
        $stream.="<tr class=rowcontent>
            <td colspan=3></td>
            <td colspan=5><hr></td>
        </tr>
        <tr class=rowcontent>
            <td style='width:10px'></td>
            <td style='width:10px'></td>
            <td><b>".$data['keterangan']."</b></td>
            <td align=right><b>".number_format($subtotal['CUR'])."</b></td>
            <td align=right><b>".number_format($subtotal['PRF'])."</b></td>    
            <td align=right><b>".number_format($subtotal['LSD'])."</b></td>    
            <td align=right><b>".number_format($subtotal['CUR']-$subtotal['PRF'])."</b></td>    
            <td align=right><b>".number_format($subtotalPER,2)."</b></td>    
        </tr>
        <tr class=rowcontent><td colspan=8></td></tr>
        ";
        if($totallagi==0){
            $subtotal2['CUR']+=$subtotal['CUR'];
            $subtotal2['PRF']+=$subtotal['PRF'];
            $subtotal2['LSD']+=$subtotal['LSD'];        
        }
        $subtotal['CUR']=0;
        $subtotal['PRF']=0;
        $subtotal['LSD']=0;  
        $totallagi=1;
    }
    else
    if($data['tipe']=='Detail'){
        $totallagi=0;
        @$dataPER=($data['CUR']-$data['PRF'])/$data['PRF']*100;
        $stream.="
        <tr class=rowcontent title='Click untuk melihat detail' style=cursor:pointer; onclick=\"switchHidden(".$data['nourut'].")\">
            <td style='width:10px'></td>
            <td colspan=2>".$data['keterangan']."</td>
            <td align=right>".number_format($data['CUR'])."</td>
            <td align=right>".number_format($data['PRF'])."</td>    
            <td align=right>".number_format($data['LSD'])."</td>    
            <td align=right>".number_format($data['CUR']-$data['PRF'])."</td>    
            <td align=right>".number_format($dataPER,2)."</td>    
        </tr>";          
        $subtotal['CUR']+=$data['CUR'];
        $subtotal['PRF']+=$data['PRF'];
        $subtotal['LSD']+=$data['LSD'];
        $stream.="<tr><td colspan=8><div style=\"display:none;\" id=".$data['nourut']."><table class=sortable border=0 cellspacing=0>";
        if(!empty($dzArr2))foreach($dzArr2 as $data2){
            @$data2PER=($data2['CUR']-$data2['PRF'])/$data2['PRF']*100;
            if(($data2['noakun']>=$data['noakundari'])and($data2['noakun']<=$data['noakunsampai']))
            $stream.="
            <tr class=rowcontent>
                <td style='width:10px'></td>
                <td style='width:10px'></td>
                <td style='width:500px'>".$akun[$data2['noakun']]."</td>
                <td style='width:120px' align=right>".number_format($data2['CUR'])."</td>
                <td style='width:120px' align=right>".number_format($data2['PRF'])."</td>    
                <td style='width:120px' align=right>".number_format($data2['LSD'])."</td>    
                <td style='width:120px' align=right>".number_format($data2['CUR']-$data2['PRF'])."</td>    
                <td style='width:50px' align=right>".number_format($data2PER,2)."</td>    
            </tr>";          
        }
        $stream.="</table></div></td></tr>";
    }
}

$stream.= "</tbody></tfoot></tfoot></table>";
echo $stream;

?>