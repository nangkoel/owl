<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/kebun_panen.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['laporanpanen']).'</b>');

//get existing period
$str="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_aktifitas
      where tipetransaksi = 'PNN' order by periode desc";


$res=mysql_query($str);
#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
while($bar=mysql_fetch_object($res))
{
        $optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	
//=================ambil PT;  
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
      where tipe='PT'
          order by namaorganisasi";
$res=mysql_query($str);
$optpt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
while($bar=mysql_fetch_object($res))
{
        $optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

}

//=================ambil gudang;  
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
                where tipe='KEBUN'";
$res=mysql_query($str);
$optgudang="<option value=''>".$_SESSION['lang']['all']."</option>";
//while($bar=mysql_fetch_object($res))
//{
//	$optgudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
//
//}

//<button class=mybutton id=create_new name=create_new onclick=createNew() >".$_SESSION['lang']['new']."</button>

$optpil="<option value='fisik'>".$_SESSION['lang']['fisik']."</option>";
$optpil.="<option value='lokasi'>".$_SESSION['lang']['lokasi']."</option>";

$frm[0].="<fieldset>
     <legend>".$_SESSION['lang']['laporanpanen']." ".$_SESSION['lang']['detail']."</legend>
         ".$_SESSION['lang']['pt']." : "."<select id=pt style='width:200px;' onchange=getKbn()>".$optpt."</select>
         ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select>
         ".$_SESSION['lang']['tanggal']." : 
          <input type=text class=myinputtext id=tgl1 onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10> -
          <input type=text class=myinputtext id=tgl2 onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10>
          <button class=mybutton onclick=getLaporanPanen()>".$_SESSION['lang']['proses']."</button>
         </fieldset>";
$frm[0].="<span id=printPanel style='display:none;'>
     <img onclick=fisikKeExcel(event,'kebun_laporanPanen_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
         <img onclick=fisikKePDF(event,'kebun_laporanPanen_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
         </span>    
         <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100%>
             <thead>
                    <tr>
                          <td align=center width=50>No.</td>
                          <td align=center width=150>".$_SESSION['lang']['tanggal']."</td>
                          <td align=center>".$_SESSION['lang']['afdeling']."</td>
                          <td align=center>".$_SESSION['lang']['lokasi']."</td>
                          <td align=center>".$_SESSION['lang']['bloklama']."</td>
                          <td align=center>".$_SESSION['lang']['tahuntanam']."</td>    
                          <td align=center>".$_SESSION['lang']['janjang']."</td>
                          <td align=center>".$_SESSION['lang']['hasilkerjad']." (Kg)</td>    
                          <td align=center>".$_SESSION['lang']['upahkerja']."</td>
                          <td align=center>".$_SESSION['lang']['upahpremi']."</td>
                          <td align=center>".$_SESSION['lang']['jumlahhk']."</td>
                          <td align=center width=150>".$_SESSION['lang']['rupiahpenalty']."</td>
                        </tr>  
                 </thead>
                 <tbody id=container>
                 </tbody>
                 <tfoot>
                 </tfoot>		 
           </table>
     </div>";

$frm[1].="<fieldset><legend>".$_SESSION['lang']['laporanpanen']." per ".$_SESSION['lang']['tanggal']."</legend>";
$frm[1].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['perusahaan']."</td><td>:</td><td>
<select id=pt_1 name=pt_1 style='width:200px;' onchange=getKbn_1()>".$optpt."</select></select>
</td></tr>
<tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td>
<select id=unit_1 name=unit_1 style=width:150px; onchange=bersih_1()>".$optgudang."</select></select>
</td></tr>
<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td>
<input type=text class=myinputtext id=tgl1_1 onchange=bersih_1() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10> - 
<input type=text class=myinputtext id=tgl2_1 onchange=bersih_1() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10>
</td></tr>
<tr><td colspan=3>
<button class=mybutton onclick=getLaporanPanen_1() >".$_SESSION['lang']['proses']."</button>
<input type=hidden name=hidden_1 id=hidden_1 value=hiddenvalue1 />

</td></tr>
</table>";

$frm[1].="</fieldset>";
$frm[1].="<span id=printPanel_1 style='display:none;'>
     <img onclick=laporanKeExcel_1(event,'kebun_laporanPanen_tanggal_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
         <img onclick=laporanKePDF_1(event,'kebun_laporanPanen_tanggal_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
         </span>    
         <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100% id=container_1>
           </table>
     </div>";

$frm[2].="<fieldset><legend>".$_SESSION['lang']['laporanpanen']." per ".$_SESSION['lang']['orang']."</legend>";
$frm[2].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['perusahaan']."</td><td>:</td><td>
<select id=pt_2 name=pt_2 style='width:200px;' onchange=getKbn_2()>".$optpt."</select></select>
</td></tr>
<tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td>
<select id=unit_2 name=unit_2 style=width:150px; onchange=bersih_2()>".$optgudang."</select></select>
</td></tr>
<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td>
<input type=text class=myinputtext id=tgl1_2 onchange=bersih_2() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10> - 
<input type=text class=myinputtext id=tgl2_2 onchange=bersih_2() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10>
</td></tr>
<tr><td>".$_SESSION['lang']['pilih']."</td><td>:</td><td>
<select id=pil_2 name=pil_2 style='width:100px;'>".$optpil."</select></select>
</td></tr>
<tr><td colspan=3>
<button class=mybutton onclick=getLaporanPanen_2() >".$_SESSION['lang']['proses']."</button>
<input type=hidden name=hidden_2 id=hidden_2 value=hiddenvalue2 />

</td></tr>
</table>";

$frm[2].="</fieldset>";
$frm[2].="<span id=printPanel_2 style='display:none;'>
     <img onclick=laporanKeExcel_2(event,'kebun_laporanPanen_orang_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
         <img onclick=laporanKePDF_2(event,'kebun_laporanPanen_orang_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
         </span>    
         <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100% id=container_2>
           </table>
     </div>";

$frm[3].="<fieldset><legend>".$_SESSION['lang']['laporanpanen']." SPB vs WB </legend>";
$frm[3].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['perusahaan']."</td><td>:</td><td>
<select id=pt_3 name=pt_3 style='width:200px;' onchange=getKbn_3()>".$optpt."</select></select>
</td></tr>
<tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td>
<select id=unit_3 name=unit_3 style=width:150px; onchange=bersih_3()>".$optgudang."</select></select>
</td></tr>
<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td>
<input type=text class=myinputtext id=tgl1_3 onchange=bersih_3() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10> - 
<input type=text class=myinputtext id=tgl2_3 onchange=bersih_3() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10>
</td></tr>
<tr><td colspan=3>
<button class=mybutton onclick=getLaporanPanen_3() >".$_SESSION['lang']['proses']."</button>
<input type=hidden name=hidden_3 id=hidden_3 value=hiddenvalue2 />

</td></tr>
</table>";

$frm[3].="</fieldset>";
$frm[3].="<span id=printPanel_3 style='display:none;'>
     <img onclick=laporanKeExcel_3(event,'kebun_laporanPanen_spbwb_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
         </span>    
         <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100% id=container_3>
           </table>
     </div>";
//	 <img onclick=laporanKePDF_3(event,'kebun_laporanPanen_spbwb_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>

//========================
$hfrm[0]=$_SESSION['lang']['laporanpanen']." ".$_SESSION['lang']['detail'];
$hfrm[1]=$_SESSION['lang']['laporanpanen']." per ".$_SESSION['lang']['tanggal'];
$hfrm[2]=$_SESSION['lang']['laporanpanen']." per ".$_SESSION['lang']['orang'];
$hfrm[3]=$_SESSION['lang']['laporanpanen']." SPB vs WB";
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,900);
//===============================================

close_body();
?>