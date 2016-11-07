<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$pt=$_POST['pt'];
$gudang=$_POST['gudang'];
$periode=$_POST['periode'];
$periode1=$_POST['periode1'];
$revisi=$_POST['revisi'];

//if($periode=='' and $gudang=='' and $pt=='')
//{               
//               $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
//		left join ".$dbname.".keu_5akun b
//		on a.noakun=b.noakun
//		where a.tanggal>=".$_SESSION['org']['period']['start']." and  a.tanggal<=".$_SESSION['org']['period']['end']."
//                and a.nojurnal NOT LIKE '%CLSM%'
//		order by a.nojurnal 
//		";
//}
//else if($periode=='' and $gudang=='' and $pt!='')
//{               
//               $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
//		left join ".$dbname.".keu_5akun b
//		on a.noakun=b.noakun
//		where a.tanggal>=".$_SESSION['org']['period']['start']." and  a.tanggal<=".$_SESSION['org']['period']['end']."
//		and a.kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' 
//                and length(kodeorganisasi)=4)
//                and a.nojurnal NOT LIKE '%CLSM%'
//                order by a.nojurnal 
//		";
//}
//else if($periode=='' and $gudang!='')
//{
//               $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
//		left join ".$dbname.".keu_5akun b
//		on a.noakun=b.noakun
//		where a.tanggal>=".$_SESSION['org']['period']['start']." and  a.tanggal<=".$_SESSION['org']['period']['end']."
//		and a.kodeorg='".$gudang."'
//                order by a.nojurnal 
//		";
//}
//else if($periode!='' and $gudang=='' and $pt=='')
//{
//               $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
//		left join ".$dbname.".keu_5akun b
//		on a.noakun=b.noakun
//		where a.tanggal like '".$periode."%'
//                and a.nojurnal NOT LIKE '%CLSM%'
//		order by a.nojurnal 
//		";
//}
//else if($periode!='' and $gudang=='' and $pt!='')
//{
//               $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
//		left join ".$dbname.".keu_5akun b
//		on a.noakun=b.noakun
//		where a.tanggal like '".$periode."%'
//                and a.kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' 
//                and a.nojurnal NOT LIKE '%CLSM%'
//                and length(kodeorganisasi)=4)                    
//		order by a.nojurnal 
//		";
//}
//else if($periode!='' and $gudang!='')
//{
//               $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
//		left join ".$dbname.".keu_5akun b
//		on a.noakun=b.noakun
//		where a.tanggal like '".$periode."%'
//		and a.kodeorg='".$gudang."'
//                and a.nojurnal NOT LIKE '%CLSM%'
//                order by a.nojurnal 
//		";
//}

if(intval(str_replace('-','',$periode1))-intval(str_replace('-','',$periode))>4){
    exit('error: periode terlalu panjang');
}

if($gudang!=''){
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

if($gudang!=''){
    $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
        left join ".$dbname.".keu_5akun b
        on a.noakun=b.noakun
        where a.tanggal between '".$fromstart."' and '".$toend."'
        and a.kodeorg='".$gudang."'
        and a.nojurnal NOT LIKE '%CLSM%'
        and a.revisi<='".$revisi."'
        order by a.nojurnal 
        ";
}else{
    $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
        left join ".$dbname.".keu_5akun b
        on a.noakun=b.noakun
        where a.tanggal between '".$periode."-01' and LAST_DAY('".$periode1."-15')
        and a.kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' 
        and a.nojurnal NOT LIKE '%CLSM%'
        and a.revisi<='".$revisi."'
        and length(kodeorganisasi)=4)                    
        order by a.nojurnal 
        ";   
}


// kamus tahun tanam
$aresta="SELECT kodeorg, tahuntanam FROM ".$dbname.".setup_blok
    ";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $tahuntanam[$res['kodeorg']]=$res['tahuntanam'];
}   

//exit("Error:".$str);
//=================================================
$res=mysql_query($str);
$no=0;
if(mysql_num_rows($res)<1)
{
    echo"<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
}
else
{
    while($bar=mysql_fetch_object($res))
    {
        $no+=1;
        $debet=0;
        $kredit=0;
        if($bar->jumlah>0)
            $debet=$bar->jumlah;
        else
            $kredit=$bar->jumlah*-1;

        echo"<tr class=rowcontent>
            <td align=center  style='width:50px;'>".$no."</td>
            <td style='width:250px;'>".$bar->nojurnal."</td>
			<td style='width:250px;'>".$bar->noreferensi."</td>
            <td style='width:80px;' nowrap>".tanggalnormal($bar->tanggal)."</td>
            <td align=center style='width:60px;'>".$bar->kodeorg."</td>
            <td style='width:60px;'>".$bar->noakun."</td>
            <td style='width:200px;'>".$bar->namaakun."</td>
            <td style='width:240px;'>".$bar->keterangan."</td>
            <td align=right style='width:100px;'>".number_format($debet,2)."</td>
            <td align=right style='width:100px;'>".number_format($kredit,2)."</td>
            <td align=center style='width:200px;'>".$bar->noreferensi."</td>    
            <td align=center style='width:80px;'>".$bar->kodeblok."</td>
            <td align=center style='width:60px;'>".$tahuntanam[$bar->kodeblok]."</td>
            <td align=center style='width:30px;'>".$bar->revisi."</td>
            </tr>"; 	
        $tdebet+=$debet;
        $tkredit+=$kredit;
    }	
    echo"<tr class=rowtitle>
        <td align=center colspan=8>Total</td>
        <td align=right width=100>".number_format($tdebet,2)."</td>
        <td align=right width=100>".number_format($tkredit,2)."</td>
        <td align=center colspan=4></td>
        </tr>"; 		
} 	

?>