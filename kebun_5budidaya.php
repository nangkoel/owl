<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script type="text/javascript" src="js/kebun_5budidaya.js"></script>
<?php
	$optpabrik='';
	$str="select * from ".$dbname.".organisasi where tipe='KEBUN' order by kodeorganisasi desc"; //echo $str;
	$res=mysql_query($str) or die(mysql_error($conn));
?>
<fieldset>
	<legend><?php echo $_SESSION['lang']['tblbudaya']?></legend>
	<table cellspacing="1" border="0">
		<tr>
			<td><?php echo $_SESSION['lang']['namaorganisasi']?></td>
			<td>:</td>
			<td><?php
			while($bar=mysql_fetch_object($res))
			{
				$optpabrik.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
			}
			?>
			<select id="kd_org" style='width:150px;'><?php echo $optpabrik; ?></select>		</td>
		</tr>
		<tr>
			<td><?php echo $_SESSION['lang']['kdbudaya']?></td>
			<td>:</td>
			<td><input type="text" id="kd_budidaya" onKeyPress="return angka_doang(event);"  /></td>
		</tr>
		<tr>
			<td><?php echo $_SESSION['lang']['keterangan']?></td>
			<td>:</td>
			<td><input type="text" id="ket" /></td>
		</tr>
		<tr>
			<td colspan="3">
			<input type="hidden" value="insert" id="method"  />
			<button class=mybutton onclick=simpanTblbudaya()><?php echo $_SESSION['lang']['save']?></button>
			<button class=mybutton onclick=btlTblbdya()><?php echo $_SESSION['lang']['cancel']?></button>
			</td>
		</tr>
	</table>
</fieldset>
<?php 
	CLOSE_BOX();
	OPEN_BOX();
?>
<fieldset>
	 <table class="sortable" cellspacing="1" border="0">
	 <thead>
	 <tr class=rowheader>
	 <td>No.</td>
	 <td><?php echo $_SESSION['lang']['kodeorg']?></td>
	 <td><?php echo $_SESSION['lang']['namaorganisasi'];?></td> 
	 <td><?php echo $_SESSION['lang']['kdbudaya'];?></td>
	 <td><?php echo $_SESSION['lang']['keterangan']; ?></td>
	 <td colspan="2">Action</td>
	 </tr>
	 </thead>
	 <tbody id="container">
	 <?php 
	 	//ambil data dari tabel kelompok customer
	 		
		$srt="select * from ".$dbname.".kebun_5budidaya order by kode desc";  //echo $srt;
		if($rep=mysql_query($srt))
		  {
			while($bar=mysql_fetch_object($rep))
			{
					
			//get akun
			$spr="select * from  ".$dbname.".organisasi where `kodeorganisasi`='".$bar->kodeorg."'";
			$rej=mysql_query($spr) or die(mysql_error($conn));
			$bas=mysql_fetch_object($rej);
			$no+=1;
			echo"<tr class=rowcontent>
				  <td>".$no."</td>
				  <td>".$bas->kodeorganisasi."</td>
				  <td>".$bas->namaorganisasi."</td>
				  <td>".$bar->kode."</td>
				  <td>".$bar->budidaya."</td>
				  <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kode."','".$bar->kodeorg."','".$bar->budidaya."');\"></td>
				  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delTbldya('".$bar->kode."');\"></td>
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
<!--<FORM NAME = " ">
<p align="center"><u><b><font face="Verdana" size="4" color="#000080">Status Tanaman</font></b></u></p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="87%" id="AutoNumber1" height="115">
  <tr>
    <td width="24%" height="1">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial">Kode</font></td>
    <td width="46%" height="1">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys"> 
    <input type=text size="6" name="koderekening">&nbsp; </font>
    </td>
    <td width="16%" height="1">
    <p style="margin-top: 0; margin-bottom: 0">
    </td>
  </tr>
  <tr>
    <td width="24%" height="22">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial">Keterangan</font></td>
    <td width="46%" height="22">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys"> 
    <input type=text size="41" name="tanggal"></font></td>
    <td width="16%" height="22">
    <p style="margin-top: 0; margin-bottom: 0"></td>
  </tr>
  <tr>
    <td width="24%" height="22">
    <p style="margin-top: 0; margin-bottom: 0">
    </td>
    <td width="46%" height="22">
    <p style="margin-top: 0; margin-bottom: 0"></td>
    <td width="16%" height="22">
    <p style="margin-top: 0; margin-bottom: 0"></td>
  </tr>
  <tr>
    <td width="24%" height="22">&nbsp;</td>
    <td width="46%" height="22">&nbsp;</td>
    <td width="16%" height="22">&nbsp;</td>
  </tr>
  </table>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="Simpan" name="Simpan">
<input type="reset" value="Batal" name="Batal"></font></p>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber2"><tr><td width="16%" align="center">Kode</td><td width="16%" align="center">Keterangan</td></tr><tr><td width="16%">&nbsp;</td><td width="16%">&nbsp;</td>
</tr></table>
<p><font face="Fixedsys">&nbsp;&nbsp;&nbsp; &nbsp;</font></p>-->

<?php
CLOSE_BOX();
echo close_body();
?>