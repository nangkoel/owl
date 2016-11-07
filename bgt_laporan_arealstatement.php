<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
// dz, sep 26 2011
?>
<script language=javascript1.2 src="js/bgt_laporan_arealstatement.js"></script>
<?php
include('master_mainMenu.php');
//OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['laporanbukubesar']).'</b>');
OPEN_BOX();

        $str="select distinct tahunbudget from ".$dbname.".bgt_blok
                  order by tahunbudget desc";
        $res=mysql_query($str);
        $opttahun="";
        while($bar=mysql_fetch_object($res))
        {
                $opttahun.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";

        }


//if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
//{   
        //=================ambil KEBUN;  
        $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
              where tipe='KEBUN'
                  order by namaorganisasi";
        $res=mysql_query($str);
        $optkebun="";
        while($bar=mysql_fetch_object($res))
        {
                $optkebun.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

        }
//}
//else
//{
//        $optkebun="";
//         $optkebun.="<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas']."</option>";
//   
//}
?>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['arealstatement']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['budgetyear']?></label></td><td><select id=tahun style='width:200px;' onchange=hideById('printPanel')><?php echo $opttahun; ?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['kodeorganisasi']?></label></td><td><select id=kebun style='width:200px;' onchange=hideById('printPanel')><?php echo $optkebun; ?></select></td></tr>
<tr><td></td><td><button class=mybutton onclick=getAreal()><?php echo $_SESSION['lang']['proses'] ?></button></td></tr>

<!--<tr height="20"><td colspan="2">&nbsp;</td></tr>-->

<!--<tr><td colspan="2"><button onclick="zPreview('sdm_slave_2rekapabsen','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('sdm_slave_2rekapabsen','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'sdm_slave_2rekapabsen.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button><button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>-->

</table>
</fieldset>
<?php
CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
     <img onclick=arealKeExcel(event,'bgt_slave_laporan_arealstatement_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
     <img onclick=arealKePDF(event,'bgt_slave_laporan_arealstatement_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span>    
	 <div id=container style='width:100%;height:359px;overflow:scroll;'>

     </div>";
CLOSE_BOX();
close_body();
?>