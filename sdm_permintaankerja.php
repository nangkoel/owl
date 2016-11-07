<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/sdm_permintaankerja.js'></script> 
<link rel=stylesheet type='text/css' href='style/zTable.css'>
<?php
$arr="##nmlowongan##notransaksi##kodeorg##penempatan##departemen##tanggal##tgldibutuhkan##kotapenempatan##pendidikan##jurusan";
$arr.="##pengalaman##kompetensi##deskpekerjaan##maxumur##persetujuan1##persetujuan2##persetujuanhrd##proses##jmlhPersoanl";
include('master_mainMenu.php');
OPEN_BOX();
$optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sorg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
   where char_length(kodeorganisasi)='4' order by namaorganisasi asc";
$qorg=mysql_query($sorg) or die(mysql_error($conn));
while($rorg=mysql_fetch_assoc($qorg)){
$optorg.="<option value='".$rorg['kodeorganisasi']."'>".$rorg['namaorganisasi']."</option>";
}
#pendidikan option
$optorg2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sorg2="select distinct  idpendidikan,kelompok from ".$dbname.".sdm_5pendidikan  order by idpendidikan asc";
$qorg2=mysql_query($sorg2) or die(mysql_error($conn));
while($rorg2=mysql_fetch_assoc($qorg2)){
$optorg2.="<option value='".$rorg2['idpendidikan']."'>".$rorg2['kelompok']."</option>";
}
#pendidikan option end

#departement option 
$optorg3="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sorg3="select distinct  kode,nama from ".$dbname.".sdm_5departemen  order by kode asc";
$qorg3=mysql_query($sorg3) or die(mysql_error($conn));
while($rorg3=mysql_fetch_assoc($qorg3)){
$optorg3.="<option value='".$rorg3['kode']."'>".$rorg3['nama']."</option>";
}
#departement option end

#karyawan option 
$optorgd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sorgd="select distinct  karyawanid,namakaryawan from ".$dbname.".datakaryawan 
        where tipekaryawan=0  and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") and karyawanid!='".$_SESSION['standard']['userid']."'
        order by namakaryawan asc";
$qorgd=mysql_query($sorgd) or die(mysql_error($conn));
while($rorgd=mysql_fetch_assoc($qorgd)){
    $optorgd.="<option value='".$rorgd['karyawanid']."'>".$rorgd['namakaryawan']."</option>";
}
#karyawan option end

#karyawan hrd option 
$optorghr="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sorghr="select distinct  karyawanid,namakaryawan from ".$dbname.".datakaryawan where 
tipekaryawan=0 and bagian='HRD' and tanggalkeluar='0000-00-00'  and karyawanid!='".$_SESSION['standard']['userid']."'
order by namakaryawan asc";
$qorghr=mysql_query($sorghr) or die(mysql_error($conn));
while($rorghr=mysql_fetch_assoc($qorghr)){
$optorghr.="<option value='".$rorghr['karyawanid']."'>".$rorghr['namakaryawan']."</option>";
}
#karyawan hrd option end
echo"<fieldset style='float:left'><legend><b>".$_SESSION['lang']['sdmpermintaankerja']."</b></legend>
<table cellpadding=1 cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['namalowongan']."</td><td>
    <input type=text class=myinputtext onkeypress='return tanpa_kutip(event)' id='nmlowongan' style='width:150px;'  maxlength=45 /> </td>
    <td>".$_SESSION['lang']['unit']." Peminta</td><td><select style='width:150px;' id='kodeorg'>".$optorg."</select></td>
    <td>".$_SESSION['lang']['unit']." ".$_SESSION['lang']['penempatan']."</td><td><select style='width:150px;'  id='penempatan'>".$optorg."</select> </td>
</tr> 
<tr>
    <td>".$_SESSION['lang']['departemen']."</td><td><select style='width:150px;'  style='width:150px;'  id='departemen'>".$optorg3."</select></td>
    <td>".$_SESSION['lang']['tanggal']."</td><td><input type=text class='myinputtext'   style='width:150px;' id='tanggal' value='".date('d-m-Y')."' disabled /></td>
    <td>".$_SESSION['lang']['tgldibutuhkan']."</td><td><input type=text class='myinputtext'  onmousemove=setCalendar(this.id) onkeypress=return false;   style='width:150px;' id='tgldibutuhkan' /></td>
</tr>
<tr>         
    <td>".$_SESSION['lang']['kotapenempatan']."</td><td><input type=text class='myinputtext' onkeypress='return tanpa_kutip(event)' style='width:150px;' id='kotapenempatan' /></td>
    <td>".$_SESSION['lang']['pendidikan']."</td><td><select style='width:150px;' id='pendidikan'>".$optorg2."</select></td>
    <td>".$_SESSION['lang']['jurusan']."</td><td><input type=text class='myinputtext' onkeypress='return tanpa_kutip(event)' style='width:150px;' id='jurusan' /></td>
</tr>
<tr>         
    <td>".$_SESSION['lang']['pengalamankerja']."</td><td><input type=text class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:150px;' id='pengalaman' maxlength=4 /></td>
    <td>".$_SESSION['lang']['kompetensi']."</td><td><textarea onkeypress='return tanpa_kutip(event)' style='width:150px;' id='kompetensi'></textarea></td>
    <td>".$_SESSION['lang']['deskpekerjaan']."</td><td><textarea onkeypress='return tanpa_kutip(event)'  style='width:150px;' id='deskpekerjaan' ></textarea></td>
</tr>
<tr>         
    <td>".$_SESSION['lang']['maxumur']."</td><td><input type=text class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:150px;' id='maxumur'  maxlength=4 /></td>
    <td>Jumlah Kebutuhan</td><td><input type=text class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:150px;' id='jmlhPersoanl'  maxlength=4 /></td>
    <td>".$_SESSION['lang']['persetujuan']." 1</td><td><select style='width:150px;' id='persetujuan1'>".$optorgd."</select></td>
    
</tr>
<tr>              
    <td>".$_SESSION['lang']['persetujuan']." 2</td><td><select style='width:150px;' id='persetujuan2'>".$optorgd."</select></td>       
    <td>".$_SESSION['lang']['persetujuan']." HRD</td><td><select style='width:150px;' id='persetujuanhrd'>".$optorghr."</select></td>
    <td>&nbsp;</td>
</tr>
    
   <tr>         
   <td colspan=12 align=center><input type=hidden value=insert id=proses><input type=hidden  id=notransaksi>
<button class=mybutton onclick=saveData('sdm_slave_permintaankerja','".$arr."')>".$_SESSION['lang']['save']."</button>
<button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button></td></tr></table></fieldset>";CLOSE_BOX();
OPEN_BOX();
$optthn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sperd="select distinct left(tanggal,4) as thn from ".$dbname.".sdm_permintaansdm order by tanggal desc";
$qperd=mysql_query($sperd) or die(mysql_error($conn));
while($rperd=  mysql_fetch_assoc($qperd)){
     
    $optthn.="<option value='".$rperd['thn']."'>".$rperd['thn']."</option>";
}
echo"<fieldset><legend><b>".$_SESSION['lang']['list']."</b></legend>
    ".$_SESSION['lang']['tahun']." : <select id=thnPeriode onchange=loadData(0)>".$optthn."</select>
    <div id=containerData>
         <script>loadData()</script>
         </div>
         </filedset>";
CLOSE_BOX();
echo close_body();
?>