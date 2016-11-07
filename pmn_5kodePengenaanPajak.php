<?php
    //@Copy nangkoelframework
    require_once('master_validation.php');
    include('lib/nangkoelib.php');
    include_once('lib/zLib.php');
    include_once('lib/rTable.php');
    echo open_body();
    include('master_mainMenu.php');
    OPEN_BOX('',"<b>".$_SESSION['lang']['form']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/pmn_5kodePengenaanPajak.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />

<div id="tambah">
<fieldset style='float:left;'>
<legend><?php echo $_SESSION['lang']['form']?></legend>
<table cellspacing="1" border="0" >
    <tr>
        <td><?php echo $_SESSION['lang']['kodeabs']?></td><td>:</td>
        <td><input type='text' class='myinputtext' id='kode'  size='10' maxlength='35' style="width:200px;" /></td>
    </tr>
    <tr>
        <td><?php echo $_SESSION['lang']['keterangan']?></td><td>:</td>
        <td><input type='text' class='myinputtext' id='nama' onkeypress="return tanpa_kutip();"  size='10' style="width:200px;" /></td>
    </tr>
    <tr>
    <td colspan="3" id="tmblHeader">
        <button class=mybutton id=saveForm onclick=saveForm()><?php echo $_SESSION['lang']['save']?></button>
        <button class=mybutton id=cancelForm onclick=cancelForm()><?php echo $_SESSION['lang']['cancel']?></button>
    </td>
    </tr>
</table><input type="hidden" id="hiddenz" name="hiddenz" />
</fieldset>
</div>
<?php CLOSE_BOX()?>
<?php OPEN_BOX()?>
<fieldset style='float:left;'>
    <legend><?php echo $_SESSION['lang']['list']?></legend> 
    <table cellspacing="1" border="0" class="sortable">
        <thead>
            <tr class="rowheader">
            <td align="center">No.</td>
            <td align="center"><?php echo $_SESSION['lang']['kodeabs']?></td>
            <td align="center"><?php echo $_SESSION['lang']['keterangan']?></td>
            <td colspan="3" align="center"><?php echo $_SESSION['lang']['action']?></td>
            </tr>
        </thead>
        <tbody id="contain">
        <?php
            
            $limit=10;
            $page=0;
            if(isset($_POST['page']))
            {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
            }
            $offset=$page*$limit;

            $sCount="select count(*) as jmlhrow from ".$dbname.".pmn_5fakturkode order by kode asc";
            $qCount=mysql_query($sCount) or die(mysql_error());
            while($rCount=mysql_fetch_object($qCount)){
                $jmlbrs= $rCount->jmlhrow;
            }

            $sShow="select * from ".$dbname.".pmn_5fakturkode order by kode asc limit ".$offset.",".$limit." ";
            $qShow=mysql_query($sShow) or die(mysql_error());
            while($row=mysql_fetch_assoc($qShow))
            {
                $no+=1;
                echo"<script>loadNData()</script>";
                echo"<td><img src=images/edit.png class=resicon  title='Edit' onclick=\"editRow('".$row['kode']."','".$row['nama']."');\" ></td>";
                echo"<td><img src=images/delete1.jpg class=resicon  title='Delete' onclick=\"delData('".$row['kode']."','".$row['nama']."')></td></tr>";
            }
            echo"<tr class=rowheader><td colspan=5 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jmlbrs."<br />
                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
        ?>

        </tbody>
    </table>
</fieldset>
<?php CLOSE_BOX()?>