<?php
    //@Copy nangkoelframework
    require_once('master_validation.php');
    include('lib/nangkoelib.php');
    include_once('lib/zLib.php');
    include_once('lib/rTable.php');
    echo open_body();
    include('master_mainMenu.php');
    OPEN_BOX('',"<b>".$_SESSION['lang']['tambah']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/help_tambah.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />

<div id="tambah">
<fieldset style='float:left;'>
<legend><?php echo $_SESSION['lang']['form']?></legend>
<table cellspacing="1" border="0" style="width:1000px;">
    <tr>
        <td><?php echo $_SESSION['lang']['index']?></td><td>:</td>
        <td><input disabled type='text' class='myinputtext' id='index'  size='10' maxlength='35' style="width:200px;" /></td>
    </tr>
    <tr>
        <td><?php echo $_SESSION['lang']['tentang']?></td><td>:</td>
        <td><input type='text' class='myinputtext' id='tentang' onkeypress="return tanpa_kutip();"  size='10' style="width:200px;" /></td>
    </tr>
    <tr>
        <td><?php echo $_SESSION['lang']['modul']?></td><td>:</td>
        <td><input type='text' class='myinputtext' id='modul' onkeypress="return tanpa_kutip();"  size='10' maxlength='35' style="width:200px;" /></td>
    </tr>
    <tr>
        <td><?php echo $_SESSION['lang']['isi']?></td><td>:</td>
        <td>
            <!--<textarea rows="2" cols="22" id='isi' onkeypress="return tanpa_kutip();" /></textarea>-->
            <script type="text/javascript" src="fckeditor/fckeditor.js"></script>
            <script type="text/javascript" src="fckeditor/fckconfig.js"></script>
            <script type="text/javascript">
                var oFCKeditor = new FCKeditor('isi');
//                oFCKeditor.BasePath = "http://localhost/fckeditor/";
                oFCKeditor.BasePath = "fckeditor/";
                oFCKeditor.SkinPath = oFCKeditor.BasePath + 'skins/office2003/';
                oFCKeditor.width = 1000;
                oFCKeditor.height= 500;
                oFCKeditor.Value   = "<?php echo rtrim(str_replace('"',"'", $row["isi"])); ?>";
                oFCKeditor.Create();
            </script>
        </td>
    </tr>
    <tr>
        <td><?php echo $_SESSION['lang']['html']?></td><td>:</td>
        <td><input type='text' class='myinputtext' id='html' onkeypress="return tanpa_kutip();"  size='10' style="width:200px;" value="help/" /></td>
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
     <table cellspacing="1" border="0">
         <tr>
            <td><?php echo $_SESSION['lang']['find']?></td><td>:</td>
            <td><input type='text' class='myinputtext' id='cariindex' onkeypress="return tanpa_kutip();"  size='10' maxlength='35' style="width:150px;" />
                <button class=mybutton id='cari' onclick=cariHelp()><?php echo $_SESSION['lang']['find']?></button></td>
         </tr>
     </table>
    <table cellspacing="1" border="0" class="sortable">
        <thead>
            <tr class="rowheader">
            <td align="center">No.</td>
            <td align="center"><?php echo $_SESSION['lang']['index']?></td>
            <td align="center"><?php echo $_SESSION['lang']['modul']?></td>
            <td align="center"><?php echo $_SESSION['lang']['tentang']?></td>
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

            $sCount="select count(*) as jmlhrow from ".$dbname.".owl_help order by `kode` asc";
            $qCount=mysql_query($sCount) or die(mysql_error());
            while($rCount=mysql_fetch_object($qCount)){
                $jmlbrs= $rCount->jmlhrow;
            }

            $sShow="select * from ".$dbname.".owl_help order by kode asc,tentang,modul,isi limit ".$offset.",".$limit." ";
            $qShow=mysql_query($sShow) or die(mysql_error());
            while($row=mysql_fetch_assoc($qShow))
            {
                $no+=1;
                echo"<script>loadNData()</script>";
                echo"<td><img src=images/edit.png class=resicon  title='Edit' onclick=\"editRow('".$row['kode']."','".$row['tentang']."','".$row['modul']."','".str_replace(array("\r", "\n"), '\n', $row['isi'])."','".$row['tujuan']."');\" ></td>";
                echo"<td><img onclick=\"detailHelp(event,'".str_replace(" ","",$row['kode'])."','".$row['modul']."');\" title=\"Detail Help\" class=\"resicon\" src=\"images/zoom.png\"></td>";
                echo"<td><img src=images/delete1.jpg class=resicon  title='Delete' onclick=\"delData('".$row['kode']."','".$row['tentang']."','".$row['modul']."','".str_replace(array("\r", "\n"), '\n', $row['isi'])."')></td></tr>";
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