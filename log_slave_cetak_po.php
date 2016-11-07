<?php
	require_once('master_validation.php');
	include('lib/nangkoelib.php');
	require_once('config/connection.php');
	include_once('lib/zLib.php');
	$method=$_POST['method'];
	switch($method)
	{
        case 'list_new_data':
        $limit=20;
        $page=0;
        if(isset($_POST['page']))
        {
        $page=$_POST['page'];
        if($page<0)
        $page=0;
        }
        $offset=$page*$limit;
			
			//$sql2="select count(*) as jmlhrow from ".$dbname.".log_poht  ORDER BY nopo DESC";
			
    if(isset($_POST['txtSearch']))
    {
            $txt_search=$_POST['txtSearch'];
            $txt_tgl=tanggalsystem($_POST['tglCari']);
            $txt_tgl_t=substr($txt_tgl,0,4);
            $txt_tgl_b=substr($txt_tgl,4,2);
            $txt_tgl_tg=substr($txt_tgl,6,2);
            $txt_tglr=$txt_tgl_t."-".$txt_tgl_b."-".$txt_tgl_tg;
            //echo "warning:".$txt_tgl;
    }
    else
    {
            $txt_search='';
            $txt_tgl='';
    }
                        if($txt_search!='')
			{
				$where.=" and nopo LIKE  '%".$txt_search."%'";
			}
			if($_POST['tglCari']!='')
			{
				$where.=" and tanggal LIKE '%".$txt_tglr."%'";
			}
			//elseif(($txt_tgl!='')&&($txt_search!=''))
//			{
//				$where.=" nopo LIKE '%".$txt_search."%' or tanggal LIKE '%".$txt_tgl."%'";
//			}
		//echo $strx; exit();
			
				//$strx="SELECT * FROM ".$dbname.".log_poht where ".$where." order by nopo desc limit ".$offset.",".$limit."";//echo $strx;	
				$strx="SELECT * FROM ".$dbname.".log_poht where statuspo<>5 and nopo!=''  ".$where." order by tanggal desc limit ".$offset.",".$limit."";
				$sql2="SELECT count(*) as jmlhrow FROM ".$dbname.".log_poht where statuspo<>5 and nopo!='' ".$where." order by tanggal desc ";	 
			
			//echo "warning:".$strx;exit();
            $query2=mysql_query($sql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
			$jlhbrs= $jsl->jmlhrow;
			}
			
                                 if($res=mysql_query($strx))
                                 {
                                     	while($bar=mysql_fetch_assoc($res))
                                    {
					
					$spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$bar['kodeorg']."'"; //echo $spr;
					$rep=mysql_query($spr) or die(mysql_error($conn));
					$bas=mysql_fetch_object($rep);
					$no+=1;
					if($bar['stat_release']==1)
						$st=$_SESSION['lang']['release_po'];
					else
						$st=$_SESSION['lang']['un_release_po'];
					echo"<tr class=rowcontent id='tr_".$no."'>
						  <td>".$no."</td>
						  <td id=td_".$no.">".$bar['nopo']."</td>
						  <td>".tanggalnormal($bar['tanggal'])."</td>
						  <td>".$bas->namaorganisasi."</td>
						  <td>".$st."</td>";
							$sql="select * from ".$dbname.".datakaryawan where karyawanid='".$bar['persetujuan1']."'";
							$query=mysql_query($sql) or die(mysql_error());
							$yrs=mysql_fetch_assoc($query);	
							echo"<td align=center>".$yrs['namakaryawan']."</td>";
						// for($i=1;$i<4;$i++)
//						 {
//							//echo $bar['hasilpersetujuan'.$i];
//							if($bar['persetujuan'.$i]!='')
//							{
//								$kr=$bar['persetujuan'.$i];
//								$sql="select * from ".$dbname.".datakaryawan where karyawanid='".$kr."'";
//								$query=mysql_query($sql) or die(mysql_error());
//								$yrs=mysql_fetch_assoc($query);
//								if($bar['hasilpersetujuan'.$i]==1)
//								{
//										$st="(".$_SESSION['lang']['approve'].")";
//								}
//								elseif($bar['hasilpersetujuan'.$i]==2)
//								{
//										$st="(".$_SESSION['lang']['ditolak'].")";
//								}
//								else
//								{
//									$st="";
//								}
//
//								echo"<td align=center>".$yrs['namakaryawan']."<br />".$st."</td>";
//							}
//							else
//							{
//								echo"<td>&nbsp;</td>";
//							}
//						  }
						 // if(($bar['purchaser']==$_SESSION['standard']['userid'])&&($bar['statuspo']==2))
//						  {
                                                        if($bar['lokalpusat']==0)
                                                        {
							  ?>
							 <td>
							 <button class=mybutton onclick="masterPDF('log_poht','<?php  echo $bar['nopo']?>','','log_slave_print_log_po',event);" ><?php echo $_SESSION['lang']['print'] ?>
							 </button>
                              <button class=mybutton onclick="masterPDF('log_poht','<?php  echo $bar['nopo']?>','','log_slave_print_log_po_keterangan',event);" ><?php echo $_SESSION['lang']['print'] ?>
							 </button>
							 </td>
						 <?php } else {?>
						<td>
							 <button class=mybutton onclick="masterPDF('log_poht','<?php  echo $bar['nopo']?>','','log_slave_print_log_po_lokal',event);" ><?php echo $_SESSION['lang']['print'] ?>
							 </button>
							 </td>
						 <?php
						 }
						 echo"</tr>";
				}
                        echo"
						 <tr><td colspan=9 align=center>
						".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
						<button class=mybutton onclick=cariPage(".($page-1).");>".$_SESSION['lang']['pref']."</button>
						<button class=mybutton onclick=cariPage(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
						</td>
						</tr><input type=hidden id=nopp_".$no." name=nopp_".$no." value='".$bar['nopp']."' />";   
                                 }
                                  else
                                {
                                echo " Gagal,".(mysql_error($conn));
                                 }
                                
                        

		break;
        case 'loadData':
//                if ($_SESSION['empl']['bagian']=='PUR')
//                $where=" WHERE kodeorg='".$_SESSION['empl']['kodeorganisasi']."' ";
		$limit=20;
			$page=0;
			if(isset($_POST['page']))
			{
			$page=$_POST['page'];
			if($page<0)
			$page=0;
			}
			$offset=$page*$limit;
			
			$sql2="select count(*) as jmlhrow from ".$dbname.".log_poht ".$where." ORDER BY nopo DESC";
			$query2=mysql_query($sql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
			$jlhbrs= $jsl->jmlhrow;
			}
	 // stat_release='1'
		$str="SELECT * FROM ".$dbname.".log_poht ".$where." ORDER BY tanggal DESC limit ".$offset.",".$limit."";
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
			if($bar['stat_release']==1)
				$st=$_SESSION['lang']['release_po'];
			else
				$st=$_SESSION['lang']['un_release_po'];
			echo"<tr class=rowcontent id='tr_".$no."'>
				  <td>".$no."</td>
				  <td id=td_".$no.">".$bar['nopo']."</td>
				  <td>".tanggalnormal($bar['tanggal'])."</td>
				  <td>".$bas->namaorganisasi."</td>
				  <td>".$st."</td>";                            
					$sql="select * from ".$dbname.".datakaryawan where karyawanid='".$bar['persetujuan1']."'";
					$query=mysql_query($sql) or die(mysql_error());
					$yrs=mysql_fetch_assoc($query);	
					echo"<td align=center>".$yrs['namakaryawan']."</td>";
				 //for($i=1;$i<4;$i++)
//				 {
//				 	//echo $bar['hasilpersetujuan'.$i];
//					if($bar['persetujuan'.$i]!='')
//					{	
//						$kr=$bar['persetujuan'.$i];
//						$sql="select * from ".$dbname.".datakaryawan where karyawanid='".$kr."'";
//						$query=mysql_query($sql) or die(mysql_error());
//						$yrs=mysql_fetch_assoc($query);	
//						if($bar['hasilpersetujuan'.$i]==1)
//						{
//								$st="(".$_SESSION['lang']['approve'].")";
//						}
//						elseif($bar['hasilpersetujuan'.$i]==2)
//						{
//								$st="(".$_SESSION['lang']['ditolak'].")";
//						}
//						else
//						{
//							$st="";
//						}
//						
//						echo"<td align=center>".$yrs['namakaryawan']."<br />".$st."</td>";
//					}
//					else
//					{
//						echo"<td>&nbsp;</td>";
//					}
//				  } 
				 
					  ?>
					 <td>			
					 <button class=mybutton onclick="masterPDF('log_poht','<?php  echo $bar['nopo']?>','','log_slave_print_log_po',event);" ><?php echo $_SESSION['lang']['print'] ?>
					 </button>
                     <button class=mybutton onclick="masterPDF('log_poht','<?php  echo $bar['nopo']?>','','log_slave_print_log_po_keterangan',event);" ><?php echo $_SESSION['lang']['keterangan'] ?>
					 </button>
					 </td>
	
				 <?php
				
				 echo"</tr>";
		}	 	 	echo"
				 <tr><td colspan=8 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr><input type=hidden id=nopp_".$no." name=nopp_".$no." value='".$bar['nopp']."' />";   	
	  }	
	  else
		{
			echo " Gagal,".(mysql_error($conn));
		}	
        break;

	default:
	break;
	}
?>