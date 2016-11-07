<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/pad_pembebasan.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['pembebasan']." ".$_SESSION['lang']['lahan']);


$str="select distinct namadesa from ".$dbname.".pad_5desa order by namadesa";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $optdesa.="<option value='".$bar->namadesa."'>".$bar->namadesa."</option>";
}
$str="select distinct kecamatan from ".$dbname.".pad_5desa order by kecamatan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $optkecamatan.="<option value='".$bar->kecamatan."'>".$bar->kecamatan."</option>";
}
$str="select distinct kabupaten from ".$dbname.".pad_5desa order by kecamatan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $optkabupaten.="<option value='".$bar->kabupaten."'>".$bar->kabupaten."</option>";
}
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN','PABRIK') and kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi";
$res=mysql_query($str);
$optpad="<option value=''>".$_SESSION['lang']['pilih']."</option>";
while($bar=  mysql_fetch_object($res))
{
    $optpad.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}

echo"<fieldset>
    <table><tbody><tr><td>
    <fieldset><legend>1.".$_SESSION['lang']['namapemilik']." ".$_SESSION['lang']['lahan']."</legend>
            <table><tbody>
                <tr><td>".$_SESSION['lang']['id']."</td><td>
                         <input type=text id=mid class=myinputtext sise=4 disabled></td></tr>    
                <tr><td>".$_SESSION['lang']['kebun']."</td><td>
                         <select id='unit' onchange=updatePemilik(this.options[this.selectedIndex].value)>".$optpad."</select></td></tr>                    
                 <tr><td>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['namapemilik']." ".$_SESSION['lang']['lahan']."</td><td>
                         <select id=pemilik>".$optPemilik."</select></td></tr>
                <tr><td>(No.Persil)</td><td>
                         <input type=text id=lokasi  size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
                 <tr><td>".$_SESSION['lang']['luas']."</td><td>
                         <input type=text id=luas size=5 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber value=0>Ha.</td></tr>              
                 <tr><td>".$_SESSION['lang']['luas']." ".$_SESSION['lang']['bisaditanam']."</td><td>
                         <input type=text id=bisaditanam size=5 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber value=0>Ha.</td></tr>           
                   <tr><td>".$_SESSION['lang']['lokasi']." ".$_SESSION['lang']['kodeblok']."</td><td>
                         <select id=blok><option value=''>Undefined</option></select></td></tr>         
                <tr><td>".$_SESSION['lang']['batastimur']."</td><td>
                         <input type=text id=batastimur  size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
                <tr><td>".$_SESSION['lang']['batasbarat']."</td><td>
                         <input type=text id=batasbarat  size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
                <tr><td>".$_SESSION['lang']['batasutara']."</td><td>
                         <input type=text id=batasutara  size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
                <tr><td>".$_SESSION['lang']['batasselatan']."</td><td>
                         <input type=text id=batasselatan  size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
                 </tbody></table>
                 </fieldset>
         </td>
         <td style='vertical-align:top;'>
                <fieldset><legend>2.".$_SESSION['lang']['biaya']."-".$_SESSION['lang']['biaya']." dan ".$_SESSION['lang']['status']."-".$_SESSION['lang']['dokumen']."</legend>
                 <table>   
              <tr><td>".$_SESSION['lang']['biaya']." ".$_SESSION['lang']['tanamtumbuh']."</td><td>Rp.
                                       <input type=text id=rptanaman size=14 onkeypress=\"return angka_doang(event);\"  onblur=change_number(this) class=myinputtextnumber value=0></td></tr>                 
              <tr><td>".$_SESSION['lang']['biaya']." ".$_SESSION['lang']['gantilahan']."</td><td>Rp.
                                       <input type=text id=rptanah size=14 onkeypress=\"return angka_doang(event);\" onblur=change_number(this) class=myinputtextnumber value=0></td></tr>                 
              <tr><td>".$_SESSION['lang']['biaya']." ".$_SESSION['lang']['kepala']." ".$_SESSION['lang']['kepala']."</td><td>Rp.
                                       <input type=text id=biayakades size=14 onkeypress=\"return angka_doang(event);\" onblur=change_number(this) class=myinputtextnumber value=0></td></tr>                 
              <tr><td>".$_SESSION['lang']['biaya']." ".$_SESSION['lang']['camat']." </td><td>Rp.
                                       <input type=text id=biayacamat size=14 onkeypress=\"return angka_doang(event);\" onblur=change_number(this) class=myinputtextnumber value=0></td></tr>                 
              <tr><td>".$_SESSION['lang']['biaya']." Matrai </td><td>Rp.
                                       <input type=text id=biayamatrai size=14 onkeypress=\"return angka_doang(event);\" onblur=change_number(this) class=myinputtextnumber value=0></td></tr>                 
              <tr><td>".$_SESSION['lang']['status']." ".$_SESSION['lang']['permintaandana']." </td><td>
                                       <select id=statuspermintaandana style='width:90px;' onchange=changeTanggal('tanggalpermintaan',this.options[this.selectedIndex].value)><option value='0'>Belum Diajukan</option><option value='1'>Sudah Diajukan</option></select>
                                       ".$_SESSION['lang']['tanggal']." <input class=myinputtext id=tanggalpermintaan size=10 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" type=text></td></tr>                 
              <tr><td>".$_SESSION['lang']['status']." ".$_SESSION['lang']['pembayaran']." </td><td>
                                       <select id=statuspermbayaran style='width:90px;' onchange=changeTanggal('tanggalbayar',this.options[this.selectedIndex].value)><option value='0'>Belum Dibayar</option><option value='1'>Belum Lunas</option><option value='2'>Sudah Lunas</option></select>
                                       ".$_SESSION['lang']['tanggal']." <input class=myinputtext id=tanggalbayar size=10 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" type=text></td></tr>                 
          <tr><td>".$_SESSION['lang']['status']." ".$_SESSION['lang']['kepala']." ".$_SESSION['lang']['desa']."</td><td>
                                       <select id=statuskades style='width:90px;' onchange=changeTanggal('tanggalkades',this.options[this.selectedIndex].value)><option value='0'>Belum Selesai</option><option value='1'>Sudah Selesai</option></select>
                                       ".$_SESSION['lang']['tanggal']." <input class=myinputtext id=tanggalkades size=10 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" type=text> </td></tr>                 
             <tr><td>".$_SESSION['lang']['status']." ".$_SESSION['lang']['camat']." </td><td>
                                       <select id=statuscamat style='width:90px;' onchange=changeTanggal('tanggalcamat',this.options[this.selectedIndex].value)><option value='0'>Belum Selesai</option><option value='1'>Sudah Selesai</option></select>
                                       ".$_SESSION['lang']['tanggal']." <input class=myinputtext id=tanggalcamat size=10 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" type=text></td></tr>                 
              <tr><td>".$_SESSION['lang']['nomor']." ".$_SESSION['lang']['dokumen']."</td><td>
                         <input type=text id=nosurat  size=45 maxlength=100 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>      
             <tr><td>".$_SESSION['lang']['keterangan']."</td><td>
                             <input type=text id=keterangan  size=45 maxlength=100 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
                  </table>
            </fieldset>         
         </td>
         </tr>
        <tr><td colspan=2 align=center>
            <input type=hidden id=method value='insert'>
            <button class=mybutton onclick=simpanJabatan()>".$_SESSION['lang']['save']."</button>
            <button class=mybutton onclick=cancelJabatan()>".$_SESSION['lang']['cancel']."</button>
        </td></tr>  
       </tbody>
         </table>
         </fieldset>";

echo "<div id=container style='width:900px;height:400px;overflow:scroll;'>";
        $str1="select a.*,b.nama,b.alamat,b.desa,c.namakaryawan from ".$dbname.".pad_lahan a
            left join ".$dbname.".pad_5masyarakat b on a.pemilik=b.padid 
            left join ".$dbname.".datakaryawan c on a.updateby=c.karyawanid    
            where posting=0 and unit='".$_SESSION['empl']['lokasitugas']."' order by b.nama,b.desa limit 500"; 
        $res1=mysql_query($str1);
        echo"<table class=sortable cellspacing=1 border=0 width=2500px>
         <thead>
                <tr class=rowheader>
               <td style='width:30px;' rowspan=2>*</td>                
                <td rowspan=2>".$_SESSION['lang']['id']."</td>
                <td rowspan=2>".$_SESSION['lang']['unit']."</td>                     
                <td rowspan=2>".$_SESSION['lang']['pemilik']."</td>
                <td rowspan=2>".$_SESSION['lang']['lokasi']."</td>                       
                <td rowspan=2>".$_SESSION['lang']['desa']."</td>               
                <td rowspan=2>".$_SESSION['lang']['luas']."</td>    
                <td rowspan=2>".$_SESSION['lang']['bisaditanam']."</td> 
                <td rowspan=2>".$_SESSION['lang']['blok']."</td>    
                <td colspan=4 align=center>".$_SESSION['lang']['batas']."</td> 
                <td colspan=7 align=center>".$_SESSION['lang']['biaya']."-".$_SESSION['lang']['biaya']."</td>  
                <td colspan=4 align=center>".$_SESSION['lang']['status']."</td>    
                <td rowspan=2>".$_SESSION['lang']['nomor']." ".$_SESSION['lang']['dokumen']."</td>
                <td rowspan=2>".$_SESSION['lang']['keterangan']."</td> 
                 <td rowspan=2>".$_SESSION['lang']['updateby']."</td>   
                 </tr><tr class=rowheader>   
                <td>".$_SESSION['lang']['batastimur']."</td>                      
                <td>".$_SESSION['lang']['batasbarat']."</td>  
                <td>".$_SESSION['lang']['batasutara']."</td>
                <td>".$_SESSION['lang']['batasselatan']."</td> 
                    
                <td>".$_SESSION['lang']['tanamtumbuh']." (Rp)</td> 
                <td>".$_SESSION['lang']['gantilahan']." (Rp)</td> 
                <td>".$_SESSION['lang']['total']."<br>".$_SESSION['lang']['gantilahan']." (Rp)</td>    
                <td>".$_SESSION['lang']['biaya']."<br>".$_SESSION['lang']['camat']." (Rp)</td> 
                <td>".$_SESSION['lang']['biaya']."<br>".$_SESSION['lang']['kades']." (Rp)</td>
                <td>".$_SESSION['lang']['biaya']."<br>Matrai (Rp)</td>
                <td>".$_SESSION['lang']['total']."<br>".$_SESSION['lang']['biaya']." (Rp)</td>     
                    
                <td>".$_SESSION['lang']['status']."<br>".$_SESSION['lang']['permintaandana']."</td>
                <td>".$_SESSION['lang']['status']."<br>".$_SESSION['lang']['pembayaran']."</td>
                <td>".$_SESSION['lang']['status']."<br>".$_SESSION['lang']['desa']."</td>
                <td>".$_SESSION['lang']['status']."<br>".$_SESSION['lang']['camat']."</td>
                
                </tr></thead>
                <tbody>";
        while($bar1=mysql_fetch_object($res1))
        {
         $stdana=$bar1->statuspermintaandana==1?tanggalnormal($bar1->tanggalpengajuan):"";
         
         if($bar1->statuspermbayaran==1){
                 $stbayar=tanggalnormal($bar1->tanggalbayar)." Belum Lunas";
         }else if($bar1->statuspermbayaran==0){
                 $stbayar='Belum Bayar';
         }else if($bar1->statuspermbayaran==2){
                  $stbayar=tanggalnormal($bar1->tanggalbayar)." Lunas";
         }
         $stkades=$bar1->statuskades==1?tanggalnormal($bar1->tanggalkades):"";
         $stcamat=$bar1->statuscamat==1?tanggalnormal($bar1->tanggalcamat):"";
                echo"<tr class=rowcontent>                 
                          <td width='100px;'>
                               <img src='images/application/application_view_gallery.png' class=resicon title='Upload Document' onclick=uploadDocument('".$bar1->idlahan."','".$bar1->pemilik."',event)>
                               <img src='images/skyblue/pdf.jpg' class='resicon' onclick=\"ptintPDF('".$bar1->idlahan."','".$bar1->pemilik."',event);\" title='Print Data Detail'>
                               <img src='images/skyblue/edit.png' class=resicon  caption='Edit' onclick=\"fillField('".$bar1->idlahan."','".$bar1->pemilik."','".$bar1->unit."','".$bar1->lokasi."','".$bar1->luas."','".$bar1->luasdapatditanam."','".$bar1->rptanaman."','".$bar1->rptanah."','".$bar1->statuspermintaandana."','".$bar1->statuspermbayaran."','".$bar1->kodeblok."','".$bar1->statuskades."','".$bar1->statuscamat."','".tanggalnormal($bar1->tanggalpengajuan)."','".tanggalnormal($bar1->tanggalbayar)."','".tanggalnormal($bar1->tanggalkades)."','".tanggalnormal($bar1->tanggalcamat)."','".$bar1->biayakades."','".$bar1->biayacamat."','".$bar1->biayamatrai."','".$bar1->keterangan."','".$bar1->nosurat."','".$bar1->batastimur."','".$bar1->batasbarat."','".$bar1->batasutara."','".$bar1->batasselatan."');\">
                                <img src='images/skyblue/posting.png' class='resicon' onclick=\"postingData('".$bar1->idlahan."','".$bar1->unit."')\" title='Posting'>
                                <img src='images/skyblue/delete.png' class='resicon' onclick=\"deleteData('".$bar1->idlahan."','".$bar1->unit."');\" title='Delete'>
                           </td>
                           <td>".$bar1->idlahan."</td>
                           <td>".$bar1->unit."</td>
                           <td>".$bar1->nama."</td>
                           <td>".$bar1->lokasi."</td>                                 
                           <td>".$bar1->desa."</td>
                           <td align=right>".$bar1->luas."</td>  
                           <td align=right>".$bar1->luasdapatditanam."</td>
                           <td>".$bar1->kodeblok."</td>    
                           <td>".$bar1->batastimur."</td>
                           <td>".$bar1->batasbarat."</td>
                           <td>".$bar1->batasutara."</td>
                           <td>".$bar1->batasselatan."</td>  
                           <td align=right>".number_format($bar1->rptanaman,0)."</td>    
                           <td align=right>".number_format($bar1->rptanah,0)."</td>
                           <td align=right>".number_format($bar1->totalgantirugi,0)."</td>    
                           <td align=right>".number_format($bar1->biayakades,0)."</td>
                           <td align=right>".number_format($bar1->biayacamat,0)."</td>
                           <td align=right>".number_format($bar1->biayamatrai,0)."</td>
                           <td align=right>".number_format(($bar1->totalgantirugi+$bar1->biayakades+$bar1->biayacamat+$bar1->biayamatrai),0)."</td>
                            <td>".$stdana."</td>
                           <td>".$stbayar."</td>
                           <td>".$stkades."</td>
                           <td>".$stcamat."</td>       
                           <td>".$bar1->nosurat."</td>  
                           <td>".$bar1->keterangan."</td>
                           <td>".$bar1->namakaryawan."</td>                                 
                            </td></tr>";
        }	 
        echo"	 
                 </tbody>
                 <tfoot>
                 </tfoot>
                 </table>";
echo "</div>";
//echo close_theme();
CLOSE_BOX();
echo close_body();
?>