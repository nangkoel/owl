<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script languange=javascript1.2 src='js/zSearch.js'></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<script languange=javascript1.2 src='js/formReport.js'></script>
<script languange=javascript1.2 src='js/zGrid.js'></script>
<script languange=javascript1.2 src='js/pmn_fakturPajak.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
$optkodept="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$s_kodept="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
$q_kodept=mysql_query($s_kodept) or die(mysql_error($conn));
while($r_kodept=mysql_fetch_assoc($q_kodept))
{
    $optkodept.="<option value='".$r_kodept['kodeorganisasi']."'>".$r_kodept['namaorganisasi']."</option>";
}

$optcust="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$s_cust="select kodecustomer,namacustomer,kodetimbangan from ".$dbname.".pmn_4customer order by namacustomer asc";
$q_cust=mysql_query($s_cust) or die(mysql_error($conn));
while($r_cust=mysql_fetch_assoc($q_cust))
{
    $optcust.="<option value='".$r_cust['kodecustomer']."'>".$r_cust['namacustomer']."</option>";
}

$optkomoditi="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$s_komoditi="select distinct a.kodebarang as kodebarang,b.namabarang as namabarang from ".$dbname.".pmn_kontrakjual a
             left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
             order by b.namabarang asc";
$q_komoditi=mysql_query($s_komoditi) or die(mysql_error($conn));
while($r_komoditi=mysql_fetch_assoc($q_komoditi))
{
    $optkomoditi.="<option value='".$r_komoditi['kodebarang']."'>".$r_komoditi['namabarang']."</option>";
}
$optkontrak="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$optjenispajak="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$s_jnspajak="select * from ".$dbname.".pmn_5fakturkode ";
$q_jnspajak=mysql_query($s_jnspajak) or die(mysql_error($conn));

while($r_jnspajak=mysql_fetch_assoc($q_jnspajak))
{
    $kodepajak=$r_jnspajak['kode'];
    if(strlen($kodepajak)<3){
            $jp="0".$kodepajak;
        }
        else{
            $jp=$kodepajak;
        }    
    $optjenispajak.="<option value='".$r_jnspajak['kode']."'>".$jp." - ".$r_jnspajak['nama']."</option>";
}

// get atas biaya enum
$optbiaya="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$arrbiaya=getEnum($dbname,'pmn_faktur','atasbiaya');
foreach($arrbiaya as $atasbiaya=>$faktur)
{
        $optbiaya.="<option value='".$atasbiaya."'>".$faktur."</option>";
}

$opt_ttd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$s_ttd="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where kodejabatan=11 ";
$q_ttd=mysql_query($s_ttd) or die(mysql_error($conn));
while($r_ttd=mysql_fetch_assoc($q_ttd))
{
    $opt_ttd.="<option value='".$r_ttd['karyawanid']."'>".$r_ttd['namakaryawan']."</option>";
}

$frm[0]='';
$frm[1]='';

$frm[0].=
"<div id=header>
  <fieldset>
    <legend>Form</legend>
    <table cellspacing=1 border=0>
        <tr>
            <td width=150>".$_SESSION['lang']['kodept']."</td><td>:</td>
            <td width=150><select id='kodept' style='width:150px;'>$optkodept</select></td>
            <td colspan=6></td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['Pembeli']."/".$_SESSION['lang']['penjual']."</td><td>:</td>
            <td width=150><select id='customer' style='width:150px;' onchange=timbangan()>$optcust</select></td>
            <td><input type='hidden' class='myinputtext' id='kodetimbangan' name='kodetimbangan' ></td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['tglfaktur']."</td><td>:</td>
            <td width=150><input type='text' class='myinputtext' id='tgl' name='tgl' onmousemove='setCalendar(this.id);' onkeypress='return false;'  maxlength=10 style='width:150px;' /></td>
            <td colspan=6></td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['komoditi']."</td><td>:</td>
            <td width=150><select id='komoditi' style='width:150px;' onchange=loadkontrak()>$optkomoditi</select></td>
            <td width=60 align=right>".$_SESSION['lang']['NoKontrak']."</td><td>:</td>
            <td width=150><select id='kontrak' style='width:150px;' onchange=loadcurr()>".$optkontrak."</select></td>
            <td width=60>".$_SESSION['lang']['curr']."</td><td>:</td>
            <td width=100><input type='text' class='myinputtext' id='curr' name='curr' 
                           onkeypress='return tanpa_kutip_dan_sepasi(event);' maxlength=10 style='width:100px;' /></td>
        <tr>
        <tr>
            <td width=150>".$_SESSION['lang']['kurs']."</td><td>:</td>
            <td width=150><input type='text' class='myinputtextnumber' id='kurs' name='kurs' 
                           onkeypress='return angka_doang(event);' maxlength=10 style='width:150px;' value=1 /></td>
            <td colspan=6></td>
        <tr>
        <tr>
            <td width=150>".$_SESSION['lang']['jenispajak']."</td><td>:</td>
            <td width=150><select id='jenispajak' style='width:150px;' onchange=loadfaktur()>$optjenispajak</select></td>
            <td width=60 align=right>".$_SESSION['lang']['nofaktur']."</td><td>:</td>
            <td width=150><input disabled type='text' class='myinputtext' id='nofaktur' name='nofaktur' 
                           onkeypress='return tanpa_kutip_dan_sepasi(event);' maxlength=10 style='width:150px;' /></td>
            <td width=60>".$_SESSION['lang']['timbangan']."</td><td>:</td>
            <td width=100>
                <select id='timbangan' style='width:100px;'>
                    <option value=''>".$_SESSION['lang']['pilihdata']."</option>
                    <option value='Sendiri'>Sendiri</option>
                    <option value='Pembeli'>Pembeli</option>
                </select>
            </td>
        <tr>
        <tr>
            <td width=150>".$_SESSION['lang']['atasbiaya']."</td><td>:</td>
            <td width=150><select id='biaya' style='width:150px;' >$optbiaya</select></td>
            <td width=60 align=right>".$_SESSION['lang']['dari']."</td><td>:</td>
            <td width=150><input type='text' class='myinputtext' id='dari' name='dari' onmousemove='setCalendar(this.id);'
                           onkeypress='return false;'  maxlength=10 style='width:150px;'/></td>
            <td width=60>".$_SESSION['lang']['sd']."</td><td>:</td>
            <td width=100><input type='text' class='myinputtext' id='sd' name='sd' onmousemove='setCalendar(this.id);' 
                           onkeypress='return false;'  maxlength=10 style='width:100px;' onchange=loadvol() /></td>
        <tr>
        <tr>
            <td width=150>".$_SESSION['lang']['valas']."</td><td>:</td>
            <td width=150><input type='text' class='myinputtextnumber' id='valas' name='valas' 
                           onkeypress='return angka_doang(event);' maxlength=10 style='width:150px;' value=0 
                           onkeyup=loadvol() /></td>
            <td width=60 align=right>".$_SESSION['lang']['volume']."</td><td>:</td>
            <td width=150><input align='right' type='text' class='myinputtextnumber' id='vol' name='vol' 
                           onkeypress='return angka_doang(event);' maxlength=10 style='width:150px;' /></td>
            <td width=100 colspan=3>".$_SESSION['lang']['kg']."</td>
        <tr>
        <tr>
            <td width=150>".$_SESSION['lang']['jumlah']."(Rp)</td><td>:</td>
            <td width=150><input align='right' type='text' class='myinputtextnumber' id='jml' name='jml' 
            onkeypress='return angka_doang(event);' maxlength=10 style='width:150px;'/></td>
            <td colspan=6></td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['potharga']."(Rp)</td><td>:</td>
            <td width=150><input type='text' class='myinputtextnumber' id='potharga' name='potharga' 
            onkeypress='return angka_doang(event);' maxlength=10 style='width:150px;'  
            onchange='this.value=remove_comma(this);this.value = _formatted(this)' onkeyup=fungsippn() /></td>
            <td colspan=6></td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['potuangmuka']."(Rp)</td><td>:</td>
            <td width=150><input type='text' class='myinputtextnumber' id='potum' name='potum' 
            onkeypress='return angka_doang(event);' maxlength=10 style='width:150px;' 
            onchange='this.value=remove_comma(this);this.value = _formatted(this)' onkeyup=fungsippn() /></td>
            <td colspan=6></td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['dasarpajak']."(Rp)</td><td>:</td>
            <td width=150><input type='text' class='myinputtextnumber' id='dasarpajak' name='dasarpajak' 
                           onkeypress='return angka_doang(event);' maxlength=10 style='width:150px;' /></td>
            <td width=100 align=right>".$_SESSION['lang']['persenppn']."</td><td>:</td>
            <td><input type='text' class='myinputtextnumber' id='persenppn' name='persenppn' 
                 onkeypress='return angka_doang(event);' maxlength=10 style='width:150px;' value=10 /></td>
            <td width=60>".$_SESSION['lang']['ppn']."(Rp)</td><td>:</td>
            <td><input disabled align='right' type='text' class='myinputtextnumber' id='ppn' name='ppn' 
                 onkeypress='return angka_doang(event);' maxlength=10 style='width:100px;' /></td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['penandatangan']."</td><td>:</td>
            <td width=150><select id='ttd' style='width:150px;'>$opt_ttd</select></td>
            <td colspan=6></td>
        </tr>
        <td colspan=9 id='tmbl' align=center>
            <button class=mybutton id=saveForm onclick=saveForm()>".$_SESSION['lang']['save']."</button>
            <button class=mybutton id=cancelForm onclick=cancelForm()>".$_SESSION['lang']['cancel']."</button>
        </td>
        </tr>
    </table>
</fieldset>
</div>";
#Daftar Faktur
$kontrak="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$bulan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$rekanan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$s_kontrak="select distinct nokontrak from ".$dbname.".pmn_faktur order by nokontrak asc";
$q_kontrak=mysql_query($s_kontrak) or die(mysql_error($conn));
while($r_kontrak=mysql_fetch_assoc($q_kontrak))
{
	$kontrak.="<option value=".$r_kontrak['nokontrak'].">".$r_kontrak['nokontrak']."</option>";
}
$s_partner="select distinct partner from ".$dbname.".pmn_faktur order by partner asc";
$q_partner=mysql_query($s_partner) or die(mysql_error($conn));
while($r_partner=mysql_fetch_assoc($q_partner))
{
        $rekanan.="<option value=".$r_partner['partner'].">".$r_partner['partner']."</option>";
}
$s_bln="select distinct substr(tanggalfaktur,1,7) as bulan from ".$dbname.".pmn_faktur order by tanggalfaktur asc";
$q_bln=mysql_query($s_bln) or die(mysql_error($conn));
while($r_bln=mysql_fetch_assoc($q_bln))
{
        $bulan.="<option value=".$r_bln['bulan'].">".$r_bln['bulan']."</option>";
}

$frm[1].="<fieldset><legend>Daftar Faktur</legend>";
$frm[1].=
    "<table cellspacing=1 border=0>
        <tr>
            <td>".$_SESSION['lang']['NoKontrak']."</td><td>:</td>
            <td><select id='nokontrak' style='width:150px;' onchange=daftarfaktur()>$kontrak</select></td>
            <td>".$_SESSION['lang']['bulan']."</td><td>:</td>
            <td><select id='bulan' style='width:150px;' onchange=daftarfaktur()>$bulan</select></td>
            <td>".$_SESSION['lang']['rekanan']."</td><td>:</td>
            <td><select id='rekanan' style='width:150px;' onchange=daftarfaktur()>$rekanan</select></td>
        </tr>
      </table>";

$frm[1].="<div id=isi><script>loaddata()</script></div>";
if($_SESSION['language']=='ID'){
    $hfrm[0]="Buat Faktur";
    $hfrm[1]="Daftar Faktur";
}else{
    $hfrm[0]=$_SESSION['lang']['baru'];
    $hfrm[1]=$_SESSION['lang']['list'];;    
}

drawTab('FRM',$hfrm,$frm,100,1000);
?>

<?php
    CLOSE_BOX();
?>
