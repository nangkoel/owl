<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/sdm_jatahBBM.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['penggantiantransport']);

$optthn="<option value=''></option>";
for($x=-1;$x<10;$x++)
{
	$mk=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optthn.="<option value='".(date('Y-m',$mk))."'>".(date('m-Y',$mk))."</option>";
}
//===============ambil list karyawan

$str="select a.namakaryawan,a.karyawanid, b.namajabatan from ".$dbname.".datakaryawan a
      left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
	  where a.alokasi=1
	  and (a.tanggalkeluar='0000-00-00' or a.tanggalkeluar>'".date('Y-m-d')."')
	  order by a.namakaryawan";	  
$res=mysql_query($str);
$optKaryawan='';
while($bar=mysql_fetch_object($res))
{
	$optKaryawan.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan." [ ".$bar->namajabatan." ]</option>";
}	  
//pt==================
$str="select kodeorganisasi,namaorganisasi from 
      ".$dbname.".organisasi where tipe='pt'";
$res=mysql_query($str);
$optPt='';
while($bar=mysql_fetch_object($res))
{
	$optPt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
//================================	  
$frm[0].="<fieldset>
     <legend>".$_SESSION['lang']['form']."</legend>
	 <table>
	   <tr>
	      <td>".$_SESSION['lang']['periode']."</td>
		  <td><select id=periode onchange=getNotransaksi(this.options[this.selectedIndex].value) style='width:200px;'>".$optthn."</select></td>
	      <td>".$_SESSION['lang']['notransaksi']."</td>
		  <td><input type=text class=myinputtext id=notransaksi size=15 disabled style='width:200px;'></td>		     
	   </tr>
	   <tr>
	      <td>".$_SESSION['lang']['karyawan']."</td>
		  <td><select id=karyawanid  style='width:200px;'>".$optKaryawan."</select></td>		  
	      <td>".$_SESSION['lang']['alokasibiaya']."</td>
		  <td><select id=pt  style='width:200px;'>".$optPt."</select></td>		  
	   </tr>
	   <tr>
	      <td>".$_SESSION['lang']['keterangan']."</td>
		  <td><input type=text id=keterangan class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=45  style='width:200px;'></td>		    
	   </tr>
	 </table>
     "; 
//==============================
$frm[0].="<fieldset>
     <legend>".$_SESSION['lang']['biayabiaya']."</legend>
	 <table>
	   <tr>
	      <td>".$_SESSION['lang']['transport']."</td>
		  <td><input type=text class=myinputtextnumber id=bytransport size=15 maxlength=12 onkeypress=\"return angka_doang(event);\" onblur=\"change_number(this);calculateTotal();\" value=0></td>
	      <td>".$_SESSION['lang']['perawatan']."</td>
		  <td><input type=text class=myinputtextnumber id=byperawatan size=15 maxlength=12 onkeypress=\"return angka_doang(event);\" onblur=\"change_number(this);calculateTotal();\" value=0></td>		     
	   </tr>
	   <tr>
	      <td>".$_SESSION['lang']['toll']."</td>
		  <td><input type=text class=myinputtextnumber id=bytoll size=15 maxlength=12 onkeypress=\"return angka_doang(event);\" onblur=\"change_number(this);calculateTotal();\" value=0></td>		  
	      <td>".$_SESSION['lang']['lain']."</td>
		  <td><input type=text class=myinputtextnumber id=bylain size=15 maxlength=12 onkeypress=\"return angka_doang(event);\" onblur=\"change_number(this);calculateTotal();\" value=0>
		  Total<input type=text id=total disabled value=0 class=myinputtextnumber size=15>
		  </td>		  
	   </tr>
	 </table>
     </fieldset>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton  id='savebtn' onclick=saveBBM();>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelBBM()>".$_SESSION['lang']['new']."</button>
	 </fieldset>
	 "; 
//======================================
$frm[0].="<fieldset>
     <legend>".$_SESSION['lang']['detail']."</legend>
	 <table>
	   <tr>
	      <td>".$_SESSION['lang']['tanggal']."</td>
		  <td><input type=text class=myinputtext id=tanggal onmouseover=setCalendar(this) size=12 maxlength=12 onkeypress=\"return false;\"></td>
	      <td>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>
		  <td><input type=text class=myinputtextnumber id=jlhbbm size=12 maxlength=5 onkeypress=\"return angka_doang(event);\" value=0>Ltr.</td>		     
	      <td>".$_SESSION['lang']['totalharga']."</td>
		  <td><input type=text class=myinputtextnumber id=totalharga size=12 maxlength=8 onkeypress=\"return angka_doang(event);\" value=0>(Rp).</td>
	      <td><button class=mybutton onclick=saveLitre()>".$_SESSION['lang']['save']."</button></td>
	   </tr>
	 </table>
     <div style='width:500px;height:150px; overflow:scroll;'>
	  <table cellspacing=1 border=0 style='width:450px'>
	  <thead>
	  <tr class=rowheader>
	     <td>No</td>
		 <td>".$_SESSION['lang']['tanggal']."</td>
		 <td>".$_SESSION['lang']['jumlah']."(Ltr)</td>
		 <td>".$_SESSION['lang']['total']."(Rp)</td>
		 <td></td>
	  </thead>
	  <tbody id=containerSolar>
	  
	  </tbody>
	  <tfoot>
	  </tfoot>
	  </table>
	 </div>	 
     </fieldset>";	
//=====================================
$frm[1].="<fieldset>
     <legend>".$_SESSION['lang']['list']."</legend>
	 Periode<select id=periox onchange=getData(this.options[this.selectedIndex].value)>".$optthn."</select>
	 <img src='images/pdf.jpg' class=resicon onclick=previewBBMPeriode(event) title='view'>
	 <div style='width;700px;height:300px;overflow:scroll;'>
	 <table class=sortable cellspacing=1 border=0>
	 <thead>
	   <tr class=rowheader>
	     <td>No.</td>
		 <td>".$_SESSION['lang']['notransaksi']."</td>
		 <td>".$_SESSION['lang']['periode']."</td>
		 <td>".$_SESSION['lang']['pt']."</td>
		 <td>".$_SESSION['lang']['karyawan']."</td>
		 <td>".$_SESSION['lang']['totalbiaya']."</td>
		 <td>".$_SESSION['lang']['dibayar']."</td>
		 <td>".$_SESSION['lang']['tanggalbayar']."</td>
		 <td>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>
		 <td>".$_SESSION['lang']['keterangan']."</td>	
		 <td></td>	 
	   </tr>
	 </thead>
	 <tbody id=container>";

$str="select a.*,sum(b.jlhbbm) as bbm,c.namakaryawan from ".$dbname.".sdm_penggantiantransport a
      left join ".$dbname.".sdm_penggantiantransportdt b 
	  on a.notransaksi=b.notransaksi
	  left join ".$dbname.".datakaryawan c
	  on a.karyawanid=c.karyawanid
	   where periode='".date('Y-m')."' and 
	  kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
	  group by notransaksi";
$res=mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($res))
{
	$no+=1;
	
	$add='';
	if($bar->posting==0)
	{
		$add.=" <img src='images/close.png' class=resicon onclick=deleteBBM('".$bar->notransaksi."') title='delete'>";
		//$add.=" <img src='images/tool.png' class=resicon onclick=editBBM('".$bar->notransaksi."') title='edit'>";
	}
		$add.=" <img src='images/pdf.jpg' class=resicon onclick=previewBBM('".$bar->notransaksi."',event) title='view'>";


	$frm[1].="<tr class=rowcontent>
	     <td>".$no."</td>
		 <td>".$bar->notransaksi."</td>
		 <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
		 <td>".$bar->alokasi."</td>
		 <td>".$bar->namakaryawan."</td>
		 <td align=right>".number_format($bar->totalklaim,2,',','.')."</td>
		 <td align=right>".number_format($bar->dibayar,2,',','.')."</td>
		 <td>".tanggalnormal($bar->tanggalbayar)."</td>
		 <td align=right>".number_format($bar->bbm,2,',','.')."</td>
		 <td>".$bar->keterangan."</td>	
		 <td>".$add."</td>	 
	   </tr>";	
}	  	 
$frm[1].="</tbody>
	 <tfoot>
	 </tfoot>
	 </table>
	 </div>
     </fieldset>";	 	 
//============================================
$frm[2].="<fieldset>
     <legend>".$_SESSION['lang']['pembayaran']."</legend>
	 <div style='width;700px;height:300px;overflow:scroll;'>
	 <table class=sortable cellspacing=1 border=0>
	 <thead>
	   <tr class=rowheader>
	     <td>No.</td>
		 <td>".$_SESSION['lang']['notransaksi']."</td>
		 <td>".$_SESSION['lang']['periode']."</td>
		 <td>".$_SESSION['lang']['pt']."</td>
		 <td>".$_SESSION['lang']['karyawan']."</td>
		 <td>".$_SESSION['lang']['totalbiaya']."</td>
		 <td>".$_SESSION['lang']['dibayar']."</td>
		 <td>".$_SESSION['lang']['tanggalbayar']."</td>
		 <td></td>	 
	   </tr>
	 </thead>
	 <tbody id=containerbayar>";
$str2="select a.*,sum(b.jlhbbm) as bbm,c.namakaryawan from ".$dbname.".sdm_penggantiantransport a
      left join ".$dbname.".sdm_penggantiantransportdt b 
	  on a.notransaksi=b.notransaksi
	  left join ".$dbname.".datakaryawan c
	  on a.karyawanid=c.karyawanid
	   where 
	    a.posting=0 and
	  a.kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
	  group by notransaksi";
$res2=mysql_query($str2);	  
$no=0;
while($bar=mysql_fetch_object($res2))
{
	$no+=1;

	$frm[2].="<tr class=rowcontent>
	     <td>".$no."</td>
		 <td>".$bar->notransaksi."</td>
		 <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
		 <td>".$bar->alokasi."</td>
		 <td>".$bar->namakaryawan."</td>
		 <td align=right>".number_format($bar->totalklaim,2,',','.')."</td>
		 <td align=right><img src='images/puzz.png' style='cursor:pointer;' title='click to get value' onclick=\"document.getElementById('bayar".$no."').value=".$bar->totalklaim."\">
		                  <input type=text id=bayar".$no." class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=12 onblur=change_number(this) size=12></td>
		 <td><input type=text id=tglbayar".$no." class=myinputtext onkeypress=\"return false;\" maxlength=10  size=10 onmouseover=setCalendar(this) value='".date('d-m-Y')."'></td>
	     <td><img src='images/save.png' title='Save' class=resicon onclick=saveBBMClaim('".$no."','".$bar->notransaksi."')></td>
	   </tr>";	
}	
	 
$frm[2].="</tbody>
	 <tfoot>
	 </tfoot>
	 </table>
	 </div>
     </fieldset>";
//==================================================	 	 
$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['list'];
$hfrm[2]=$_SESSION['lang']['pembayaran'];
	 
drawTab('FRM',$hfrm,$frm,100,900);	  
CLOSE_BOX();
echo close_body();
?>