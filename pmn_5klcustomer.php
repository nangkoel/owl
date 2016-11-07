<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<!--<link rel=stylesheet type=text/css href=style/zTable.css>-->
<script language="javascript" src="js/pmn_5klcustomer.js"></script>
<fieldset>
<legend><b><?php echo $_SESSION['lang']['klmpkPlgn']?></b></legend>
<table cellpadding="2" cellspacing="2" border="0">
        <tr>
                <td><?php echo $_SESSION['lang']['kode']?></td>
                <td>:</td>
                <td><input type="text" class="myinputtext" id="kode_grp_cus" onkeypress="return tanpa_kutip(event);" /></td>
        </tr>
        <tr>
                <td><?php echo $_SESSION['lang']['kelompok_pem']?></td>
                <td>:</td>
                <td><input type="text" class="myinputtext" id="klmpk_cust" onkeypress="return tanpa_kutip(event);"  /></td>
        </tr>
        <tr>
                <td><?php echo $_SESSION['lang']['findnoakun']?></td>
                <td>:</td>
                <td><input type="hidden" id="akun_cust"  /><input type="text" id="nama_akun" class="myinputtext" disabled="disabled"/> <img src=images/search.png class=dellicon title=<?php echo $_SESSION['lang']['find']?> onclick="searchAkun('<?php echo $_SESSION['lang']['findnoakun']?>','<fieldset><legend><?php echo $_SESSION['lang']['findnoakun']?></legend>Find<input type=text class=myinputtext id=no_akun><button class=mybutton onclick=findAkun()>Find</button></fieldset><div id=container></div>',event)";>
                <input type="hidden" value="insert" id="method" />
                </td>
        </tr>
        <tr>
                <td colspan="3" align="center">
                <button class=mybutton onclick=simpanKlmpkplgn()><?php echo $_SESSION['lang']['save']?></button>
         <button class=mybutton onclick=batalKlmpkplgn()><?php echo $_SESSION['lang']['cancel']?></button>
                </td>
        </tr>
</table>
</fieldset>
<?php CLOSE_BOX();
?>

<?php OPEN_BOX();?>
<fieldset>
     <!--<legend><b><?php //echo $_SESSION['lang']['pmn_4klcustomer']; ?></b></legend>-->
         <table class=sortable cellspacing="1" border="0">
         <thead>
         <tr class=rowheader>
         <td>No.</td>
         <td><?php echo $_SESSION['lang']['kode']?></td>
         <td><?php echo $_SESSION['lang']['kelompok_pem']; ?></td>
         <td><?php echo $_SESSION['lang']['noakun']; ?></td>
         <td><?php echo $_SESSION['lang']['findnoakun'];?></td>
         <td colspan="2">Action</td>
         </tr>
         </thead>
         <tbody id=containersatuan>

         <?php
         $srt="select * from ".$dbname.".keu_5akun order by noakun";
         $po=mysql_query($srt) or die(mysql_error());
         $bar=mysql_fetch_object($po);
          $str="select * from ".$dbname.".pmn_4klcustomer order by kode desc";
          if($res=mysql_query($str))
          {
                while($bar=mysql_fetch_object($res))
                {
                        $noakun=$bar->noakun;
                         if($_SESSION['language']=='EN'){
                             $kol='namaakun1  as namaakun';
                         }else{
                             $kol='namaakun';
                         }
                        $spr="select $kol from  ".$dbname.".keu_5akun where `noakun`='".$noakun."'";
                        $rep=mysql_query($spr) or die(mysql_error($conn));
                        $bas=mysql_fetch_object($rep);
                        $no+=1;
                        echo"<tr class=rowcontent>
                                  <td>".$no."</td>
                                  <td>".$bar->kode."</td>
                                  <td>".$bar->kelompok."</td>
                                  <td>".$bar->noakun."</td>
                                  <td>".$bas->namaakun."</td>
                                  <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kode."','".$bar->kelompok."','".$bar->noakun."','".$bas->namaakun."');\"></td>
                                  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delKlmpkplgn('".$bar->kode."','".$bar->kelompok."','".$bar->noakun."');\"></td>
                                 </tr>";
                }	 	   	
          }	
          else
                {
                        echo " Gagal,".(mysql_error($conn));
                }

        ?>	
         </tbody>
         <tfoot>
         </tfoot>
         </table>
     </fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>