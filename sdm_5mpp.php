<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
echo open_body();
?>
<script language=javascript1.2 src='js/sdm_5mpp.js'></script>
<?php
include('master_mainMenu.php');

OPEN_BOX('',"<b>".$_SESSION['lang']['mpp'].":</b>");

//get kodeorg
$optkodeorg="<option value=''></option>";
$optkodeorg.="<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas']."</option>";

//get bagian
$optbagian="<option value=''></option>";
$optbagian.="<option value='".$_SESSION['empl']['bagian']."'>".$_SESSION['empl']['bagian']."</option>";

//get golongan
$str="select * from ".$dbname.".sdm_5golongan order by kodegolongan";
$res=mysql_query($str);
$optgol="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
    $gol[$bar->kodegolongan]=$bar->namagolongan;
    $optgol.="<option value='".$bar->kodegolongan."'>".$bar->namagolongan."</option>";
}

//get jabatan
$str="select * from ".$dbname.".sdm_5jabatan order by kodejabatan";
$res=mysql_query($str);
$optjabatan="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
    $jab[$bar->kodejabatan]=$bar->namajabatan;
    $optjabatan.="<option value='".$bar->kodejabatan."'>".$bar->namajabatan."</option>";
}

//get jeniskelamin from enum
$optjeniskelamin="<option value=''></option>";
$arrenum=getEnum($dbname,'sdm_5mpp','jkelamin');
foreach($arrenum as $key=>$val)
{
    $optjeniskelamin.="<option value='".$key."'>".$val."</option>";
} 

//get pendidikan
$str="select * from ".$dbname.".sdm_5pendidikan order by idpendidikan";
$res=mysql_query($str);
$optpendidikan="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
    $optpendidikan.="<option value='".$bar->kelompok."'>".$bar->kelompok." ".$bar->pendidikan."</option>";
}

//get tahunbudget
$str="select distinct tahunbudget from ".$dbname.".sdm_5mpp order by tahunbudget desc";
$res=mysql_query($str);
$opttahun="<option value=''>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
    $opttahun.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
}

//            <td><input type=text class=myinputtext id=kodetraining name=kodetraining onkeypress=\"return tanpa_kutip();\" maxlength=30 style=\"width:150px;\" /></td>	

//echo "<pre>";
//print_r($_SESSION['empl']);
//echo "</pre>";

echo "<fieldset style='width:700px;'>
    <legend>".$_SESSION['lang']['form'].":</legend>
    <table><tr><td valign=top>
        <table cellspacing=1 border=0 width=700px>
        <tr>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td><input type=text class=myinputtextnumber id=tahunbudget name=tahunbudget onkeypress=\"return angka_doang(event);\" maxlength=4 style=\"width:150px;\"></td>	
            <td>".$_SESSION['lang']['min']." ".$_SESSION['lang']['umur']."</td>
            <td><input type=text class=myinputtextnumber id=minumur name=minumur onkeypress=\"return angka_doang(event);\" maxlength=3 style=\"width:150px;\"></td>	
        </tr>
        <tr> 
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td><select id=kodeorg name=kodeorg style=\"width:150px;\">".$optkodeorg."</select></td>	
            <td>".$_SESSION['lang']['max']." ".$_SESSION['lang']['umur']."</td>
            <td><input type=text class=myinputtextnumber id=maxumur name=maxumur onkeypress=\"return angka_doang(event);\" maxlength=3 style=\"width:150px;\"></td>	
        </tr>
        <tr>
            <td>".$_SESSION['lang']['bagian']."</td>
            <td><select id=bagian name=bagian style=\"width:150px;\">".$optbagian."</select></td>
            <td>".$_SESSION['lang']['jeniskelamin']."</td>
            <td><select id=jeniskelamin name=jeniskelamin style=\"width:150px;\">".$optjeniskelamin."</select></td>	
        </tr>
        <tr>
            <td>".$_SESSION['lang']['kodegolongan']."</td>
            <td><select id=golongan name=golongan style=\"width:150px;\">".$optgol."</select></td>
            <td>".$_SESSION['lang']['pendidikan']."</td>
            <td><select id=pendidikan name=pendidikan style=\"width:150px;\">".$optpendidikan."</select></td>	
        </tr>
        <tr>
            <td>".$_SESSION['lang']['jabatan']."</td>
            <td><select id=jabatan name=jabatan style=\"width:150px;\">".$optjabatan."</select></td>
            <td>".$_SESSION['lang']['pengalamankerja']."</td>
            <td><input type=text class=myinputtextnumber id=pengalaman name=pengalaman onkeypress=\"return angka_doang(event);\" maxlength=3 style=\"width:150px;\"> ".$_SESSION['lang']['tahun']."</td>	
        </tr>
        <tr>
            <td>".$_SESSION['lang']['min']." ".$_SESSION['lang']['gaji']."</td>
            <td><input type=text class=myinputtextnumber id=mingaji name=mingaji onkeypress=\"return angka_doang(event);\" maxlength=12 style=\"width:150px;\"></td>
            <td>".$_SESSION['lang']['poh']."</td>
            <td><input type=text class=myinputtext id=poh name=poh onkeypress=\"return tanpa_kutip();\" maxlength=30 style=\"width:150px;\" /></td>	
        </tr>
        <tr>
            <td>".$_SESSION['lang']['max']." ".$_SESSION['lang']['gaji']."</td>
            <td><input type=text class=myinputtextnumber id=maxgaji name=maxgaji onkeypress=\"return angka_doang(event);\" maxlength=12 style=\"width:150px;\"></td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td><input type=text class=myinputtextnumber id=jumlah name=jumlah onkeypress=\"return angka_doang(event);\" maxlength=5 style=\"width:150px;\"> ".$_SESSION['lang']['orang']."</td>	
        </tr>
        <tr>
            <td>".$_SESSION['lang']['tanggalmasuk']."</td>
            <td><input type=text class=myinputtext id=tanggalmasuk onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" style=\"width:150px;\"></td>
            <td><input type=hidden id=kunci name=kunci value='' /></td>
            <td></td>
        </tr>
        </table>	  
    </td>
    </tr>	  
    </table>	
    <center><button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>
    <button class=mybutton onclick=batal()>".$_SESSION['lang']['cancel']."</button></center>
    </fieldset>
	 ";
CLOSE_BOX();
 
OPEN_BOX();
echo "<fieldset><legend>".$_SESSION['lang']['list']."</legend>
     ".$_SESSION['lang']['budgetyear']." : <select onchange=displayList() id=listtahun name=listtahun>".$opttahun."</select>
     <input type=hidden id=pilihantahun name=pilihantahun value='' />
    <table class=sortable cellspacing=1 border=0 width=100%>
        <thead>
            <tr class=rowheader>
            <td align=center>".$_SESSION['lang']['nourut']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td align=center>".$_SESSION['lang']['bagian']."</td>
            <td align=center>".$_SESSION['lang']['kodegolongan']."</td>
            <td align=center>".$_SESSION['lang']['jabatan']."</td>
            <td align=center>".$_SESSION['lang']['min']." ".$_SESSION['lang']['gaji']."</td>
            <td align=center>".$_SESSION['lang']['max']." ".$_SESSION['lang']['gaji']."</td>
            <td align=center>".$_SESSION['lang']['tanggalmasuk']."</td>
            <td align=center>".$_SESSION['lang']['min']." ".$_SESSION['lang']['umur']."</td>
            <td align=center>".$_SESSION['lang']['max']." ".$_SESSION['lang']['umur']."</td>
            <td align=center>".$_SESSION['lang']['jeniskelamin']."</td>
            <td align=center>".$_SESSION['lang']['pendidikan']."</td>
            <td align=center>".$_SESSION['lang']['pengalamankerja']."</td>
            <td align=center>".$_SESSION['lang']['poh']."</td>
            <td align=center>".$_SESSION['lang']['jumlah']."</td>
            <td colspan=2 align=center>".$_SESSION['lang']['action']."</td>
            </tr>
        </thead>
	<tbody id=container>";
$str="select * from ".$dbname.".sdm_5mpp
      ";
$res=mysql_query($str);
$no=1;
while($bar=mysql_fetch_object($res))
{
    echo"<tr class=rowcontent>
    <td>".$no."</td>
    <td>".$bar->tahunbudget."</td>
    <td>".$bar->kodeorg."</td>
    <td>".$bar->departement."</td>
    <td>".$gol[$bar->golongan]."</td>
    <td>".$jab[$bar->jabatan]."</td>
    <td align=right>".number_format($bar->startgaji,2,'.',',')."</td>
    <td align=right>".number_format($bar->endgaji,2,'.',',')."</td>
    <td>".tanggalnormal($bar->tanggalmasuk)."</td>
    <td align=right>".$bar->startumur."</td>
    <td align=right>".$bar->endumur."</td>
    <td>".$bar->jkelamin."</td>
    <td>".$bar->pendidikan."</td>
    <td align=right>".$bar->pengalaman."</td>
    <td>".$bar->poh."</td>
    <td align=right>".$bar->jumlah."</td>

    <td>
        <img src=images/application/application_edit.png class=resicon  title='edit' onclick=\"edit('".$bar->tahunbudget."','".$bar->kodeorg."','".$bar->departement."','".$bar->golongan."','".$bar->jabatan."','".$bar->startgaji."','".$bar->endgaji."','".tanggalnormal($bar->tanggalmasuk)."',
        '".$bar->startumur."','".$bar->endumur."','".$bar->jkelamin."','".$bar->pendidikan."','".$bar->pengalaman."','".$bar->poh."','".$bar->jumlah."','".$bar->kunci."');\">
    </td>
    <td>
        <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"del('".$bar->kunci."');\">
    </td>
    </tr>";	
    $no+=1;
}	  
		
echo"	
        </tbody>
        <tfoot>
        </tfoot>
    </table>
    </fieldset>";
CLOSE_BOX();

close_body();
?>