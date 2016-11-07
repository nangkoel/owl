<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript1.2 src='js/keu_5kursbulanan.js'></script>
<?php
OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
        $optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $optMt=$optPeriode;
        $sPrd="select distinct periode from ".$dbname.".setup_periodeakuntansi 
               where tutupbuku=0 and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
        $qPrd=mysql_query($sPrd) or die(mysql_error($conn));
        while($rPrd=  mysql_fetch_assoc($qPrd)){
            $optPeriode.="<option value='".$rPrd['periode']."'>".$rPrd['periode']."</option>";
        }
		$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		for($x=0;$x<=5;$x++){
			$dte=mktime(0,0,0,(date('m')+2)-$x,15,date('Y'));
			$optPeriode.="<option value=".date("Y-m",$dte).">".date("m-Y",$dte)."</option>";
		}

        
        $sPrd="select distinct kode from ".$dbname.".setup_matauang 
               where kode!='IDR' order by kode asc";
        $qPrd=mysql_query($sPrd) or die(mysql_error($conn));
        while($rPrd=  mysql_fetch_assoc($qPrd)){
            $optMt.="<option value='".$rPrd['kode']."'>".$rPrd['kode']."</option>";
        }
        


                echo"<fieldset style='float:left;'>";
		echo"<legend>".$_SESSION['lang']['kursbulanan']."</legend>";
		
			echo"<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>".$_SESSION['lang']['periode']."</td> 
					<td>:</td>
					<td><select id=periodeDt style=\"width:150px;\">".$optPeriode."</select></td>
				</tr>

				<tr>
					<td>".$_SESSION['lang']['matauang']."</td> 
					<td>:</td>
					<td><select id=mtUang style=\"width:150px;\">".$optMt."</select></td>
				</tr>
				
				<tr>
					<td>".$_SESSION['lang']['kurs']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=krsDt onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>
				</tr>
				
				<tr><td colspan=2></td>
					<td colspan=3>
						<button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>
						<button class=mybutton onclick=cancel()>".$_SESSION['lang']['reset']."</button>
					</td>
				</tr>
			
			</table></fieldset>
	              <input type=hidden id=periodeold value=''>
                      <input type=hidden id=mtUangold value=''>
                      <input type=hidden id=method value='insert'>";
 


CLOSE_BOX();
?>



<?php
OPEN_BOX();
//$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
//ISI UNTUK DAFTAR 
echo"<fieldset  style=float:left;clear:both;><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['data']."</legend>";
echo"<table>";
echo"<tr><td>".$_SESSION['lang']['periode']."</td><td><input type=text id=periodeCr style=\"width:150px;\" onfocus='bersihField()' onblur=loadData(0) /></td>";
echo"<td>ex. 2013-03</td>
     <td>&nbsp;</td></tr>";
echo"<tr><td>".$_SESSION['lang']['matauang']."</td><td><select id=mtUangCr style=\"width:150px;\">".$optMt."</select></td>";
echo"<td>&nbsp;</td>
     <td>&nbsp;</td></tr>";
echo"<tr><td colspan=4><button class=mybutton onclick=loadData(0)>".$_SESSION['lang'] ['find']."</button></td></tr>";
echo"</table>";
echo"</fieldset>";
echo "<fieldset style=float:left;clear:both;>
		<legend>".$_SESSION['lang']['list']."</legend>
		<div id=container> 
			<script>loadData(0)</script>
		</div>
	</fieldset>";
CLOSE_BOX();
echo close_body();					
?>