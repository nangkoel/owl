<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/keu_2agingSchedule.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX();

	
//=================ambil PT;  
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
          where tipe='PT' order by namaorganisasi";
} else {
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
          where tipe='PT' and kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."' order by namaorganisasi";
}
$res=mysql_query($str);
$optpt="<option value=''>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
    if ($bar->kodeorganisasi==$_SESSION['org']['kodeorganisasi'])
	$optpt.="<option value='".$bar->kodeorganisasi."' selected>".$bar->namaorganisasi."</option>";
    else
	$optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}

//=================ambil gudang;  
//$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
//		where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'
//		or tipe='HOLDING')  and induk!=''";
//$str="select distinct a.kodeorg,b.namaorganisasi from ".$dbname.".setup_periodeakuntansi a
//      left join ".$dbname.".organisasi b
//	  on a.kodeorg=b.kodeorganisasi
//     where b.tipe='KEBUN'
//	  order by namaorganisasi";
$optgudang="<option value=''>".$_SESSION['lang']['all']."</option>";
$sUnit="select distinct unit from ".$dbname.".aging_sch_vw where kodeorg='".$_SESSION['org']['kodeorganisasi']."' and unit is not null and dibayar<(nilaiinvoice+nilaippn)";
$qUnit=  mysql_query($sUnit) or die(mysql_error($conn));
while($rUnit=  mysql_fetch_assoc($qUnit)){
    $whpt="kodeorganisasi='".$rUnit['unit']."'";
    $optNmOrg=  makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whpt);
    $optgudang.="<option value='".$rUnit['unit']."'>".$rUnit['unit']."-".$optNmOrg[$rUnit['unit']]."</option>";
}
$optper="<option value=''>".$_SESSION['lang']['all']."</option>";
//$res=mysql_query($str);
//while($bar=mysql_fetch_object($res))
//{
//#	$optgudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
//
//}
$dayYear="01-01-".date('Y');
$arrPil=array("0"=>"Total","1"=>"Detail");
foreach($arrPil as $lstPil=>$nmPil){
    $optPil.="<option value='".$lstPil."'>".$nmPil."</option>";
}
echo"<fieldset style='float:left;'>
    <legend><b>".strtoupper($_SESSION['lang']['usiahutang'])."</b></legend>
	 ".$_SESSION['lang']['pt']." : "."<select id=pt style='width:200px;'  onchange=ambilAnak(this.options[this.selectedIndex].value)>".$optpt."</select>
	 ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select>
         ".$_SESSION['lang'][''].""."<select id=pilDt style='width:100px;' hidden>".$optPil."</select><br>
            Cari mulai tanggal <input type=\"text\" value=\"".$dayYear."\" class=\"myinputtext\" id=\"tanggal\" name=\"tanggal\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" /><br>
            Tanggal Cetak s/d <input type=\"text\" value=\"".$tanggalpivot=date('d-m-Y')."\" class=\"myinputtext\" id=\"tanggalpivot\" name=\"tanggalpivot\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" />
	 &nbsp;&nbsp;&nbsp;&nbsp;<button class=mybutton onclick=getUsiaHutang()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
echo"<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['catatan']."</b></legend>
    <table>
        <tr>
            <td>Pencarian akan berdasarkan Tanggal Invoice Diterima<br>
            Tanggal Cetak akan mengacu pada Tanggal Pembuatan VP dan Tanggal Pembayaran</td>
        </tr>
    </table></fieldset>";
CLOSE_BOX();
//			  <td rowspan=2 align=center width=60>".$_SESSION['lang']['nilaiinvoice']."</td>
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
     <img onclick=fisikKeExcel(event,'keu_laporanUsiaHutang_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=fisikKePDF(event,'keu_laporanUsiaHutang_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span>    
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0>
	     <thead>
		    <tr>
			  <td rowspan=2 align=center width=50>".$_SESSION['lang']['nourut']."</td>
			  <td rowspan=2 align=center width=50>".$_SESSION['lang']['tanggal']."</td>
			  <td rowspan=2 align=center width=200>".$_SESSION['lang']['noinvoice']."</td>
			  <td rowspan=2 align=center width=200>".$_SESSION['lang']['namasupplier']."</td>
			  <td rowspan=2 align=center width=75>".$_SESSION['lang']['novp']."</td>
			  <td rowspan=2 align=center width=75>".$_SESSION['lang']['matauang']."</td>
                          <td rowspan=2 align=center width=75>".$_SESSION['lang']['kurs']."</td>
                          <td rowspan=2 align=center width=75>".$_SESSION['lang']['jatuhtempo']."</td>
			  <td rowspan=2 align=center width=75>".$_SESSION['lang']['nopokontrak']."</td>
			  <td rowspan=2 align=center width=75>".$_SESSION['lang']['nilaipokontrak']."</td>
			  <td rowspan=2 align=center width=75>".$_SESSION['lang']['nilaivp']."</td>
			  <td rowspan=2 align=center width=75>".$_SESSION['lang']['nilaivp']." ".$_SESSION['lang']['rp']."</td>
			  <td rowspan=2 align=center width=100>".$_SESSION['lang']['belumjatuhtempo']."</td>
			  <td align=center colspan=4 width=400>".$_SESSION['lang']['sudahjatuhtempo']."</td>
			  <td rowspan=2 align=center width=100>".$_SESSION['lang']['dibayar']."</td>
			  <td rowspan=2 align=center width=50>".$_SESSION['lang']['jmlh_hari_outstanding']."</td>
			</tr>  
		    <tr>
			  <td align=center width=50>1-30 ".$_SESSION['lang']['hari']."</td>
			  <td align=center width=50>31-60 ".$_SESSION['lang']['hari']."</td>
			  <td align=center width=50>61-90 ".$_SESSION['lang']['hari']."</td>
			  <td align=center width=50>over 90 ".$_SESSION['lang']['hari']."</td>
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