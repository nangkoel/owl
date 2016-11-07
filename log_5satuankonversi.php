<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/konversi.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX();
echo"<fieldset>
     <legend><b>".$_SESSION['lang']['uomconversion']."</b></legend>
	 <table>
	 <tr>
	    <td>".$_SESSION['lang']['materialname']."</td><td><span id=kodebarang></span><input type=text id=namadisabled size=50 class=myinputtext disabled>
		<img src=images/search.png class=dellicon title='".$_SESSION['lang']['find']."' onclick=\"searchBarang('".$_SESSION['lang']['findmaterial']."','<fieldset><legend>".$_SESSION['lang']['findmaterial']."</legend>Find<input type=text class=myinputtext id=namabrg><button class=mybutton onclick=findBarang()>Find</button></fieldset><div id=container></div>',event);\">
		</td>
	 </tr> 
	 </table>
     </fieldset>";
CLOSE_BOX();
OPEN_BOX();
echo"<fieldset>
     <legend><b>".$_SESSION['lang']['newconversion'].":</b></legend>
	 ".$_SESSION['lang']['materialname'].": <b><span id=captionbarang></span></b><br>
	 ".$_SESSION['lang']['smallestuom'].": <b><span id=captionsatuan></span></b><br>
	 ".$_SESSION['lang']['uomsource']." 1<input type=text class=myinputtext id=satuansource disabled size=10 maxlength=10 onkeypress=\"return tanpa_kutip(event);\">
	 =<input type=text class=myinputtextnumber id=jumlah size=8 maxlength=8 onkeypress=\"return angka_doang(event);\">
         ".$_SESSION['lang']['satuan']."<input type=text class=myinputtext id=satuandest size=10 maxlength=10 onkeypress=\"return tanpa_kutip(event);\">
	 ".$_SESSION['lang']['keterangan']." <input type=text class=myinputtext id=keterangan size=25 maxlength=30 onkeypress=\"return tanpa_kutip(event);\">
     <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=simpanKonversi()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=batalKonversi()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
CLOSE_BOX();

OPEN_BOX();
echo"<fieldset>
     <legend><b>".$_SESSION['lang']['conversionlist']."</b></legend>
	 ".$_SESSION['lang']['materialname'].": <b><span id=captionbarang1></span></b><br>
	 ".$_SESSION['lang']['smallestuom'].": <b><span id=captionsatuan1></span></b>
	 <table class=sortable cellspacing=1 border=0>
	 <thead>
	 <tr class=rowheader>
	 <td>No.</td>
	 <td>".$_SESSION['lang']['uomsource']."</td>
	 <td>".$_SESSION['lang']['uomdestination']."</td>
	 <td>".$_SESSION['lang']['jumlah']."</td>
	 <td>".$_SESSION['lang']['keterangan']."</td>
	 <td></td>
	 </tr>
	 </thead>
	 <tbody id=containersatuan>

	 </tbody>
	 <tfoot>
	 </tfoot>
	 </table>
     </fieldset>";
if($_SESSION['language']=='EN'){
    $zz='kelompok1 as kelompok';
}
else{
    $zz='kelompok';
}
$str="select kode,".$zz." from ".$dbname.".log_5klbarang order by kelompok";
$res=mysql_query($str);
$optkelompok="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optkelompok.="<option value='".$bar->kode."'>".$bar->kelompok." [ ".$bar->kode." ] </option>";
}
echo"<fieldset>
     <legend><b>".$_SESSION['lang']['daftarbarang']."</b></legend>
      ".$_SESSION['lang']['pilihdata']." <select id=kelompok onchange=ambilBarang(this.options[this.selectedIndex].value)>".$optkelompok."</select> 
     <div style='height:300px;width:600px;overflow:scroll'>
     <table class=data cellspacing=1 border=0>
     <thead>
         <tr class=rowheader>
         <td class=firsttd>No.</td>
         <td>".$_SESSION['lang']['kodebarang']."</td>
         <td>".$_SESSION['lang']['namabarang']."</td>
         <td>".$_SESSION['lang']['satuan']."</td>
         <td>".$_SESSION['lang']['ke']." ".$_SESSION['lang']['satuan']."</td>
         <td>Vol</td>
         </tr>
         </thead>
	 <tbody id=containerdetail>";
$str="select a.*,b.namabarang,b.satuan as satuanori from ".$dbname.".log_5stkonversi a
      left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang";
//echo $str;
$res=mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($res))
{  $no+=1;
    echo "<tr class=rowcontent>
         <td class=firsttd>".$no."</td>
         <td>".$bar->kodebarang."</td>
         <td>".$bar->namabarang."</td>
         <td>".$bar->satuanori."</td>
         <td>".$bar->satuankonversi."</td>
         <td align=right>".$bar->jumlah."</td>
         </tr>";
}
    echo"</tbody>
	 <tfoot>
	 </tfoot>
	 </table></div>
     </fieldset>";
CLOSE_BOX();
echo close_body();
?>