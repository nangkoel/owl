<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/sdm_payrollHO.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?php
include('master_mainMenu.php');
//+++++++++++++++++++++++++++++++++++++++++++++
	OPEN_BOX('','<b>PPh21 REPORT</b>');
		echo"<div id=EList>";
		echo OPEN_THEME('PPh21 Report Form:');

$optp="<option value='".date('Y-m')."'>".date('m-Y')."</option>";	 
for($x=-1;$x<=24;$x++)
{
	$d=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optp.="<option value='".date('Y-m',$d)."'>".date('m-Y',$d)."</option>";
}
 	  
$hfrm[0]='PPh21 Bulanan';
$hfrm[1]='PPh21 Tahunan';
$hfrm[2]='Option';

$frm[0]="PPh21 Bulanan: Periode
         <select id=bulanan>".$optp."</select> <button onclick=showPPh21Monthly() class=mybutton>Show</button>
		 <img src=images/excel.jpg height=17px style='cursor:pointer;' onclick=convertPPh21Excel('bulan') title='Convert to Ms.Excel'>
		 <div style='display:none;'>
		 <iframe id=ifrm></iframe>
		 </div>	         
		 <table class=sortable border=0 cellspacing=1 width=100%>
		 <thead>
		   <tr class=rowheader>
		    <td class=firsttd>No.</td>
			<td>No.Karyawan</td>
			<td>Nama.Karyawan</td>
			<td>Status</td>
			<td>N.P.W.P</td>
			<td>Periode</td>
			<td>Sumber</td>
			<td>PPh21</td>
		   </tr>
		 </thead><tbody id=tbody>
		";
$frm[0].="</tbody>
          <tfoot>
		  <tr><td colspan=8>Jika Status pajak tidak sesuai atau kosong maka akan dikenakan status K/3.
		  </tr>
		  </tfoot>
		  </table>";		
$frm[1]="PPh21 Tahunan:
        Tahun<select id=tahun>
		      <option value='".(date('Y')-0)."'>".(date('Y')-0)."</option>
		      <option value='".(date('Y')+1)."'>".(date('Y')+1)."</option>
			  <option value='".(date('Y')-1)."'>".(date('Y')-1)."</option>
			  <option value='".(date('Y')-2)."'>".(date('Y')-2)."</option>
			  <option value='".(date('Y')-3)."'>".(date('Y')-3)."</option>
			  <option value='".(date('Y')-4)."'>".(date('Y')-4)."</option>
			  <option value='".(date('Y')-5)."'>".(date('Y')-5)."</option>
		     </select> <button onclick=showPPh21Yearly() class=mybutton>Show</button>
		 <img src=images/excel.jpg height=17px style='cursor:pointer;' onclick=convertPPh21Excel('tahun') title='Convert to Ms.Excel'>
		 <div style='display:none;'>
		 <iframe id=ifrm1></iframe>
		 </div>	         
		 <table class=sortable border=0 cellspacing=1 width=100%>
		 <thead>
		   <tr class=rowheader>
		    <td class=firsttd>No.</td>
			<td>No.Karyawan</td>
			<td>Nama.Karyawan</td>
			<td>Status</td>
			<td>N.P.W.P</td>
			<td>Tahun</td>
			<td>Sumber</td>
			<td>PPh21</td>
		   </tr>
		 </thead><tbody id=tbodyYear>
		";
$frm[1].="</tbody>
          <tfoot>
		  <tr><td colspan=8>Jika Status pajak tidak sesuai atau kosong maka akan dikenakan status K/3.
		  </tr>		  
		  </tfoot>
		  </table>";		
$frm[2]="
         <fieldset><legend><b>Sertakan Jamsostek tanggungan perusahaan ?</b></legend>
         <input type=checkbox id=jmsperusahaan value=jmsperusahaan checked>
		 (Ya/Tidak) Jamsostek tanggungan perusahaan<br>
		 </fieldset>
         <fieldset><legend><b>Jenis pendapatan yang disertakan</b></legend>
		 Berlaku pada PPh21 tahunan(Tidak berlaku pada PPh21 Bulanan)<br>
         <input type=checkbox id=regular value=regular checked>Gaji Regular<br>
		 <input type=checkbox id=thr value=thr checked>Tunjangan Hari Raya (THR)<br>
		 <input type=checkbox id=jaspro value=jaspro checked>Jasa produksi (Bonoes)<br>
		 </fieldset> 
        ";
drawTab('FRM',$hfrm,$frm,150,800);  	  			 
		echo"</div>";
		echo CLOSE_THEME();		
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>