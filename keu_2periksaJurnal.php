<?php //@Copy nangkoelframework 
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src="js/keu_laporan.js"></script>
<?php
include('master_mainMenu.php');
//OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['laporanbukubesar']).'</b>');
OPEN_BOX();

//get unit where length=4
if($_SESSION['empl']['tipelokasitugas']=='HOLDING') {
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
    where CHAR_LENGTH(kodeorganisasi)=4 order by tipe,kodeorganisasi";
} else if ($_SESSION['empl']['tipelokasitugas']=='KANWIL') {
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
    where tipe!='HOLDING' and CHAR_LENGTH(kodeorganisasi)=4 and kodeorganisasi LIKE '".substr($_SESSION['empl']['lokasitugas'],0,1)."%'
    order by tipe,kodeorganisasi";
} else {
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
    where CHAR_LENGTH(kodeorganisasi)=4 and kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%' order by tipe,kodeorganisasi";
}
$res=mysql_query($str);
$optunit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
while($bar=mysql_fetch_object($res))
{
        $optunit.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi."-".$bar->namaorganisasi."</option>";
}

//get existing period
$str="select distinct periode from ".$dbname.".setup_periodeakuntansi
      order by periode desc
      ";
$res=mysql_query($str); 
$optperiode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
while($bar=mysql_fetch_object($res))
{
	$optperiode.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}

?>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['laporanperiksajurnal']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id=unit style='width:200px;' onchange=ambilJurnal()><?php echo $optunit; ?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id=periode style='width:200px;' onchange=ambilJurnal()><?php echo $optperiode; ?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['nojurnal']." ".$_SESSION['lang']['dari']?></label></td><td><select id=jurnaldari style='width:200px;' onchange=hideById('printPanel')><option value=""></option></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['nojurnal']." ".$_SESSION['lang']['sampai']?></label></td><td><select id=jurnalsampai style='width:200px;' onchange=hideById('printPanel')><option value=""></option></select></td></tr>

<tr height="20"><td colspan="2"><button class=mybutton onclick=getLaporanPeriksaJurnal()><?php echo $_SESSION['lang']['proses'] ?></button></td></tr>


</table>
</fieldset>
<?php

CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
     <img onclick=periksajurnalKeExcel(event,'keu_slave_2periksaJurnal_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=periksajurnalKePDF(event,'keu_slave_2periksaJurnal_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span>    
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		    <tr>
			  <td align=center>".$_SESSION['lang']['nojurnal']."</td>
			  <td align=center>".$_SESSION['lang']['noakun']."</td>
			  <td align=center>".$_SESSION['lang']['namaakun']."</td>
			  <td align=center>".$_SESSION['lang']['keterangan']."</td>
			  <td align=center>".$_SESSION['lang']['debet']."</td>
			  <td align=center>".$_SESSION['lang']['kredit']."</td>
			  <td align=center>".$_SESSION['lang']['selisih']."</td>
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




exit;


?>
















//get existing period
$str="select distinct substr(tanggal,1,7) as periode from ".$dbname.".keu_jurnaldt
      order by periode desc";
	  
$res=mysql_query($str);
#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
$optper="";
while($bar=mysql_fetch_object($res))
{
	$optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}
/*
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{   
 * 
 */
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
        $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
                        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'
                        or tipe='HOLDING')  and induk!=''
                        ";
        $res=mysql_query($str);
        $optgudang="<option value=''>".$_SESSION['lang']['all']."</option>";
        while($bar=mysql_fetch_object($res))
        {
                $optgudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

        }
/*        
}
else
{
        $optpt="";
        $optpt.="<option value='".$_SESSION['empl']['kodeorganisasi']."'>". $_SESSION['empl']['kodeorganisasi']."</option>";
         $optgudang.="<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas']."</option>";
   
}
 * 
 */
        $str="select noakun,namaakun from ".$dbname.".keu_5akun
                        where level = '5'
                        order by noakun
                        ";
        $res=mysql_query($str);
//        $optakun="<option value=''>".$_SESSION['lang']['all']."</option>";
        $optakun="<option value=''></option>";
//        $optakun="";
        while($bar=mysql_fetch_object($res))
        {
                $optakun.="<option value='".$bar->noakun."'>".$bar->noakun." - ".$bar->namaakun."</option>";

        }
$qwe="01-".date("m-Y");
?>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['laporanbukubesar']?> v1</b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['pt']?></label></td><td><select id=pt style='width:200px;'  onchange=ambilAnak(this.options[this.selectedIndex].value)><?php echo $optpt; ?></select></tr>
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id=gudang style='width:200px;' onchange=hideById('printPanel')><?php echo $optunit; ?></select></td></tr>

<tr><td><label><?php echo $_SESSION['lang']['tanggalmulai']?></label></td><td><input type="text" class="myinputtext" id="tgl1" name="tgl1" onchange="cekTanggal1(this.value);" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" value="<?php echo $qwe; ?>" /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggalsampai']?></label></td><td><input type="text" class="myinputtext" id="tgl2" name="tgl2" onchange="cekTanggal2(this.value);" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['noakundari']?></label></td><td><select id=akundari style='width:200px;' onchange=ambilAkun2(this.options[this.selectedIndex].value)><?php echo $optakun; ?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['noakunsampai']?></label></td><td><select id=akunsampai style='width:200px;' onchange=hideById('printPanel')><option value=""></option></select></td></tr>

<!--<tr height="20"><td colspan="2">&nbsp;</td></tr>-->
<tr height="20"><td colspan="2"> <button class=mybutton onclick=getLaporanBukuBesarv1()><?php echo $_SESSION['lang']['proses'] ?></button></td></tr>

<!--<tr><td colspan="2"><button onclick="zPreview('sdm_slave_2rekapabsen','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('sdm_slave_2rekapabsen','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'sdm_slave_2rekapabsen.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button><button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>-->

</table>
</fieldset>
<?php
/*

echo"<fieldset>
     <legend>".$_SESSION['lang']['laporanbukubesar']." v1</legend>
	 ".$_SESSION['lang']['pt']." : "."<select id=pt style='width:200px;'  onchange=ambilAnak(this.options[this.selectedIndex].value)>".$optpt."</select><br>
	 ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select><br>
	 ".$_SESSION['lang']['periode']." : "."<select id=periode onchange=hideById('printPanel')>".$optper."</select>
         ".$_SESSION['lang']['tglcutisampai']."
         ".$_SESSION['lang']['periode']." : "."<select id=periode1 onchange=hideById('printPanel')>".$optper."</select>
	 <button class=mybutton onclick=getLaporanBukuBesar()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
 */
CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
     <img onclick=jurnalv1KeExcel(event,'keu_laporanBukuBesarv1_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=jurnalv1KePDF(event,'keu_laporanBukuBesarv1_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span>    
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		    <tr>
			  <td align=center>".$_SESSION['lang']['nomor']."</td>
			  <td align=center>".$_SESSION['lang']['nojurnal']."</td>
			  <td align=center>".$_SESSION['lang']['tanggal']."</td>
			  <td align=center>".$_SESSION['lang']['noakun']."</td>
			  <td align=center>".$_SESSION['lang']['keterangan']."</td>
			  <td align=center>".$_SESSION['lang']['saldoawal']."</td>
			  <td align=center>".$_SESSION['lang']['debet']."</td>
			  <td align=center>".$_SESSION['lang']['kredit']."</td>
			  <td align=center>".$_SESSION['lang']['saldoakhir']."</td>
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