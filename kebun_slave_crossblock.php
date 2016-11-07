<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('lib/fpdf.php');

if(isset($_POST['proses']))
{
    $proses=$_POST['proses'];
}
else
{
    $proses=$_GET['proses'];
}
$id=$_POST['id'];
$tanggal=tanggalsystem($_POST['tanggal']);
$kodeorg=$_POST['kodeorg'];
$jabatan=$_POST['jabatan'];
$karyawan=$_POST['karyawan'];
$cek=$_POST['cek'];
$keterangan=$_POST['keterangan'];
$kelompok=$_POST['kelompok'];
$jumlahkegiatan=$_POST['jumlahkegiatan'];
for ($i = 1; $i <= $jumlahkegiatan; $i++) {
    ${'kegiatanvalue'.$i}=$_POST['kegiatanvalue'.$i];
    ${'kegiatanid'.$i}=$_POST['kegiatanid'.$i];
}

$kodeorg1=$_POST['kodeorg1'];
$periode1=$_POST['periode1'];

$optnamaorg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optnamajab=makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$optnamakar=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan',"lokasitugas like '".$_SESSION['empl']['lokasitugas']."%'
            and (tanggalkeluar>'".date('Y-m-d')."' or tanggalkeluar='0000-00-00')");
$optnamaqcid=makeOption($dbname, 'qc_5parameter', 'id,nama');
$optcek=Array(0=>'Cek',1=>'Ricek');

switch($proses)
{
case'excel':
    $jukol=0;
    $kodeorg1=$_GET['kodeorg1'];
    $periode1=$_GET['periode1'];
    $bgcolor="bgcolor=#DEDEDE align=center";
    $brdr=1;
    $sOrg2="select * from ".$dbname.".qc_5parameter
        where tipe = 'XBLOK'
        order by kelompok, id";
    $qOrg2=mysql_query($sOrg2) or die(mysql_error());
    while($rOrg2=mysql_fetch_assoc($qOrg2))
    {
        $jukol+=1;
        $parameterid[$rOrg2['id']]=$rOrg2['id'];
        $parameter[$rOrg2['id']]=$rOrg2['nama'];
        $parametersat[$rOrg2['id']]=$rOrg2['satuan'];
    }     
    $jukol+=6;
    $kojud=$jukol-2;
    
    $tab.="<table>
        <tr>
            <td colspan=".$jukol." align=left><b>RECAP CROSSBLOCK</b></td>
        </tr>
        <tr>
            <td colspan=2 align=left>".$_SESSION['lang']['kodeorg']."</td><td colspan=".$kojud.">: ".$optnamaorg[$kodeorg1]." </td>
        </tr>
        <tr>
            <td colspan=2 align=left>".$_SESSION['lang']['periode']."</td><td colspan=".$kojud.">: ".$periode1." </td>
        </tr>
        </table>";
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable>
        <thead>
            <tr class=rowheader>
            <td ".$bgcolor.">".$_SESSION['lang']['nomor']."</td>
            <td ".$bgcolor.">".$_SESSION['lang']['tanggal']."</td>
            <td ".$bgcolor.">".$_SESSION['lang']['diperiksa']."</td>
            <td ".$bgcolor.">Check/Re-Check</td>
            <td ".$bgcolor.">Afdeling/Block</td>";
            if(!empty($parameterid))foreach($parameterid as $paramz){
                $tab.="<td ".$bgcolor.">".$parameter[$paramz]." (".$parametersat[$paramz].")</td>";
                if($paramz=='49'){
                    $tab.="<td ".$bgcolor.">Angka Kerapatan Panen (%)</td>";
                }
                if($paramz=='51'){
                    $tab.="<td ".$bgcolor.">Rasio Buah Tinggal (%)</td>";
                    $tab.="<td ".$bgcolor.">Rasio Berondolan Tinggal (%)</td>";
                }
            }
            $tab.="<td ".$bgcolor.">".$_SESSION['lang']['keterangan']."</td>
            </tr>
        </thead>
        <tbody>
        ";
            
    $sData="select a.* from ".$dbname.".kebun_crossblock_dt a 
        left join ".$dbname.".kebun_crossblock_ht b on a.id=b.id 
        where b.kodeorg like '".$kodeorg1."%' and b.tanggal like '".$periode1."%'
        ";
    $qData=mysql_query($sData) or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
        $dataparameter[$rData['id']][$rData['qcid']]=$rData['jumlah'];
    }
            
    $no=0;
    $sData="select * from ".$dbname.".kebun_crossblock_ht 
        where kodeorg like '".$kodeorg1."%' and tanggal like '".$periode1."%'
        order by tanggal desc, kodeorg";
    $qData=mysql_query($sData) or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
        $no+=1;
        $tab.="<tr class=rowcontent>";
        $tab.="<td align=right>".$no."</td>";
        $tab.="<td>".tanggalnormal($rData['tanggal'])."</td>";
        $tab.="<td>".$rData['jabatan']." ".$optnamakar[$rData['karyawanid']]."</td>";
        $tab.="<td>".$optcek[$rData['cek']]."</td>";
        $tab.="<td>".$optnamaorg[$rData['kodeorg']]."</td>";
        if(!empty($parameterid))foreach($parameterid as $paramz){
            $tote[$paramz]+=$dataparameter[$rData['id']][$paramz];
            $tab.="<td align=right>".number_format($dataparameter[$rData['id']][$paramz])."</td>";
            if($paramz=='49'){
                @$angkakerapatanpanen=$dataparameter[$rData['id']]['48']/$dataparameter[$rData['id']]['47']*100;
                $tab.="<td align=right>".number_format($angkakerapatanpanen,2)."</td>";
            }
            if($paramz=='51'){
                @$angkakerapatanpanen=$dataparameter[$rData['id']]['50']/($dataparameter[$rData['id']]['47']/$dataparameter[$rData['id']]['46']);
                $tab.="<td align=right>".number_format($angkakerapatanpanen,2)."</td>";
                @$angkakerapatanpanen=$dataparameter[$rData['id']]['51']/($dataparameter[$rData['id']]['47']/$dataparameter[$rData['id']]['46']);
                $tab.="<td align=right>".number_format($angkakerapatanpanen,2)."</td>";
            }
        }
        $tab.="<td>".$rData['keterangan']."</td>";
        $tab.="</tr>";
    }
    $tab.="<tr>";
    $tab.="<td ".$bgcolor." align=center colspan=5>Total</td>";
    if(!empty($parameterid))foreach($parameterid as $paramz){
        $tab.="<td ".$bgcolor." align=right>".number_format($tote[$paramz])."</td>";
        if($paramz=='49'){
            @$angkakerapatanpanen=$tote['48']/$tote['47']*100;
            $tab.="<td ".$bgcolor." align=right>".number_format($angkakerapatanpanen,2)."</td>";
        }
        if($paramz=='51'){
            @$angkakerapatanpanen=$tote['50']/($tote['47']/$tote['46']);
            $tab.="<td ".$bgcolor." align=right>".number_format($angkakerapatanpanen,2)."</td>";
            @$angkakerapatanpanen=$tote['51']/($tote['47']/$tote['46']);
            $tab.="<td ".$bgcolor." align=right>".number_format($angkakerapatanpanen,2)."</td>";
        }
    }
    $tab.="<td ".$bgcolor."></td>";
    $tab.="</tr>";

    $tab.="</tbody></table><br>";    
    $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
    $dte=date("Hms");
    $nop_="laporancrossblock_".$kodeorg1.$periode1."_".$dte;
    $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
    gzwrite($gztralala, $tab);
    gzclose($gztralala);
    echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";	
break;    
case'preview':
    $sOrg2="select * from ".$dbname.".qc_5parameter
        where tipe = 'XBLOK'
        order by kelompok, id";
    $qOrg2=mysql_query($sOrg2) or die(mysql_error());
    while($rOrg2=mysql_fetch_assoc($qOrg2))
    {
        $parameterid[$rOrg2['id']]=$rOrg2['id'];
        $parameter[$rOrg2['id']]=$rOrg2['nama'];
        $parametersat[$rOrg2['id']]=$rOrg2['satuan'];
    }     
    
    $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>
        <thead>
            <tr class=rowheader>
            <td>".$_SESSION['lang']['nomor']."</td>
            <td>".$_SESSION['lang']['tanggal']."</td>
            <td>".$_SESSION['lang']['diperiksa']."</td>
            <td>Check/Re-Check</td>
            <td>Afdeling/Block</td>";
            if(!empty($parameterid))foreach($parameterid as $paramz){
                $tab.="<td>".$parameter[$paramz]." (".$parametersat[$paramz].")</td>";
                if($paramz=='49'){
                    $tab.="<td>Angka Kerapatan Panen (%)</td>";
                }
                if($paramz=='51'){
                    $tab.="<td>Rasio Buah Tinggal (%)</td>";
                    $tab.="<td>Rasio Berondolan Tinggal (%)</td>";
                }
            }
            $tab.="<td>".$_SESSION['lang']['keterangan']."</td>
            </tr>
        </thead>
        <tbody>
        ";
            
    $sData="select a.* from ".$dbname.".kebun_crossblock_dt a 
        left join ".$dbname.".kebun_crossblock_ht b on a.id=b.id 
        where b.kodeorg like '".$kodeorg1."%' and b.tanggal like '".$periode1."%'
        ";
    $qData=mysql_query($sData) or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
        $dataparameter[$rData['id']][$rData['qcid']]=$rData['jumlah'];
    }
            
    $no=0;
    $sData="select * from ".$dbname.".kebun_crossblock_ht 
        where kodeorg like '".$kodeorg1."%' and tanggal like '".$periode1."%'
        order by tanggal desc, kodeorg";
    $qData=mysql_query($sData) or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
        $no+=1;
        $tab.="<tr class=rowcontent>";
        $tab.="<td align=right>".$no."</td>";
        $tab.="<td>".tanggalnormal($rData['tanggal'])."</td>";
        $tab.="<td>".$rData['jabatan']." ".$optnamakar[$rData['karyawanid']]."</td>";
        $tab.="<td>".$optcek[$rData['cek']]."</td>";
        $tab.="<td>".$optnamaorg[$rData['kodeorg']]."</td>";
        if(!empty($parameterid))foreach($parameterid as $paramz){
            $tote[$paramz]+=$dataparameter[$rData['id']][$paramz];
            $tab.="<td align=right>".number_format($dataparameter[$rData['id']][$paramz])."</td>";
            if($paramz=='49'){
                @$angkakerapatanpanen=$dataparameter[$rData['id']]['48']/$dataparameter[$rData['id']]['47']*100;
                $tab.="<td align=right>".number_format($angkakerapatanpanen,2)."</td>";
            }
            if($paramz=='51'){
                @$angkakerapatanpanen=$dataparameter[$rData['id']]['50']/($dataparameter[$rData['id']]['47']/$dataparameter[$rData['id']]['46']);
                $tab.="<td align=right>".number_format($angkakerapatanpanen,2)."</td>";
                @$angkakerapatanpanen=$dataparameter[$rData['id']]['51']/($dataparameter[$rData['id']]['47']/$dataparameter[$rData['id']]['46']);
                $tab.="<td align=right>".number_format($angkakerapatanpanen,2)."</td>";
            }
        }
        $tab.="<td>".$rData['keterangan']."</td>";
        $tab.="</tr>";
    }
    $tab.="<tr>";
    $tab.="<td align=center colspan=5>Total</td>";
    if(!empty($parameterid))foreach($parameterid as $paramz){
        $tab.="<td align=right>".number_format($tote[$paramz])."</td>";
        if($paramz=='49'){
            @$angkakerapatanpanen=$tote['48']/$tote['47']*100;
            $tab.="<td align=right>".number_format($angkakerapatanpanen,2)."</td>";
        }
        if($paramz=='51'){
            @$angkakerapatanpanen=$tote['50']/($tote['47']/$tote['46']);
            $tab.="<td align=right>".number_format($angkakerapatanpanen,2)."</td>";
            @$angkakerapatanpanen=$tote['51']/($tote['47']/$tote['46']);
            $tab.="<td align=right>".number_format($angkakerapatanpanen,2)."</td>";
        }
    }
    $tab.="<td></td>";
    $tab.="</tr>";
    $tab.="</tbody></table>";    
    echo $tab;
break;    
case'getperiode':
//    $optperiode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $sOrg2="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_crossblock_ht 
        order by tanggal desc";
    $qOrg2=mysql_query($sOrg2) or die(mysql_error());
    while($rOrg2=mysql_fetch_assoc($qOrg2))
    {
        $optperiode.="<option value=".$rOrg2['periode'].">".$rOrg2['periode']."</option>";
    }     
    echo $optperiode;
break;
case'openkegiatan':
    $sOrg2="select * from ".$dbname.".qc_5parameter
        where kelompok = '".$kelompok."'
        order by id";
    $sData="select * from ".$dbname.".kebun_crossblock_dt 
        where id = '".$id."'";
    $qData=mysql_query($sData) or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
        $nilainya[$rData['qcid']]=$rData['jumlah'];      
    }

    $no=0;
    $tab.="<table cellspacing=1 border=0>";
    $qOrg2=mysql_query($sOrg2) or die(mysql_error());
    while($rOrg2=mysql_fetch_assoc($qOrg2))
    {
        $no+=1;
        $kegiatanid='kegiatanid'.$no;
        $kegiatanvalue='kegiatanvalue'.$no;
        $veliu=0;
        if($nilainya[$rOrg2['id']]!='')$veliu=$nilainya[$rOrg2['id']];
        $tab.="<tr>
            <td style=width:200px;><input type='hidden' id='".$kegiatanid."' value='".$rOrg2['id']."'/>".$rOrg2['nama']."</td>
            <td>:</td>
            <td><input type='text' class='myinputtextnumber' style='width:150px;' id='".$kegiatanvalue."' onkeypress='return angka_doang(event)' value='".$veliu."'/> ".$rOrg2['satuan']."</td>
        </tr>";
    }     
    $tab.="</table>";
    $tab.="####".$no;
    echo $tab;
break;
case'getkaryawan':
    $optkaryawan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $sOrg2="select karyawanid, namakaryawan from ".$dbname.".datakaryawan
        where kodejabatan = '".$jabatan."' and lokasitugas like '".$_SESSION['empl']['lokasitugas']."%'
            and (tanggalkeluar>'".date('Y-m-d')."' or tanggalkeluar='0000-00-00')
        order by namakaryawan
        ";
    $qOrg2=mysql_query($sOrg2) or die(mysql_error());
    while($rOrg2=mysql_fetch_assoc($qOrg2))
    {
        $optkaryawan.="<option value=".$rOrg2['karyawanid'].">".$rOrg2['namakaryawan']."</option>";
    }     
    echo $optkaryawan;
break;
case'savedata0':
    $sInsert="SELECT Auto_increment as nextid
        FROM information_schema.tables 
        WHERE table_name='kebun_crossblock_ht'
        AND table_schema = '".$dbname."'";
    $qOrg2=mysql_query($sInsert) or die(mysql_error());
    while($rOrg2=mysql_fetch_assoc($qOrg2))
    {
        $nextid=$rOrg2['nextid'];
    }     

    $sInsert="insert into ".$dbname.".kebun_crossblock_ht (tanggal, kodeorg, jabatan, karyawanid, cek, keterangan, updateby, lastupdate) 
    values('".$tanggal."','".$kodeorg."','".$jabatan."','".$karyawan."','".$cek."','".$keterangan."','".$_SESSION['standard']['userid']."', CURRENT_TIMESTAMP)";
    if(!mysql_query($sInsert))
    {
        echo "DB Error : ".$sInsert."\n".mysql_error($conn);
    }
    
    for ($i = 1; $i <= $jumlahkegiatan; $i++) {
        $kegiatanid=${'kegiatanid'.$i};
        $kegiatanvalue=${'kegiatanvalue'.$i};
        $sInsert="insert into ".$dbname.".kebun_crossblock_dt (id, qcid, jumlah) 
        values('".$nextid."','".$kegiatanid."','".$kegiatanvalue."')";
        if(!mysql_query($sInsert))
        {
            echo "DB Error : ".$sInsert."\n".mysql_error($conn);
        }
    }
    
break; 
case'editdata0':
    $sInsert="update ".$dbname.".kebun_crossblock_ht set tanggal = '".$tanggal."', kodeorg = '".$kodeorg."', jabatan = '".$jabatan."', karyawanid = '".$karyawan."', 
        cek = '".$cek."', keterangan = '".$keterangan."', updateby = '".$_SESSION['standard']['userid']."', lastupdate = CURRENT_TIMESTAMP
        where id = '".$id."'";
    if(!mysql_query($sInsert))
    {
        echo "DB Error : ".$sInsert."\n".mysql_error($conn);
    }
    
    $adadata=0;
    for ($i = 1; $i <= $jumlahkegiatan; $i++) {
        $kegiatanid=${'kegiatanid'.$i};
        $kegiatanvalue=${'kegiatanvalue'.$i};
        $sData="select * from ".$dbname.".kebun_crossblock_dt 
            where id = '".$id."' and qcid = '".$kegiatanid."'";
        $qData=mysql_query($sData) or die(mysql_error());
        while($rData=mysql_fetch_assoc($qData))
        {
            $adadata=1;            
        }
    }    
    
    if($adadata==1){
        for ($i = 1; $i <= $jumlahkegiatan; $i++) {
            $kegiatanid=${'kegiatanid'.$i};
            $kegiatanvalue=${'kegiatanvalue'.$i};
            $sInsert="update ".$dbname.".kebun_crossblock_dt set id = '".$id."', jumlah = '".$kegiatanvalue."'
                where id = '".$id."' and qcid = '".$kegiatanid."'";
            if(!mysql_query($sInsert))
            {
                echo "DB Error : ".$sInsert."\n".mysql_error($conn);
            }
        }
    }else{
        for ($i = 1; $i <= $jumlahkegiatan; $i++) {
            $kegiatanid=${'kegiatanid'.$i};
            $kegiatanvalue=${'kegiatanvalue'.$i};
            $sInsert="insert into ".$dbname.".kebun_crossblock_dt (id, qcid, jumlah) 
            values('".$id."','".$kegiatanid."','".$kegiatanvalue."')";
            if(!mysql_query($sInsert))
            {
                echo "DB Error : ".$sInsert."\n".mysql_error($conn);
            }
        }        
    }
    
break; 
case'loaddata0':
    $limit=50;
    $page=0;
    if(isset($_POST['page']))
    {
        $page=$_POST['page'];
        if($page<0)
        $page=0;
    }
    $offset=$page*$limit;

    $sql2="select * from ".$dbname.".kebun_crossblock_ht where kodeorg like '".$_SESSION['empl']['lokasitugas']."%'";
    $query2=mysql_query($sql2) or die(mysql_error());
    $jlhbrs=mysql_num_rows($query2);
    if($jlhbrs!=0)
    {
        $no=0;
        $sData="select * from ".$dbname.".kebun_crossblock_ht where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by tanggal desc, kodeorg limit ".$offset.",".$limit." ";
        $qData=mysql_query($sData) or die(mysql_error());
        while($rData=mysql_fetch_assoc($qData))
        {
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td align=right>".$no."</td>";
            $tab.="<td>".tanggalnormal($rData['tanggal'])."</td>";
            $tab.="<td>".$optnamaorg[$rData['kodeorg']]."</td>";
            $tab.="<td>".$optnamakar[$rData['karyawanid']]."</td>";
            $tab.="<td>".$rData['jabatan']."</td>";
            $tab.="<td>".$optcek[$rData['cek']]."</td>";
            $tab.="<td>".$rData['keterangan']."</td>";
            $tab.="<td><img id='detailedit' &nbsp; style='cursor:pointer;' title='Edit ".$rData['kodeorg']."' class=zImgBtn 
                onclick=\"filldata0('".$rData['id']."','".tanggalnormal($rData['tanggal'])."','".$rData['kodeorg']."','".$rData['jabatan']."','".$rData['karyawanid']."','".$rData['cek']."','".$rData['keterangan']."')\" src='images/application/application_edit.png'/>";
            $tab.="&nbsp;<img id='detaildel' style='cursor:pointer;' title='Delete ".$rData['kodeorg']."' class=zImgBtn 
                onclick=\"deldata0('".$rData['id']."')\" src='images/application/application_delete.png'/>";
            $tab.="</tr>";
        }
        $tab.="
        <tr class=rowheader><td colspan=10 align=center>
        ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
        <button class=mybutton onclick=exploredata(".($page-1).");>".$_SESSION['lang']['pref']."</button>
        <button class=mybutton onclick=exploredata(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
        </td>
        </tr>";
    }
    else
    {
        $tab.="<tr class=rowcontent><td colspan=10>".$_SESSION['lang']['dataempty']."</td></tr>";
    }
    echo $tab;
break;
case'deldata0': // DELETE FROM `owlv2`.`kebun_crossblock` WHERE `kebun_crossblock`.`id` = 2
    $sInsert="delete from ".$dbname.".kebun_crossblock_ht where id = '".$id."'";
    if(!mysql_query($sInsert))
    {
        echo "DB Error : ".$sInsert."\n".mysql_error($conn);
    }
    
    $sInsert="delete from ".$dbname.".kebun_crossblock_dt where id = '".$id."'";
    if(!mysql_query($sInsert))
    {
        echo "DB Error : ".$sInsert."\n".mysql_error($conn);
    }
break; 
default:
break;
}
    
?>