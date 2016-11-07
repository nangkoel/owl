<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language="javascript" src="js/pmn_5customer.js"></script>
<fieldset>
<legend><b><?php echo $_SESSION['lang']['customerlist']?></b></legend>
<table cellpadding="2" cellspacing="2" border="0">
        <tr>
                <td><?php echo $_SESSION['lang']['kodecustomer']?></td>
                <td>:</td>
                <td><input type="text" class="myinputtext" id="kode_cus" onkeypress="return tanpa_kutip(event);" /></td>
        </tr>
        <tr>
                <td><?php echo $_SESSION['lang']['klmpkcust']?></td>
                <td>:</td>
                <td><input type="hidden" id="klcustomer_code"  />
                <input type="text" id="nama_group" class="myinputtext" disabled="disabled"/> 
                <img src=images/search.png class=dellicon title=<?php echo $_SESSION['lang']['find']?> onclick="searchGruop('<?php echo $_SESSION['lang']['findgroup']?>','<fieldset><legend><?php echo $_SESSION['lang']['findgroup']?></legend>Find<input type=text class=myinputtext id=group_name><button class=mybutton onclick=findGroup()>Find</button></fieldset><div id=container_cari></div>',event)";></td>
        </tr>
        <tr>
                <td><?php echo $_SESSION['lang']['akun']?></td>
                <td>:</td>
                <td>
                <input type="hidden" id="akun_cust"  /><input type="text" id="nama_akun" class="myinputtext" disabled="disabled"/> <img src=images/search.png class=dellicon title=<?php echo $_SESSION['lang']['find']?> onclick="searchAkun('<?php echo $_SESSION['lang']['findnoakun']?>','<fieldset><legend><?php echo $_SESSION['lang']['findnoakun']?></legend>Find<input type=text class=myinputtext id=no_akun><button class=mybutton onclick=findAkun()>Find</button></fieldset><div id=container_cari_akun></div>',event)";>
                <!--<input type="text" class="myinputtext" id="no_akun" onkeypress="return tanpa_kutip(event);"  />-->
                </td>
        </tr>
        <tr>
                <td><?php echo $_SESSION['lang']['nmcust']?></td>
                <td>:</td>
                <td><input type="text" class="myinputtext" id="cust_nm" onkeypress="return tanpa_kutip(event);"  /></td>
        </tr>
        <tr>
                <td><?php echo $_SESSION['lang']['alamat']?></td>
                <td>:</td>
                <td>
                <input type="text" class="myinputtext" id="almt" onkeypress="return tanpa_kutip(event);"  />
                </td>
        </tr>
        <tr>
                <td><?php echo $_SESSION['lang']['kota']?></td>
                <td>:</td>
                <td>
                <input type="text" class="myinputtext" id="kta" onkeypress="return tanpa_kutip(event);"  />
                </td>
        </tr>
        <tr>
                <td><?php echo $_SESSION['lang']['telepon']?></td>
                <td>:</td>
                <td>
                <input type="text" class="myinputtext" id="tlp_cust" onkeypress="return angka_doang(event);"  />
                </td>
        </tr>
        <tr>
                <td><?php echo $_SESSION['lang']['kntprson']?></td>
                <td>:</td>
                <td>
                <input type="text" class="myinputtext" id="kntk_person" onkeypress="return tanpa_kutip(event);"  />
                </td>
        </tr>

        <tr>
                <td><?php echo $_SESSION['lang']['plafon']?></td>
                <td>:</td>
                <td>
                <input type="text" class="myinputtext" id="plafon_cus" onkeypress="return angka_doang(event);" value="0" />
                </td>
        </tr>
        <tr>
                <td><?php echo $_SESSION['lang']['nilaihutang']?></td>
                <td>:</td>
                <td>
                <input type="text" class="myinputtext" id="n_hutang" onkeypress="return angka_doang(event);"  value="0"/>
                </td>
        </tr>
        <tr>
                <td><?php echo $_SESSION['lang']['npwp']?></td>
                <td>:</td>
                <td>
                <input type="text" class="myinputtext" id="npwp_no" onkeypress="return tanpa_kutip(event);"  />
                </td>
        </tr>
        <tr>
                <td><?php echo $_SESSION['lang']['noseripajak']?></td>
                <td>:</td>
                <td>
                <input type="text" class="myinputtext" id="seri_no" onkeypress="return tanpa_kutip(event);"  />
        
                </td>
        </tr>
        
        
        <tr>
                <td>Penandatangan Kontrak</td>
                <td>:</td>
                <td>
                <input type="text" class="myinputtext" id="pk" onkeypress="return tanpa_kutip(event);"  />
       
                </td>
        </tr>
        
        <tr>
                <td>Jabatan Penandatangan Kontrak</td>
                <td>:</td>
                <td>
                <input type="text" class="myinputtext" id="jpk" onkeypress="return tanpa_kutip(event);"  />
        <input type="hidden" value="insert" id="method" />
                </td>
        </tr>
        
        
        
        <tr>
                <td colspan="3" align="center">
                <button class=mybutton onclick=simpanPlgn()><?php echo $_SESSION['lang']['save']?></button>
         <button class=mybutton onclick=batalPlgn()><?php echo $_SESSION['lang']['cancel']?></button>
                </td>
        </tr>
</table>
</fieldset>
<?php CLOSE_BOX();
OPEN_BOX();
?>
<fieldset>
         <table class="sortable" cellspacing="1" border="0">
         <thead>
         <tr class=rowheader>
         <td>No.</td>
         <td><?php echo $_SESSION['lang']['kodecustomer']?></td>
         <td><?php echo $_SESSION['lang']['kntprson'];?></td>
         <td><?php echo $_SESSION['lang']['nmcust'];?></td>
         <td><?php echo $_SESSION['lang']['telepon']; ?></td>
         <td><?php echo $_SESSION['lang']['noakun'];?></td>
         <td><?php echo $_SESSION['lang']['akun'];?></td>
         <td><?php echo $_SESSION['lang']['plafon'];?></td> 
         <td><?php echo $_SESSION['lang']['nilaihutang'];?></td>
         <td><?php echo $_SESSION['lang']['klmpkcust'];?></td>
         <td>Penandatangan Kontrak</td>
         <td>Jabatan Penandatangan Kontrak</td>
         <td colspan="2">Action</td>
         </tr>
         </thead>
         <tbody id="container">
         <?php 
                //ambil data dari tabel kelompok customer

                $srt="select * from ".$dbname.".pmn_4customer order by kodecustomer desc";  //echo $srt;
                if($rep=mysql_query($srt))
                  {
                        $no=0;
                        while($bar=mysql_fetch_object($rep))
                        {
                        //get kelompok cust
                        $sql="select * from ".$dbname.".pmn_4klcustomer where `kode`='".$bar->klcustomer."'";
                        $query=mysql_query($sql) or die(mysql_error($conn));
                        $res=mysql_fetch_object($query);

                        //get akun
                        $spr="select * from  ".$dbname.".keu_5akun where `noakun`='".$bar->akun."'";
                        $rej=mysql_query($spr) or die(mysql_error($conn));
                        $bas=mysql_fetch_object($rej);
                        $no++;
                        echo"<tr class=rowcontent>
                                  <td>".$no."</td>
                                  <td>".$bar->kodecustomer."</td>
                                  <td>".$bar->kontakperson."</td>
                                  <td>".$bar->namacustomer."</td>
                                  <td>".$bar->telepon."</td>
                                  <td>".$bar->akun."</td>
                                  <td>".$bas->namaakun."</td>
                                  <td>".$bar->plafon."</td>
                                  <td>".$bar->nilaihutang."</td>
                                  <td>".$res->kelompok."</td>
								  <td>".$bar->pk."</td>
								  <td>".$bar->jpk."</td>
                                  <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodecustomer."','".$bar->namacustomer."','".$bar->alamat."','".$bar->kota."','".$bar->telepon."','".$bar->kontakperson."','".$bar->akun."','".$bar->plafon."','".$bar->nilaihutang."','".$bar->npwp."','".$bar->noseri."','".$bar->klcustomer."','".$bas->namaakun."','".$res->kelompok."','".$bar->pk."','".$bar->jpk."');\"></td>
                                  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPlgn('".$bar->kodecustomer."');\"></td>
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