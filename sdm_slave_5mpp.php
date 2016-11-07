<?php
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');

    $kamar          =$_POST['kamar'];
    if($kamar==''){
        $kamar=$_GET['kamar'];
        $kunci=$_GET['kunci'];
    }else{
        $listtahun    =$_POST['listtahun'];
        $tahunbudget    =$_POST['tahunbudget'];
        $kodeorg        =$_POST['kodeorg'];
        $bagian         =$_POST['bagian'];
        $golongan       =$_POST['golongan'];
        $jabatan        =$_POST['jabatan'];
        $mingaji        =$_POST['mingaji'];
        $maxgaji        =$_POST['maxgaji'];
        $tanggalmasuk   =$_POST['tanggalmasuk'];
        $tanggalmasuk   =tanggalsystem($tanggalmasuk);

        $minumur        =$_POST['minumur'];
        $maxumur        =$_POST['maxumur'];
        $jeniskelamin   =$_POST['jeniskelamin'];
        $pendidikan     =$_POST['pendidikan'];
        $pengalaman     =$_POST['pengalaman'];
        $poh            =$_POST['poh'];
        $jumlah     =$_POST['jumlah'];
        $kunci     =$_POST['kunci'];
    }
	
//kamus jabatan
$str="select * from ".$dbname.".sdm_5jabatan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $jab[$bar->kodejabatan]=$bar->namajabatan;
}

//get golongan
$str="select * from ".$dbname.".sdm_5golongan order by kodegolongan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $gol[$bar->kodegolongan]=$bar->namagolongan;
}

if($kamar=='tahun')
{
    $str="select distinct tahunbudget from ".$dbname.".sdm_5mpp order by tahunbudget desc";
    $res=mysql_query($str);
    $opttahun="<option value=''>".$_SESSION['lang']['all']."</option>";
    while($bar=mysql_fetch_object($res))
    {
        $opttahun.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
    }
    echo $opttahun;
}    

if($kamar=='list')
{
$str="select * from ".$dbname.".sdm_5mpp
      where tahunbudget like '%".$listtahun."%'";
$res=mysql_query($str);
$no=1;
while($bar=mysql_fetch_object($res))
{
    echo"<tr class=rowcontent>
    <td>".$no."</td>
    <td>".$bar->tahunbudget."</td>
    <td>".$bar->kodeorg."</td>
    <td>".$bar->departement."</td>
    <td>".$gol[$bar->golongan]."</td>
    <td>".$jab[$bar->jabatan]."</td>
    <td align=right>".number_format($bar->startgaji,2,'.',',')."</td>
    <td align=right>".number_format($bar->endgaji,2,'.',',')."</td>
    <td>".tanggalnormal($bar->tanggalmasuk)."</td>
    <td align=right>".$bar->startumur."</td>
    <td align=right>".$bar->endumur."</td>
    <td>".$bar->jkelamin."</td>
    <td>".$bar->pendidikan."</td>
    <td align=right>".$bar->pengalaman."</td>
    <td>".$bar->poh."</td>
    <td align=right>".$bar->jumlah."</td>

    <td>
        <img src=images/application/application_edit.png class=resicon  title='edit' onclick=\"edit('".$bar->tahunbudget."','".$bar->kodeorg."','".$bar->departement."','".$bar->golongan."','".$bar->jabatan."','".$bar->startgaji."','".$bar->endgaji."','".tanggalnormal($bar->tanggalmasuk)."',
        '".$bar->startumur."','".$bar->endumur."','".$bar->jkelamin."','".$bar->pendidikan."','".$bar->pengalaman."','".$bar->poh."','".$bar->jumlah."','".$bar->kunci."');\">
    </td>
    <td>
        <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"del('".$bar->kunci."');\">
    </td>
    </tr>";	
    $no+=1;
}    
}  

if($kamar=='save')
{
//        tahunbudget=='' ||  kodeorg==''  || bagian=='' || golongan=='' || jabatan=='' || mingaji=='' || maxgaji=='' || tanggalmasuk=='' ||
//        minumur=='' ||  maxumur==''  || jeniskelamin=='' || pendidikan=='' || pengalaman=='' || poh=='' || jumlah==''
    $strx="insert into ".$dbname.".sdm_5mpp
        (tahunbudget,kodeorg,departement,
	golongan,jabatan,startgaji,
	endgaji,startumur,endumur,jkelamin,pendidikan,pengalaman,poh,jumlah,tanggalmasuk)
	values('".$tahunbudget."','".$kodeorg."','".$bagian."',
	'".$golongan."','".$jabatan."','".$mingaji."',
	'".$maxgaji."','".$minumur."','".$maxumur."','".$jeniskelamin."','".$pendidikan."','".$pengalaman."','".$poh."','".$jumlah."','".$tanggalmasuk."')";			   
  if(mysql_query($strx))
    {
    }	
  else
    {
        echo " Gagal,".addslashes(mysql_error($conn));
    }
}
 
if($kamar=='delete')
{
$strx="delete from ".$dbname.".sdm_5mpp 
    where kunci='".$kunci."'";
if(mysql_query($strx))
{
	
} else {
    echo " Gagal,".addslashes(mysql_error($conn));
}
}

if($kamar=='edit')
{
//        tahunbudget=='' ||  kodeorg==''  || bagian=='' || golongan=='' || jabatan=='' || mingaji=='' || maxgaji=='' || tanggalmasuk=='' ||
//        minumur=='' ||  maxumur==''  || jeniskelamin=='' || pendidikan=='' || pengalaman=='' || poh=='' || jumlah==''
//        (tahunbudget,kodeorg,departement,
//	golongan,jabatan,startgaji,
//	endgaji,startumur,endumur,jkelamin,pendidikan,pengalaman,poh,jumlah,tanggalmasuk)
    $strx="update ".$dbname.".sdm_5mpp set
        tahunbudget = '".$tahunbudget."',
        kodeorg = '".$kodeorg."',
        departement = '".$bagian."',
        golongan = '".$golongan."',
        jabatan = '".$jabatan."',
        startgaji = '".$mingaji."',
        endgaji = '".$maxgaji."',   
        tanggalmasuk = '".$tanggalmasuk."',
        startumur = '".$minumur."',
        endumur = '".$maxumur."',
        jkelamin = '".$jeniskelamin."',
        pendidikan = '".$pendidikan."',
        pengalaman = '".$pengalaman."',
        poh = '".$poh."',
        jumlah = '".$jumlah."'   
        where kunci = '".$kunci."'";
//    echo ("Error:".$strx);
    if(mysql_query($strx))
    {
    } else {
        echo " Gagal,".addslashes(mysql_error($conn));
    }
}
	
?>
