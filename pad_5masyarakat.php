<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/pad_masyarakat.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['masyarakat']);


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
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN','PABRIK') order by namaorganisasi";
$res=mysql_query($str);
while($bar=  mysql_fetch_object($res))
{
    $optpad.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
echo"<fieldset style='width:500px;'><table>
    <tr><td>".$_SESSION['lang']['id']."</td><td>
             <input type=text id=mid class=myinputtext sise=4 disabled></td></tr>
     <tr><td>".$_SESSION['lang']['nama']."</td><td>
             <input type=text id=nama size=30 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
     <tr><td>".$_SESSION['lang']['alamat']."</td><td>
             <input type=text id=alamat size=45 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>           
       <tr><td>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['desa']."</td><td>
             <select id=desa>".$optdesa."</select></td></tr>
       <tr><td>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['kecamatan']."</td><td>
             <select id=kecamatan>".$optkecamatan."</select></td></tr>
       <tr><td>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['kabupaten']."</td><td>
             <select id=kabupaten>".$optkabupaten."</select></td></tr>
     <tr><td>".$_SESSION['lang']['noktp']."</td><td>
             <input type=text id=ktp size=45 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
     <tr><td>".$_SESSION['lang']['nohp']."</td><td>
             <input type=text id=hp size=45 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>             
     </table>
         <input type=hidden id=method value='insert'>
         <button class=mybutton onclick=simpanJabatan()>".$_SESSION['lang']['save']."</button>
         <button class=mybutton onclick=cancelJabatan()>".$_SESSION['lang']['cancel']."</button>
         </fieldset>";
echo open_theme($_SESSION['lang']['list']);
echo "<img onclick=desaexcel(event,'pad_slave_save_masyarakat.php') src=images/excel.jpg class=resicon title='MS.Excel'>"; 
echo $_SESSION['lang']['kebun'].": <select id='unitbawah' onchange=gantikebun()><option value=''>".$_SESSION['lang']['all']."</option>".$optpad."</select>";
echo "<div id=container>";
        $str1="select a.*,b.unit from ".$dbname.".pad_5masyarakat a
            left join ".$dbname.".pad_5desa b on a.desa=b.namadesa order by desa,nama";
        $res1=mysql_query($str1);
        echo"<table class=sortable cellspacing=1 border=0>
         <thead>
                <tr class=rowheader>
                <td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td>
                <td style='width:150px;'>".$_SESSION['lang']['nama']."</td>                    
                <td style='width:150px;'>".$_SESSION['lang']['alamat']."</td>                        
                <td style='width:150px;'>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['desa']."</td>
                <td>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['kecamatan']."</td>                  
                <td>".$_SESSION['lang']['kabupaten']."</td>    
                <td>".$_SESSION['lang']['noktp']."</td>             
                <td>".$_SESSION['lang']['nohp']."</td>                       
               <td style='width:30px;'>*</td></tr>
                </thead>
                <tbody>";
        while($bar1=mysql_fetch_object($res1))
        {
                echo"<tr class=rowcontent>
                         <td>".$bar1->unit."</td>
                           <td>".$bar1->nama."</td>
                           <td>".$bar1->alamat."</td>
                           <td>".$bar1->desa."</td>                               
                           <td>".$bar1->kecamatan."</td>
                           <td>".$bar1->kabupaten."</td>  
                           <td>".$bar1->noktp."</td>  
                           <td>".$bar1->hp."</td>                                 
                           <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->padid."','".$bar1->nama."','".$bar1->alamat."','".$bar1->desa."','".$bar1->kecamatan."','".$bar1->kabupaten."','".$bar1->noktp."','".$bar1->hp."');\">
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