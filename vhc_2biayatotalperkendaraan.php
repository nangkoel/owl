<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/vhc_2biayatotalperkendaraan.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['biayatotalperkendaraan']).'</b>');

//=================ambil unit;  
$str="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
      where tipe='TRAKSI'";

$res=mysql_query($str);
$optunit="<option value=''>".$_SESSION['lang']['all']."</option>";
$optunit="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optunit.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}

echo"<fieldset>
     <legend>".$_SESSION['lang']['biayatotalperkendaraan']."</legend>
	 ".$_SESSION['lang']['unit']."<select id=unit style='width:150px;'>".$optunit."</select>
	 ".$_SESSION['lang']['tgldari']." <input type=\"text\" class=\"myinputtext\" id=\"tglAwal\" name=\"tglAwal\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" />
         ".$_SESSION['lang']['tglsmp']." <input type=\"text\" class=\"myinputtext\" id=\"tglAkhir\" name=\"tglAkhir\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" />
	 <button class=mybutton onclick=getBiayaTotalPerKendaraan()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');
//	 <img onclick=hutangSupplierKePDF(event,'log_laporanhutangsupplier_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>

echo"<span id=printPanel style='display:none;'>
     <img onclick=biayaTotalPerKendaraanKeExcel(event,'vhc_slave_2biayatotalperkendaraan_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 </span>    
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=600>
	     <thead>
		    <tr>
			  <td align=center>No.</td>
			  <td align=center>".$_SESSION['lang']['kodevhc']."</td>
			
			  <td align=center>".$_SESSION['lang']['jumlah']."</td>
                          <td align=center>".$_SESSION['lang']['jmljamkerja']."</td>  
                          <td align=center>Price/Unit</td>    
                          <td align=center>".$_SESSION['lang']['detail']."</td>
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
  //<td align=center>".$_SESSION['lang']['periode']."</td>
?>