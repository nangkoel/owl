<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/pabrik_produksi.js'></script>
<?php
include('master_mainMenu.php');


OPEN_BOX('',"<b>".$_SESSION['lang']['produksipabrik'].":</b>");
//get org
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' and tipe='PABRIK'";
$res=mysql_query($str);
$optorg='';
while($bar=mysql_fetch_object($res))
{
	$optorg.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
echo "<fieldset style='width:830px;'>
        <legend>".$_SESSION['lang']['form'].":</legend>
		<table><tr><td>
		
		<table>
		   <tr>
		     <td>
			    ".$_SESSION['lang']['kodeorganisasi']."
			 </td>
		     <td>
			    <select id=kodeorg>".$optorg."</select>
			 </td>
		   </tr>
		   <tr> 
			 <td>".$_SESSION['lang']['tanggal']."</td>
			 <td><input type=text class=myinputtext id=tanggal size=12 onmousemove=setCalendar(this.id) onchange=gettbs() maxlength=10 onkeypress=\"return false;\">
			 </td>	
		     <td>		 
		 </tr>
		   <tr>
		     <td>
			    ".$_SESSION['lang']['sisatbskemarin']."
			 </td>
		     <td>
			    <input type=text id=sisatbskemarin value=0 class=myinputtextnumber onblur=hitungSisa() maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">Kg.
			 </td>
		   </tr>
		   <tr> 
		     <td>
			    ".$_SESSION['lang']['tbsmasuk']."
			 </td>
			 <td>
			    <input type=text id=tbsmasuk value=0  class=myinputtextnumber onblur=hitungSisa()  maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">Kg. 
			 </td>	 		 
		 </tr>		
		 <tr>
		     <td>
			    ".$_SESSION['lang']['tbsdiolah']."
			 </td>
		     <td>
			    <input type=text id=tbsdiolah value=0  class=myinputtextnumber onblur=hitungSisa()  maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">Kg. 
			 </td>		 
		 </tr>
		 <tr>
		     <td>
			    ".$_SESSION['lang']['sisa']."
			 </td>
		     <td>
			    <input type=text id=sisa  value=0 class=myinputtextnumber  maxlength=10 size=10 readonly>Kg. 
			 </td>		 
		 </tr>	";
               echo" <tr>
		     <td>% USB Before Crusher
			 </td>
		     <td>
			    <input type=text id=usbbefore  value=0 class=myinputtextnumber  maxlength=10 size=10 >%
			 </td>		 
		 </tr>	  
                  <tr>
		     <td>% USB After Crusher
			 </td>
		     <td>
			    <input type=text id=usbafter  value=0 class=myinputtextnumber  maxlength=10 size=10 >% 
			 </td>		 
		 </tr>	
                  <tr>
		     <td>% Oil Diluted Crude Oil
			 </td>
		     <td>
			    <input type=text id=oildiluted  value=0 class=myinputtextnumber  maxlength=10 size=10 >%
			 </td>		 
		 </tr>	
                  <tr>
		     <td>% Oil in underflow (CST)
			 </td>
		     <td>
			    <input type=text id=oilin  value=0 class=myinputtextnumber  maxlength=10 size=10 >%
			 </td>		 
		 </tr>	
                  <tr>
		     <td>% Oil in Heavy Phase - S/D
			 </td>
		     <td>
			    <input type=text id=oilinheavy  value=0 class=myinputtextnumber  maxlength=10 size=10 >%
			 </td>		 
		 </tr>	
                  <tr>
		     <td>CaCO3
			 </td>
		     <td>
			    <input type=text id=caco  value=0 class=myinputtextnumber  maxlength=10 size=10 > Kg
			 </td>		 
		 </tr>	";
	  echo"</table>	  
	  </td>
	  <td valign=top>  
  	<table>
		<tr>
		<td> 
		 <fieldset><legend>".$_SESSION['lang']['cpo']."</legend>
		 <table>
		 <tr style=display:none;><td>
			    ".$_SESSION['lang']['cpo']."(Kg)
			 </td>
			 <td>
			    <input type=text id=oercpo  value=0  onfocus='getKgCpo()' class=myinputtextnumber maxlength=7 size=10 onkeypress=\"return angka_doang(event);\">Kg. 
			 </td>
		  </tr>
		 <tr>
		     <td>
			    ".$_SESSION['lang']['kotoran']."
			 </td>
		     <td>
			    <input type=text id=dirtcpo value=0 onblur=periksaCPO(this)   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>
		 </tr>	
		 <tr>
		     <td>
			    ".$_SESSION['lang']['kadarair']."
			 </td>
			 <td>
			    <input type=text id=kadaraircpo value=0 onblur=periksaCPO(this)   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>
		 </tr>	
		 <tr>
		     <td>
			    FFa
			 </td>
		     <td>
			    <input type=text id=ffacpo value=0 onblur=periksaCPO(this)   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>			 
		 </tr>		
                  <tr>
		     <td>
			    Dobi
			 </td>
		     <td>
			    <input type=text id=dobi value=0   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>			 
		 </tr>		                 
		</table>
		</fieldset>
		
		</td>
		</tr>
                
<tr>
		<td> 
		 <fieldset><legend>".$_SESSION['lang']['cpo']." Loses</legend>
		 <table>
		 <tr><td>USB
			 </td>
			 <td>
			    <input type=text id=fruitineb  value=0   class=myinputtextnumber maxlength=7  size=10 onkeypress=\"return angka_doang(event);\"> KG/TON
			 </td>
		  </tr>
		 <tr>
		     <td>EB. Stalk 
			 </td>
		     <td>
			    <input type=text id=ebstalk value=0    class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">  
			 </td>
		 </tr>	
		 <tr>
		     <td> Fibre From Press Cake
			 </td>
			 <td>
			    <input type=text id=fibre value=0  class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">  
			 </td>
		 </tr>	
		 <tr>
		     <td>Nut From Press Cake
			 </td>
		     <td>
			    <input type=text id=nut value=0   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">  
			 </td>			 
		 </tr>	
                  <tr>
		     <td>Effluent
			 </td>
		     <td>
			    <input type=text id=effluent value=0   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\"> 
			 </td>			 
		 </tr>	
                   <tr>
		     <td>Decanter Solid
			 </td>
		     <td>
			    <input type=text id=soliddecanter value=0   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\"> 
			 </td>			 
		 </tr>	
		</table>
		</fieldset>
		
		</td>
		</tr>
		</table>	
    </td>
	<td valign=top>
  	<table>
		<tr>
		<td> 
		 <fieldset><legend>".$_SESSION['lang']['kernel']."</legend>
		 <table>
		 <tr style=display:none;><td>
			    ".$_SESSION['lang']['kernel']."(Kg)
			 </td>
			 <td>
			    <input type=text id=oerpk  value=0 onblur=periksaOERPK(this)  class=myinputtextnumber maxlength=7 size=10 onkeypress=\"return angka_doang(event);\">Kg.
			 </td>
		  </tr>
		 <tr>
		     <td>
			    ".$_SESSION['lang']['kotoran']."
			 </td>
		     <td>
			    <input type=text id=dirtpk  value=0 onblur=periksaPK(this)  class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>
		 </tr>	
		 <tr>
		     <td>
			    ".$_SESSION['lang']['kadarair']."
			 </td>
			 <td>
			    <input type=text id=kadarairpk  value=0 onblur=periksaPK(this)  class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>
		 </tr>	
		 <tr>
		     <td>
			    FFA
			 </td>
		     <td>
			    <input type=text id=ffapk  value=0 onblur=periksaPK(this)  class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>			 
		 </tr>	
		 
		 <tr>
		     <td>
			    Inti Pecah
			 </td>
		     <td>
			    <input type=text id=intipecah  value=0  class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>			 
		 </tr>
                 <tr>
		     <td>
			   Batu
			 </td>
		     <td>
			    <input type=text id=batu  value=0  class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>			 
		 </tr>
                 
		</table>
		</fieldset>
		
		</td>
		</tr>
                <tr>
		<td> 
		 <fieldset><legend>".$_SESSION['lang']['kernel']." Loses</legend>
		 <table>
		 <tr><td>USB

			 </td>
			 <td>
			    <input type=text id=fruitinebker  value=0   class=myinputtextnumber maxlength=7 size=10 onkeypress=\"return angka_doang(event);\">
			 </td>
		  </tr>
		 <tr>
		     <td>Fibre Cyclone
			 </td>
		     <td>
			    <input type=text id=cyclone  value=0   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\"> 
			 </td>
		 </tr>	
		 <tr>
		     <td>LTDS
			 </td>
			 <td>
			    <input type=text id=ltds  value=0   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\"> 
			 </td>
		 </tr>	
		 <tr>
		     <td>Hydro
			 </td>
		     <td>
			    <input type=text id=claybath  value=0  class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">
			 </td>			 
		 </tr>	
                 
		</table>
		</fieldset>
		
		</td>
		</tr>
		</table>	
			
	
	</td>
	</tr>	  
	  
	</table>	
                <input type=hidden id=method value='insert'>	
		<center>
                <button class=mybutton onclick=simpanProduksi()>".$_SESSION['lang']['save']."</button>
                <button class=mybutton onclick=bersihkanForm()>".$_SESSION['lang']['cancel']."</button>
                </center>
	  </fieldset><input type=hidden id=statSounding value=0 />
	 ";
CLOSE_BOX();

OPEN_BOX();
echo "<fieldset><legend>".$_SESSION['lang']['list']."</legend>
      <table class=sortable cellspacing=1 border=0 width=100%>
	    <thead>
		  <tr class=rowheader>
		   <!--<td rowspan=2 align=center>".$_SESSION['lang']['kodeorganisasi']."</td>-->
		   <td rowspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['sisatbskemarin']."</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['tbsmasuk']." (Kg.)</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['tbsdiolah']." (Kg.)</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['sisa']." (Kg.)</td>
		   <td colspan=5 align=center>".$_SESSION['lang']['cpo']."
		   </td>
		   <td colspan=6 align=center>".strtoupper($_SESSION['lang']['kernel'])."
		   </td>
		   <td rowspan=2 align=center></td>	   
		  </tr>  
		  <tr class=rowheader> 
		   <td align=center>".$_SESSION['lang']['cpo']." (Kg)</td>
		   <td align=center>".$_SESSION['lang']['oer']." (%)</td>
		   <td align=center>(FFa)(%)</td>
		   <td align=center>".$_SESSION['lang']['kotoran']." (%)</td>
		   <td align=center>".$_SESSION['lang']['kadarair']."<br>(%)</td>
		   
		   <td align=center>".$_SESSION['lang']['kernel']." (Kg)</td>
		   <td align=center>".$_SESSION['lang']['oer']." (%)</td>
		   <td align=center>(FFa) (%)</td>
		   <td align=center>".$_SESSION['lang']['kotoran']." (%)</td>
		   <td align=center>".$_SESSION['lang']['kadarair']."<br>(%)</td>
		   <td align=center>Inti Pecah<br>(%)</td>
		  </tr>
		</thead>
		<tbody id=container>";
$str="select a.* from ".$dbname.".pabrik_produksi a
      where kodeorg='".$_SESSION['empl']['lokasitugas']."'
      order by a.tanggal desc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
		// echo"<tr class=rowcontent onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\" style='cursor:pointer'>
		  echo"<tr class=rowcontent style=cursor:pointer;>
		   <!--<td onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kodeorg."</td>-->
		   <td onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".tanggalnormal($bar->tanggal)."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->sisatbskemarin,0,'.',',')."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->tbsmasuk,0,'.',',')."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->tbsdiolah,0,'.',',.')."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->sisahariini,0,'.',',')."</td>
		   
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->oer,2,'.',',')."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".(@number_format($bar->oer/$bar->tbsdiolah*100,2,'.',','))."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->ffa."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kadarkotoran."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kadarair."</td>
		   
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->oerpk,2,'.',',')."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".(@number_format(@$bar->oerpk/$bar->tbsdiolah*100,2,'.',','))."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->ffapk."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kadarkotoranpk."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kadarairpk."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->intipecah."</td>
		   		   
		   <td><img src=images/application/application_add.png class=resicon  title='add detail ".tanggalnormal($bar->tanggal)."' onclick=\"addDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">
                     <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".$bar->tanggal."','".$bar->sisatbskemarin."','".$bar->tbsmasuk."','".$bar->tbsdiolah."','".$bar->sisahariini."','".$bar->oer."','".$bar->kadarkotoran."','".$bar->kadarair."','".$bar->ffa."','".$bar->oerpk."','".$bar->kadarkotoranpk."','".$bar->kadarairpk."','".$bar->ffapk."','".$bar->intipecah."','".$bar->dobi."','".$bar->batu."','".$bar->usbbefore."','".$bar->usbafter."','".$bar->oildiluted."','".$bar->oilin."','".$bar->oilinheavy."','".$bar->caco."','".$bar->fruitineb."','".$bar->ebstalk."','".$bar->fibre."','".$bar->nut."','".$bar->effluent."','".$bar->soliddecanter."','".$bar->fruitinebker."','".$bar->cyclone."','".$bar->ltds."','".$bar->claybath."');\">
		     <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delProduksi('".$bar->kodeorg."','".$bar->tanggal."','".$bar->kodebarang."');\">
		   </td>
		  </tr>";	
}	  
		
echo"	
		</tbody>
		<tfoot>
		</tfoot>
	  </table>
	  </fieldset>";
CLOSE_BOX();

close_body();
?>