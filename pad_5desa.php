<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/pad_desa.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['desa']);

$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN','PABRIK') order by namaorganisasi";
$res=mysql_query($str);
while($bar=  mysql_fetch_object($res))
{
    $optpad.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}

echo"<fieldset style='width:500px;'><table>
    <tr><td>".$_SESSION['lang']['kebun']."</td><td>
             <select id='unit'>".$optpad."</select></td></tr>
     <tr><td>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['desa']."</td><td>
             <input type=text id=desa size=30 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
      <tr><td>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['kecamatan']."</td><td>
             <input type=text id=kecamatan size=30 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
      <tr><td>".$_SESSION['lang']['kabupaten']."</td><td>
             <input type=text id=kabupaten size=30 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>             
     </table>
         <input type=hidden id=method value='insert'>
         <button class=mybutton onclick=simpanJabatan()>".$_SESSION['lang']['save']."</button>
         <button class=mybutton onclick=cancelJabatan()>".$_SESSION['lang']['cancel']."</button>
         </fieldset>";
echo open_theme($_SESSION['lang']['list']);
echo "<img onclick=desaexcel(event,'pad_slave_save_desa.php') src=images/excel.jpg class=resicon title='MS.Excel'>"; 
echo $_SESSION['lang']['kebun'].": <select id='unitbawah' onchange=gantikebun()><option value=''>".$_SESSION['lang']['all']."</option>".$optpad."</select>";
echo "<div id=container>";
        $str1="select * from ".$dbname.".pad_5desa order by namadesa";
        $res1=mysql_query($str1);
        echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
         <thead>
                <tr class=rowheader>
                <td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td>
                <td style='width:150px;'>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['desa']."</td>
                <td>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['kecamatan']."</td>
                <td>".$_SESSION['lang']['kabupaten']."</td>    
               <td style='width:30px;'>*</td></tr>
                </thead>
                <tbody>";
        while($bar1=mysql_fetch_object($res1))
        {
                echo"<tr class=rowcontent>
                          <td align=center>".$bar1-> unit."</td>
                           <td>".$bar1-> namadesa."</td>
                           <td>".$bar1->kecamatan."</td>
                           <td>".$bar1->kabupaten."</td>    
                           <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->unit."','".$bar1->namadesa."','".$bar1->kecamatan."','".$bar1->kabupaten."');\">
                            </td></tr>";
        }	 
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