<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
 
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/sdm_daftartenagakerja.js'></script> 
<link rel=stylesheet type='text/css' href='style/zTable.css'>
<?php
$arr="##notransaksi##kodeorg##penempatan##departemen##tanggal##tgldibutuhkan##kotapenempatan##pendidikan##jurusan##pengalaman##kompetensi##deskpekerjaan##maxumur##persetujuan1##persetujuan2##persetujuanhrd##proses";
include('master_mainMenu.php');

OPEN_BOX();
$optthn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sperd="select distinct left(tanggal,4) as thn from ".$dbname.".sdm_permintaansdm order by tanggal desc";
$qperd=mysql_query($sperd) or die(mysql_error($conn));
while($rperd=  mysql_fetch_assoc($qperd)){
     
    $optthn.="<option value='".$rperd['thn']."'>".$rperd['thn']."</option>";
}
echo"<fieldset style=float:left><legend>Sort Data</legend>
    ".$_SESSION['lang']['tahun']." : <select id=thnPeriode onchange=loadData(0)>".$optthn."</select></fieldset>
        <fieldset style=float:left>
        <legend><b><img src=images/info.png align=left height=25px valign=asmiddle>[Info]</b></legend>
         Tanggal Confirm Merupakan Tanggal Akhir display lowongan pada website karir.minanga.co.id. 
        </fieldset>		 

    <div style='clear:both'></div>    
    <fieldset style=float:left><legend><b>".$_SESSION['lang']['list']."</b></legend><div id=containerData>
         <script>loadData()</script>
         </div>
         </filedset>";
CLOSE_BOX();
echo close_body();
?>