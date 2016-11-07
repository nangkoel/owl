<?php
    //@Copy nangkoelframework
    require_once('master_validation.php');
    include('lib/nangkoelib.php');
    include_once('lib/zLib.php');
    echo open_body();
    include('master_mainMenu.php');
    OPEN_BOX('',"<b>".$_SESSION['lang']['material']." SPK</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/kebun_slave_pemakaianMaterialSPK.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />
<div id="headher">
<?php
    $optKeg="<option value=''>[ no SPK data ]</option>";
    $optBlok="<option value=''>[ no SPK data ]</option>";
?>
<fieldset style='float:left;'>
    <legend><?php echo $_SESSION['lang']['form']?></legend>
    <table cellspacing="1" border="0">
       <tr>
            <td><?php echo $_SESSION['lang']['nomor']?> SPK</td><td>:</td>
            <td>
                <input type='text' class='myinputtext' id='nospk' onkeypress="return tanpa_kutip();"  size='10' maxlength='30' style="width:150px;" />
                <button class=mybutton id='carispk' onclick=carispk()><?php echo $_SESSION['lang']['find']?></button>
            </td>
        </tr>
        <tr>
            <td><?php echo $_SESSION['lang']['kegiatan']?></td><td>:</td>
            <td><select id='kegiatan' style="width:200px"><?php echo $optKeg; ?></select></td>
        </tr>
        <tr>
            <td><?php echo $_SESSION['lang']['blok']?></td><td>:</td>
            <td><select id='blok' onchange="caritanggal();" style="width:200px"><?php echo $optBlok; ?></select></td>
        </tr>
        <tr>
            <td><?php echo $_SESSION['lang']['tanggal']?></td><td>:</td>
            <td><input type='text' class='myinputtext' id='tanggal' onkeypress='return false;'  size='10' maxlength='10' style="width:150px;" /></td>
        </tr>
        <tr>
            <td><?php echo $_SESSION['lang']['namabarang']?></td><td>:</td>
            <td>
                <input type='text' class='myinputtext' id='namabarang' onkeyup ='resetkobar();' onkeypress="return tanpa_kutip();"  size='10' maxlength='30' style="width:150px;" />
                <input type='hidden' id='kodebarang' name='kodebarang' />
                <?php echo"<input type='image' id=search1 src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg(1,'".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg value=".$namabarang."><button class=mybutton onclick=findBrg(1)>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=".$key.">',event)\";>";?>
            </td>
        </tr>
        <tr>
            <td><?php echo $_SESSION['lang']['jumlah']?></td><td>:</td>
            <td>
                <input type='text' class='myinputtext' id='jumlah' onkeypress="angka_doang(event);"  size='10' maxlength='30' style="width:150px;" />
                <label id='satuan'>
            </td>
        </tr>

        <tr>
        <td colspan="3" id="tmblHeader">
            <button class=mybutton id=dtlForm onclick=saveForm()><?php echo $_SESSION['lang']['save']?></button>
            <button class=mybutton id=cancelForm onclick=cancelForm()><?php echo $_SESSION['lang']['cancel']?></button>
        </td>
        </tr>
    </table><input type="hidden" id="hiddenz" name="hiddenz" />
</fieldset>

<?php
    CLOSE_BOX();
?>
</div>
<div id="list_ganti">
<?php OPEN_BOX()?>
<div id="action_list">

</div>
<fieldset style='float:left;'>
    <legend><?php echo $_SESSION['lang']['list']?></legend>
    <table cellspacing="1" border="0" class="sortable">
        <thead>
            <tr class="rowheader">
            <td>No.</td>
            <td><?php echo $_SESSION['lang']['nomor'].' SPK'?></td>
            <td><?php echo $_SESSION['lang']['kegiatan']?></td>
            <td><?php echo $_SESSION['lang']['kodeblok']?></td>
            <td><?php echo $_SESSION['lang']['tanggal']?></td>
            <td><?php echo $_SESSION['lang']['namabarang']?></td>
            <td><?php echo $_SESSION['lang']['jumlah']?></td>
            <td><?php echo $_SESSION['lang']['satuan']?></td>
            <td><?php echo $_SESSION['lang']['action']?></td>
            </tr>
        </thead>
        <tbody id="contain">
        <?php
            $kamusbarang=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
            $kamussatuan=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
            $kamuskegiatan=makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');

            $arrNmkary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');

            $limit=10;
            $page=0;
            if(isset($_POST['page']))
            {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
            }
            $offset=$page*$limit;

            $ql2="select count(*) as jmlhrow from ".$dbname.".log_baspk_material order by `notransaksi` desc";// echo $ql2;
            $query2=mysql_query($ql2) or die(mysql_error());
            while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
            }


            $slvhc="select * from ".$dbname.".log_baspk_material order by `notransaksi` desc,`kodekegiatan`,`blok`,`tanggal`,`kodebarang` limit ".$offset.",".$limit." ";
            $qlvhc=mysql_query($slvhc) or die(mysql_error());
            $user_online=$_SESSION['standard']['userid'];
            while($rlvhc=mysql_fetch_assoc($qlvhc))
            {
            $no+=1;

        ?>
        <tr class="rowcontent">
        <td><?php echo $no?></td>
        <td><?php echo $rlvhc['notransaksi']?></td>
        <td><?php echo $kamuskegiatan[$rlvhc['kodekegiatan']]?></td>
        <td><?php echo $rlvhc['blok']?></td>
        <td><?php echo $rlvhc['tanggal']?></td>
        <td><?php echo $kamusbarang[$rlvhc['kodebarang']]?></td>
        <td align="right"><?php echo $rlvhc['jumlah'];?></td>
        <td><?php echo $kamussatuan[$rlvhc['kodebarang']]?></td>

        <?php 
            echo"<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['notransaksi']."','".$rlvhc['kodekegiatan']."','".$rlvhc['blok']."','".$rlvhc['tanggal']."','".$rlvhc['kodebarang']."');\" ></td>";
        ?>
        </tr>

        <?php 
            }
            echo"<tr class=rowheader><td colspan=9 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
        ?>

        </tbody>
    </table>
</fieldset>
<?php CLOSE_BOX()?>
</div>
<?php 
    echo close_body();
?>