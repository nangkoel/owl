<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX(); //1 O

?>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/log_persetujuan_po.js"></script>
<div id="action_list">
<?php
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=refresh_data()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['carinopo']."</legend>"; 
			echo $_SESSION['lang']['nopo'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>&nbsp;";
			echo $_SESSION['lang']['tgl_po'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariNopo()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
</div>
<?php
CLOSE_BOX(); //1 C //2 O
?>
<div id=list_pp_verication>
<?php OPEN_BOX();?>
<fieldset>
<legend><?php echo $_SESSION['lang']['list_po'];?></legend>
<div style="overflow:scroll; height:420px;">
	 <table class="sortable" cellspacing="1" border="0">
	 <thead>
	 <tr class=rowheader>
	 <td>No.</td>
	 <td><?php echo $_SESSION['lang']['nopo']?></td>
	 <td><?php echo $_SESSION['lang']['tgl_po'];?></td> 
	 <td><?php echo $_SESSION['lang']['namaorganisasi'];?></td>
	  <td>Detail PO</td>
	   <td colspan="2" align="center">Verification</td>
	  <?php		
				for($i=1;$i<4;$i++)
				 {
					echo"<td align=center>Persetujuan".$i."</td>";
				 }
	   ?>
	
	 </tr>
	 </thead>
	 <tbody id="contain">
	
	 <?php 
	
       /*     if(($_SESSION['empl']['tipeinduk']=='HOLDING')||($_SESSION['empl']['tipeinduk']=='KANWIL'))
            {
				$sorg="select alokasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'";
				$qorg=mysql_query($sorg) or die(mysql_error($conn));
				$resorg=mysql_fetch_assoc($qorg);
				$kod_org=$resorg['alokasi'];
                   $str="SELECT * FROM ".$dbname.".log_poht where kodeorg='".$_SESSION['empl']['kodeorganisasi']."' and statuspo = '1' 
					and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' 
					or persetujuan3='".$_SESSION['standard']['userid']."') ORDER BY nopo DESC";//echo $str;
            }
            else
            {
					$kod_org=$_SESSION['empl']['kodeorganisasi'];
                  $str="SELECT * FROM ".$dbname.".log_poht where kodeorg='".$_SESSION['empl']['kodeorganisasi']."' and statuspo = '1' 
					and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' 
					or persetujuan3='".$_SESSION['standard']['userid']."') ORDER BY nopo DESC";
            }*/
			//$str="SELECT * FROM ".$dbname.".log_poht where kodeorg='".$kod_org."' and statuspo = '1' 
//					and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' 
//					or persetujuan3='".$_SESSION['standard']['userid']."') ORDER BY nopo DESC";
		$userid=$_SESSION['standard']['userid'];
		$str="select * from ".$dbname.".log_poht
         where stat_release<1 and((persetujuan1=".$userid." and (hasilpersetujuan1 is null or hasilpersetujuan1=''))
         or (persetujuan2=".$userid." and (hasilpersetujuan2 is null or hasilpersetujuan2=''))
         or (persetujuan3=".$userid." and (hasilpersetujuan3 is null or hasilpersetujuan3='')))";

			//echo $str;
	  if($res=mysql_query($str))
	  {
		while($bar=mysql_fetch_assoc($res))
		{
			$kodeorg=$bar['kodeorg'];
			$spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
			$rep=mysql_query($spr) or die(mysql_error($conn));
			$bas=mysql_fetch_object($rep);
			$no+=1;
			echo"<tr class=rowcontent id='tr_".$no."'>
				  <td>".$no."</td>
				  <td id=td_".$no.">".$bar['nopo']."</td>
				  <td>".tanggalnormal($bar['tanggal'])."</td>
				  <td>".$bas->namaorganisasi."</td>
				  <td align=center><img src=images/pdf.jpg class=resicon width='30' height='30' title='Print' onclick=\"masterPDF('log_poht','".$bar['nopo']."','','log_slave_print_log_po',event);\"></td>";                            
                                for ($a=1;$a<4;$a++)
                                 {	
                                    if($bar['persetujuan'.$a]!='')
                                    {
                                            if(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&($bar['hasilpersetujuan'.$a]!=''))
                                             {
                                                  echo"
                                                <td><button class=mybutton disabled onclick=\"get_data_po('".$bar['nopo']."')\">".$_SESSION['lang']['approve']."</button></td>
                                                <td><button class=mybutton disabled onclick=rejected_po('".$bar['nopo']."') >".$_SESSION['lang']['ditolak']."</button></td>
                                                ";                           

                                             }
                                             else if(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&($bar['hasilpersetujuan'.$a]==''))
                                             {
                                              
											    echo"
                                                <td><button class=mybutton onclick=\"get_data_po('".$bar['nopo']."')\">".$_SESSION['lang']['approve']."</button></td>
                                                <td><button class=mybutton onclick=rejected_po('".$bar['nopo']."') >".$_SESSION['lang']['ditolak']."</button></td>
                                                </td>";


                                             }

 
                                    }
                                 }
				 for($i=1;$i<4;$i++)
				 {
				 	//echo $bar['hasilpersetujuan'.$i];
					if($bar['persetujuan'.$i]!='')
					{	
						$kr=$bar['persetujuan'.$i];
						$sql="select * from ".$dbname.".datakaryawan where karyawanid='".$kr."'";
						$query=mysql_query($sql) or die(mysql_error());
						$yrs=mysql_fetch_assoc($query);
						//echo $bar['hasilpersetujuan'.$i];
						if($bar['hasilpersetujuan'.$i]=='')
						{
							$b="Belum Ada Keputusan ";
						}
						elseif($bar['hasilpersetujuan'.$i]=='1')
						{	
							$b=$_SESSION['lang']['approve'];
						}
						elseif($bar['hasilpersetujuan'.$i]=='3')
						{
							$b=$_SESSION['lang']['ditolak'];
						}	
						echo"<td align=center>".$yrs['namakaryawan']."<br />(".$b.")</td>";
					}
					else
					{
						echo"<td>&nbsp;</td>";
					}
				 }
				 echo"</tr><input type=hidden id=nopo_".$no." name=nopo_".$no." value='".$bar['nopo']."' />";
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
	 </table></div>
</fieldset
><?php
CLOSE_BOX();
?>
</div>
<input type="hidden" name="method" id="method"  /> 
<input type="hidden" id="no_po" name="no_po" />
<input type="hidden" name="user_login" id="user_login" value="<?php echo $_SESSION['standard']['userid']?>" />
<?php
echo close_body();
?>