<?php //@Copy nangkoelframework
// dhyaz aug 10, 2011
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
echo open_body();
?>
<script language=javascript1.2 src='js/budget_5hargabarang.js'></script>
<?php
include('master_mainMenu.php');
//pilihan new / list
OPEN_BOX('',$_SESSION['lang']['budget']." ".$_SESSION['lang']['material']);
echo"<table>
     <tr valign=middle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayFormInput()>
	   <img class=delliconBig src=images/user_add.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
     </tr>
     </table>"; 
CLOSE_BOX();
OPEN_BOX('','');
//ambil PT yang ada di masterbarangdt
$optpt='';
$str="select distinct kodeorg from ".$dbname.".log_5masterbarangdt 
      order by kodeorg";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $optpt.="<option value='".$bar->kodeorg."'>".$bar->kodeorg."</option>";	
}
//ambil kelompok barang 
$optkl='';
$str="select kode, kelompok from ".$dbname.".log_5klbarang 
      order by kode";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $optkl.="<option value='".$bar->kode."'>".$bar->kode." - ".$bar->kelompok."</option>";	
}
//ambil regional 
$optreg='';
$str="select regional, nama from ".$dbname.".bgt_regional 
      order by regional";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $optreg.="<option value='".$bar->regional."'>".$bar->regional." - ".$bar->nama."</option>";	
}
        
//form input
echo"<div id='frminput' style='display:none;'>";
    echo"<fieldset><legend id=legendinput name=legendinput>New</legend>";
    echo"<table><tr>";
    echo "<tr><td>".$_SESSION['lang']['budgetyear']."</td><td><input onkeyup=\"resetcontainer();\" type=text id=tahunbudget size=4 maxlength=4 class=myinputtext onkeypress=\"return angka_doang(event);\"></td></tr>";
    echo "<tr><td>".$_SESSION['lang']['regional']."</td><td><select onchange=\"resetcontainer();\" id=regional style='width:150px'><option value=''>".$optreg."</select></td></tr>";
    echo "<tr><td>".$_SESSION['lang']['sumberHarga']."</td><td><select onchange=\"resetcontainer();\" id=sumberharga style='width:150px'><option value=''></option>".$optreg."</select></td></tr>";
    echo "<tr><td>".$_SESSION['lang']['kelompokbarang']."</td><td><select onchange=\"resetcontainer();\" id=kelompokbarang style='width:150px'><option value=''>".$optkl."</select></td></tr>";
    echo"<tr><td></td><td><button id= buttonproses class=mybutton onclick=tampolHarga()>".$_SESSION['lang']['proses']."</button>
        <input type=\"hidden\" id=\"hiddenprocess\" name=\"hiddenprocess\" value=\"\" />
        </td></tr></table>";
    echo"</fieldset>";

//list input, printPanel ga dipake, container
echo"<span id=printPanel style='display:none;'>
     </span>    
     <div id=container style='width:100%;height:359px;overflow:scroll;'>
     </div>";
echo"</div>";

//form list
echo"<div id='frmlist' style='display:none;'>";
echo"<fieldset style='float:left;'><legend>".$_SESSION['lang']['list']."</legend>";
echo"<table class=sortable cellspacing=1 border=0>	     
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['nomor']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['regional']."</td>";
       echo"<td align=center>".$_SESSION['lang']['list']."</td>
            <td align=center>".$_SESSION['lang']['delete']."</td>
            <td align=center>Excel</td>
            <td align=center>".$_SESSION['lang']['edit']."</td>
            <td align=center>".$_SESSION['lang']['close']."</td>
	</tr>
     </thead>
     <tbody id=container3>";
echo"</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
echo"Klik <b>".$_SESSION['lang']['list']."</b> untuk <b>".$_SESSION['lang']['close']."</b>";
echo"</fieldset>";
echo"<fieldset style='float:left;'><legend>".$_SESSION['lang']['input']."</legend>";
    echo"<table><tr>";
    echo "<tr><td>".$_SESSION['lang']['budgetyear']."</td><td><input type=text id=tahunbudget1 size=4 maxlength=4 class=myinputtext onkeypress=\"return angka_doang(event);\"></td></tr>";
    echo "<tr><td>".$_SESSION['lang']['regional']."</td><td><select id=regional1 style='width:150px'><option value=''>".$optreg."</select></td></tr>";
    echo "<tr><td>".$_SESSION['lang']['kodebarang']."</td><td>
        <input type=text class=myinputtext id=kodebarang1 name=kodebarang1 onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:150px;/>
        <input type=\"image\" id=search1 src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg(1,'".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg value=".$kodebarang1."><button class=mybutton onclick=findBrg(1)>Find</button></fieldset><div id=containerq></div><input type=hidden id=nomor name=nomor value=".$key."><input type=hidden id=regbrg value=0 /><input type=hidden id=thnbgtbrg  value=0 />',event)\";>
        <label id=namabarang1></label><label id=satuan1></label>
        </td></tr>";
    echo "<tr><td>".$_SESSION['lang']['hargasatuan']."</td><td><input type=text id=hargasatuan1 size=20 maxlength=10 class=myinputtextnumber onkeypress=\"return angka_doang(event);\"></td></tr>";
    echo"<tr><td></td><td><button disabled=true id=buttonedit class=mybutton onclick=editHarga()>".$_SESSION['lang']['save']."</button>
        <input type=\"hidden\" id=\"hiddenedit\" name=\"hiddenedit\" value=\"\" />
        </td></tr></table>";
echo"</fieldset><br>";

//list list, printPanel2 dipake buat nampilin list Excel, container3
//     <img onclick=jurnalv1KePDF(event,'keu_laporanBukuBesarv1_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
//     <img onclick=hargaKeExcel(event,'budget_5hargabarang_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
echo"<span id=printPanel2 style='display:none;'>
     </span>    
     <div style='width:100%;height:359px;overflow:scroll;'>
     <table class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['nomor']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['regional']."</td>
            <td align=center>".$_SESSION['lang']['kodebarang']."</td>
            <td align=center>".$_SESSION['lang']['namabarang']."</td>
            <td align=center>".$_SESSION['lang']['satuan']."</td>
            <td align=center>".$_SESSION['lang']['sumberHarga']."</td>
            <td align=center>".$_SESSION['lang']['hargatahunlalu']."</td>
            <td align=center>".$_SESSION['lang']['varian']."</td>
            <td align=center>".$_SESSION['lang']['hargabudget']."</td>
	</tr>  
     </thead>
     <tbody id=container2>
     </tbody>
     <tfoot>
     </tfoot>		 
     </table>
     </div>";
echo"</div>";
CLOSE_BOX();
close_body('');
?>

