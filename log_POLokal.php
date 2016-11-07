<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/log_po_lokal.js" /></script>
<div id="action_list">
<?php
/*echo"<pre>";
print_r($_SESSION);
echo"</pre>";*/
echo"<table>
     <tr valign=moiddle>
         <!--<td align=center style='width:100px;cursor:pointer;' onclick=show_list_pp()>
           <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>-->
         <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
           <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
         <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
                        echo $_SESSION['lang']['carinopo'].":<input type=text id=txtsearch size=25 maxlength=30 onkeypress=\"return validat(event);\" class=myinputtext>";
                        echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
                        echo"<button class=mybutton onclick=cariNopo()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>";
echo"<td><fieldset><legend>List Job</legend><div id=notifikasiKerja>";
echo"<script>loadNotifikasi()</script>";
echo"</div>
</fieldset></td>";
echo"</tr>
         </table> "; 
?>
</div>
<?php
CLOSE_BOX(); //1 C
echo "<div id=\"list_po\">";
OPEN_BOX(); //2 O
?>	
<input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['standard']['userid']?>" />
<input type="hidden" id="proses" name="proses" value="insert" />
<fieldset>
    <legend><?php echo $_SESSION['lang']['listpo']?></legend>
  <div  id='contain'><script>load_new_data()</script></div>   
</fieldset>
<?php CLOSE_BOX();?>
</div>
<div id="list_pp" name="form_po" style="display:none;">
   <?php OPEN_BOX();?>
    <fieldset>
        <legend><?php echo $_SESSION['lang']['list_pp'] ?></legend>
    <?php
        $optPt='';
        $sql3="select `kodeorganisasi`,`namaorganisasi` from ".$dbname.".organisasi where tipe='PT'";
                //echo $sql3;
        $query3=mysql_query($sql3) or die(mysql_error());
        while($res3=mysql_fetch_object($query3))
        {		
                        $optPt.="<option value='".$res3->kodeorganisasi."'>".$res3->namaorganisasi."</option>";
        }

    ?>
         <div style="height:340px; width:100%; overflow:auto;">
        <table cellspacing="1" border="0">
        <tr>
        <td>Please Select Company</td>
        <td>:</td>
        <td><select id="kode_pt" name="kode_pt" onchange="cek_pp_pt('0')">
        <option value=""></option>
        <?php echo $optPt;?>
        </select></td></tr>	</table>
    <table cellspacing="1" border="0" id="list_pp_table">
        <thead>

        <tr class="rowheader">
            <td>No.</td>
            <td><?php echo $_SESSION['lang']['nopp']?></td>
            <td><?php echo $_SESSION['lang']['kodebarang']?></td>
            <td><?php echo $_SESSION['lang']['namabarang']?></td>
            <td><?php echo $_SESSION['lang']['satuan']?></td>
            <td><?php echo $_SESSION['lang']['jmlhDiminta']?></td>
            <td><?php echo $_SESSION['lang']['tgldibutuhkan']?></td>            
            <td><?php echo $_SESSION['lang']['jmlh_brg_blm_po']?></td>
            <td><?php echo $_SESSION['lang']['jmlhPesan']?></td>
            <td>Action</td>
        </tr>
        </thead>
            <tbody id="container_pp">	


        </tbody>
    </table>
    </div>
<input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['standard']['userid']?>" />
        </fieldset>

<?php
CLOSE_BOX();
?>
</div>
<div id="form_po" style="display:none;">
    <?php 

    OPEN_BOX();
        $isiOpt= array(1=>'Cash',2=>'Transfer',3=>'Giro',4=>'Cheque');
        $tgl_skrg=date("d-m-Y");
        foreach($isiOpt as $ter => $OptIsi)
        {
                $optTermpay.="<option value='".$ter."'>".$OptIsi."</option>";
        }
    $optSupplier='';
    $sql="select * from ".$dbname.".log_5supplier where kodekelompok='S001' order by namasupplier asc";
    $query=mysql_query($sql) or die(mysql_error());
    while($res=mysql_fetch_assoc($query))
    {
       $optSupplier.="<option value='".$res['supplierid']."'>".$res['namasupplier']."</option>";
    }
    ?>
    <fieldset>
        <legend><?php echo $_SESSION['lang']['form_po']?></legend>
    <table cellspacing="1" border="0">
        <tr>
            <td><?php echo $_SESSION['lang']['nopo']?></td>
            <td>:</td>
            <td><input type="text" name="no_po" id="no_po" class="myinputtext" style="width:200px;" disabled="disabled"/></td>
        </tr>
        <tr>
            <td><?php echo $_SESSION['lang']['tanggal']?></td>
            <td>:</td>
            <td><input type="text" name="tgl_po" id="tgl_po" class="myinputtext" value="<?php echo $tgl_skrg;?>" disabled="disabled" style="width:200px;"  /></td>
        </tr>
         <tr>
            <td><?php echo $_SESSION['lang']['namasupplier']?></td>
            <td>:</td>
            <td>
                        <select id="supplier_id" name="supplier_id" onchange="get_supplier()" style="width:200px;" >
                        <option value=""></option>
                        <?php echo $optSupplier; ?>
                        </select> <img src="images/search.png" class="resicon" title='<?php echo $_SESSION['lang']['findRkn']; ?>' onclick="searchSupplier('<?php echo $_SESSION['lang']['findRkn']; ?>','<fieldset><legend><?php echo $_SESSION['lang']['find']?></legend><?php echo $_SESSION['lang']['namasupplier']; ?>&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()><?php echo $_SESSION['lang']['find']; ?></button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>',event);"></td>
        </tr>
        <tr>
            <td><?php echo $_SESSION['lang']['norekeningbank']?></td>
                        <td>:</td>
                        <td><input type="text" id="bank_acc" name="bank_acc" class="myinputtext" onkeypress="return angka_doang(event)" disabled="disabled" style="width:200px;"  /></td>
        </tr>
                <tr>
            <td><?php echo $_SESSION['lang']['npwp']?></td>
                        <td>:</td>
                        <td><input type="text" id="npwp_sup" name="npwp_sup" class="myinputtext" onkeypress="return angka_doang(event)" disabled="disabled" style="width:200px;"  /></td>
        </tr>
        </table>
        <tr>
            <td colspan="3">
                        <fieldset style="width:60%">
                                <legend><?php echo $_SESSION['lang']['daftarbarang']?></legend>
                <table cellspacing="1" border="0" id="detail_content_table" name="detail_content_table">
                    <tbody id="detail_content" name="detail_content">
                       <tr><td>
                       <!-- form detail barang--><table id='ppDetailTable'> </table>

                       <!-- end form detail barang-->
                       <!-- addtional data-->
                       <?php

                                           ?>
                      <table cellspacing='1' border='0'>
        <tr>
            <td><?php echo $_SESSION['lang']['tgl_kirim'] ?></td>
                        <td>:</td>
                        <td><input type="text" class="myinputtext" id="tgl_krm" name="tgl_krm" onmousemove="setCalendar(this.id)" onkeypress="return false";   maxlength="10"  style="width:200px" value="" /></td>
        </tr>
                  <tr>
            <td><?php echo $_SESSION['lang']['almt_kirim'] ?></td>
                        <td>:</td>
                        <td><input type='text'  id='tmpt_krm' name='tmpt_krm' maxlength='45' class='myinputtext' onkeypress='return tanpa_kutip(event);' style="width:200px" value=""  /></td>
        </tr>
		<?php
                $arragama=getEnum($dbname,'log_poht','statusbayar');
                foreach($arragama as $kei=>$fal)
                {
                        $OptCrByr.="<option value='".$kei."'>".$fal."</option>";
                }  
        ?>
        <tr>
            <td><?php echo $_SESSION['lang']['pembayaran'] ?></td>
            <td>:</td>
            <td><select id="crByr" style="width:200px" ><?php echo $OptCrByr; ?></select></td>
        </tr>
         <tr>
            <td><?php echo $_SESSION['lang']['syaratPem'] ?></td>
                        <td>:</td>
                        <td><input type='text' id='term_pay' name='term_pay' class='myinputtext' onkeypress='return tanpa_kutip(event);' style="width:200px"  value="" /></td>
        </tr>
                 <tr>
            <td><?php echo $_SESSION['lang']['keterangan'] ?></td>
                        <td>:</td>
                        <td><textarea id='ketUraian' name='ketUraian' onkeypress='return tanpa_kutip(event);'></textarea></td>
        </tr>
                <tr>
            <td><?php echo $_SESSION['lang']['purchaser'] ?></td>
                        <td>:</td>
                        <td><input type="text" id="purchaser_id" name="purchaser_id" disabled="disabled" class="myinputtext" value="<?php echo $_SESSION['empl']['name']?>" style="width:200px"  /> </td>
        </tr>
        </table>





                       </td></tr>
                    </tbody>
                </table>
                        </fieldset>
            </td>
        </tr>

        <table cellspacing="1" border="0">
        <tr>
            <td colspan="3">
                <button class="mybutton" onclick=save_headher()><?php echo $_SESSION['lang']['save']?></button>
                <button class="mybutton" onclick=cancel_headher()><?php echo $_SESSION['lang']['cancel']?></button>
                                <?php
                                //cek persetujuan1
                                        $sql_cek="select persetujuan1 from ".$dbname.".log_poht where";
                                ?>
                                <!--<button class="mybutton" onclick=get_data_pp() ><?php echo $_SESSION['lang']['done']?></button>-->
            </td>
        </tr> 

    </table>
        </fieldset>
    <?php CLOSE_BOX(); ?>
</div>

<?php
echo close_body();
?>