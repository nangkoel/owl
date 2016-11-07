<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');

?>
<script language=javascript src=js/pabrik_timbangan.js></script>
<script language="javascript" src="js/zMaster.js"></script>
<?php
OPEN_BOX('','WEIGHBRIDGE');

$sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where `kelompokbarang`='400'";
$qBrg=mysql_query($sBrg) or die(mysql_error());
while($rBrg=mysql_fetch_assoc($qBrg))
{
        $optBrg.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";
}


$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT'"; //echo $sOrg;
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
        $optPt.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

$arrStat=array("On","Off");
foreach($arrStat as $isi =>$tks)
{
        $optStatTimb.="<option value=".$isi.">".$tks."</option>";
}
//=========================
$frm[0].="

<fieldset>
          <legend>".$_SESSION['lang']['form']."</legend>
          <fieldset>
                        <legend>".$_SESSION['lang']['pilihdata']."</legend>
                <table>
            <tr> 	 
                 <td style='valign:top'>".$_SESSION['lang']['namabarang']."</td><td>
                <select id=kdBrg name=kdBrg onchange=\"getForm()\" style=\"width:150px;\"><option value=></option>".$optBrg."</select></td>
          </tr>
          </table>
          </fieldset>
          <fieldset>
                <legend>".$_SESSION['lang']['result']."</legend>
                <table cellspacing=1 border=0>
                        <tr>
                        <td id='content'>
                        </td>
                        </tr>
                        <tr>
                        <td>nb. Weiging default is TON</td></tr>
                </table>
          </fieldset>
          <br />
         </fieldset>";

$frm[1]="<fieldset>
           <legend>".$_SESSION['lang']['list']."</legend>
          <fieldset><legend></legend>
          ".$_SESSION['lang']['notransaksi']."
          <input type=text id=txtnotransaksi size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>
          <button class=mybutton onclick=cariNotansaksi()>".$_SESSION['lang']['find']."</button>
          </fieldset>
          <table class=sortable cellspacing=1 border=0>
      <thead>
          <tr class=rowheader>
          <td>No.</td>
          <td>".$_SESSION['lang']['notransaksi']."</td>
          <td>".$_SESSION['lang']['tanggal']."</td>
          <td>".$_SESSION['lang']['kodebarang']."</td>
          <td>".$_SESSION['lang']['namabarang']."</td>
          <td>".$_SESSION['lang']['jammasuk']."</td>
          <td>".$_SESSION['lang']['jamkeluar']."</td>
          <td>Action</td>
          </tr>
          </head>
           <tbody id=containerlist>
           <script>
           loadNewData();
           </script>
           </tbody>
           <tfoot>
           </tfoot>
           </table>
         </fieldset>";

$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['list'];

drawTab('FRM',$hfrm,$frm,100,900);
?>

<?php
CLOSE_BOX();
echo close_body();
?>