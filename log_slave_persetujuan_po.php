<?php
	require_once('master_validation.php');
	include('lib/nangkoelib.php');
	require_once('config/connection.php');
	include_once('lib/zLib.php');
	$method=$_POST['method'];
	$nopo=$_POST['nopo'];
	$user_id=$_SESSION['standard']['userid'];
	$rlse_user_id=$_POST['id_user'];
	$comment_persetujuan=$_POST['cm_hasil'];
	$user_id_frwd=$_POST['id_user_frwd'];
	$kolom=$_POST['kolom'];	
	$kolom_persetujuan='hasilpersetujuan'.$kolom;
	$this_date=date("Y-m-d");
	//$iduser=$_POST['id_user'];
	
	switch ($method)
	{
	case 'insert_forward_po':
	//echo "warning:masuk";exit();
	$sql="select statuspo,persetujuan1,persetujuan2,persetujuan3 from ".$dbname.".log_poht where `nopo`='".$nopo."'";
	$query=mysql_query($sql) or die(mysql_error($conn));
	$res=mysql_fetch_assoc($query);
		
		if($res['statuspo']=='2')
		{
			echo"Warning:This No.PO :".$nopo." is Already Release";
			exit();
		}		
		elseif($res['statuspo']=='1')
		{			
			
			if($res['persetujuan1']!='')
			{		
					
					$a=1;
					for($i=2;$i<4;$i++)
					{	
						//echo "warning".$i;
						if($user_id_frwd==$res['persetujuan'.$a])
						{
							echo"Warning:Please Check Employee Name, Maybe Already Used It";
							exit();									
						}
						elseif($res['persetujuan'.$i]==''&&$res['hasilpersetujuan'.$a]=='')
						{
							//echo "warning masuk".$i.$a;exit();
							
							$strx="update ".$dbname.".log_poht set persetujuan".$i."='".$user_id_frwd."',".$kolom_persetujuan."='1',tglp".$a."='".$this_date."' where `nopo`='".$nopo."'"; 					
							//$stat="Verivication Yang Ke ".$i;
							
							if($res=mysql_query($strx))
							{
								//echo $stat;
								exit();
							}
							else
							{
								echo $strx;
								echo " Gagal,".addslashes(mysql_error($conn));
								exit();
							}
							//echo "warning:berhasil".$strx;exit();
						}
						elseif(($res['persetujuan3']!='')&&($res['persetujuan3']==$user_id))
						{
							$strx="update ".$dbname.".log_poht set hasilpersetujuan3='1',statuspo='2',tglp3='".$this_date."' where `nopo`='".$nopo."'";	
							if($res=mysql_query($strx))
							{
								//echo $stat;
								break;
								exit();
							}
							else
							{
								echo $strx;exit();
								echo " Gagal,".addslashes(mysql_error($conn));
							}				
						}
						$a++;
						
						
					}						
			}//echo "WARNING:".$strx; exit();
		}
		
	
	break;
	
	case 'insert_close_po':
		//echo "warning:masuk";exit();
		$sql="select* from ".$dbname.".log_poht where nopo='".$nopo."'";
		$query=mysql_query($sql) or die(mysql_error());
		$res=mysql_fetch_assoc($query);
	
		if(($res['persetujuan3']!='')&&($user_id==$res['persetujuan3']))
		{
			
			$sql2="update ".$dbname.".log_poht set `statuspo`='2',hasilpersetujuan3='1',tglp3='".$this_date."' where nopo='".$nopo."'";	//echo"warning".$sql2;
			if($query2=mysql_query($sql2))
			{
				
			}
			else
			{
				echo $sql2;
				echo " Gagal,".addslashes(mysql_error($conn));
				exit();
			}
		}
		elseif($res['persetujuan3']=='')
		{
			
				if(($res['statuspo']==1)&&($res['purchaser']!=$user_id))
				{
					for($i=1;$i<4;$i++)
					{
						//echo "warning:masuk".$res['persetujuan'];exit();
						if(($res['persetujuan'.$i]!='')&&($res['hasilpersetujuan'.$i]==''))
						{
							$sql2="update ".$dbname.".log_poht set persetujuan".$i."='".$rlse_user_id."',hasilpersetujuan".$i."='1',`statuspo`='2',tglp".$i."='".$this_date."' where nopo='".$nopo."'";								
							if($query2=mysql_query($sql2))
							{
								//exit();
							}
							else
							{
								echo $sql2;exit();
								echo " Gagal,".addslashes(mysql_error($conn));
							}
							//echo "warning:berhasil".$sql2; exit();
						}
					}
				}
			}
			else
			{
				echo "Warning: You're not have authorized in this PP";
				exit();
			}
	break;
	case 'rejected_pp_ex':
	$sql="select* from ".$dbname.".log_poht where nopo='".$nopo."'";
	$query=mysql_query($sql) or die(mysql_error());
	$res=mysql_fetch_assoc($query);
	if(($res['statuspo']==1)&&($res['purchaser']!=$user_id))
	{
					for($c=1;$c<4;$c++)
					{
	
						if($res['persetujuan'.$c]!='')
						{
							if(($res['hasilpersetujuan'.$c]=='')&&($res['persetujuan'.$c]==$user_id))
								{
											 //echo "warning:jamhari";
											  $sql2="update ".$dbname.".log_poht set statuspo='2',hasilpersetujuan".$c."='3',tglp".$c."='".$this_date."' where nopo='".$nopo."'" ;					//echo $sql2; exit();
											  if($query2=mysql_query($sql2))
												{
													
												}
												else
												{
														echo " Gagal,".addslashes(mysql_error($conn));
														echo $sql2;exit();
												}
													
								  }
								  elseif(($res['persetujuan'.$c]==$user_id)&&($bar['hasilpersetujuan'.$c]!=''))
								  {
												echo "Warning: You already proceccd this  PP";
												exit();
	
								   }
						}
					}
	  }
	  else
	  {
		echo "Warning: You don`t have Authorizde for this PP";
		exit();
	  }
	break;
	
	case 'list_new_data':
	$userid=$_SESSION['standard']['userid'];
		/*$str="select * from ".$dbname.".log_poht where stat_release<1 and((persetujuan1=".$userid." and (hasilpersetujuan1 is null or hasilpersetujuan1=''))
         or (persetujuan2=".$userid." and (hasilpersetujuan2 is null or hasilpersetujuan2=''))
         or (persetujuan3=".$userid." and (hasilpersetujuan3 is null or hasilpersetujuan3='')))";
*/			//echo $str;
$str="SELECT * FROM ".$dbname.".log_poht where  (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' ) ORDER BY `tanggal` DESC";
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
				  <td align=center><img src=images/pdf.jpg class=resicon width='30' height='30' title='Print' onclick=\"masterPDF('log_poht','".$bar['nopo']."','','log_slave_print_detail_po',event);\"></td>";                            
                                for ($a=1;$a<4;$a++)
                                 {	
                                    if($bar['persetujuan'.$a]!='')
                                    {
                                            if(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&($bar['hasilpersetujuan'.$a]!=''))
                                             {
                                                  echo"
                                                <td><button class=mybutton disabled onclick=\"get_data_po('".$bar['nopo']."')\">".$_SESSION['lang']['disetujui']."</button></td>
                                                <td><button class=mybutton disabled onclick=rejected_po('".$bar['nopo']."') >".$_SESSION['lang']['ditolak']."</button></td>
                                                ";                           

                                             }
                                             else if(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&($bar['hasilpersetujuan'.$a]==''))
                                             {
                                              
											    echo"
                                                <td><button class=mybutton onclick=\"get_data_po('".$bar['nopo']."','".$a."')\">".$_SESSION['lang']['disetujui']."</button></td>
                                                <td><button class=mybutton onclick=rejected_po('".$bar['nopo']."','".$a."') >".$_SESSION['lang']['ditolak']."</button></td>
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
							$b=$_SESSION['lang']['wait_approval'];
						}
						elseif($bar['hasilpersetujuan'.$i]=='1')
						{	
							$b=$_SESSION['lang']['disetujui'];
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
	break;
	case 'release_po' :
	//echo "warning:masuk";
	
	$sql="select * from ".$dbname.".log_poht where nopo='".$nopo."'";
	$query=mysql_query($sql) or die(mysql_error());
	$res=mysql_fetch_assoc($query);
	if(($res['persetujuan1']!='') || ($res['persetujuan2']!='')|| ($res['hasilpersetujuan1']!='') || ($res['hasilpersetujuan2']!='') || ($res['hasilpersetujuan3']!='')) 
	{
		//echo "warning:masuk";
		if(($res['stat_release']==0) && ($res['useridreleasae']=='0000000000'))
		{		
		//	echo "warning:masuk";
			//$unopo="update ".$dbname.".log_poht set stat_release='1',useridreleasae='".$rlse_user_id."',tglrelease='".$this_date."',tanggal='".$this_date."' where nopo='".$nopo."' ";
			$unopo="update ".$dbname.".log_poht set stat_release='1',useridreleasae='".$rlse_user_id."',tglrelease='".$this_date."',tanggal='".$this_date."' where nopo='".$nopo."' ";
			$qnopo=mysql_query($unopo) or die(mysql_error());
		}
		else
		{
			echo "warning:Already Release";
			exit();
		}
	}
	else
	{
		echo"warning:Can`t Release The PO Yet";
	}
	break;
	case 'un_release_po' :
	//echo "warning:masuk";
	$sql="select * from ".$dbname.".log_poht where nopo='".$nopo."'";
	$query=mysql_query($sql) or die(mysql_error());
	$res=mysql_fetch_assoc($query);

		if(($res['stat_release']=='1') && ($res['useridreleasae']==$rlse_user_id)&&($res['tglrelease']==$this_date))
		{		
			$unopo="update ".$dbname.".log_poht set stat_release='0', useridreleasae='0000000000',tglrelease='0000-00-00' where nopo='".$nopo."' ";
			$qnopo=mysql_query($unopo) or die(mysql_error());
		}
		else
		{
			echo "warning:You Don`t Have Autorize to Unrelease This PO No. ".$nopo;
			exit();
		}
	
	break;
	case 'list_new_data_release_po':
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$sql2="select count(*) as jmlhrow from ".$dbname.".log_poht where lokalpusat='0' ORDER BY nopo DESC";
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
	 
			$str="SELECT * FROM ".$dbname.".log_poht where lokalpusat='0' ORDER BY nopo DESC LIMIT ".$offset.",".$limit."";
			//echo $str;
	  if($res=mysql_query($str))
	  {
		while($bar=mysql_fetch_assoc($res))
		{
			$kodeorg=$bar['kodeorg'];
			$spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$kodeorg."' or induk='".$kodeorg."'"; //echo $spr;
			$rep=mysql_query($spr) or die(mysql_error($conn));
			$bas=mysql_fetch_object($rep);
			$no+=1;
			echo"<tr class=rowcontent id='tr_".$no."'>
				  <td>".$no."</td>
				  <td id=td_".$no.">".$bar['nopo']."</td>
				  <td>".tanggalnormal($bar['tanggal'])."</td>
				  <td align=center>".$kodeorg."</td>
				  <!--<td align=center><img src=images/pdf.jpg class=resicon width='30' height='30' title='Print' onclick=\"masterPDF('log_poht','".$bar['nopo']."','','log_slave_print_log_po',event);\"></td>-->";                            
                                
				 for($i=1;$i<4;$i++)
				 {
				 	//echo $bar['hasilpersetujuan'.$i];
					if($bar['persetujuan'.$i]!='')
					{	
						
						if($bar['hasilpersetujuan'.$i]=='1')
						{
								$st=$_SESSION['lang']['disetujui'];
						}
						elseif($bar['hasilpersetujuan'.$i]=='2')
						{
								$st=$_SESSION['lang']['ditolak'];
						}
						else
						{
							$st=$_SESSION['lang']['wait_approve'];
						}
						$kr=$bar['persetujuan'.$i];
						$sql="select * from ".$dbname.".datakaryawan where karyawanid='".$kr."'";
						$query=mysql_query($sql) or die(mysql_error());
						$yrs=mysql_fetch_assoc($query);	
						echo"<td align=center><a href=# onclick=\"cek_status_pp('".$bar['hasilpersetujuan'.$i]."')\">".$yrs['namakaryawan']."<br />(".$st.")</a></td>";
					}
					else
					{
						echo"<td>&nbsp;</td>";
					}
				  }
			   if(($bar['statuspo']=='2'))
				  {
					 if(($bar['stat_release']=='1')&&($bar['useridreleasae']!='0000000000'))
					 { 	$disbled="<td align=center>".tanggalnormal($bar['tglrelease'])."</td>";}
					  else
					  {	$disbled="<td><button class=mybutton onclick=\"release_po('".$bar['nopo']."')\" >".$_SESSION['lang']['release_po']."</button></td>";}
					if(($bar['stat_release']=='0')&&($bar['useridreleasae']=='0000000000'))
					  { 
					 	 $disbled2="<td><button class=mybutton onclick=\"un_release_po('".$bar['nopo']."') \" disabled>".$_SESSION['lang']['un_release_po']."</button></td>";
					  }
					  else
					  {	if($bar['tglrelease']==$this_date)
					  	{	
					  		$disbled2="<td><button class=mybutton onclick=\"un_release_po('".$bar['nopo']."') \">".$_SESSION['lang']['un_release_po']."</button></td>";
						}
						else
						{
							$disbled2="<td>&nbsp;</td>";
						}
					  }
					  ?>
					<?php echo $disbled; echo $disbled2; ?>
				 <?php } else {?>
				  <td colspan="2" align="center"><?php echo $_SESSION['lang']['wait_approval']?></td>
				 <?php
				 }
				 echo"</tr><input type=hidden id=nopo_".$no." name=nopo_".$no." value='".$bar['nopo']."' />";
		}	 	   	
			echo" <tr><td colspan=8 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr>";   	
	  }	
	  else
		{
			echo " Gagal,".(mysql_error($conn));
		}	
	break;
	default:
	break;
	}
	
	    