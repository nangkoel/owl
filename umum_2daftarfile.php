<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script   language=javascript1.2 src='js/umum_2daftarfile.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX(''," ");
#ambil kode

$str="select * from ".$dbname.".rencana_gis_jenis order by namajenis";
//echo $str;
$res=mysql_query($str);
$optjenis="<option value=''>All</option>";
while($bar=mysql_fetch_object($res))
{
    $optjenis.="<option value='".$bar->kode."'>".$bar->namajenis."</option>";
}
#ambil unit
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4
           order by namaorganisasi";
$res=mysql_query($str);
$optOrg="<option value=''>All</option>";
while($bar=mysql_fetch_object($res))
{
    $optOrg.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
#ambil periode yang ada
$str="select distinct left(tanggal,7) as periode from ".$dbname.".rencana_gis_file order by tanggal desc";
$res=mysql_query($str);
$optperiode="<option value=''>All</option>";
while($bar=mysql_fetch_object($res))
{
    $optperiode.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}    


echo"<fieldset><legend>DAFTAR FILE</legend><select style='width:175px;' name='kodeorg1'  id='kodeorg1'>".$optOrg."</select>
          Jenis Data <select name='kode1'  id='kode1'>".$optjenis."</select>
          Periode <select name='periode'  id='periode'>".$optperiode."</select>
          <button onclick=cariFile() class=mybutton>".$_SESSION['lang']['find']."</button>"; 
echo "<div style='height:350px;width:100%;overflow:scroll;'>
      <table class=sortable border=0 cellspacing=1>
	  <thead>
	  <tr>
	  <td>No.</td>
	  <td>".$_SESSION['lang']['unit']."</td>
	  <td>".$_SESSION['lang']['jenis']."</td>
	  <td>".$_SESSION['lang']['tanggal']."</td>
	  <td>".$_SESSION['lang']['user']."</td>
	  <td>".$_SESSION['lang']['updateby']."</td>
	  <td>".$_SESSION['lang']['keterangan']."</td>
	  <td>".$_SESSION['lang']['filegis']."</td>
	  <td>Size</td>              
          <td>".$_SESSION['lang']['namakaryawan']."</td>
	  <td>".$_SESSION['lang']['action']."</td>  
	  </tr>
	  </thead>
	  <tbody id=container>";
$str1="select a.*,b.namakaryawan from ".$dbname.".rencana_gis_file a
       left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
       where a.karyawanid='".$_SESSION['standard']['userid']."'       order by a.lastupdate  desc limit 20";
if($res1=mysql_query($str1))
{
    $no=0;
    while($bar1=mysql_fetch_object($res1))
    {
            $no+=1;
            echo"<tr class=rowcontent>
               <td>".$no."</td>
                <td>".$bar1->unit."</td>
                    <td>".$bar1->kode."</td>
                    <td>".tanggalnormal($bar1->tanggal)."</td>
                    <td>".$bar1->namakaryawan."</td>
                    <td>".$bar1->lastupdate."</td>
                    <td>".$bar1->keterangan."</td>
                    <td>".$bar1->namafile."</td>
                    <td align=right>".$bar1->ukuran."</td>
                    <td>".$bar1->namakaryawan."</td>
                    <td>";
            if($bar1->karyawanid==$_SESSION['standard']['userid']){
            echo"<img class='zImgBtn' src='images/skyblue/delete.png' title='Edit' onclick=\"delFile('".$bar1->unit."','".$bar1->kode."','".$bar1->namafile."');\"> &nbsp  &nbsp  &nbsp"; 
            }                
            echo "<img class='zImgBtn'  src='images/skyblue/save.png'  title='Save' onclick=\"download('".$bar1->namafile."');\"></td></tr>";
    }	 
}	  
echo "</tbody>
	  <tfoot>
	  </tfoot>
	  </table>
	  </div></fieldset>";	 
CLOSE_BOX();
echo close_body();
?>
