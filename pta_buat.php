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
<script languange=javascript1.2 src='js/pta.js'></script>
<script>
    tolak="<?php echo $_SESSION['lang']['ditolak'];?>";
    ajukan="<?php echo $_SESSION['lang']['diajukan'];?>";
    setujuak="<?php echo $_SESSION['lang']['setujuakhir'];?>";
    </script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
$pta = "PTA".$_SESSION['empl']['lokasitugas'].date('Ymd');

$sCek="select distinct notransaksi from ".$dbname.".pta_ht where notransaksi='".$pta."'";
$qCek=mysql_query($sCek) or die(mysql_error($conn));

$optAkun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if($_SESSION['language']=='EN'){
    $dd='namaakun1 as namaakun';
}else{
    $dd='namaakun as namaakun';
}
$sAkun="select distinct  noakun,".$dd." from ".$dbname.".keu_5akun where detail=1 order by noakun asc";
$qAkun=mysql_query($sAkun) or die(mysql_error($conn));
while($rAkun=mysql_fetch_assoc($qAkun))
{
    $optAkun.="<option value='".$rAkun['noakun']."'>".$rAkun['noakun']." - ".$rAkun['namaakun']."</option>";
}

$optKeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";


$optAlokasi="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sAlokasi="select  kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi asc";
$qAlokasi=mysql_query($sAlokasi) or die(mysql_error($conn));
while($rAlokasi=mysql_fetch_assoc($qAlokasi))
{
    $optAlokasi.="<option value='".$rAlokasi['kodeorganisasi']."'>".$rAlokasi['kodeorganisasi']."-".$rAlokasi['namaorganisasi']."</option>";
}

$optVhc="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sVhc="select distinct kodevhc from ".$dbname.".vhc_5master order by kodevhc";
$qVhc=mysql_query($sVhc) or die(mysql_error($conn));
while($rVhc=mysql_fetch_assoc($qVhc))
{
    $optVhc.="<option value='".$rVhc['kodevhc']."'>".$rVhc['kodevhc']."</option>";
}

$frm[0]='';
$frm[1]='';

$frm[0].=
"<div id=header>
  <fieldset>
    <legend>Header</legend>
    <table cellspacing=1 border=0>
        <tr>
            <td width=150>".$_SESSION['lang']['notransaksi']."</td><td>:</td>
            <td><input disabled type='text' class='myinputtext' id='nopta'  size=10 maxlength=30 style='width:150px;' value='$pta'/></td>
            <td width=150>".$_SESSION['lang']['penjelasan']."</td><td>:</td>
            <td rowspan=3 colspan=2 width=260><textarea id='penjelasan' onkeypress='return tanpa_kutip();' rows=3 cols=30/></textarea></td>
        </tr>
        <tr>
            <td td width=150>".$_SESSION['lang']['namakelompok']."</td><td>:</td>
            <td><input disabled type='text' class='myinputtext' id='kelompok' onkeypress='return tanpa_kutip();' size=10 maxlength=10 style='width:150px;' value=".$_SESSION['empl']['tipelokasitugas']." \></td>  
        </tr>
        <tr>
            <td td width=150>".$_SESSION['lang']['tanggal']."</td><td>:</td>
            <td><input type='text' class='myinputtext' id='tgl' name='tgl' onmousemove='setCalendar(this.id);' onkeypress='return false;'  maxlength=10 style='width:150px;' />
        </tr>
    </table>
</fieldset>
</div>";

$frm[0].=
"<div id='detail'>
  <fieldset>
    <legend>Detail</legend>
    <table cellspacing=1 border=0>
        <tr>
            <td width=150>".$_SESSION['lang']['tipe']." PTA</td><td>:</td>";
            // get tipe pta enum
            $optTipe="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $arrTipe=getEnum($dbname,'pta_dt','tipepta');
            foreach($arrTipe as $tipe=>$pta)
            {
                    $optTipe.="<option value='".$tipe."'>".$pta."</option>";
            }  
            
     #=========================================
     #untuk mengaktifkan semua pilihan silahkan buka ini
     #saat ini kebijakan PTA hanya untuk kapital saja
     $optTipe="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";       
     $optTipe.="<option value='KAPITAL'>CAPITAL</option>";       
     #=========================================       
$frm[0].=
            "<td width=150><select id='tipe_pta' style='width:150px;'>$optTipe</select>
            </td>
            <td width=150>".$_SESSION['lang']['volume']." ".$_SESSION['lang']['pekerjaan']."</td><td>:</td>
            <td><input type='text' class='myinputtext' id='vol_pekerjaan' onkeypress='return tanpa_kutip();' size=10 maxlength=10 style='width:150px;' value=0></td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['kelompok']." PTA</td><td>:</td>";
            // get jenis pta enum
            $optJn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $arrJn=getEnum($dbname,'pta_dt','jenispta');
            foreach($arrJn as $jenis=>$pta)
            {
                    $optJn.="<option value='".$jenis."'>".$pta."</option>";
            }  
     #=========================================
     #untuk mengaktifkan semua pilihan silahkan buka ini
     #saat ini kebijakan PTA hanya untuk MATERIAL saja 
     $optJn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";       
     $optJn.="<option value='MATERIAL'>MATERIAL</option>";       
     #=========================================                
                        
$frm[0].=
            "<td><select id='jenis_pta' onchange='kunciForm()' style='width:150px;'>$optJn</select>
            </td>
            <td width=150>".$_SESSION['lang']['satuan']." Volume</td><td>:</td>
            <td><input type='text' class='myinputtext' id='satuan_vol' size=10 maxlength=10 style='width:150px;' /></td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['noakun']."</td><td>:</td>
            <td><select id=noakunData style='width:150px;' onchange=getKegiatan()>$optAkun</select></td>
            <td width=150>".$_SESSION['lang']['jumlah']."</td><td>:</td>
            <td><input type='text' class='myinputtext' id='jml' onkeypress='return tanpa_kutip();' size=10 maxlength=10 style='width:150px;' value=0></td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['kegiatan']."</td><td>:</td>
            <td><select id=kegId onchange='createNew()' style='width:150px;'>$optKeg</select></td>
            <td width=150>".$_SESSION['lang']['satuan']." Amount</td><td>:</td>
            <td><input type='text' class='myinputtext' id='satuan_jml' onkeypress='return tanpa_kutip();' size=10 maxlength=10 style='width:150px;'></td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['alokasi']."</td><td>:</td>
            <td><select id=alokasi style='width:150px;'>$optAlokasi</select></td>
            <td width=150>".$_SESSION['lang']['total']."(Rp)</td><td>:</td>
            <td><input type='text' class='myinputtext' id='jml_rp' onkeypress='return tanpa_kutip();' size=10 maxlength=10 style='width:150px;'  value=0></td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['kodevhc']."</td><td>:</td>
            <td><select id='kode_vhc' style='width:150px;'>$optVhc</select></td>
            <td colspan=3></td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['kodebarang']."</td><td>:</td>
            <td><input readonly type='text' class='myinputtext' id='kdbrng' onkeypress='return tanpa_kutip();' size=10 maxlength=10 style='width:150px;'></td>
            <td colspan=3>
            <img src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";>
            </td>
        </tr>
        <tr>
            <td width=150>".$_SESSION['lang']['namabarang']."</td><td>:</td>
            <td><input readonly type='text' class='myinputtext' id='nmbrng' onkeypress='return tanpa_kutip();' size=10 maxlength=10 style='width:150px;' /></td>
        </tr>
        <tr>
        <td colspan=6 id='tmbl' align=center>
            <button class=mybutton id=saveForm onclick=saveForm()>".$_SESSION['lang']['save']."</button>
            <button class=mybutton id=cancelForm onclick=cancelForm()>".$_SESSION['lang']['cancel']."</button>
            <button class=mybutton id=ajukanForm onclick=showAjukan()>Ajukan</button>
        </td>
        </tr>
    </table>
</fieldset>
</div>";

$sLoad="select * from ".$dbname.".pta_dt 
        where unit='".$_SESSION['empl']['lokasitugas']."'";
$qLoad=mysql_query($sLoad) or die(mysql_error());
$frm[0].=  
"<div>
  <fieldset>    
    <table cellspacing=1 border=0 class='sortable'>
      <thead><tr class=rowcontent>
            <td align=center>".$_SESSION['lang']['action']."</td>
            <td align=center>No.</td>
            <td align=center>".$_SESSION['lang']['tipe']." PTA</td>
            <td align=center>".$_SESSION['lang']['kelompok']." PTA</td>
            <td align=center>".$_SESSION['lang']['namaakun']."</td>
            <td align=center>".$_SESSION['lang']['kegiatan']."</td>
            <td align=center>Total (Rp)</td>
            <td align=center>".$_SESSION['lang']['alokasi']."</td>
            <td align=center>".$_SESSION['lang']['kodevhc']."</td>
            <td align=center>".$_SESSION['lang']['namabarang']."</td>
            <td align=center>".$_SESSION['lang']['volume']."</td>
            <td align=center>".$_SESSION['lang']['satuan']." volume</td>
            <td align=center>".$_SESSION['lang']['jumlah']."</td>
            <td align=center>".$_SESSION['lang']['satuan']." Amount</td>
            <td align=center>".$_SESSION['lang']['dibuatoleh']."</td>
         </tr></thead><tbody id=contain>";

$frm[0].="<script>loaddata()</script></tbody>
        </table>
    
</fieldset>
</div><input type=hidden id=method value=add />";

$frm[1].="<fieldset><legend>Daftar PTA</legend><div id=daftarData>";
$frm[1].="</div></fieldset>";

$hfrm[0]="Buat PTA";
$hfrm[1]="Daftar PTA";

drawTab('FRM',$hfrm,$frm,100,1000);
?>

<?php
    CLOSE_BOX();
?>
