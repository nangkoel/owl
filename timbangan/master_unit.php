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

//$str="select * from ".$dbname.".bagian order by kd_seksi";
$str="select * from ".$dbname.".msunit order by WILCODE";
$res=mysql_query($str);
$stg="select * from ".$dbname.".mswilayah order by WILCODE";
$reg=mysql_query($stg);
$opt_wil="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_object($reg))
		{
			$opt_wil.="<option value='".$bag->WILCODE."'>".$bag->WILNAME."</option>";
		}
$xcv="select * from ".$dbname.".mscompany order by WILCODE";
$rex=mysql_query($xcv);
$opt_comp="<option value='0'>Silakan Pilih...</option>";
	/*while($bag=mysql_fetch_object($rex))
		{
			$opt_comp.="<option value='".$bag->COMPCODE."'>".$bag->COMPNAME."</option>";
		}*/
OPEN_BOX('');
echo OPEN_THEME('Master Data Unit/Estate :');
echo"<fieldset>
     <legend>
	 <img src=images/info.png align=left height=35px valign=asmiddle>
	 </legend>
	 <b>Data Unit/Estate</b> Yang Sudah berhasil Disimpan Tidak Dapat Dihapus,<br>Harap Berhati2 dalam Melakukan Penambahan Data.Hanya Perubahan Data yang Diperkenankan.
     </fieldset>";
echo"<br>
    <input type=hidden value=simpan id=idx>
	<span id=note style='font-weight:bolder;'>Simpan</span> Unit/Estate :
    <br><br>
	<dd>Wilayah &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	: &nbsp;<select id=wilayah onchange=loaW(this.options[selectedIndex].value,'wilayah');>".$opt_wil."</select><br><br>
	Perusahaan &nbsp;&nbsp;&nbsp;&nbsp;
	: &nbsp;<select id=company>".$opt_comp."</select><br><br>
	Kode Unit &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	: &nbsp;<input type=text class=myinputtext size=4 maxlength=4 id=newTitle style='text-align:center' onkeypress='return charAndNum(event);'><br><br>
    Nama Unit &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    : &nbsp;<input type=text class=myinputtext size=40 maxlength=40 id=name>
	<button class=mybutton onclick=saveUnit()>
	 Simpan
	 </button>
	<button class=mybutton onclick=clearUnit()>
	 Batal
	 </button>
	 </dd>
	 <hr>";
echo"
<form onSubmit=cari()>
List Data Unit/Estate:
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit class=mybutton2 value=Cari>
</form>
     <span id=result>
     <div style='height:170px;overflow:auto'>
     <table width=500px class=sortable border=0 cellspacing=1 >
     <thead>
	 <tr class=rowheader>
	 <td align=center>No.</td>
	 <td>Wilayah</td>
	 <td>Perusahaan</td>
	 <td>Kode Unit</td>
	 <td>Nama Unit</td>
	 <td>Edit</td></tr>
	 </thead>
	 <tbody>";
$no=0;
while($bar=mysql_fetch_object($res))
 {  $no+=1;
    $ter="select WILNAME from ".$dbname.".mswilayah where WILCODE='".$bar->WILCODE."' order by WILCODE";
 	$se=mysql_query($ter);
 	while($ccc=mysql_fetch_object($se)){
 		$wilname=$ccc->WILNAME;
 	}
 	$txc="select COMPNAME from ".$dbname.".mscompany where COMPCODE='".$bar->COMPCODE."' order by COMPCODE";
 	$seh=mysql_query($txc);
 	while($sss=mysql_fetch_object($seh)){
 		$compname=$sss->COMPNAME;
 	}
 	echo "<tr class=rowcontentClick>
	      <td class=firsttd align=center>".$no."</td>
		  <td align=center>".$wilname."</td>
		  <td align=center>".$compname."</td>
		  <td align=center>".$bar->UNITCODE."</td>
		  <td align=center>".$bar->UNITNAME."</td>
		  <td align=center><img title='Click to edit' class=editbtn src=images/edit.png  onclick=\"changeUnit('".$bar->COMPCODE."','".$compname."','".$bar->WILCODE."','".$wilname."','".$bar->UNITCODE."','".$bar->UNITNAME."');\"></td>
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
