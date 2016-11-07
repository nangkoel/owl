<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script>pilih="<?php echo $_SESSION['lang']['pilihdata'] ?>"</script>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/sdm_prasarana.js'></script>
<?php
$arr="##kdOrg##idKlmpk##idJenis##idLokasi##jmlhSarana##method##thnPerolehan##blnPerolehan##statFr##idData";
include('master_mainMenu.php');
OPEN_BOX();
$optKlmpk="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optJns=$optKlmpk;
$sKlmpk="select distinct * from ".$dbname.".sdm_5kl_prasarana order by kode asc";
$qKlmpk=mysql_query($sKlmpk) or die(mysql_error());
while($rKlmpk=mysql_fetch_assoc($qKlmpk))
{
    $orgNmKlmpk[$rKlmpk['kode']]=$rKlmpk['nama'];
    $optKlmpk.="<option value='".$rKlmpk['kode']."'>".$rKlmpk['nama']."</option>";
}
$optKlmpk2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";


$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='6' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";
}
echo"<fieldset style=width:350px;float:left;>
     <legend>".$_SESSION['lang']['prasarana']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['kodeorg']."</td>
	   <td><input type=text class=myinputtext id=kdOrg name=kdOrg onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\" disabled value='".$_SESSION['empl']['lokasitugas']."' /></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['kodekelompok']."</td>
	   <td><select id=idKlmpk style=\"width:150px;\" onchange=getJenis(0,0)>".$optKlmpk."</select></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['jenis']."</td>
	   <td><select id=idJenis style=\"width:150px;\" onchange=getSatuan(0)>".$optKlmpk2."</select></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['lokasi']."</td>
	   <td><select id=idLokasi style=\"width:150px;\">".$optOrg."</select></td>
	 </tr>	 
	  <tr>
	   <td>".$_SESSION['lang']['jumlah']."</td>
	   <td><input type=text class=myinputtext id=jmlhSarana name=jmlhSarana onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20 /><span id=satuan></span></td>
	 </tr>
          <tr>
	   <td>".$_SESSION['lang']['tahunperolehan']."</td>
	   <td><input type=text class=myinputtext id=thnPerolehan name=thnPerolehan onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=4 /></td>
	 </tr>
          <tr>
	   <td>".$_SESSION['lang']['blnperolehan']."</td>
	   <td><input type=text class=myinputtext id=blnPerolehan name=blnPerolehan onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=2 /></td>
	 </tr>
         <tr>
	   <td>".$_SESSION['lang']['status']."</td>
	   <td><input type='checkbox' id=statFr name=statFr /> Tidak Aktif</td>
	 </tr> 
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveFranco('sdm_slave_prasarana','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset><input type=hidden id=idData />";
CLOSE_BOX();
OPEN_BOX();
$str="select * from ".$dbname.".sdm_prasarana order by tahunperolehan,bulanperolehan desc";
$res=mysql_query($str);
echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
	   <td>".$_SESSION['lang']['kodeorg']."</td>
	   <td>".$_SESSION['lang']['kodekelompok']."</td>
	   <td>".$_SESSION['lang']['jenis']."</td>
	   <td>".$_SESSION['lang']['lokasi']."</td>
	   <td>".$_SESSION['lang']['jumlah']."</td>
           <td>".$_SESSION['lang']['tahunperolehan']."</td>
           <td>".$_SESSION['lang']['blnperolehan']."</td>
           <td>".$_SESSION['lang']['status']."</td>
	   <td>Action</td>
	  </tr>
	 </thead>
	 <tbody id=container>";
	 echo"<script>loadData()</script>";

echo"</tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset>";
CLOSE_BOX();
echo close_body();
?>