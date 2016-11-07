<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/hr.js></script>
<script language=javascript1.2 src=js/blocker.js></script>
<script language=javascript1.2 src=js/generic.exe></script>
<?php
include('master_mainMenu.php');

$str="select * from ".$dbname.".msdivisi order by UNITCODE";
$res=mysql_query($str);
$stg="select * from ".$dbname.".msunit order by UNITCODE";
$reg=mysql_query($stg);
$opt_unit="<option value='0'>Silakan Pilih...</option>";
	/*while($bag=mysql_fetch_object($reg))
		{
			$opt_unit.="<option value='".$bag->UNITCODE."'>".$bag->UNITNAME."</option>";
		}*/
$xcv="select * from ".$dbname.".mscompany order by WILCODE";
$rex=mysql_query($xcv);
$opt_comp="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_object($rex))
		{
			$opt_comp.="<option value='".$bag->COMPCODE."'>".$bag->COMPNAME."</option>";
		}
OPEN_BOX('');
echo OPEN_THEME('Master Data Afdeling :');
echo"<fieldset>
     <legend>
	 <img src=images/info.png align=left height=35px valign=asmiddle>
	 </legend>
	 <b>Data Afdeling</b> Yang Sudah berhasil Disimpan Tidak Dapat Dihapus,<br>Harap Berhati2 dalam Melakukan Penambahan Data.Hanya Perubahan Data yang Diperkenankan.
     </fieldset>";
echo"<br>
    <input type=hidden value=simpan id=idx>
	<span id=note style='font-weight:bolder;'>Simpan</span> Afdeling :
    <br><br>
	<dd>Perusahaan &nbsp;&nbsp;&nbsp;
	: &nbsp;<select id=company onchange=loaX(this.options[selectedIndex].value,'Company');>".$opt_comp."</select><br><br>
	Unit/Estate &nbsp;&nbsp;&nbsp;&nbsp;
	: &nbsp;<select id=unit>".$opt_unit."</select><br><br>
	Kode Afdeling &nbsp;: &nbsp;<input type=text class=myinputtext size=6 maxlength=6 id=newTitle style='text-align:center' onkeypress='return charAndNum(event);'><br><br>
    Nama Afdeling
    : &nbsp;<input type=text class=myinputtext size=40 maxlength=40 id=name>
	<button class=mybutton onclick=saveAfdeling()>
	 Simpan
	 </button>
	<button class=mybutton onclick=clearAfdeling()>
	 Batal
	 </button>
	 </dd>
	 <hr>";
echo"
<form onSubmit=cari()>
List Data Afdeling:
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit class=mybutton2 value=Cari>
</form>
     <span id=result>
     <div style='height:170px;overflow:auto'>
     <table width=600px class=sortable border=0 cellspacing=1>
     <thead>
	 <tr class=rowheader>
	 <td align=center>No.</td>
	 <td>Perusahaan</td>
	 <td>Unit</td>
	 <td>Kode Afdeling</td>
	 <td>Nama Afdeling</td>
	 <td>Edit</td></tr>
	 </thead>
	 <tbody>";
$no=0;
while($bar=mysql_fetch_object($res))
 {  $no+=1;
    $ter="select UNITNAME from ".$dbname.".msunit where UNITCODE='".$bar->UNITCODE."' order by WILCODE";
 	$se=mysql_query($ter);
 	while($ccc=mysql_fetch_object($se)){
 		$unitname=$ccc->UNITNAME;
 	}
 	$txc="select COMPNAME from ".$dbname.".mscompany where COMPCODE='".$bar->COMPCODE."' order by COMPCODE";
 	$seh=mysql_query($txc);
 	while($sss=mysql_fetch_object($seh)){
 		$compname=$sss->COMPNAME;
 	}
 	echo "<tr class=rowcontentClick>
	      <td class=firsttd align=center>".$no."</td>
		  <td align=center>".$compname."</td>
		  <td align=center>".$unitname."</td>
		  <td align=center>".$bar->DIVCODE."</td>
		  <td align=center>".$bar->DIVNAME."</td>
		  <td align=center><img title='Click to edit' class=editbtn src=images/edit.png  onclick=\"changeAfdeling('".$bar->COMPCODE."','".$compname."','".$bar->UNITCODE."','".$unitname."','".$bar->DIVCODE."','".$bar->DIVNAME."');\"></td>
		  </tr>
		 ";
 }

echo"</tbody>
     </table>
     </div>
	 </span>";

echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();
?>
