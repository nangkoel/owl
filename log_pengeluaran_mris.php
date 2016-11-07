<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
//OPEN_BOX(); //1 O
OPEN_BOX('',"<b>Pengeluaran Barang MRIS :</b><br />");
$frm[0]='';
$frm[1]='';
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/log_pengeluaran_mris.js" /></script>
<script>
 pild='<?php echo '<option value="">'.$_SESSION['lang']['pilihdata'].'</option>';?>';
</script><br />
<?php
$optNmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optKbn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optPrd=$optAfd=$optKbn;
$skbn="select distinct left(untukunit,4) as kodeorg from ".$dbname.".log_mrisht 
       where left(untukunit,4) in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
$qkbn=mysql_query($skbn) or die(mysql_error($conn));
while($rkbn=  mysql_fetch_assoc($qkbn)){
    $optKbn.="<option value='".$rkbn['kodeorg']."'>".$optNmOrg[$rkbn['kodeorg']]."</option>";
}

$frm[0].="<fieldset style=float:left>
    <legend>".$_SESSION['lang']['form']."</legend>"; 
$frm[0].="<table>
    <tr><td>".$_SESSION['lang']['kebun']."</td><td><select id=kbnId style=width:150px onchange=getAfd()>".$optKbn."</select></td></tr>
    <tr><td>".$_SESSION['lang']['afdeling']."</td><td><select id=afdId style=width:150px onchange=getPrd()>".$optAfd."</select></td></tr>
    <tr><td>".$_SESSION['lang']['periode']."</td><td><select id=periodeId style=width:150px >".$optPrd."</select></td></tr>
    </table>";
$frm[0].="<button class=mybutton onclick=prevData()>".$_SESSION['lang']['find']."</button>&nbsp;
          <button class=mybutton onclick=hapusForm()>".$_SESSION['lang']['reset']."</button>";
$frm[0].="</fieldset>"; 
$frm[0].="<fieldset  style=float:left>
          <legend>".$_SESSION['lang']['find']."</legend>
            ".$_SESSION['lang']['nomris']." <input type=text onkeypress='return tanpa_kutip(event)' id=crDataMris style=width:150px />
           <button class=mybutton onclick=prevData()>".$_SESSION['lang']['find']."</button>
           
          </fieldset>
          ";
?>


<?php

$frm[0].="<div style=clear:both;></div>
    <div id=formPertama style=display:none;float:left;overflow:auto;height:350px;max-width:1220px><table><tr><td valign=top>";
$frm[0].="<fieldset><legend>".$_SESSION['lang']['data']."</legend>";
$frm[0].="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr class=rowheader>";
$frm[0].="<td>".$_SESSION['lang']['nomris']."</td>";
$frm[0].="<td>".$_SESSION['lang']['tanggal']."</td>";
$frm[0].="<td>".$_SESSION['lang']['kebun']."</td>";
$frm[0].="<td>".$_SESSION['lang']['afdeling']."</td>";
$frm[0].="<td>".$_SESSION['lang']['dibuat']."</td>
     <td>".$_SESSION['lang']['action']."</td>
    </tr><tbody id=detailContainer>";
$frm[0].="</tbody></table>";
$frm[0].="</fieldset>
          </td><td  valign=top><div id=formKedua style='display:none;'><fieldset><legend>".$_SESSION['lang']['detail']."</legend>"; 
$frm[0].="<table><tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td><span id=tglPermintaan></span></td>";
$frm[0].="<td>".$_SESSION['lang']['tanggalkeluarbarang']."</td><td>:</td><td><input type=text class=myinputtext id=tglKeluar onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 onblur=getPost() /></td></tr>";
$frm[0].="<tr><td>".$_SESSION['lang']['nomris']."</td><td>:</td><td><span id=nomris></span></td>";
$frm[0].="<td>".$_SESSION['lang']['afdeling']."</td><td>:</td><td><span id=kbnId2></span></td></tr>";
$frm[0].="<tr><td>".$_SESSION['lang']['sloc']."</td><td>:</td><td><span id=gudangId></span></td>";
$frm[0].="<td>".$_SESSION['lang']['periode']."</td><td>:</td><td><span id=periodeStr></span> - <span id=periodeEnd></span>
     <input type=hidden id=tglMulai value='' /><input type=hidden id=tglSelesai value='' />
     </td></tr>";
$frm[0].="</table>";

$frm[0].="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr class=rowheader>";
$frm[0].="<td>".$_SESSION['lang']['kodebarang']."</td>";
$frm[0].="<td>".$_SESSION['lang']['namabarang']."</td>";
$frm[0].="<td>".$_SESSION['lang']['satuan']."</td>";
$frm[0].="<td>".$_SESSION['lang']['kodeblok']."</td>";
$frm[0].="<td>".$_SESSION['lang']['kodevhc']."</td>
     <td>".$_SESSION['lang']['jumlah']."</td>";
$frm[0].="<td>".$_SESSION['lang']['realisasisblmnya']."</td>";
$frm[0].="<td>".$_SESSION['lang']['realisasi']."</td>";
$frm[0].="<td>".$_SESSION['lang']['action']."</td>";
$frm[0].="</tr><tbody id=detailContainer2>";
$frm[0].="</tbody>
     </table>
    <button class=mybutton onclick=donePengeluaran()>".$_SESSION['lang']['done']."</button>
     </fieldset></div></td></tr></table>";
$frm[0].="</div>";
$optGdngCr.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sGdng="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
        where induk in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
        and tipe='GUDANG'";
$qGng=mysql_query($sGdng) or die(mysql_error($conn));
while($rGdng=mysql_fetch_assoc($qGng)){
    $optGdngCr.="<option value='".$rGdng['kodeorganisasi']."'>".$rGdng['namaorganisasi']."</option>";
}
$frm[1].="<fieldset>
	   <legend>".$_SESSION['lang']['list']."</legend>
	  <fieldset><legend></legend>
          <table><tr><td>
	  ".$_SESSION['lang']['cari_transaksi']."</td><td>:</td><td>
	  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=12></td>
          <td>
	  ".$_SESSION['lang']['sloc']."</td><td>:</td>
          <td><select id=gdngCr style=width:100px>".$optGdngCr."</select></td></tr></table>
	  <button class=mybutton onclick=cariBast(0)>".$_SESSION['lang']['find']."</button>
	  </fieldset>
	 <div id=containerlist></div>
         <script>cariBast(0)</script>
	 </fieldset>	 
	 ";	 


$hfrm[0]=$_SESSION['lang']['pengeluaranbarang'];
$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,1150);
 
CLOSE_BOX();
echo close_body(); ?>