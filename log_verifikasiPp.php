<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['verifikasi']."</b>"); //1 O
?>
<script>semua="<?php echo $_SESSION['lang'] ['all']; ?>";</script>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/log_verivikasi.js"></script>


<div id="action_list">
<?php
$optPur="<option value=''>".$_SESSION['lang']['all']."</option>";
 $sPur="select karyawanid,namakaryawan from ".$dbname.".datakaryawan 
               where bagian='PUR' and kodejabatan in (1,33,39,109)  and (tanggalkeluar>'".date('Y-m-d')."' or tanggalkeluar='0000-00-00')  order by namakaryawan asc";
           //exit("Error".$sPur);
           $qPur=fetchData($sPur);
           foreach($qPur as $brsKary)
           {
               $optPur.="<option value=".$brsKary['karyawanid'].">".$brsKary['namakaryawan']."</option>";
           }
           $optListUnit.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
           $sListUnit="select distinct substr(nopp,16,4) as kodeunit from ".$dbname.".log_prapoht where close='2'";
           $qListUnit=mysql_query($sListUnit) or die(mysql_error($sListUnit));
           while($rListUnit=mysql_fetch_assoc($qListUnit))
           {
               $optListUnit.="<option value='".$rListUnit['kodeunit']."'>".$rListUnit['kodeunit']."</option>";
           }
           $optKelompokBrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
           $optBrgCari="<option value=''>".$_SESSION['lang']['all']."</option>";
           $sKelompok="select distinct kode,kelompok from ".$dbname.".log_5klbarang order by kelompok asc";
           $qKelompok=mysql_query($sKelompok) or die(mysql_error($sKelompok));
           while($rKelompok=mysql_fetch_assoc($qKelompok))
           {
               $optKelompokBrg.="<option value='".$rKelompok['kode']."'>".$rKelompok['kelompok']."</option>";
           }
           $optPeriodeCari="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
           $sPeriodeCari="select distinct substr(tanggal,1,7) as periode from ".$dbname.".log_prapoht order by substr(tanggal,1,7) desc";
           $qPeriodeCari=mysql_query($sPeriodeCari) or die(mysql_error());
           while($rPeriodeCari=mysql_fetch_assoc($qPeriodeCari))
           {
               $optPeriodeCari.="<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode']."</option>";
           }
           $optStatusPP="<option value='2'>".$_SESSION['lang']['pilihdata']."</option>";
           $stataPP=array("0"=>$_SESSION['lang']['blmAlokasi'],"1"=>$_SESSION['lang']['sdhPO']);
           foreach($stataPP as $dataIni=>$listNama)
           {
               $optStatusPP.="<option value='".$dataIni."'>".$listNama."</option>";
           }
 echo"<table>
     <tr valign=middle>
         <td onclick=displaySummary() align=center style='width:55px;cursor:pointer;'><img class=delliconBig src=images/book_icon.gif title='Summary'><br>Summary</td>
         <td onclick=displayTools() align=center style='width:55px;cursor:pointer;'><img class=delliconBig src=images/gear_64.png title='Tools'><br>Tools</td>
         <td align=center style='width:55px;cursor:pointer;' onclick=displayList()>	   
           <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
         <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
         echo"<table border=0 cellpadding=1 cellspacing=1><tr><td>";
                        echo $_SESSION['lang']['carinopp']."</td><td><input type=text id=txtsearch size=25 maxlength=30 onkeypress=\"return validat(event);\" class=myinputtext></td>";
                        //echo $_SESSION['lang']['namabarang'].": <input type=text id=txtCari name=txtCaro class=myinputtext />&nbsp;";
                        echo "<td>".$_SESSION['lang']['periode']."</td><td><select id=tgl_cari style=width:100px>".$optPeriodeCari."</select></td>";
                        echo "<td>".$_SESSION['lang']['purchaser']."</td><td><select id=purId name=purId>".$optPur."</select></td>";
                        echo "<td>".$_SESSION['lang']['unit']."</td><td><select id=unitIdCr name=unitIdCr>".$optListUnit."</select></td>";
                        echo "<td>".$_SESSION['lang']['status']."</td><td><select id='statPP' name='statPP'>".$optStatusPP."</select></td>";

                        echo"<td rowspan=2><button class=mybutton onclick=cariNopp()>".$_SESSION['lang']['find']."</button></td></tr>";
                        echo"<tr><td>".$_SESSION['lang']['kelompokbarang']."</td><td> <select id=klmpkBrg style=width:150px onchange=getBarangCari()>".$optKelompokBrg."</select></td><td>".$_SESSION['lang']['namabarang']."</td><td><select id=kdBarangCari style=width:100px>".$optBrgCari."</select>&nbsp;<img src=\"images/search.png\" class=\"resicon\" title='".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."' onclick=\"searchBrgCari('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."','<fieldset><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."</legend>".$_SESSION['lang']['find']."&nbsp;<input type=text class=myinputtext id=nmBrg><button class=mybutton onclick=findBrg2()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerBarang style=overflow=auto;height=380;width=485></div>',event);\"></td><td colspan=3></td></tr></table>";
echo"</fieldset></td>
     </tr>
         </table> "; 
?>
</div>
<?php
CLOSE_BOX(); //1 C
echo "<div id=\"list_pp_verication\">";
OPEN_BOX(); //2 O
?>
  <input type='hidden' id='method' name='method' />

<fieldset>
<legend><?php echo $_SESSION['lang']['list_pp'];?></legend>
  <img onclick=dataKeExcel(event) src=images/excel.jpg class=resicon title='MS.Excel'> 
<div style="overflow:scroll; height:420px;" id=contain>
<script>displayList()</script>
</div>
</fieldset>
<?php
echo"</div>";
CLOSE_BOX();
echo close_body();
?>