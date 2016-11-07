<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/sdm_ruangrapat.js'></script>
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
$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select karyawanid, namakaryawan from ".$dbname.".datakaryawan 
       where tipekaryawan='0'  and lokasitugas='".$_SESSION['empl']['lokasitugas']."'
           and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")
       order by namakaryawan asc";
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optKary.="<option value=".$rOrg['karyawanid'].">".$rOrg['namakaryawan']."</option>";
}

$optPeriodec="<option value=".date('Y').">".date('Y')."</option>";
$optPeriodec.="<option value=".(date('Y')+1).">".(date('Y')+1)."</option>"; 

$frm[0]='';
$frm[1]='';
$arr="##tanggalDt##tglAwal##tglEnd##method##agenda##room##pic##jam1##mnt1##jam2##mnt2";
include('master_mainMenu.php');
OPEN_BOX();

$frm[0].="<fieldset>
     <legend>".$_SESSION['lang']['form']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['tanggal']."</td>
	   <td><input type='text' class='myinputtext' id='tanggalDt' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>
	 </tr>
	 <tr>
	   <td>Mulai</td>
	   <td>
           <input type='text' class='myinputtext' id='tglAwal' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /><select id=\"jam1\">".$jm."</select>:<select id=\"mnt1\">".$mnt."</select></td>
	 </tr>	
	 <tr>
	   <td>Sampai</td>
	   <td>
           <input type='text' class='myinputtext' id='tglEnd' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /><select id=\"jam2\">".$jm."</select>:<select id=\"mnt2\">".$mnt."</select></td>
	 </tr>	
         <tr>
	   <td>Agenda</td>
	   <td><input type=\"text\" class=\"myinputtext\" id=\"agenda\" name=\"agenda\" onkeypress=\"return tanpa_kutip(event);\" maxlength=\"30\" style=\"width:150px;\" /></td>
	 </tr>
         <tr>
	   <td>Ruangan</td>
	   <td><select id=room name=room>
                                 <option value='R.Rapat Lt.1 HO'>R.Rapat Lt.1 HO</option>
                                 <option value='R.Rapat Lt.Dasar HO'>R.Rapat Lt.Dasar HO</option>
                                 <option value='R.Rapat Lt.2 HO'>R.Rapat Lt.2 HO</option>
                                 <option value='R.Rapat Besar SSRO'>R.Rapat Besar SSRO</option>
                                 <option value='R.Rapat Direksi SSRO'>R.Rapat Direksi SSRO</option>
                                 <option value='R.Rapat KTRO'>R.Rapat KTRO</option>
                                 </select></td>
	 </tr>
         <tr>
	   <td>PIC</td>
	   <td><select id=\"pic\" style=\"width:150px\">".$optKary."</select></td>
	 </tr>
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveFranco('sdm_slave_ruangrapat','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset><input type='hidden' id=idData name=idData value='' />";

$frm[0].="<fieldset><legend>".$_SESSION['lang']['list']."</legend>
    <table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>".$_SESSION['lang']['tanggal']."</td>
           <td>".$_SESSION['lang']['roomname']."</td>
           <td>".$_SESSION['lang']['mulai']."</td>
           <td>".$_SESSION['lang']['sampai']."</td>
	   <td>Agenda</td>
           <td>".$_SESSION['lang']['pic']."</td>
           <td>".$_SESSION['lang']['status']."</td>
	   <td>Action</td>
	  </tr>
	 </thead>
	 <tbody id=container><script>loadData()</script></tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset>";



$frm[1].="
<fieldset>
Jika ada keperluan yang lebih penting dalam penggunaan ruang rapat, dan ruang rapat sudah dipesan oleh orang lain, 
silahkan negosiasi dengan PIC bersangkutan untuk melakukan 'Cancel'<hr>
<table cellpadding=1 cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['tanggal']."</td>
    <td align=left><input type='text' class='myinputtext' id='tglCari' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>
    </tr></table>
 <button class=mybutton onclick=loadData2()>".$_SESSION['lang']['preview']."</button>
</table>
</fieldset>
<fieldset><legend>".$_SESSION['lang']['list']."</legend>
    <table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>".$_SESSION['lang']['tanggal']."</td>
           <td>".$_SESSION['lang']['roomname']."</td>
           <td>".$_SESSION['lang']['mulai']."</td>
           <td>".$_SESSION['lang']['sampai']."</td>
	   <td>Agenda</td>
           <td>".$_SESSION['lang']['pic']."</td>
           <td>".$_SESSION['lang']['reservedby']."</td>
           <td>".$_SESSION['lang']['status']."</td>
            <td>".$_SESSION['lang']['waktu']."</td>   
	  </tr>
	 </thead>
	 <tbody id=container2>";
	 

$frm[1].="</tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset>";
$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['list'];

//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
?>
<?php
CLOSE_BOX();
echo close_body();
?>