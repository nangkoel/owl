<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['izinkntor']."/".$_SESSION['lang']['cuti']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script>
 jdl_ats_0='<?php echo $_SESSION['lang']['find']?>';
// alert(jdl_ats_0);
 jdl_ats_1='<?php echo $_SESSION['lang']['findBrg']?>';
 content_0='<fieldset><legend><?php echo $_SESSION['lang']['findnoBrg']?></legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>';

nmSaveHeader='';
nmCancelHeader='';
nmDetialDone='<?php echo $_SESSION['lang']['done']?>';
nmDetailCancel='<?php echo $_SESSION['lang']['cancel']?>';

</script>
<script type="application/javascript" src="js/sdm_ijin_meninggalkan_kantor.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />
<div id="headher">
<?php

for($i=0;$i<24;)
{
        if(strlen($i)<2)
        {
                $i="0".$i;
        }
   $jm.="<option value=".$i.">".$i."</option>";
   $i++;
}
for($i=0;$i<60;)
{
        if(strlen($i)<2)
        {
                $i="0".$i;
        }
   $mnt.="<option value=".$i.">".$i."</option>";
   $i++;
}

//print_r($_SESSION['empl']);

$whrKry="karyawanid='".$_SESSION['standard']['userid']."'";

$keKdGol=makeOption($dbname,'datakaryawan','karyawanid,kodegolongan',$whrKry);
$kdGol=$keKdGol[$_SESSION['standard']['userid']];
//$kdGolatas=$kdGol+1;

//ini di note untuk jika kodegolongan = 2a contoh pak sulaiman
/*$kdGolatas=$kdGol;
if(strlen($kdGolatas)==2)
{
	//exit("Error:MASUK");
	$x=substr($kdGolatas,0,1);
	$y=substr($kdGolatas,1,1);
	$z=$x+1;
}

$kdGolatas=$z.$y;
echo $kdGolatas;*/
//echo $kdGolatas;

if(substr($kdGol,0,1)==2)
{
    $whrKdgol="and kodegolongan in ('2A','2B','2C','3A')";
}
else  if(substr($kdGol,0,1)==4)
{
    $x=substr($kdGol,0,1)+1;
    $y=substr($kdGol,1,1);  
    $kdGol=$x.$y;
     $whrKdgol="and kodegolongan<='".$kdGol."' or left(kodegolongan,1)='".substr($kdGol,0,1)."'";
}
else
{
    $whrKdgol="and kodegolongan<='".$kdGol."' or left(kodegolongan,1)='".substr($kdGol,0,1)."'";
}



$whrKdgol="and kodegolongan<='".$kdGol."' or left(kodegolongan,1)='".substr($kdGol,0,1)."'";

//5a
//4a

//print_r($kdGol);
$optGanti="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    $x="select karyawanid,lokasitugas,namakaryawan,nik from ".$dbname.".datakaryawan where lokasitugas like '%HO'
        and tanggalkeluar='0000-00-00' and karyawanid!='".$_SESSION['standard']['userid']."' order by namakaryawan asc";
} else {
    $x="select karyawanid,lokasitugas,namakaryawan,nik from ".$dbname.".datakaryawan where lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment
        where regional='".$_SESSION['empl']['regional']."') ".$whrKdgol." and tanggalkeluar='0000-00-00' and 
        karyawanid!='".$_SESSION['standard']['userid']."' order by namakaryawan asc";
}
//echo $x;
$y=mysql_query($x) or die(mysql_error());
while($z=mysql_fetch_assoc($y))
{
        $optGanti.="<option value=".$z['karyawanid'].">".$z['namakaryawan']." [".$z['nik']."] [".$z['lokasitugas']."]</option>";
}

#HRD
#Modifikasi filter hanya utk Manager HRD -- by Cosa
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    if ($_SESSION['empl']['bagian']=='HRD' && $_SESSION['empl']['kodejabatan']=='151'){
        $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
              where tipekaryawan=0 and bagian='HRD' and tanggalkeluar='0000-00-00' and 
              karyawanid <>".$_SESSION['standard']['userid']. " and kodejabatan in (21,33) order by namakaryawan";
    } else {
        $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
              where tipekaryawan=0 and bagian='HRD' and tanggalkeluar='0000-00-00' and 
              lokasitugas like '%HO' and kodejabatan=151 order by namakaryawan";
    }
} else {
    $str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
          where tipekaryawan=0 and bagian='HRD' and tanggalkeluar='0000-00-00' and 
          karyawanid <>".$_SESSION['standard']['userid']. " and kodejabatan in (21,33) order by namakaryawan";
}
$res=mysql_query($str);
$optKarHrd="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optKarHrd.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
}

$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select karyawanid, namakaryawan,nik from ".$dbname.".datakaryawan where tipekaryawan='0' and karyawanid!='".$_SESSION['standard']['userid']."' order by namakaryawan asc";
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
        $optKary.="<option value=".$rOrg['karyawanid'].">".$rOrg['namakaryawan']." [".$rOrg['nik']."]</option>";
}

#atasan
/*$optKarat="<option value=''></option>";
$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
      where tipekaryawan=0 and kodegolongan>='4B' and kodegolongan <'6' and karyawanid <>".$_SESSION['standard']['userid']. " order by namakaryawan";
$res=mysql_query($str);
$optKar2="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optKarat.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
}*/

#atasan dari atasan
$str="select namakaryawan,karyawanid,nik from ".$dbname.".datakaryawan
      where tipekaryawan=0 and tanggalkeluar='0000-00-00' and kodegolongan>='4B' and 
      karyawanid <>".$_SESSION['standard']['userid']. " order by namakaryawan";
$res=mysql_query($str);
$optKar2="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optKar2.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan." [".$bar->nik."]</option>";
}	


$optJenis="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $arragama=getEnum($dbname,'sdm_ijin','jenisijin');
                foreach($arragama as $kei=>$fal)
                {
                    if($_SESSION['language']=='ID'){
                        $optJenis.="<option value='".$kei."'>".$fal."</option>";
                    }else{
                        switch($fal){
                            case 'TERLAMBAT':
                                $fal='Late for work';
                                break;
                            case 'KELUAR':
                                $fal='Out of Office';
                                break;         
                            case 'PULANGAWAL':
                                $fal='Home early';
                                break;     
                            case 'IJINLAIN':
                                $fal='Other purposes';
                                break;   
                            case 'CUTI':
                                $fal='Leave';
                                break;       
                            case 'MELAHIRKAN':
                                $fal='Maternity';
                                break;           
                            default:
                                $fal='Wedding, Circumcision or Graduation';
                                break;                              
                        }
                        $optJenis.="<option value='".$kei."'>".$fal."</option>";       
                    }
                }  

//ambil cuti ybs
// Ambil tanggal masuk ybs

$stc="select distinct periodecuti from ".$dbname.".sdm_cutiht where karyawanid=".$_SESSION['standard']['userid']." order by periodecuti";
$rec=mysql_query($stc);
//$tglmasup='';
//$hrini=date('md');#default
while($bac=mysql_fetch_object($rec))
{
    $optPeriodec.="<option value=".$bac->periodecuti.">".$bac->periodecuti."</option>";
    //$tglmasup=str_replace("-","",$bac->tanggalmasuk);#replace with data karyawan
}
if($tglmasup>$hrini){
    $tahunplafon=(date('Y')-1);
} else {
    $tahunplafon=date('Y');
}
$tahunplafon = fetchData($stc);
//#penguncian agar cuti yang sudah hangus tidak dapat diambil
//$optPeriodec="<option value=".$tahunplafon.">".$tahunplafon."</option>";
//$optPeriodec.="<option value=".($tahunplafon+1).">".($tahunplafon+1)."</option>"; 

$strf="select sisa from ".$dbname.".sdm_cutiht where karyawanid=".$_SESSION['standard']['userid']." 
       and periodecuti=".$tahunplafon[0]['periodecuti'];
$res=mysql_query($strf);

$sisa='';
while($barf=mysql_fetch_object($res))
{
    $sisa=$barf->sisa;
}
if($sisa=='')
    $sisa=0;

?>
<fieldset style='float:left;'>
<legend><?php echo $_SESSION['lang']['form']?></legend>
<table cellspacing="1" border="0">

<tr>
<td><?php echo $_SESSION['lang']['tanggal']?></td>
<td>:</td>
<td><input type='text' class='myinputtext' id='tglIzin' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style="width:150px;" value='<?php echo date('d-m-Y')?>'/></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['jenisijin']?></td>
<td>:</td>
<td><select id="jnsIjin" name="jnsIjin" style="width:150px"><?php echo $optJenis;?></select></td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['pengabdian']." ".$_SESSION['lang']['tahun'];?></td>
<td>:</td>
<td><select id="periodec"  style="width:150px" onchange="loadSisaCuti(this.options[this.selectedIndex].value,<?echo $_SESSION['standard']['userid']?>)"><?php echo $optPeriodec;?></select></td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['dari']."  ".$_SESSION['lang']['tanggal']." & ".$_SESSION['lang']['jam']?></td>
<td>:</td>
<td><input type='text' class='myinputtext' id='tglAwal' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style="width:150px;" /><select id="jam1"><?php echo $jm;?></select>:<select id="mnt1"><?php echo $mnt;?></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['sampai']."  ".$_SESSION['lang']['tanggal']." & ".$_SESSION['lang']['jam']?></td>
<td>:</td>
<td><input type='text' class='myinputtext' id='tglEnd' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style="width:150px;" /><select id="jam2"><?php echo $jm;?></select>:<select id="mnt2"><?php echo $mnt;?></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['jumlahhk']." ".$_SESSION['lang']['diambil'];?></td>
<td>:</td>
<td>
<input type="text" class="myinputtext" id="jumlahhk" name="keperluan" onkeypress="return angka_doang(event);" maxlength="5" value="0"/><?php echo $_SESSION['lang']['hari']; ?> -
(<?echo $_SESSION['lang']['sisa']; ?>:<span id="sis"><?echo $sisa." ".$_SESSION['lang']['hari']; ?></span>)</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['keperluan']?></td>
<td>:</td>
<td>
<input type="text" class="myinputtext" id="keperluan" name="keperluan" onkeypress="return tanpa_kutip(event);" maxlength="30" style="width:150px;" /></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['keterangan']?></td>
<td>:</td>
<td>
<textarea id='ket'  onkeypress="return tanpa_kutip(event);"></textarea>
</td>
</tr>

<tr>
<td>Pengganti Tugas</td>
<td>:</td>
<td>
    <select id="ganti" style="width:150px"><?php echo $optGanti; ?></select>
</td>
</tr>


<tr>
<td><?php echo $_SESSION['lang']['atasan']?></td>
<td>:</td>
<td>
    <select id="atasan" style="width:150px"><?php echo $optKar2; ?></select>
</td>
</tr>

<tr  style="display:none">
<td><?php echo $_SESSION['lang']['atasan']?> <?php echo $_SESSION['lang']['dari']?> <?php echo $_SESSION['lang']['atasan']?></td>
<td>:</td>
<td>
    <select id="atasan2" style="width:150px"><?php echo $optKar2; ?></select>
</td>
</tr>


<tr style="display:none">
<td><?php echo $_SESSION['lang']['hrd']?></td>
<td>:</td>
<td>
    <select id="hrd" style="width:150px"><?php echo $optKarHrd; ?></select>
</td>
</tr>

<tr>
<td colspan="3" id="tmblHeader">
    <button class=mybutton id=dtlForm onclick=saveForm()><?php echo $_SESSION['lang']['save']?></button>
    <button class=mybutton id=cancelForm onclick=cancelForm()><?php echo $_SESSION['lang']['cancel']?></button>
</td>
</tr>
</table><input type="hidden" id="atsSblm" name="atsSblm" />
</fieldset>

<?php
CLOSE_BOX();
?>
</div>
<div id="list_ganti">
<?php OPEN_BOX()?>
    <div id="action_list">

</div>
<fieldset style='float:left;'>
<legend><?php echo $_SESSION['lang']['list']?></legend>

<table cellspacing="1" border="0" class="sortable">
<thead>
<tr class="rowheader">
<td rowspan=2 align=center>No.</td>
<td rowspan=2 align=center><?php echo $_SESSION['lang']['tanggal']?></td>
<td rowspan=2 align=center><?php echo $_SESSION['lang']['keperluan']?></td>
<td rowspan=2 align=center><?php echo $_SESSION['lang']['jenisijin']?></td>
<td rowspan=2 align=center><?php echo $_SESSION['lang']['persetujuan']?></td>
<td rowspan=2 align=center><?php echo $_SESSION['lang']['atasan'].' '.$_SESSION['lang']['dari'].' '.$_SESSION['lang']['atasan']?></td>
<td colspan=3 align=center><?php echo $_SESSION['lang']['approval_status']?></td>
<td rowspan=2 align=center><?php echo $_SESSION['lang']['dari']."  ".$_SESSION['lang']['jam']?></td>
<td rowspan=2 align=center><?php echo $_SESSION['lang']['tglcutisampai']."  ".$_SESSION['lang']['jam']?></td>
<td rowspan=2 align=center><?php echo $_SESSION['lang']['ganti']?></td>
<td rowspan=2 align=center>Action</td>
</tr>
<tr class="rowheader">
<td align=center><?php echo $_SESSION['lang']['atasan']?></td>
<td align=center><?php echo $_SESSION['lang']['atasan'].' '.$_SESSION['lang']['dari'].' '.$_SESSION['lang']['atasan']?></td>
<td align=center><?php echo $_SESSION['lang']['hrd']?></td>
</tr>
</thead>
<tbody id="contain">
<script>loadNData()</script>
</tbody>
</table>
</fieldset>
<?php CLOSE_BOX()?>
</div>

<?php 
echo close_body();
?>