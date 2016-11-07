<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language='JavaScript1.2' src='js/supplier.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.$_SESSION['lang']['find'].' '.$_SESSION['lang']['supplier'].'/'.$_SESSION['lang']['kontraktor'].'</b>');
	echo "<br>".$_SESSION['lang']['nama'].":<input type=text class=myinputtext id=cari size=30 maxlength=30 onkeypress=\"return tanpa_kutip(event)\">
	      <button class=mybutton onclick=findSupplier()>".$_SESSION['lang']['find']."</button>";     
	echo"<fieldset>
	     <legend>".$_SESSION['lang']['pilih']."</legend>
		 <div style='width=100%; height:200px;overflow:scroll'>
	     <table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=header>
	     <td>".$_SESSION['lang']['kodekelompok']."</td>
		 <td>Id.".$_SESSION['lang']['supplier']."</td>
		 <td>".$_SESSION['lang']['namasupplier']."</td>
		 <td>".$_SESSION['lang']['alamat']."</td>
		 <td>".$_SESSION['lang']['cperson']."</td>
		 <td>".$_SESSION['lang']['kota']."</td>
		 <td>".$_SESSION['lang']['telp']."</td>		 
		 <td>".$_SESSION['lang']['fax']."</td>		 
		 <td>".$_SESSION['lang']['email']."</td>		 
		 <td>".$_SESSION['lang']['npwp']."</td>	 
		 <td>".$_SESSION['lang']['plafon']."</td>
		 <td>".$_SESSION['lang']['noakun']."</td>
		 <td>".$_SESSION['lang']['akunpajak']."</td>
		 <td>".$_SESSION['lang']['noseripajak']."</td>
		 <td>".$_SESSION['lang']['namabank']."</td>
		 <td>".$_SESSION['lang']['norekeningbank']."</td>
		 <td>".$_SESSION['lang']['atasnama']."</td>
		 <td>".$_SESSION['lang']['nilaihutang']."</td>
		 </tr>
		 <tbody id=container>
		 </tbody>
		 <tfoot></tfoot>
		 </table>
		 </div>
		 </fieldset>
		 ";

CLOSE_BOX();

OPEN_BOX('','SUPPLIER/CONTRACTOR BANK ACCOUNTs');
//akun ini hanya dibutuhkan jika setiap supplier memiliki akun sendiri-sendiri
//jika akun hutang supplier digabungkan, akun ini tidak perlu
if($_SESSION['language']=='EN'){
    $zz='namaakun1 as namaakun';
}
else{
    $zz='namaakun';
}
$str="select noakun,".$zz." from ".$dbname.".keu_5akun where detail=1 and (noakun like '211%')";
$res=mysql_query($str);
$opt="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$opt.="<option value='".$bar->noakun."'>".$bar->namaakun."</option>";
}

//ambil no akun hutang pajak
$str1="select noakun,".$zz." from ".$dbname.".keu_5akun where detail=1 and (noakun like '212%')";
$res1=mysql_query($str1);
$opt1="<option value=''></option>";
while($bar1=mysql_fetch_object($res1))
{
	$opt1.="<option value='".$bar1->noakun."'>".$bar1->namaakun."</option>";
}
 echo"<fieldset>
      <legend>Form</legend>
	  <table>
	  <tr>
	      <td>".$_SESSION['lang']['kode']."</td><td><input type=text class=myinputtext disabled id=idsupplier></td>
	      <td>".$_SESSION['lang']['namabank']."</td><td><input type=text class=myinputtext id=bank onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>
	  </tr>	  
	  <tr>    
	     <td>".$_SESSION['lang']['noakun']."</td><td><select id=noakun>".$opt."</select></td> 
                          <td>Bank Acc.No</td><td><input type=text class=myinputtext id=rek onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>	  
 	  </tr>
	  <tr>
	      <td>".$_SESSION['lang']['namasupplier']."</td><td><input type=text class=myinputtext id=namasupplier onkeypress=\return tanpa_kutip(event);\" size=30 maxlength=30 disabled></td>
	      <td>A/c on Bhf (Bank.A/N)</td><td><input type=text class=myinputtext id=an onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>
	  </tr>
	  <tr>
	      <td>".$_SESSION['lang']['noakun'].".".$_SESSION['lang']['pajak']."</td><td><select id=akunpajak>".$opt1."</select></td>
	      <td>".$_SESSION['lang']['noseripajak']."</td><td><input type=text class=myinputtext id=noseripajak onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>
	  </tr>
	  <tr>
	      <td>".$_SESSION['lang']['nilaihutang']."</td><td colspan=3><input type=text  onblur=\"change_number(this);\"class=myinputtextnumber id=nilaihutang onkeypress=\"return angka_doang(event);\" size=15 maxlength=15 value=0></td>
	  </tr>
	  </table>
	<button class=mybutton onclick=saveAkunSupplier()>".$_SESSION['lang']['save']."</button>
	<button class=mybutton onclick=cancelAkunSupplier()>".$_SESSION['lang']['cancel']."</button>	  
	  </fieldset>";
?>
<?php
CLOSE_BOX();
echo close_body();
?>