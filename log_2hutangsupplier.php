<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/log_laporan.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['hutangsupplierbpb']).'</b>');

//get existing period
$str="select distinct periode from ".$dbname.".log_5saldobulanan
      order by periode desc";
$res=mysql_query($str);
//$optper="<option value=''>".$_SESSION['lang']['all']."</option>";
$optper="<option value=''></option>";
//$optper="";
while($bar=mysql_fetch_object($res))
{
	$optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	
//=================ambil PT;  
/*
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
      where tipe='PT'
	  order by namaorganisasi";
$res=mysql_query($str);
$optpt="";
while($bar=mysql_fetch_object($res))
{
	$optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

}

*/
//=================ambil gudang;  
$str="select distinct a.kodeorg,b.namaorganisasi from ".$dbname.".setup_periodeakuntansi a
      left join ".$dbname.".organisasi b
	  on a.kodeorg=b.kodeorganisasi
      where b.tipe='GUDANG'
	  order by namaorganisasi";
$res=mysql_query($str);
//$optgudang="<option value=''>".$_SESSION['lang']['all']."</option>";
$optgudang="";
while($bar=mysql_fetch_object($res))
{
	$optgudang.="<option value='".$bar->kodeorg."'>".$bar->namaorganisasi."</option>";

}
//	 ".$_SESSION['lang']['pt']."<select id=pt style='width:150px;' onchange=hideById('printPanel')>".$optpt."</select>

echo"<fieldset>
     <legend>".$_SESSION['lang']['hutangsupplierbpb']."</legend>
	 ".$_SESSION['lang']['sloc']."<select id=gudang style='width:150px;' onchange=ambilPeriode(this.options[this.selectedIndex].value)>".$optgudang."</select>
	 ".$_SESSION['lang']['periode']."<select id=periode onchange=hideById('printPanel')>".$optper."</select>
	 <button class=mybutton onclick=getHutangSupplier()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
     <img onclick=hutangSupplierKeExcel(event,'log_laporanhutangsupplier_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=hutangSupplierKePDF(event,'log_laporanhutangsupplier_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span>    
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		    <tr>
			  <td align=center>No.</td>
			  <td align=center>".$_SESSION['lang']['tanggal']."</td>
			  <td align=center>".$_SESSION['lang']['kodebarang']."</td>
			  <td align=center>".$_SESSION['lang']['namabarang']."</td>
			  <td align=center>".$_SESSION['lang']['jumlah']."</td>
			  <td align=center>".$_SESSION['lang']['satuan']."</td>
			  <td align=center>".$_SESSION['lang']['kodesupplier']."</td>
			  <td align=center>".$_SESSION['lang']['namasupplier']."</td>
			  <td align=center>".$_SESSION['lang']['nopo']."</td>
			  <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
			  <td align=center>".$_SESSION['lang']['total']."</td>
			</tr>  
		 </thead>
		 <tbody id=container>
		 </tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>
     </div>";
CLOSE_BOX();
close_body();
?>