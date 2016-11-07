<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('lib/zLib.php');
echo open_body();
?>
<script>
    warn="<?php echo $_SESSION['lang']['transaksi'].' '.$_SESSION['lang']['detail']
                .' '.$_SESSION['lang']['harus'].' '.$_SESSION['lang']['lihat']
                .' '.$_SESSION['lang']['per'].' '.$_SESSION['lang']['gudang']?>";
</script>
<script language=javascript1.2 src='js/log_laporan.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['laporanstok']).'</b>');

//get existing period
$str="select distinct periode from ".$dbname.".log_5saldobulanan
      order by periode desc";
$res=mysql_query($str);
$optper="";
while($bar=mysql_fetch_object($res))
{
	$optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	

//get existing period1
$str="select distinct left(periode,4) as per from ".$dbname.".log_5saldobulanan
      order by periode desc";
//echo $str;
$res=mysql_query($str);
//$optper1="<option value=''>Current</option>";
$optper1="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optper1.="<option value='".$bar->per."'>".$bar->per."</option>";
}	

//=================ambil PT;  
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
      where tipe='PT'
	  order by namaorganisasi";
$res=mysql_query($str);
$optpt="";
while($bar=mysql_fetch_object($res))
{
	$optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

}

//=================ambil gudang;  
$str="select distinct a.kodeorg,b.namaorganisasi from ".$dbname.".setup_periodeakuntansi a
      left join ".$dbname.".organisasi b
	  on a.kodeorg=b.kodeorganisasi
      where b.tipe like 'GUDANG%'
	  order by kodeorg";
$res=mysql_query($str);
$optgudang1="<option value=''>All</option>";
$optgudang="";
while($bar=mysql_fetch_object($res))
{
	$optgudang1.="<option value='".$bar->kodeorg."'>".$bar->namaorganisasi."</option>";
                    $optgudang.="<option value='".$bar->kodeorg."'>".$bar->namaorganisasi."</option>";

}

$frm[0].="<fieldset>
     <legend>".$_SESSION['lang']['persediaanfisikharga']." ".$_SESSION['lang']['bulanan']."</legend>
	 ".$_SESSION['lang']['pt']."<select id=pt style='width:150px;' onchange=hideById('printPanel')>".$optpt."</select>
	 ".$_SESSION['lang']['sloc']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang1."</select>
	 ".$_SESSION['lang']['periode']."<select id=periode onchange=hideById('printPanel')>".$optper."</select>
	 <button class=mybutton onclick=getLaporanFisikHarga()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
//CLOSE_BOX();
//OPEN_BOX('','Result:');
$frm[0].="<span id=printPanel style='display:none;'>
     <span id=orglegend></span>   
     <img onclick=fisikKeExceltab0(event,'log_laporanPersediaanFisikHarga_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=fisikKePDFHarga(event,'log_laporanPersediaanFisikHarga_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span> 
     <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		    <tr>
			  <td rowspan=2 align=center>No.</td>
			  <td rowspan=2 align=center>".$_SESSION['lang']['periode']."</td>
			  <td rowspan=2 align=center>".$_SESSION['lang']['kodebarang']."</td>
			  <td rowspan=2 align=center>".$_SESSION['lang']['namabarang']."</td>
			  <td rowspan=2 align=center>".$_SESSION['lang']['satuan']."</td>
			  <td colspan=3 align=center>".$_SESSION['lang']['saldoawal']."</td>
			  <td colspan=3 align=center>".$_SESSION['lang']['masuk']."</td>
			  <td colspan=3 align=center>".$_SESSION['lang']['keluar']."</td>
			  <td colspan=3 align=center>".$_SESSION['lang']['saldo']."</td>
			</tr>
			<tr>
			   <td align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center>".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center>".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center>".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center>".$_SESSION['lang']['totalharga']."</td>	   
			</tr>   
		 </thead>
		 <tbody id=container>
		 </tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>
     </div>";

$frm[1].="<fieldset>
     <legend>".$_SESSION['lang']['persediaanfisikharga']." ".$_ESSION['language']['setahun']."</legend>
	 ".$_SESSION['lang']['pt']."<select id=pt1 style='width:150px;' onchange=hideById('printPanel1')>".$optpt."</select>
	 ".$_SESSION['lang']['sloc']."<select id=gudang1 style='width:150px;' onchange=hideById('printPanel1')>".$optgudang1."</select>
	 ".$_SESSION['lang']['periode']."<select id=periode1 onchange=hideById('printPanel1')>".$optper1."</select>
	 <button class=mybutton onclick=getLaporanFisikHarga1()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
//CLOSE_BOX();
//OPEN_BOX('','Result:');
$frm[1].="<span id=printPanel1 style='display:none;'>
     <span id=orglegend1></span>   
     <img onclick=fisikKeExcelT1(event,'log_laporanPersediaanFisikHargaTahunan_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=fisikKePDFT1(event,'log_laporanPersediaanFisikHargaTahunan_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span> 
     <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		    <tr>
			  <td rowspan=2 align=center>No.</td>
			  <td rowspan=2 align=center>".$_SESSION['lang']['periode']."</td>
			  <td rowspan=2 align=center>".$_SESSION['lang']['kodebarang']."</td>
			  <td rowspan=2 align=center>".$_SESSION['lang']['namabarang']."</td>
			  <td rowspan=2 align=center>".$_SESSION['lang']['satuan']."</td>
			  <td colspan=3 align=center>".$_SESSION['lang']['saldoawal']."</td>
			  <td colspan=3 align=center>".$_SESSION['lang']['masuk']."</td>
			  <td colspan=3 align=center>".$_SESSION['lang']['keluar']."</td>
			  <td colspan=3 align=center>".$_SESSION['lang']['saldo']."</td>
			</tr>
			<tr>
			   <td align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center>".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center>".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center>".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center>".$_SESSION['lang']['totalharga']."</td>	   
			</tr>   
		 </thead>
		 <tbody id=container1>
		 </tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>
     </div>";
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optGdng=$optUnit;
$sUnit="select distinct substr(kodeorganisasi,1,4) as kodeorganisasi from ".$dbname.".organisasi where tipe in ('GUDANG','GUDANGTEMP') order by namaorganisasi asc";
$qUnit=mysql_query($sUnit) or die(mysql_error($conn));
while($rUnit=mysql_fetch_assoc($qUnit))
{
    $optUnit.="<option value='".$rUnit['kodeorganisasi']."'>".$optNmOrg[$rUnit['kodeorganisasi']]."</option>";
}
$optPeriode="";
for($x=0;$x<13;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPeriode.="<option value=".date("Y-m",$dt).">".date("m-Y",$dt)."</option>";
}
$frm[2].="<fieldset>
     <legend>".$_SESSION['lang']['persediaanfisikharga'].' Per '.$_SESSION['lang']['unit']."</legend>
	 ".$_SESSION['lang']['unit']."<select id=unitDt style='width:150px;'>".$optUnit."</select>
	 ".$_SESSION['lang']['periode']."<select id=periode2 onchange=hideById('printPanel2')>".$optPeriode."</select>
	 <button class=mybutton onclick=getLaporanFisikHarga2()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";

$frm[2].="<span id=printPanel2 style='display:none;'>
     <span id=orglegend2></span>   
     <img onclick=fisikKeExcelT2(event,'log_LaporanPersediaanFisikHarga_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=fisikKePDFT2(event,'log_LaporanPersediaanFisikHarga_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span> 
     <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		    <tr>
			  <td rowspan=2 align=center>No.</td>
			  <td rowspan=2 align=center>".$_SESSION['lang']['periode']."</td>
			  <td rowspan=2 align=center>".$_SESSION['lang']['kodebarang']."</td>
			  <td rowspan=2 align=center>".$_SESSION['lang']['namabarang']."</td>
			  <td rowspan=2 align=center>".$_SESSION['lang']['satuan']."</td>
			  <td colspan=3 align=center>".$_SESSION['lang']['saldoawal']."</td>
			  <td colspan=3 align=center>".$_SESSION['lang']['masuk']."</td>
			  <td colspan=3 align=center>".$_SESSION['lang']['keluar']."</td>
			  <td colspan=3 align=center>".$_SESSION['lang']['saldoakhir']."</td>
			</tr>
			<tr>
			   <td align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center>".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center>".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center>".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center>".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center>".$_SESSION['lang']['totalharga']."</td>	   
			</tr>   
		 </thead>
		 <tbody id=container2>
		 </tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>
     </div>";
//========================
$hfrm[0]=$_SESSION['lang']['persediaanfisikharga'].' '.$_SESSION['lang']['bulanan'];
$hfrm[1]=$_SESSION['lang']['persediaanfisikharga'].' '.$_SESSION['lang']['setahun'];
$hfrm[2]=$_SESSION['lang']['persediaanfisikharga'].' Per '.$_SESSION['lang']['unit'];
//$hfrm[1]=$_SESSION['lang']['laporanstok']." vs ".$_SESSION['lang']['pengiriman'];
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,900);
//===============================================
CLOSE_BOX();
close_body();
?>