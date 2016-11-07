<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/vhc_2ratio.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>FUEL USAGE RATIO</b>');

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
                     <select id=tahun><option value='".date('Y')."'>".date('Y')."</option>
                         <option value='".(date('Y')-1)."'>".(date('Y')-1)."</option>
                         <option value='".(date('Y')-2)."'>".(date('Y')-2)."</option>
                      </select>       
	 <button class=mybutton onclick=getRatioKendaraan()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');
//	 <img onclick=hutangSupplierKePDF(event,'log_laporanhutangsupplier_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>

echo"<span id=printPanel style='display:none;'>
     <img onclick=printFile('vhc_slave_2ratio.php',event) src=images/excel.jpg class=resicon title='MS.Excel'> 
	 </span>    
	 <div style='width:100%;height:359px;overflow:scroll;' id=container>
     </div>";
CLOSE_BOX();
close_body();
?>