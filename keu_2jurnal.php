<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/keu_laporan.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['laporanjurnal']).'</b>');

//get existing period
$str="select distinct periode as periode from ".$dbname.".setup_periodeakuntansi
      order by periode desc";

$res=mysql_query($str);
//$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
while($bar=mysql_fetch_object($res))
{
        $optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{   
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
}
else
    if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{   
    //=================ambil PT;  
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
          where tipe='PT' and kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'
          order by namaorganisasi";
    $res=mysql_query($str);
    $optpt="";
    while($bar=mysql_fetch_object($res))
    {
        $optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
    }

    //=================ambil gudang;  
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL')  and induk='".$_SESSION['empl']['kodeorganisasi']."'
        ";
    $res=mysql_query($str);
    $optgudang="<option value=''>".$_SESSION['lang']['all']."</option>";
    while($bar=mysql_fetch_object($res))
    {
        $optgudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
    }
}else
{
    $optpt="";
    $optpt.="<option value='".$_SESSION['empl']['kodeorganisasi']."'>". $_SESSION['empl']['kodeorganisasi']."</option>";
    $optgudang.="<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas']."</option>";   
}   

//get revisi available
//$str="select distinct revisi from ".$dbname.".keu_jurnalht
//      order by revisi";	  
//$res=mysql_query($str);
//#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
//$optrev="";
//while($bar=mysql_fetch_object($res))
//{
for($mulaidrnol=0;$mulaidrnol<6;$mulaidrnol++){
    $optrev.="<option value='".$mulaidrnol."'>".$mulaidrnol."</option>";
}
   
//}	keu_slave_2globalfungsi

echo"<br><fieldset style='float:left;'>
     <legend>".$_SESSION['lang']['laporanjurnal']."</legend>
	 ".$_SESSION['lang']['pt']." : "."<select id=pt style='width:200px;'  onchange=ambilAnakBB(this.options[this.selectedIndex].value)>".$optpt."</select>
	 ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select>
	 ".$_SESSION['lang']['periode']." : "."<select id=periode onchange=hideById('printPanel')>".$optper."</select>
	  - "."<select id=periode1 onchange=hideById('printPanel')>".$optper."</select>             
	 ".$_SESSION['lang']['revisi']." : "."<select id=revisi onchange=getPeriodeRev()>".$optrev."</select>
	 <br><button class=mybutton onclick=getLaporanJurnal()>".$_SESSION['lang']['proses']."</button>
	 <button class=mybutton onclick=fisikKeExcel(event,'keu_laporanJurnal_Excel.php')>".$_SESSION['lang']['excel']."</button>
	 <button class=mybutton onclick=fisikKePDF(event,'keu_laporanJurnal_pdf.php')>".$_SESSION['lang']['pdf']."</button>
	 </fieldset>";
echo"<fieldset>
     <legend>".$_SESSION['lang']['catatan']."</legend>
         <b><i>* Jika dipilih SELURUHNYA maka periode akan mengambil tanggal menurut kalender dalam 1 bulan (1 s.d 30)
         <br>* Jika dipilih salah satu unit maka periode akan mengambil tanggal menurut periode akuntansinya</i></b>
	 </fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
     <img onclick=fisikKeExcel(event,'keu_laporanJurnal_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=fisikKePDF(event,'keu_laporanJurnal_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span>
	 <div style='width:1480px;display:fixed;'>
       <table class=sortable cellspacing=1 border=0 width=1480px>
	     <thead>
		    <tr>
                        <td align=center style='width:50px;'>".$_SESSION['lang']['nourut']."</td>
                        <td align=center style='width:250px;'>".$_SESSION['lang']['nojurnal']."</td>
						<td align=center style='width:250px;'>".$_SESSION['lang']['nodok']."</td>
                        <td align=center style='width:80px;'>".$_SESSION['lang']['tanggal']."</td>
                        <td align=center style='width:60px;'>".$_SESSION['lang']['organisasi']."</td>
                        <td align=center style='width:60px;'>".$_SESSION['lang']['noakun']."</td>
                        <td align=center style='width:200px;'>".$_SESSION['lang']['namaakun']."</td>
                        <td align=center  style='width:240px;'>".$_SESSION['lang']['keterangan']."</td>
                        <td align=center  style='width:100px;'>".$_SESSION['lang']['debet']."</td>
                        <td align=center style='width:100px;'>".$_SESSION['lang']['kredit']."</td>
                        <td align=center style='width:200px;'>".$_SESSION['lang']['noreferensi']."</td>    
                        <td align=center style='width:80px;'>".$_SESSION['lang']['kodeblok']."</td>
                        <td align=center style='width:60px;'>".$_SESSION['lang']['tahuntanam']."</td>
                        <td align=center style='width:30px;'>".$_SESSION['lang']['revisi']."</td>
		   </tr>  
		 </thead>
		 <tbody>
		 </tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>
     </div>         

	 <div style='width:1500px;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		  <tr>
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