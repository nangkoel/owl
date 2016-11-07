<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/keu_laporan.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX("","<b>".$_SESSION['lang']['daftarHutang']."/".$_SESSION['lang']['usiapiutang']."</b>");

//list akun
$str="select b.noakun, b.namaakun from  ".$dbname.".keu_5akun b 
      where detail=1 and (noakun like '113%' or noakun like '114%' or noakun like '211%' or noakun like '118%' 
      or noakun like '122%') order by b.noakun"; 
$res=mysql_query($str);
$optnoakun="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
        $optnoakun.="<option value='".$bar->noakun."'>".$bar->noakun." - ".$bar->namaakun."</option>";
}
//list org
$str="select kodeorganisasi, namaorganisasi from  ".$dbname.".organisasi 
      where length(kodeorganisasi)=3 order by kodeorganisasi
"; 
 $optorg.="<option value=''>".$_SESSION['lang']['all']."</option>";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
        $optorg.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi." - ".$bar->namaorganisasi."</option>";
}

//list karyawan
$str="select a.nik, b.namakaryawan from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".datakaryawan b on a.nik = b.karyawanid
      where a.kodeorg ='".$_SESSION['empl']['lokasitugas']."' and a.nik!='0'
      and a.nik != '' and a.noakun != '' group by a.nik order by b.namakaryawan
"; // hanya menampilkan nama yang ada di jurnal 
$res=mysql_query($str);
$optnamakaryawan="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
        $optnamakaryawan.="<option value='".$bar->nik."'>".$bar->namakaryawan."</option>";
}



echo"<fieldset>
     <legend>".$_SESSION['lang']['laporanjurnal']."</legend>
         ".$_SESSION['lang']['tanggalmulai']." : <input class=\"myinputtext\" id=\"tanggalmulai\" size=\"12\" onmousemove=\"setCalendar(this.id)\" maxlength=\"10\" onkeypress=\"return false;\" type=\"text\">
         s/d <input class=\"myinputtext\" id=\"tanggalsampai\" size=\"12\" onmousemove=\"setCalendar(this.id)\" maxlength=\"10\" onkeypress=\"return false;\" type=\"text\">
         ".$_SESSION['lang']['noakun']." <select id=noakun >".$optnoakun."</select>
         ".$_SESSION['lang']['kodeorg']." <select id=kodeorg >".$optorg."</select>    
         <button class=mybutton onclick=getLaporanJurnalPiutangKaryawan()>".$_SESSION['lang']['proses']."</button>
         </fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
     <img onclick=piutangKaryawanKeExcel(event,'keu_laporanJurnalPiutangKaryawan_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel (Header Only)'>
     <img onclick=piutangKaryawanKeExcel2(event,'keu_laporanJurnalPiutangKaryawan_Excel_v2.php') src=images/excel.jpg class=resicon title='MS.Excel (Detail)'>
         </span>    
         <div style='width:99.5%;display:fixed;'>
       <table class=sortable cellspacing=1 border=0 width=99.5%>
             <thead>
                    <tr class=rowcontent>
                          <td align=center width=30>".$_SESSION['lang']['nourut']."</td>
                          <td align=center width=60>".$_SESSION['lang']['organisasi']."</td>
                          <td align=center width=50>".$_SESSION['lang']['noakun']."</td>
                          <td align=center width=150>".$_SESSION['lang']['namaakun']."</td>
                          <td align=center width=150>".$_SESSION['lang']['karyawan']."/".$_SESSION['lang']['supplier']."</td>
                          <td align=center width=100>".$_SESSION['lang']['saldoawal']."</td>                             
                          <td align=center width=100>".$_SESSION['lang']['debet']."</td>
                          <td align=center width=100>".$_SESSION['lang']['kredit']."</td>
                          <td align=center width=100>".$_SESSION['lang']['saldoakhir']."</td>                               
                        </tr>
              </thead>
                 <tbody>
                 </tbody>
           </table>
     </div>
            <div id=container></div>";
CLOSE_BOX();
close_body();
?>