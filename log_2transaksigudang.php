<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src="js/log_2transaksigudang.js"></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['transaksigudang']).'</b>');

//=================ambil unit;  
//if($_SESSION['empl']['tipelokasitugas']=='HOLDING') 
$str="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
      where tipe like 'GUDANG%'
	  order by kodeorganisasi"; 

/*
else
$str="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
      where tipe= 'GUDANG' and kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%'
	  order by namaorganisasi";
*/
$res=mysql_query($str);
//$optunit="<option value=''>".$_SESSION['lang']['all']."</option>";
$optunit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
while($bar=mysql_fetch_object($res))
{
	$optunit.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi." (".$bar->namaorganisasi.")</option>";
}
//	 ".$_SESSION['lang']['pt']."<select id=pt style='width:150px;' onchange=hideById('printPanel')>".$optpt."</select>
$optper="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if($_SESSION['language']=='EN'){
$optjenis="<option value=''></option>
                <option value='0'>Goods movement on the way</option>
                <option value='1'>Goods Receipt(GR)</option>
                <option value='2'>Return of GI</option>
                <option value='3'>Goods movement receipt</option>
                <option value='5'>Good Issue(GI)</option>
                <option value='6'>Return of GR</option>
                <option value='7'>Goods movement issue</option>
                <option value='9'>All</option>";    
}else{
$optjenis="<option value=''></option>
                <option value='0'>Mutasi dalam perjalanan</option>
                <option value='1'>Penerimaan</option>
                <option value='2'>Pengembalian pengeluaran</option>
                <option value='3'>Penerimaan mutasi</option>
                <option value='5'>Pengeluaran</option>
                <option value='6'>Pengembalian penerimaan</option>
                <option value='7'>Pengeluaran mutasi</option>
                <option value='9'>Seluruhnya</option>";
}

$optbarang="<option value=''>".$_SESSION['lang']['all']."</option>";

echo"<fieldset>
     <legend>".$_SESSION['lang']['transaksigudang']."</legend>
	 <table cellspacing=1 border=0><tr>
	   <td>".$_SESSION['lang']['unit']."</td>
	   <td>
	     <select id=unit style='width:150px;' onchange=ambilPeriode(this.options[this.selectedIndex].value)>".$optunit."</select></td>
	 </tr><tr>
	   <td>".$_SESSION['lang']['periode']."</td>
	   <td><select id=periode onchange=hideById('printPanel')>".$optper."</select></td>
	 </tr><tr>
	   <td>".$_SESSION['lang']['tipetransaksi']."</td>
	   <td><select id=jenis onchange=hideById('printPanel')>".$optjenis."</select></td>
	 </tr><tr>
	   <td>".$_SESSION['lang']['kodebarang']."</td>
       <td><input type=text size=10 maxlength=10 id=kodebarang class=myinputtext onkeypress=\"return false;\" onclick=\"showWindowBarang('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."',event);\">
	   <button class=mybutton onclick=setAll()>".$_SESSION['lang']['all']."</button></td>
	 </tr><tr>
	   <td colspan=2><button class=mybutton onclick=getTransaksiGudang()>".$_SESSION['lang']['proses']."</button></td>
	 </tr></table>
	 </fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');
//	 <img onclick=hutangSupplierKePDF(event,'log_laporanhutangsupplier_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>

echo"<span id=printPanel style='display:none;'>
     <img onclick=transaksiGudangKeExcel(event,'log_slave_2transaksigudang_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 </span>    
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100% id=container>
	   </table>
     </div>";
CLOSE_BOX();
close_body();
?>