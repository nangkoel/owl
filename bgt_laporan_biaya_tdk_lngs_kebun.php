<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/zTools.js'></script>
<script language=javascript1.2 src='js/bgt_btl_kebun.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','BIAYA TIDAK LANGSUNG');
#ambil tahun budget
$str="select distinct(tahunbudget) as tahunbudget from  ".$dbname.".bgt_budget order by tahunbudget desc";
$res=mysql_query($str);
$opttahun="<option value=''>Pilih..</option>";
while($bar=mysql_fetch_object($res))
{
    $opttahun.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
}
#ambil kode kebun
$str="select kodeorganisasi as kodeorg from  ".$dbname.".organisasi where (tipe='KEBUN' or tipe='KANWIL') order by kodeorganisasi";
$res=mysql_query($str);
$optunit="<option value=''>Pilih..</option>";
while($bar=mysql_fetch_object($res))
{
    $optunit.="<option value='".$bar->kodeorg."'>".$bar->kodeorg."</option>";
}

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['tahunanggaran']."</td><td><select id=thnbudget style='width:200px'>".$opttahun."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['kodeorganisasi']."</td><td><select id=kodeunit style='width:200px'>".$optunit."</select></td></tr>
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=tampilkanBTLKebun()>".$_SESSION['lang']['save']."</button>
	 </fieldset>";

	echo"<fieldset><legend>".$_SESSION['lang']['list']."
            Result:
            <span id=\"printPanel\" style=\"display:none;\">
            <img onclick=\"fisikKeExcel(event,'bgt_laporan_biaya_tdk_lngs_kebun_excel.php')\" src=\"images/excel.jpg\" class=\"resicon\" title=\"MS.Excel\"> 
	     <img onclick=\"fisikKePDF(event,'...')\" title=\"PDF\" class=\"resicon\" src=\"images/pdf.jpg\">
            </span>
            </legend>
             Unit:<label id=unit></label> Tahun Budget:<label id=tahun></label>
             <table class=sortable cellspacing=1 border=0 style='width:1600px;'>
	     <thead>
		 <tr class=rowheader>
                   <td align=center>".$_SESSION['lang']['nourut']."</td>
                   <td align=center>".$_SESSION['lang']['noakun']."</td>
                   <td align=center>".$_SESSION['lang']['namaakun']."</td>
                   <td align=center>".$_SESSION['lang']['luas']."</td>
                   <td align=center>".$_SESSION['lang']['jumlahrp']."</td>
                   <td align=center>".$_SESSION['lang']['rpperha']."</td>  
                   <td align=center>01(Rp)</td>
                   <td align=center>02(Rp)</td>
                   <td align=center>03(Rp)</td>
                   <td align=center>04(Rp)</td>
                   <td align=center>05(Rp)</td>
                   <td align=center>06(Rp)</td>
                   <td align=center>07(Rp)</td>
                   <td align=center>08(Rp)</td>
                   <td align=center>09(Rp)</td>
                   <td align=center>10(Rp)</td>
                   <td align=center>11(Rp)</td>
                   <td align=center>12(Rp)</td>
                 </tr>
		 </thead>
		 <tbody id=container>"; 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table></fieldset>";

CLOSE_BOX();
echo close_body();
?>