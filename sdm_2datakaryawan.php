<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
echo open_body();
?>

<script language=javascript1.2 src='js/datakaryawan.js'></script>

<?php		
//MASUK
//thn
$optthnmsk="<option value=''>".$_SESSION['lang']['all']."</option>";
$sql="SELECT distinct left(tanggalmasuk,4) as thnmsk FROM ".$dbname.".datakaryawan order by tanggalmasuk desc";

$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
                        {
                                $optthnmsk.="<option value=".$data['thnmsk'].">".$data['thnmsk']."</option>";
                        }		
//bln
$optblnmsk="<option value=''>".$_SESSION['lang']['all']."</option>";
$sql = "SELECT distinct mid(tanggalmasuk,6,2) as blnmsk FROM ".$dbname.".datakaryawan order by mid(tanggalmasuk,6,2) desc";
//exit($sql);
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
                        {
                                $optblnmsk.="<option value=".$data['blnmsk'].">".$data['blnmsk']."</option>";
                        }


//KELUAR
//thn
$optthnkel="<option value=''>".$_SESSION['lang']['all']."</option>";
$sql="SELECT distinct left(tanggalkeluar,4) as thnkel FROM ".$dbname.".datakaryawan order by tanggalkeluar desc";

$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
                        {
                                $optthnkel.="<option value=".$data['thnkel'].">".$data['thnkel']."</option>";
                        }		
//bln
$optblnkel="<option value=''>".$_SESSION['lang']['all']."</option>";
$sql = "SELECT distinct mid(tanggalkeluar,6,2) as blnkel FROM ".$dbname.".datakaryawan order by mid(tanggalkeluar,6,2) desc";
//exit($sql);
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
                        {
                                $optblnkel.="<option value=".$data['blnkel'].">".$data['blnkel']."</option>";
                        }
$optPhoto="<option value=''>".$_SESSION['lang']['all']."</option>";
$optPhoto.="<option value='1'>Ada</option>";
$optPhoto.="<option value='2'>Tidak</option>";

include('master_mainMenu.php');

OPEN_BOX('',$_SESSION['lang']['inputdatakaryawan']);
//lokasi tugas
$optlokasitugas='';
//if(user is under holding)
$saveable='';
$str="select 1=1";
if(trim($_SESSION['empl']['tipelokasitugas'])=='HOLDING')//user holding dapat menempatkan dimana saja
{
        //$str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe not in('BLOK','PT','STENGINE','STATION') order by namaorganisasi";
        $str="select distinct a.lokasitugas as kodeorganisasi,namaorganisasi from ".$dbname.".datakaryawan a 
              left join ".$dbname.".organisasi b on a.lokasitugas=b.kodeorganisasi order by namaorganisasi asc";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
                        $optlokasitugas.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
        }
        $str="select distinct a.subbagian as kodeorganisasi,namaorganisasi from ".$dbname.".datakaryawan a 
              inner join ".$dbname.".organisasi b on a.subbagian=b.kodeorganisasi order by namaorganisasi asc";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
                        $optlokasitugas.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
        }
}
else if(trim($_SESSION['empl']['tipelokasitugas'])=='KANWIL')//user holding dapat menempatkan dimana saja
{
        //$str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe not in('BLOK','PT','STENGINE','STATION') order by namaorganisasi";
        $str="select distinct a.lokasitugas as kodeorganisasi,namaorganisasi from ".$dbname.".datakaryawan a 
              left join ".$dbname.".organisasi b on a.lokasitugas=b.kodeorganisasi where lokasitugas in("
                . "select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')"
                . " order by namaorganisasi asc";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
                        $optlokasitugas.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
        }
        $str="select distinct a.subbagian as kodeorganisasi,namaorganisasi from ".$dbname.".datakaryawan a 
              inner join ".$dbname.".organisasi b on a.subbagian=b.kodeorganisasi where lokasitugas in("
                . "select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')"
                . " order by namaorganisasi asc";
        
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
                        $optlokasitugas.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
        }
}
else if(trim($_SESSION['org']['induk']!=''))//user unit hanya dapat menempatkan pada unitnya dan anak unitnya
{
  //$str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe<>'BLOK' and (induk='".trim($_SESSION['org']['kodeorganisasi'])."' or kodeorganisasi='".trim($_SESSION['org']['kodeorganisasi'])."') order by namaorganisasi";
      $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe not in('BLOK','PT','STENGINE','STATION') 
              and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
                        $optlokasitugas.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
        }
}
else
{
  $saveable='disabled';
  echo"<script>
        alert('You are not authorized');
       </script>";
}



//Tipe karyawan
$opttipekaryawan='';
if((trim($_SESSION['empl']['tipelokasitugas'])=='HOLDING')||(trim($_SESSION['empl']['tipelokasitugas'])=='KANWIL'))//jika user holding dapat memunculkan pilihan Permanen(staff))
      $str="select * from ".$dbname.".sdm_5tipekaryawan order by tipe";
else//pilihan staff dihilangkan, input data staff hanya dari pusat
      $str="select * from ".$dbname.".sdm_5tipekaryawan where id<>0 order by tipe";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
      $opttipekaryawan.="<option value='".$bar->id."'>".$bar->tipe."</option>";	
}	
$opttipekaryawan.="<option value=100>Kecuali BHL/KHL</option>";	

//===========get jeniskelamin enum
//get Golongan darah from enum
$optJK="<option value=''>".$_SESSION['lang']['all']."</option>";
$arrenum=getEnum($dbname,'datakaryawan','jeniskelamin');
foreach($arrenum as $key=>$val)
{

        $optJK.="<option value='".$key."'>".$val."</option>";
}

//departemen
$optDept="<option value=''>".$_SESSION['lang']['all']."</option>";
$str="select * from ".$dbname.".sdm_5departemen";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
      $optDept.="<option value='".$bar->kode."'>".$bar->nama."</option>";	
}	




$aPt="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='pt' ";
if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
	$aPt="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT'";
}else{
	$aPt="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' and kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
}

$bPt=mysql_query($aPt) or die (mysql_error($conn));
while($cPt=mysql_fetch_assoc($bPt))
{
    $optPt.="<option value='".$cPt['kodeorganisasi']."'>".$cPt['namaorganisasi']."</option>";
}


echo"<table>
     <tr valign=middle>
         <td><fieldset><legend>".$_SESSION['lang']['find']."</legend><table><tr><td>"; 
                        echo" ".$_SESSION['lang']['caripadanama']." : <input type=text id=txtsearch  style='width:120px' size=25 maxlength=30 class=myinputtext> &nbsp";
						
                        echo" ".$_SESSION['lang']['nik']." : <input type=text id=nik  style='width:75px' size=25 maxlength=30 class=myinputtext> &nbsp";


                        echo $_SESSION['lang']['tipekaryawan']." : <select id=schtipe  onchange=changeCaption1(this.options[this.selectedIndex].text);><option value=''>".$_SESSION['lang']['all']."</option>".$opttipekaryawan."</select> &nbsp ";

                        echo $_SESSION['lang']['status']." : <select id=schstatus  style='width:75px' onchange=changeCaption(this.options[this.selectedIndex].text);><option value=''>".$_SESSION['lang']['all']."</option><option value='0000-00-00'>".$_SESSION['lang']['aktif']."</option><option value='*'>".$_SESSION['lang']['tidakaktif']."</select> &nbsp ";

                        echo $_SESSION['lang']['jeniskelamin']." : <select id=schjk  style='width:150px' onchange=changeCaption(this.options[this.selectedIndex].text);>".$optJK."</select></td></tr>";

                        echo "<tr><td>".$_SESSION['lang']['departemen']." : <select id=schdept  style='width:150px' onchange=changeCaption(this.options[this.selectedIndex].text);>".$optDept."</select> &nbsp";

                        echo $_SESSION['lang']['photo']." : <select id=schphoto  style='width:75px'>".$optPhoto."</select>";

                //echo "<td align=right>".$_SESSION['lang']['jeniskelamin']."</td><td><select id=jeniskelamin  style='width:150px;'>".$optJK."</select></td>";

                        echo"<button class=mybutton onclick=cariKaryawanLaporan(1)>".$_SESSION['lang']['find']."</button>";
echo"</table></fieldset></td>
     </tr>
         </table> "; 


         ///////////////////

 echo"<table>
     <tr valign=middle>
         <td><fieldset><legend>Find by ".$_SESSION['lang']['kodeorganisasi']."</legend>"; 

                        echo $_SESSION['lang']['lokasitugas']." : <select id=schorg  style='width:150px' onchange=changeCaption(this.options[this.selectedIndex].text);><option value=''>".$_SESSION['lang']['all']."</option>".$optlokasitugas."</select> &nbsp ";
                        echo $_SESSION['lang']['pt']." :  <select id=schpt  style='width:150px' onchange=changeCaption(this.options[this.selectedIndex].text);><option value=''>".$_SESSION['lang']['all']."</option>".$optPt."</select> &nbsp ";


                        echo"<button class=mybutton onclick=cariKaryawanLaporan(1)>".$_SESSION['lang']['find']."</button> ";
echo"</fieldset></td>
     </tr>
         </table> "; 

///

         echo"<table>
    <tr valign=middle>
         <td><fieldset><legend>Find by period</legend><table><tr><td>"; 


                        echo"".$_SESSION['lang']['tahun']." ".$_SESSION['lang']['masuk']." : <select id='thnmsk' style='width:150px;' onchange=changeCaption1(this.options[this.selectedIndex].text);>".$optthnmsk." &nbsp  </select>";

                        echo" ".$_SESSION['lang']['bulan']." ".$_SESSION['lang']['masuk']." : <select id='blnmsk' style='width:150px;' onchange=changeCaption1(this.options[this.selectedIndex].text);>".$optblnmsk." &nbsp  </select>";

                        echo"".$_SESSION['lang']['tahun']." ".$_SESSION['lang']['keluar']." : <select id='thnkel' style='width:150px;' onchange=changeCaption1(this.options[this.selectedIndex].text);>".$optthnkel." &nbsp  </select>";

                        echo" ".$_SESSION['lang']['bulan']." ".$_SESSION['lang']['keluar']." : <select id='blnkel' style='width:150px;' onchange=changeCaption1(this.options[this.selectedIndex].text);>".$optblnkel."</select>&nbsp";
                        
                        echo $_SESSION['lang']['jeniskelamin']." : <select id=schjk  style='width:150px' onchange=changeCaption(this.options[this.selectedIndex].text);>".$optJK."</select></td></tr>";
                        
                        echo "<tr><td>".$_SESSION['lang']['tanggalmulai']." :<input type=text class=myinputtext id=tgl1 name=tgl1 onmousemove=setCalendar(this.id) onkeypress=return false;  maxlength=10 style='width:150px;' />";
                       
                        echo " ".$_SESSION['lang']['tanggalsampai']." :<input type=text class=myinputtext id=tgl2 name=tgl2 onmousemove=setCalendar(this.id) onkeypress=return false;  maxlength=10 style='width:150px;' />";

                        echo"<button class=mybutton onclick=cariKaryawanLaporan(1)>".$_SESSION['lang']['find']."</button> ";

echo"</table></fieldset></td>
     </tr>
         </table> "; 
                        
//echo"</fieldset></td>
//     </tr>
//         </table> "; 


echo"<div id='searchplace'>".$_SESSION['lang']['daftarkaryawan']." ".$_SESSION['empl']['lokasitugas'].":<span id=cap1></span>-<span id=cap2></span>

         ";

        echo" <img src=images/excel.jpg class=resicon title='Excel' onclick=datakaryawanExcel(event,'','sdm_slave_datakaryawan_Excel.php')>
         <img src=images/excel.jpg class=resicon title='Excel' onclick=datakaryawanExcel2(event,'','sdm_slave_datakaryawan_Excel2.php')>


         <table class=sortable border=0 cellspacing=1>
         <thead>
           <tr class=rowheader>
             <td align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
                 <td align=center>".$_SESSION['lang']['nik']."</td>
                 <td align=center>".$_SESSION['lang']['nama']."</td>
                 <td align=center>".$_SESSION['lang']['functionname']."</td>
                 <td align=center>".$_SESSION['lang']['kodegolongan']."</td>
                 <td align=center>".$_SESSION['lang']['lokasitugas']."</td>
                 <td align=center>".$_SESSION['lang']['pt']."</td>
                 <td align=center>".$_SESSION['lang']['subunit']."</td>
                 <td align=center>".$_SESSION['lang']['pendidikan']."</td>
                 <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['statuspajak'])."</td>
                 <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['statusperkawinan'])."</td>
                 <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['jumlahanak'])."</td>
                 <td align=center>".$_SESSION['lang']['tanggalmasuk']."</td>
                 <td align=center>".$_SESSION['lang']['tanggalkeluar']."</td>
                 <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['tipekaryawan'])."</td>
                 <td align=center>".$_SESSION['lang']['action']."</td>
           </tr>
         </thead>

         <tbody id=searchplaceresult>
         </tbody>
         <tfoot>
         </tfoot> 
                 <tr align=center><td colspan=20 align=center>
         <button align=center class=mybutton value=0 onclick=prefDatakaryawan1(this,this.value) id=prefbtn>< ".$_SESSION['lang']['pref']." </button> 
         &nbsp 
         <button align=center class=mybutton value=2 onclick=nextDatakaryawan1(this,this.value) id=nextbtn> ".$_SESSION['lang']['lanjut']." ></button>
        </td><tr>
         </table>
     </div>";

CLOSE_BOX();
close_body('');
?>