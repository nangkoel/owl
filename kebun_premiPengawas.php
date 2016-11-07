<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/kebun_premiPengawas.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['pendapatanlainkaryawan']);//indra	  

$optPeriode='<option value=""></option>';
$sGp="select DISTINCT periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and `sudahproses`=0 order by periode desc limit 0,6";
$qGp=mysql_query($sGp) or die(mysql_error());
while($rGp=mysql_fetch_assoc($qGp))
{
        $optPeriode.="<option value=".$rGp['periode'].">".substr(tanggalnormal($rGp['periode']),1,7)."</option>";
}


if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
  $str1="select * from ".$dbname.".datakaryawan
      where ((tanggalkeluar='0000-00-00') or tanggalkeluar >'".date('Y')."-01-01"."')
          and tipekaryawan!=0 and lokasitugas='".$_SESSION['empl']['lokasitugas']."'
          order by namakaryawan";	  
}
else if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
	 $str1="select * from ".$dbname.".datakaryawan
      	where ((tanggalkeluar='0000-00-00') or tanggalkeluar >'".date('Y')."-01-01"."')
          and tipekaryawan!=0 and LEFT(lokasitugas,4)  
		  in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
          order by namakaryawan";
}
else
{
   $str1="select * from ".$dbname.".datakaryawan
      	where ((tanggalkeluar='0000-00-00') or tanggalkeluar >'".date('Y')."-01-01"."')
          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
          order by namakaryawan";	
}

  
$res1=mysql_query($str1,$conn);
$optIdKaryawan='<option value=""></option>';
while($bar1=mysql_fetch_object($res1))
{
        $optIdKaryawan.="<option value=".$bar1->karyawanid.">".$bar1->nik." - ".$bar1->namakaryawan." [".$bar1->lokasitugas."]</option>";//indra	  
        $nama[$bar1->karyawanid]=$bar1->namakaryawan;
}
$strKom="select * from ".$dbname.".sdm_ho_component where id in ('16','43','58')";//indra	  
$resKom=mysql_query($strKom,$conn);
$optKom='';
while($bar1=mysql_fetch_object($resKom))
{
        $optKomponen.="<option value=".$bar1->id.">".$bar1->name."</option>";
}

echo"<fieldset style='width:500px;'>
    EN: Make sure the entire payroll process is carried out either daily or monthly based,if not then the data will be replaced<br>
    ID:Pastikan proses penggajian sudah dilaksanakan keseluruhan baik yang berbasis harian maupun bulanan, jika belum maka data ini akan tertimpa.    
<table>
     <tr>
                <td>".$_SESSION['lang']['periodegaji']."</td>
                <td><select id=\"periodegaji\" name=\"periodegaji\" style=\"width:150px;\" onchange=showPremi1(this.options[this.selectedIndex].value)>".$optPeriode."</select></td>
         </tr>
         <tr>
                <td>".$_SESSION['lang']['namakaryawan']."</td>
                <td><select id=\"idkaryawan\" name=\"idkaryawan\" style=\"width:200px\">".$optIdKaryawan."</select></td>
         </tr>
         <tr>
                <td>".$_SESSION['lang']['upahpremi']."</td>
                <td><input type=text id=upahpremi size=10 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=10 value=0></td>
         </tr>
     <tr>
                <td>".$_SESSION['lang']['komponenpayroll']."</td>
                <td><select id=\"komponenpayroll\" name=\"komponenpayroll\" style=\"width:150px\">".$optKomponen."</select></td>
         </tr>
         </table>
         <input type=hidden id=method value='insert'>
         <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
         <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
         </fieldset>";


echo open_theme($_SESSION['lang']['list']);

$strJ="select * from ".$dbname.".sdm_5jabatan";
$resJ=mysql_query($strJ,$conn);
while($barJ=mysql_fetch_object($resJ))
{
                $jab[$barJ->kodejabatan]=$barJ->namajabatan;
}
echo "<div>";

/*if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	$strRes="select a.*, b.kodejabatan, b.lokasitugas from ".$dbname.".sdm_gaji a 
	left join ".$dbname.".datakaryawan b
	on a.karyawanid = b.karyawanid
	where a.idkomponen in ('16','43','58') 
	order by a.karyawanid";
}
else if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
	$strRes="select a.*, b.kodejabatan, b.lokasitugas from ".$dbname.".sdm_gaji a 
	left join ".$dbname.".datakaryawan b
	on a.karyawanid = b.karyawanid
	where a.idkomponen in ('16','43','58') and  b.lokasitugas in 
	(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') 
	order by a.karyawanid";
}
else
{
	 $strRes="select a.*, b.kodejabatan, b.lokasitugas from ".$dbname.".sdm_gaji a 
        left join ".$dbname.".datakaryawan b
        on a.karyawanid = b.karyawanid
        where a.idkomponen in  ('16','43','58') and  b.lokasitugas = '".$_SESSION['empl']['lokasitugas']."'
        order by a.karyawanid";
        $resRes=mysql_query($strRes);
}*/






       /* $strRes="select a.*, b.kodejabatan, b.lokasitugas from ".$dbname.".sdm_gaji a 
        left join ".$dbname.".datakaryawan b
        on a.karyawanid = b.karyawanid
        where a.idkomponen in  ('16','43','58') and  b.lokasitugas = '".$_SESSION['empl']['lokasitugas']."'
        order by a.karyawanid";
        $resRes=mysql_query($strRes);*/
//echo"$strJ<br>";
echo"".$_SESSION['lang']['periode']." : "."<select id=periodegaji2 style='width:200px;' onchange=showPremi2(this.options[this.selectedIndex].value)>".$optPeriode."</select>";
        echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
             <thead>
                 <tr class=rowheader>
                    <td style='width:150px;'>".$_SESSION['lang']['namakaryawan']."</td>
					 <td>".$_SESSION['lang']['nik']."</td>
					  <td>".$_SESSION['lang']['lokasitugas']."</td>
                        <td>".$_SESSION['lang']['jabatan']."</td>
                        <td>".$_SESSION['lang']['periode']."</td>
                        <td>".$_SESSION['lang']['upahpremi']."</td>
                        <td style='width:30px;'>*</td></tr>
                 </thead>
                 <tbody id=container>"; 
        echo"	 
                 </tbody>
                 <tfoot>
                 </tfoot>
                 </table>";
echo "</div>";
echo close_theme();
CLOSE_BOX();
echo close_body();
?>