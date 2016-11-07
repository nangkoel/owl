<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/pabrik_5volumtangki.js'></script>
<?php
include('master_mainMenu.php');
//ambil periode penggajian
$optPeriode2=$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$str="select distinct kodetangki from ".$dbname.".pabrik_5tangki 
      where kodeorg='".$_SESSION['empl']['lokasitugas']."'  order by kodetangki asc";
$res=mysql_query($str);
while($bar=  mysql_fetch_object($res)){
    $optPeriode.="<option value='".$bar->kodetangki."'>".$bar->kodetangki."</option>";
}
$str2="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
      where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'  order by namaorganisasi asc";
$res2=mysql_query($str2);
while($bar2=  mysql_fetch_object($res2)){
    $optPeriode2.="<option value='".$bar2->kodeorganisasi."'>".$bar2->namaorganisasi."</option>";
}


OPEN_BOX('',$_SESSION['lang']['pabrikvolume']);

$frm[0].="<fieldset><legend>Form</legend>
              <table>
              <tr><td>".$_SESSION['lang']['kodeorg']."<td><td><input type=text style=width:150px id=kodeorg disabled class=myinputtext value='".$_SESSION['empl']['lokasitugas']."'></td></tr>
              <tr><td>".$_SESSION['lang']['kodetangki']."<td><td><select id=kdTangki style=width:150px>".$optPeriode."</select></td></tr>     
              <tr><td>".$_SESSION['lang']['tinggi']."<td><td><input type=text id=tinggi  class=myinputtextnumber  onkeypress='return angka_doang(event)'  style=width:150px /></td></tr>
              <tr><td>".$_SESSION['lang']['volume']."<td><td><input type=text id=vol  class=myinputtextnumber  onkeypress='return angka_doang(event)'  style=width:150px /></td></tr>
              </table>
             <button class=mybutton onclick=saveData()>".$_SESSION['lang']['save']."</button>
             </fieldset>
             <div style='width:850px;height:400px;overflow:scroll;'>";
$frm[0].="<table cellpadding=1 cellspacing=1 border=0><tr><td>".$_SESSION['lang']['kodetangki']."</td><td>:</td><td><select id=tangkiCr style=width:150px onchange=loadData()>".$optPeriode."</select></td></tr>
          <tr><td>".$_SESSION['lang']['tinggi']."</td><td>:</td><td><input type=text id=tinggiCm  class=myinputtext  onkeypress='return angka_doang(event)'  style=width:150px /></td></tr>
          <tr><td colspan=3><button class=mybutton  onclick=loadData()>".$_SESSION['lang']['find']."</button></td></tr>    
          </table><input type=hidden id=oldTinggi value='' /><input type=hidden id=oldkdTangki value='' />
          <input type=hidden id=proses value='saveAll' />
         <br /><table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";             
$frm[0].="<tr class=rowheader>";
$frm[0].="<td>No.</td><td>".$_SESSION['lang']['kodeorg']."</td>";
$frm[0].="<td>".$_SESSION['lang']['kodetangki']."</td>";
$frm[0].="<td>".$_SESSION['lang']['tinggi']."</td>";
$frm[0].="<td>".$_SESSION['lang']['volume']."</td>
          <td>".$_SESSION['lang']['action']."</td></tr><tbody  id=container ><script>loadData()</script>";
$frm[0].="</tbody></table>";
$frm[0].="</div>";



$frm[1].="<fieldset><legend>".$_SESSION['lang']['data']."</legend>";
$frm[1].="<div style='width:850px;height:400px;overflow:scroll;'><table cellpadding=1 cellspacing=1 border=0>
          <tr>
          <td>".$_SESSION['lang']['kodeorg']."</td>
          <td>:</td><td><select id=kodeOrg2 style=width:150px onchange=loadData2()>".$optPeriode2."</select></td>
          <td>".$_SESSION['lang']['kodetangki']."</td><td>:</td><td><select id=tangkiCr2 style=width:150px onchange=loadData2()>".$optPeriode."</select>
          <img src='images/excel.jpg' class='resicon' title='Excel' onclick=getExcel(event,'pabrik_slave_5volumetangki.php','','') >
          </td></tr>
          
          </table>
         <br /><table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";             
$frm[1].="<tr class=rowheader>";
$frm[1].="<td>No.</td><td>".$_SESSION['lang']['kodeorg']."</td>";
$frm[1].="<td>".$_SESSION['lang']['kodetangki']."</td>";
$frm[1].="<td>".$_SESSION['lang']['tinggi']."</td>";
$frm[1].="<td>".$_SESSION['lang']['volume']."</td>
          </tr><tbody  id=containerlist >";
$frm[1].="</tbody></table></div>";
               




//========================
$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['daftar'];

//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,300,900);
//===============================================
CLOSE_BOX();
echo close_body();
?>