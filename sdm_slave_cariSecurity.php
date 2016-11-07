<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');


$nikKar=makeOption($dbname,'datakaryawan','karyawanid,nik');
$txtnama	=$_POST['txtnama'];



$str1="select induk,kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 order by kodeorganisasi";

$res1=mysql_query($str1);
$optorg='<option value=*></option>';
while($bar1=mysql_fetch_object($res1))
{
	$optorg.="<option value='".$bar1->induk."'>".$bar1->kodeorganisasi." [".$bar1->namaorganisasi."]</option>";
}
$str="select karyawanid,namakaryawan,lokasitugas from ".$dbname.".datakaryawan 
      where namakaryawan like '%".$txtnama."%' and nik like '%".$_POST['nik']."%'
	  and alokasi=0 and tipekaryawan<>0 
          and lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by namakaryawan";
$res=mysql_query($str);
echo"<table class=sortable border=0 cellspacing=1>
    <thead>
     <tr class=header>
        <td>".$_SESSION['lang']['nik']."</td>
        <td>".$_SESSION['lang']['namakaryawan']."</td>
            <td>".$_SESSION['lang']['lokasitugas']."</td>
            <td></td>
            <td>".$_SESSION['lang']['rotasike']."</td>
    </tr>
    <thead>
    <tbody>
    ";
while($bar=mysql_fetch_object($res))
{
      echo"<tr class=rowcontent>
            <td>".$nikKar[$bar->karyawanid]."</td>
                <td>".$bar->namakaryawan."</td>
                <td>".$bar->lokasitugas."</td>
                <td><img src=images/zoom.png class=resicon  title='".$_SESSION['lang']['view']."' onclick=\"previewKaryawan('".$bar->karyawanid."','".$bar->namakaryawan."',event);\"></td>
            <td><select id=tujuan".$bar->karyawanid." onchange=setKarTo('".$bar->karyawanid."')>".$optorg."</select> 
            </tr>";
}	 
echo"</tbody></table>"; 
?>