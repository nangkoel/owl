<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src=js/sdm_pengobatan.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?php
OPEN_BOX('',$_SESSION['lang']['pembayaranclaim']);
//option periode akuntansi
//ambil daftar pengobatan dengan tahun sekarang
echo" Periode :<select id='periode'>";

for($x=0;$x<=24;$x++)
{
    $t=mktime(0,0,0,date('m')-$x,15,date('Y'));
    if(isset($_GET['periode'])){
        if (date('Y-m',$t)==$_GET['periode'])
                echo"<option value='".date('Y-m',$t)."' selected>".date('m-Y',$t)."</option>";
    }
    echo"<option value='".date('Y-m',$t)."'>".date('m-Y',$t)."</option>";
}
echo"</select>
          <button onclick=getDaftar() class=mybutton>".$_SESSION['lang']['proses']."</button>";

echo "<div id=cont>";
if(isset($_GET['periode']))
{
$str="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag from ".$dbname.".sdm_pengobatanht a left join
      ".$dbname.".sdm_5rs b on a.rs=b.id 
	  left join ".$dbname.".datakaryawan c
	  on a.karyawanid=c.karyawanid
	  left join ".$dbname.".sdm_5diagnosa d
	  on a.diagnosa=d.id
	  where  a.kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                     and periode='".$_GET['periode']."'
	  order by a.updatetime desc, a.tanggal desc";
$res=mysql_query($str);
echo"<fieldset>
	  <legend>".$_SESSION['lang']['belumbayar']."</legend>
	  <table class=sortable cellspacing=1 border=0>
	  <thead>
	    <tr class=rowheader>
		<td width=20></td>
		  <td>No</td>
		  <td width=100>".$_SESSION['lang']['notransaksi']."</td>
		  <td width=50>".$_SESSION['lang']['periode']."</td>
		  <td width=30>".$_SESSION['lang']['tanggal']."</td>
		  <td width=200>".$_SESSION['lang']['namakaryawan']."</td>
		  <td width=150>".$_SESSION['lang']['rumahsakit']."</td>
		  <td width=50>".$_SESSION['lang']['jenisbiayapengobatan']."</td>
		  <td width=90>".$_SESSION['lang']['nilaiklaim']."</td>
		  <td>".$_SESSION['lang']['dibayar']."</td>
		  <td>".$_SESSION['lang']['tanggalbayar']."</td>
		  <td></td>
		</tr>
	  </thead>
	  <tbody id='container'>";
	  $no=0;
	  while($bar=mysql_fetch_object($res))
	  {
	   $no+=1;
	   echo"<tr class=rowcontent>
	   <td><img src=images/zoom.png  title='Lihat Detail Pengobatan' class=resicon onclick=previewPengobatan('".$bar->notransaksi."',event)></td>
           <td>".$no."</td>
            <td>".$bar->notransaksi."</td>
            <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
            <td nowrap>".tanggalnormal($bar->tanggal)."</td>
            <td>".$bar->namakaryawan."</td>
            <td>".$bar->namars."[".$bar->kota."]"."</td>
            <td>".$bar->kodebiaya."</td>
            <td align=right>".number_format($bar->totalklaim,2,'.',',')."</td>";
            if($bar->posting==0){
            echo"<td align=right><img src='images/puzz.png' style='cursor:pointer;' title='click to get value' onclick=\"document.getElementById('bayar".$no."').value=".$bar->totalklaim."\">
                            <input type=text id=bayar".$no." class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=12 onblur=change_number(this) size=12 value=".$bar->jlhbayar."></td>
            <td align=right><input type=text id=tglbayar".$no." class=myinputtext onkeypress=\"return false;\" maxlength=10  size=10 onmouseover=setCalendar(this) value='".date('d-m-Y')."'></td>
            <td><img src='images/save.png' title='".$_SESSION['lang']['save']."' class=resicon onclick=savePClaim('".$no."','".$bar->notransaksi."')></td>";
            }else{
            echo"<td align=right>".number_format($bar->jlhbayar,2)."</td>
                <td align=right><input type=text id=tglbayar".$no." class=myinputtext onkeypress=\"return false;\" maxlength=10  size=10 onmouseover=setCalendar(this) value='".tanggalnormal($bar->tanggalbayar)."'></td>
                <td>Posted</td>";    
            }
	    echo"</tr>";	  	
	  }
echo"</tbody>
	 <tfoot>
	 </tfoot>
	 </table>
	 </fieldset> 	 
	 ";	 
}
echo "</div>";
CLOSE_BOX();
echo close_body();
?>