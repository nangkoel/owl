<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['riwayatPP']).'</b>'); //1 O
?>
<!--<script type="text/javascript" src="js/log_2keluarmasukbrg.js" /></script>
-->
<script type="text/javascript" src="js/log_2riwayatPP.js" /></script>
<script language="javascript" src="js/zMaster.js"></script>
<div id="action_list">
<?php

$arrPil=array("1"=>$_SESSION['lang']['proses'].' '.$_SESSION['lang']['persetujuan'].' '.$_SESSION['lang']['prmntaanPembelian'],
              "2"=>$_SESSION['lang']['proses'].' '.$_SESSION['lang']['purchasing'],
              "3"=>$_SESSION['lang']['jmlh_brg_sdh_po'],
              "4"=>$_SESSION['lang']['jmlh_brg_blm_po']);
foreach($arrPil as $id =>$isi)
{
        $optPil.="<option value=".$id.">".$isi."</option>";
}
$optLokal="<option value=''>".$_SESSION['lang']['all']."</option>";
$arrPo=array("0"=>"Pusat","1"=>"Lokal");
foreach($arrPo as $brsLokal =>$isiLokal)
{
    $optLokal.="<option value=".$brsLokal.">".$isiLokal."</option>";
}
$optper="<option value=''>".$_SESSION['lang']['all']."</option>";
$sTgl="select distinct substr(tanggal,1,7) as periode from ".$dbname.".log_prapoht order by tanggal desc";
$qTgl=mysql_query($sTgl) or die(mysql_error());
while($rTgl=mysql_fetch_assoc($qTgl))
{
   if(substr($rTgl['periode'],5,2)=='12')
   {
         $optper.="<option value='".substr($rTgl['periode'],0,4)."'>".substr($rTgl['periode'],0,4)."</option>";
   }
   $optper.="<option value='".$rTgl['periode']."'>".substr($rTgl['periode'],5,2)."-".substr($rTgl['periode'],0,4)."</option>";
}
$optSupplier="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="select namasupplier,supplierid from ".$dbname.".log_5supplier  where kodekelompok='S001' and namasupplier!='' order by namasupplier asc";
$query=mysql_query($sql) or die(mysql_error());
while($res=mysql_fetch_assoc($query))
{
   $optSupplier.="<option value='".$res['supplierid']."'>".$res['namasupplier']."</option>";
} 
$sPurchaser="select distinct purchaser from ".$dbname.".log_prapodt order by purchaser asc";
$qPurchaser=  mysql_query($sPurchaser) or die(mysql_error($conn));
while($rPur=  mysql_fetch_assoc($qPurchaser)){
    $crpur="karyawanid='".$rPur['purchaser']."'";
    $optNmKar=  makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan',$crpur);
    $optPurchaser.="<option value='".$rPur['purchaser']."'>".$optNmKar[$rPur['purchaser']]."</option>";
}
         echo"<table>
     <tr valign=moiddle>
          <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
           <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
                 <td><fieldset><legend>".$_SESSION['lang']['pilihdata']."</legend>"; 
                    echo "<table cellpadding=1 cellspacing=1 border=0>
                          <tr><td>".$_SESSION['lang']['nopp']."</td><td>:</td><td><input type='text' id='txtNopp' name='txtNopp' onkeypress='return validat(event);' style='width:150px' class=myinputtext /></td>";
                    echo "<td>".$_SESSION['lang']['tanggal']." PP </td><td>:</td><td><input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;   maxlength=10 style=width:150px /></td>";
                    echo "</td><td>".$_SESSION['lang']['periode']." ".$_SESSION['lang']['dari']."</td><td>:</td><td><select id=periodedari name=periodedari onchange=validDate() style='width:100px;'>".$optper."</select>&nbsp;&nbsp;";
                    echo $_SESSION['lang']['sampai']."&nbsp;:&nbsp;<td><select id=periodesampai name=periodesampai onchange=validDate() style='width:100px;'>".$optper."</select></td></tr>";
                    echo "<tr><td>".$_SESSION['lang']['lokasiBeli']."</td><td>:</td><td><select id=lokBeli name=lokBeli style='width:150px;'>".$optLokal."</select></td>";
                    echo "<td>".$_SESSION['lang']['status']." PP</td><td>:</td><td><select id=statPP name=statPP style='width:150px;'><option value=''>".$_SESSION['lang']['all']."</option>".$optPil."</select></td>";
                    echo "<td>".$_SESSION['lang']['namasupplier']."</td><td>:</td><td><select id=\"supplier_id\" name=\"supplier_id\"  style=\"width:150px;\" >
                        ".$optSupplier."</select><img src=\"images/search.png\" class=\"resicon\" title='".$_SESSION['lang']['findRkn']."' onclick=\"searchSupplier('".$_SESSION['lang']['findRkn']."','<fieldset><legend>".$_SESSION['lang']['find']."</legend>".$_SESSION['lang']['find']."&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>',event);\"></td></tr>";
                    echo"<tr><td>".$_SESSION['lang']['namabarang']."</td><td>:</td><td><input type='text' id='txtNmBrg' name='txtNmBrg' onkeypress='return validat(event);' style='width:150px' class=myinputtext /></td>
                         <td>".$_SESSION['lang']['purchaser']."</td><td>:</td><td><select id=purchaserId style=150px>".$optPurchaser."</td>"     
                       . "</tr></table>";
                    echo"<button class=mybutton onclick=savePil()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
         </table> "; 
?>
</div>
<?php 
CLOSE_BOX();
OPEN_BOX();

?>

    <fieldset>
    <legend><?php echo $_SESSION['lang']['list']?></legend>
     <img onclick=dataKeExcel(event,'log_slave_2riwayatPPExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
         <img onclick=dataKePDF(event) title='PDF' class=resicon src=images/pdf.jpg>

        <div style="overflow:scroll; height:400px; width:1000px;">
                <table class="sortable" cellspacing="1" border="0" width="2000px">
                                <thead>
                                <tr class=rowheader>
                                        <td>No.</td>
                    <td><?php echo $_SESSION['lang']['nopp'] ?></td>
                    <td><?php echo $_SESSION['lang']['tanggal'] ?></td>
                                        <td><?php echo $_SESSION['lang']['namabarang'] ?></td>
                    <td><?php echo $_SESSION['lang']['jumlah']; ?></td>
                    <td><?php echo $_SESSION['lang']['satuan']; ?></td>
                                        <td><?php echo $_SESSION['lang']['status']; ?></td>
                                        <td><?php echo "O.Std";?></td>
                    <td><?php echo $_SESSION['lang']['chat']; ?></td>
                                        <td><?php echo $_SESSION['lang']['nopo']; ?></td>
                                        <td><?php echo $_SESSION['lang']['tgl_po']; ?></td>
                                        <td><?php echo $_SESSION['lang']['status']." PO";?></td>
                                        
                                         <td><?php echo $_SESSION['lang']['purchaser']; ?></td>
                                          <td>QTY PO</td>
                                           <td>QTY BAPB</td>

                                        <td><?php echo $_SESSION['lang']['namasupplier'] ?></td>
                                        <td><?php echo $_SESSION['lang']['rapbNo'] ?></td>
                                        <td><?php echo $_SESSION['lang']['tanggal'] ?></td>
                    <td>Action</td>
                                </tr>
                                </thead>
                                <tbody  id="contain">
        <script>loadData()</script>
        </tbody>
    </table>
    </div>
    </fieldset>

<?php
CLOSE_BOX();
?>
<?php
echo close_body();
?>