<?PHP
    //@Copy nangkoelframework
    require_once('master_validation.php');
    include('lib/nangkoelib.php');
    include_once('lib/zLib.php');
    echo open_body();
    include('master_mainMenu.php');
    OPEN_BOX(); //1 O
    ?>

    <link rel="stylesheet" type="text/css" href="style/zTable.css">
    <script language="javascript" src="js/zMaster.js"></script>
     <script language=javascript src='js/zTools.js'></script>
    <script type="text/javascript" src="js/log_pnwrharga.js" /></script>

    <script>
     jdl_ats_0='<?php echo $_SESSION['lang']['find']?>';
    // alert(jdl_ats_0);
     jdl_ats_1='<?php echo $_SESSION['lang']['findBrg']?>';
     content_0='<fieldset><legend><?php echo $_SESSION['lang']['findnoBrg']?></legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>';
     Option_Isi='<?php 
            $optKurs="<option value=>".$_SESSION['lang']['pilihdata']."</option>";
     $sKurs="select kode,kodeiso from ".$dbname.".setup_matauang order by kode desc";
            $qKurs=mysql_query($sKurs) or die(mysql_error());
            while($rKurs=mysql_fetch_assoc($qKurs))
            {
                    $optKurs.="<option value=".$rKurs['kode'].">".$rKurs['kodeiso']."</option>";
            } 
            echo $optKurs;?>';
     isi_option="<?php ?>";
    </script>
    <div id="action_list">
    <?php
    echo"<table>
         <tr valign=moiddle>
             <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
               <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
             <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>";
                            echo $_SESSION['lang']['notransaksi'].":<input type=text id=txtsearch size=25 maxlength=30 onkeypress=\"return validat(event);\" class=myinputtext>";
                            echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
                            echo"<button class=mybutton onclick=cariPnwrn()>".$_SESSION['lang']['find']."</button>";
    echo"</fieldset></td>";
    echo"<td><fieldset><legend>List Job</legend><div id=notifikasiKerja>";

    echo"</div>
    </fieldset></td>";


    echo"</tr>
             </table> ";

    ?>
    </div>
    <?php
    CLOSE_BOX();
    ?>

    <div id="list_permintaan" name="list_permintaan">
        <?php OPEN_BOX();?>
        <fieldset>
            <legend><?php echo $_SESSION['lang']['permintaan'];?></legend>
            <div id="dlm_list_permintaan" name="dlm_list_permintaan" style="overflow: scroll; height:420px;">
                <table class="sortable" cellspacing="1" border="0">
                <thead>
                <tr class=rowheader>
                <td>No.</td>
                <td><?php echo $_SESSION['lang']['notransaksi']?></td>
                 <td><?php echo $_SESSION['lang']['urutan'];?></td>
                <td><?php echo $_SESSION['lang']['tanggal'];?></td>
                <td><?php echo $_SESSION['lang']['namasupplier'];?></td>
                <td align="center">Action</td>
                </tr>
                </thead>
                <tbody id="contain">
                <script>get_data();</script>
                </tbody>
                </table>
            </div>
        </fieldset>
        <?php CLOSE_BOX();?>
    </div>
    <?php 
     $arr="";
    echo"<div id=formPP style=display:none>";
    OPEN_BOX();
    echo"</fieldset><input type=hidden id=noUrut value='1' /><input type=hidden id=notransaksi value='' />";
    $optKlmpk="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $sKlmpk="select distinct * from ".$dbname.".log_5klbarang order by kode asc";
    $qKlmpk=  mysql_query($sKlmpk) or die(mysql_error($conn));
    while($rKlmpk=  mysql_fetch_assoc($qKlmpk)){
        $optKlmpk.="<option value='".$rKlmpk['kode']."'>".$rKlmpk['kode']."-".$rKlmpk['kelompok']."</option>";
    }
    //tampilkan form mengambil get PP
    echo"<div id=listBrgPP  style=display:none>";
    echo"<fieldset  style=width:960px;><legend>".$_SESSION['lang']['daftarbarang']."</legend>";
    echo"<table border=0 cellpadding=1 cellspacing=1><tr class=rowheader>";
    echo"<td>".$_SESSION['lang']['nopp']."</td>";
    echo"<td><input type=text id=crNopp onkeypress=\"return validatPp(event);\" class=myinputtext style=width:150px /></td>";
    echo"<td>".$_SESSION['lang']['kelompokbarang']."</td>";
    echo"<td><select id=klmpkBrgCr style=width:150px>".$optKlmpk."</select></td>";
    echo"<td><button class=mybutton onclick=getPPDph()>".$_SESSION['lang']['find']."</button></td></tr></table><input type=hidden id=ptcari />";
       echo" <div style='width:940px;display:fixed;'>
        <table border=0 cellpadding=1 cellspacing=1 class=sortable>
        <thead><tr class=rowheader>
        <td style=width:20px>No.</td>
        <td style=width:180px>".$_SESSION['lang']['nopp']."</td>
        <td style=width:88px>".$_SESSION['lang']['kodebarang']."</td>
        <td style=width:550px>".$_SESSION['lang']['namabarang']."</td>
        <td style=width:50px>".$_SESSION['lang']['jumlah']."</td>
        <td style=width:50px>".$_SESSION['lang']['satuan']."</td>
        <td style=width:10px><input type=checkbox onclick=clikcAll() id=dtSemua /></td></tr></thead><tbody> 
        </tbody></table></div>
        <div style='width:960px;height:340px;overflow:scroll;'>
               <table class=sortable cellspacing=1 border=0 width=940px>
                <thead>
                <tr>
                </tr>  
                </thead>
                <tbody id=dataBarang>

                </tbody>
             </table>
         </div>
    </fieldset>
    </div>";

    //tampilkan form persyaratan permintaan
        $optTermPay="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $optStock=$optTermPay;
        $optKrm=$optTermPay;
        $arrOptTerm=array("1"=>"Tunai","2"=>"Kredit 2 Minggu","3"=>"Kredit 1 Bulan","4"=>"Termin","5"=>"DP");
        foreach($arrOptTerm as $brsOptTerm =>$listTerm)
        {
            $optTermPay.="<option value='".$brsOptTerm."'>".$listTerm."</option>";
        }
        $sKrm="select id_franco,franco_name from ".$dbname.".setup_franco where status=0 order by franco_name asc";
        $qKrm=mysql_query($sKrm) or die(mysql_error($conn));
        while($rKrm=mysql_fetch_assoc($qKrm))
        {
                        $optKrm.="<option value=".$rKrm['id_franco'].">".$rKrm['franco_name']."</option>";
        }
         $arrStock=array("1"=>"Ready Stock","2"=>"Not Ready");   
         foreach($arrStock as $brsStock => $listStock)
         {
             $optStock.="<option value='".$brsStock."'>".$listStock."</option>";
         }
         $optMt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $sMt="select kode,kodeiso from ".$dbname.".setup_matauang order by kode desc";
            $qMt=mysql_query($sMt) or die(mysql_error());
            while($rMt=mysql_fetch_assoc($qMt))
            {
                    $optMt.="<option value=".$rMt['kode'].">".$rMt['kodeiso']."</option>";
            }
    echo"<br /><div id=listSupplier style=display:none>";
    echo"<fieldset style=width:450px;><legend>".$_SESSION['lang']['permintaan']."</legend>";
    echo"<table cellspacing=\"1\" border=\"0\">
                <tr>
                <td>".$_SESSION['lang']['matauang']."</td>
                <td>:</td>
                <td><select id=\"mtUang\" name=\"mtUang\" style=\"width:150px;\" >".$optMt."</select></td>
                </tr>
                <tr>
                <td>".$_SESSION['lang']['kurs']."</td>
                <td>:</td>
                <td><input type=\"text\" class=\"myinputtext\" id=\"Kurs\" name=\"Kurs\" style=\"width:150px;\" onkeypress=\"return angka_doang(event)\"  /></td>
                </tr>
                <tr>
                <td>".$_SESSION['lang']['syaratPem']."</td>
                <td>:</td>
                <td><select id='term_pay' name='term_pay' style=\"width:200px\">".$optTermPay."</select></td>
                <td>&nbsp;</td>
                </tr>
                <tr>
                <td>".$_SESSION['lang']['almt_kirim']."</td>
                        <td>:</td>
                        <td><select id='tmpt_krm' name='tmpt_krm' style=\"width:200px;\">".$optKrm."</select></td>
                        <td>&nbsp;</td>
                </tr>
                <tr>
                <td>".substr($_SESSION['lang']['stockdetail'],0,5)."</td>
                <td>:</td>
                <td><select id='stockId' name='stockId' style=\"width:200px\">".$optStock."</select></td>
                <td>&nbsp;</td>
                </tr>
                <tr>
                <td>". $_SESSION['lang']['keterangan']."</td>
                <td>:</td>
                <td><textarea id='ketUraian' name='ketUraian' onkeypress='return tanpa_kutip(event);'></textarea></td>
                <td>&nbsp;</td>
                </tr>
                <tr><td colspan=3 align=center><button class=mybutton onclick='lanjutAdd2()'  >".$_SESSION['lang']['lanjut']."</button></td></tr>
            </table>";
    echo"</fieldset>";
    echo"</div>";
    //end tampilkan form persyaratan
        $sql="select namasupplier,supplierid from ".$dbname.".log_5supplier order by namasupplier asc";
        $query=mysql_query($sql) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
        $optSupplier.="<option value='".$res['supplierid']."'>".$res['namasupplier']."</option>";
        }
    //form supplier

    echo"<div id=supplierForm style=display:none><input type=hidden id=noppr  />";
    echo"<fieldset style=width:550px;><legend>Data Supplier</legend>";
    echo"<table cellpadding=1 cellspacing=1 border=0>";
    echo"<tr>
                    <td>".$_SESSION['lang']['namasupplier']."</td>
                    <td>:</td>
                    <td>
                        <select id=\"id_supplier\" name=\"id_supplier\" style=\"width:200px;\" disabled=\"disabled\">".$optSupplier."</select>
                    </td>
                    <td><img src='images/search.png' class=dellicon title='".$_SESSION['lang']['findRkn']."' onclick=\"searchSupplier('".$_SESSION['lang']['findRkn']."','<fieldset><legend>".$_SESSION['lang']['findRkn']."</legend>".$_SESSION['lang']['namasupplier']."&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>',event);\"></td>
                </tr>";
    echo"<tr><td colspan=3 ><button class=mybutton onclick='addDataSma()'  >Add Data</button>&nbsp;<button class=mybutton onclick=zPreview2('log_slave_save_permintaan_harga','". $arr."','printContainer2')  >".$_SESSION['lang']['done']."</button></td></tr></table>";
    echo"</fieldset>";
    echo"<fieldset style=width:550px;><legend>".$_SESSION['lang']['data']."</legend>";
    echo"<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
    echo"<tr class=rowheader>";
    echo"<td>No.</td>";
    echo"<td>".$_SESSION['lang']['nopermintaan']."</td>";
    echo"<td>".$_SESSION['lang']['namasupplier']."</td>";
    echo"<td>".$_SESSION['lang']['action']."</td>";
    echo"</thead><tbody id=listHasilSave>";
    echo"</tbody></table>";
    echo"</fieldset>";
    echo"</div>";

    CLOSE_BOX();
    echo"</div>";

    echo"<div id=formPP2  style=display:none>";
    OPEN_BOX();
    $optListNopp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $sLnopp="select distinct nomor from ".$dbname.".log_perintaanhargaht where 
             purchaser='".$_SESSION['standard']['userid']."' order by nomor desc";
    $qLnopp=mysql_query($sLnopp) or die(mysql_error($conn));
    while($rLnopp=mysql_fetch_assoc($qLnopp))
    {

        $optListNopp.="<option value='".$rLnopp['nomor']."'>".$rLnopp['nomor']."</option>";
    }


    $arr="##nopp2##formPil";
    echo"<br /><fieldset style=width:350px;><legend>Form PP</legend>";
    echo"<input type=hidden id='formPil' name='formPil' value='1' /><table cellspacing=\"1\" border=\"0\" >
    <tr><td><label>".$_SESSION['lang']['nopp']."</label></td><td><select id=\"nopp2\" name=\"nopp2\"  style=\"width:200px;\" >".$optListNopp."</select><img  src='images/search.png' class=dellicon title='".$_SESSION['lang']['find']." ".$_SESSION['lang']['nopp']."' onclick=\"searchNopp('".$_SESSION['lang']['find']." ".$_SESSION['lang']['nopp']."','<fieldset><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['nopp']."</legend>".$_SESSION['lang']['find']."&nbsp;<input type=text class=myinputtext id=kdNopp><button class=mybutton onclick=findNopp2()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerNopp style=overflow=auto;height=380;width=485></div>',event);\"></td></tr>
    <tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
    <tr><td colspan=\"2\">
    <button onclick=\"zPreview('log_slave_2perbandingan_harga','". $arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
    <button onclick=\"zExcel(event,'log_slave_2perbandingan_harga.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>    
    </td></tr>
    </table>";
    echo"</fieldset>";
    CLOSE_BOX();

        echo"<div id=formEditData  style=display:none>";
        OPEN_BOX();
        echo"<fieldset style='clear:both'><legend><b>Edit Area</b></legend>";
        echo"<div id='printContainer'  style='overflow:auto;height:550px;width:1200px'>";
        echo"</div>";
        echo"</fieldset>";
        CLOSE_BOX();
        echo"</div>";
    echo"</div>";
    echo"<div id='formEditData2'  style=display:none>";
    OPEN_BOX();
    echo"<fieldset style='clear:both'><legend><b>Edit Area</b></legend>";
    echo"<div id='printContainer2'  style='overflow:auto;height:550px;width:1200px'>";
    echo"</div>";
    echo"</fieldset>";
    CLOSE_BOX();
    echo"</div>";
    echo close_body(); 
    ?>