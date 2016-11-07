<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];

//header
$kodeTrans=$_POST['kodeTrans'];
$batchVar=$_POST['batchVar'];
$kdOrg=$_POST['kdOrg'];
$jmlhBibitan=$_POST['jmlhBibitan'];
$ket=$_POST['ket'];
$jnsBibitan=$_POST['jnsBibitan'];
$supplierid=$_POST['supplierid'];
$tglProduksi=tanggalsystem($_POST['tglProduksi']);
$tglTnm=tanggalsystem($_POST['tglTnm']);

//$where=" tahunbudget='".$thnBudget."' and kodeorg='".$kdBlok."' and tipebudget='".$tpBudget."' and kegiatan='".$kegId."' and volume='".$volKeg."' and satuanv='".$satuan."' and rotasi='".$rotThn."'";
#penyemaian#
 


$optnmCust=makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$optnmSup=makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmkaryawan=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');

$oldJenisBibit=$_POST['oldJenisBibit'];
$kdOrgTjn=$_POST['kdOrgTjn'];
$intexDt=$_POST['intexDt'];

$kdvhc=$_POST['kdvhc'];
$nmSupir=$_POST['nmSupir'];
$intexDt=$_POST['intexDt'];
$detPeng=$_POST['detPeng'];
$assistenPnb=$_POST['assistenPnb'];
$custId=$_POST['custId'];
$kodeAfd=$_POST['kodeAfd'];
$KegiatanId=$_POST['KegiatanId'];

//param+='&kbnId='+kbnId+'&nodo='+nodo+'&afkirKcmbh='+afkirKcmbh+'&jmlhdDo='+jmlhdDo;
$jmlhTrima=$_POST['jmlhTrima'];
$nodo=$_POST['nodo'];
$afkirKcmbh=$_POST['afkirKcmbh'];
$jmlhdDo=$_POST['jmlhdDo'];
$jmlRit=$_POST['jmlRit'];

switch($proses)
{
        case'saveTab1':
           if(($kdOrg=='')||($jmlhBibitan=='')||($jnsBibitan=='')||($supplierid=='')||($tglProduksi=='')||($tglTnm==''))
           {
               exit(" Error: ".$_SESSION['lang']['isifield']."");
           }
           $scek="select  post from ".$dbname.".bibitan_mutasi where batch='".$tglTnm."' and kodeorg='".$kdOrg."'";
           //exit("Error".$scek);
           $qcek=mysql_query($scek) or die("Error ".mysql_error($conn));
           $rcek=mysql_num_rows($qcek);
           if($rcek=='0')
           {
               $sInsert="insert into ".$dbname.".bibitan_batch (batch, tanggal, tanggaltanam, jenisbibit, supplerid, tanggalproduksi,jumlahdo,jumlahterima,jumlahafkir,nodo) 
                   values('".$tglTnm."','".$tglTnm."','".$tglTnm."','".$jnsBibitan."','".$supplierid."','".$tglProduksi."','".$jmlhdDo."','".$jmlhTrima."','".$afkirKcmbh."','".$nodo."')";
               if(mysql_query($sInsert))
               {
                   $sInsert2="insert into ".$dbname.".bibitan_mutasi (batch, kodeorg, tanggal, kodetransaksi, jumlah, keterangan, updateby) 
                   values('".$tglTnm."','".$kdOrg."','".$tglTnm."','".$kodeTrans."','".$jmlhBibitan."','".$ket."','".$_SESSION['standard']['userid']."')";
                   if(!mysql_query($sInsert2))
                   {
                        echo "DB Error : ".$sInsert2."\n".mysql_error($conn);
                   }
               }
               else
               {
                    echo "DB Error : ".$sInsert."\n".mysql_error($conn);
               }
           }
           else
           {
               exit(" Error:".$_SESSION['lang']['post']."");
           }
        break;
        case'loadDataStock':
           
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                $tglSkrng=date("Y-m-d");
                $thnSkrng=date("Y");
                $sql2="select * from ".$dbname.".bibitan_mutasi where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' 
                       group by batch,kodeorg order by tanggal desc";
                $query2=mysql_query($sql2) or die("Error ".mysql_error($conn));
                $jlhbrs=mysql_num_rows($query2);
                if($jlhbrs!=0)
                {
                $sData="select  batch,kodeorg,sum(jumlah) as jumlah from ".$dbname.".bibitan_mutasi 
                        where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and post=1 
                        group by batch,kodeorg order by tanggal desc limit ".$offset.",".$limit." ";
               // exit("error".$sData);
                $qData=mysql_query($sData) or die("Error ".mysql_error($conn));
                while($rData=mysql_fetch_assoc($qData))
                {
                    $data='';
                    $sDatabatch="select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi from ".$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
                    $qDataBatch=mysql_query($sDatabatch) or die("Error ".mysql_error($conn));
                    $rDataBatch=mysql_fetch_assoc($qDataBatch);
                     
                    $thnData=substr($rDataBatch['tanggaltanam'],0,4);
                    $starttime=strtotime($rDataBatch['tanggaltanam']);//time();// tanggal sekarang
                    $endtime=strtotime($tglSkrng);//tanggal pembuatan dokumen

                    $jmlHari=($endtime-$starttime)/(60*60*24*30);
                    
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$rData['batch']."</td>";
                    $tab.="<td>".$optNm[$rData['kodeorg']]."</td>";
                    $tab.="<td align=right>".number_format($rData['jumlah'],0)."</td>";
                    $tab.="<td>".$optnmSup[$rDataBatch['supplerid']]."</td>";
                    $tab.="<td align=right>".number_format($jmlHari,2)."</td>";
                    $tab.="</tr>";
                }
                  $tab.="
		<tr class=rowheader><td colspan=10 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
                }
                else
                {
                    $tab.="<tr class=rowcontent><td colspan=12>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
            
            echo $tab;
        break;
        case'loadData1':
           //param+='&statCari='+statCar+'&batchCari='+batchCar+'&tglCari='+tglCar;
            $tanggal = substr(tanggalsystem($_POST['tglCari2']),0,4).'-'.substr(tanggalsystem($_POST['tglCari2']),4,2).'-'.substr(tanggalsystem($_POST['tglCari2']),6,2);
            if($_POST['tglCari2']!='')
            {
                $wher.=" and tanggal like '%".$tanggal."%'";
            }
            if($_POST['batchCari2']!='')
            {
                $wher.=" and batch like '%".$_POST['batchCari2']."%'";
            }
            if($_POST['statCari2']!='')
            {
                $wher.=" and post='".$_POST['statCari2']."'";
            }
//            exit("error: ".$wher);
            
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                
                $sql2="select * from ".$dbname.".bibitan_mutasi where kodetransaksi='TMB' and  kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ".$wher."
                       order by tanggal desc ";
//                exit("error".$sql2);
                $query2=mysql_query($sql2) or die("Error ".mysql_error($conn));
                $jlhbrs=mysql_num_rows($query2);
                if($jlhbrs!=0)
                {
                $sData="select distinct kodetransaksi, jumlah,batch,kodeorg,tanggal,post,flag from ".$dbname.".bibitan_mutasi  
                        where kodetransaksi='TMB' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ".$wher."
                        order by tanggal desc limit ".$offset.",".$limit." ";
//                exit("error".$sData);
                $qData=mysql_query($sData) or die("Error ".mysql_error($conn));
                while($rData=mysql_fetch_assoc($qData))
                {
                    $data='';
                    $sDatabatch="select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi,jumlahdo,jumlahterima,jumlahafkir,nodo from ".$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
                    $qDataBatch=mysql_query($sDatabatch) or die("Error ".mysql_error($conn));
                    $rDataBatch=mysql_fetch_assoc($qDataBatch);
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$rData['kodetransaksi']."</td>";
                    $tab.="<td>".$rData['batch']."</td>";
                    $tab.="<td>".$optNm[$rData['kodeorg']]."</td>";
                    $tab.="<td align=right>".$rData['jumlah']."</td>";
                    //$tab.="<td>".$rData['keterangan']."</td>";
                    $tab.="<td>".tanggalnormal($rData['tanggal'])."</td>";
                    $tab.="<td>".$rDataBatch['jenisbibit']."</td>";
                    $tab.="<td>".$optnmSup[$rDataBatch['supplerid']]."</td>";
                    $tab.="<td>".tanggalnormal($rDataBatch['tanggalproduksi'])."</td>";
                    if(($rData['post']==1)&&($rData['flag']=='manual'))
                    {
                        $data=1;
                    }//
                    else if(($rData['flag']=='AUTO')&&($rData['post']==1))
                    {
                        $data=1;
                    }
                    else if(($rData['post']==0)&&($rData['flag']=='manual'))
                    {
                         $data=0;          
                    }
                    else
                    {
                        $data=3;
                    }
                    
                    if($data==0)
                    {
                        $tab.="<td  align=center colspan=2><img id='detail_edit' &nbsp; style='cursor:pointer;' title='Edit ".$rData['batch']."' class=zImgBtn onclick=\"filFieldHead('".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['jumlah']."','".tanggalnormal($rDataBatch['tanggaltanam'])."','".$rDataBatch['jenisbibit']."','".$rDataBatch['supplerid']."','".tanggalnormal($rDataBatch['tanggalproduksi'])."','".$rDataBatch['nodo']."'
                              ,'".$rDataBatch['jumlahdo']."','".$rDataBatch['jumlahterima']."','".$rDataBatch['jumlahafkir']."')\" src='images/application/application_edit.png'/>";
                        $tab.="&nbsp;<img id='detail_del' style='cursor:pointer;' title='Delete ".$rData['batch']."' class=zImgBtn onclick=\"delFieldHead('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."','".$rDataBatch['jenisbibit']."')\" src='images/application/application_delete.png'/>";
                        $tab.="&nbsp;<img id='detail_del' style='cursor:pointer;' title='Posting Data ".$rData['batch']."' class=zImgBtn onclick=\"postingData('".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."')\" src='images/skyblue/posting.png'/></td>";
                    }
                    else if($data==3)
                    {
                       $tab.="<td>References</td>"; 
                    }
                    else
                    {
                        $tab.="<td>".$_SESSION['lang']['posting']."</td>";   
                    }

                    $tab.="</tr>";
                }
                  $tab.="
		<tr class=rowheader><td colspan=10 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
                }
                else
                {
                    $tab.="<tr class=rowcontent><td colspan=12>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
            
            echo $tab;
        break;
        case'loadData2':
           if($_POST['statCari']!='')
            {
                $wher.=" and post='".$_POST['statCari']."'";
            }
            if($_POST['batchCari']!='')
            {
                $wher.=" and batch like '%".$_POST['batchCari']."%'";
            }
            $tanggal = substr(tanggalsystem($_POST['tglCari']),0,4).'-'.substr(tanggalsystem($_POST['tglCari']),4,2).'-'.substr(tanggalsystem($_POST['tglCari']),6,2);
            if($_POST['tglCari']!='')
            {
                $wher.=" and tanggal like '%".$tanggal."%'";
            }
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                
                $sql2="select * from ".$dbname.".bibitan_mutasi where kodetransaksi='TPB' and  
                       kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ".$wher." order by tanggal desc ";
                $query2=mysql_query($sql2) or die("Error ".mysql_error($conn));
                $jlhbrs=mysql_num_rows($query2);
                if($jlhbrs!=0)
                {
                $sData="select distinct kodetransaksi, jumlah,batch,kodeorg,tanggal,post,flag,tujuan,keterangan from ".$dbname.".bibitan_mutasi  where 
                        kodetransaksi='TPB' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'  ".$wher." 
                        order by tanggal desc limit ".$offset.",".$limit." ";
                //exit("error".$sData);
                $qData=mysql_query($sData) or die("Error ".mysql_error($conn));
                while($rData=mysql_fetch_assoc($qData))
                {
                    $data='';
                    $sDatabatch="select distinct jumlah from ".$dbname.".bibitan_mutasi where batch='".$rData['batch']."' and kodeorg='".$rData['tujuan']."' ";
                    $qDataBatch=mysql_query($sDatabatch) or die("Error ".mysql_error($conn));
                    $rDataBatch=mysql_fetch_assoc($qDataBatch);
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$rData['kodetransaksi']."</td>";
                    $tab.="<td>".$rData['batch']."</td>";
                    $tab.="<td>".tanggalnormal($rData['tanggal'])."</td>";
                    $tab.="<td>".$optNm[$rData['kodeorg']]."</td>";
                    $tab.="<td>".$optNm[$rData['tujuan']]."</td>";
                    $tab.="<td align=right>".$rData['jumlah']."</td>";
                    $tab.="<td>".$rData['keterangan']."</td>";
                    if(($rData['post']==1)&&($rData['flag']=='manual'))
                    {
                        $data=1;
                    }//
                    elseif(($rData['flag']=='AUTO')&&($rData['post']==0))
                    {
                        $data=1;
                    }
                    elseif(($rData['post']==0)&&($rData['flag']=='manual'))
                    {
                         $data=0;          
                    }
                    if($data==0)
                    {
                        $tab.="<td  align=center colspan=2><img id='detail_edit' &nbsp; style='cursor:pointer;' title='Edit ".$rData['batch']."' class=zImgBtn onclick=\"filField2('".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['tujuan']."','".tanggalnormal($rData['tanggal'])."','".substr($rData['jumlah'],1)."')\" src='images/application/application_edit.png'/>";
                        $tab.="&nbsp;<img id='detail_del' style='cursor:pointer;' title='Delete ".$rData['batch']."' class=zImgBtn onclick=\"delField2('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['tujuan']."')\" src='images/application/application_delete.png'/>";
                        $tab.="&nbsp;<img id='detail_del' style='cursor:pointer;' title='Posting Data ".$rData['batch']."' class=zImgBtn onclick=\"postingData2('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['tujuan']."','".$rData['jumlah']."')\" src='images/skyblue/posting.png'/></td>";
                    }
                    else
                    {
                        $tab.="<td>".$_SESSION['lang']['posting']."</td>";   
                    }

                    $tab.="</tr>";
                }
                  $tab.="
		<tr class=rowheader><td colspan=10 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast2(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast2(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
                }
                else
                {
                    $tab.="<tr class=rowcontent><td colspan=12>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
            
            echo $tab;
        break;
         case'loadData3':
            if($_POST['statCari']!='')
            {
                $wher.=" and post='".$_POST['statCari']."'";
            }
            if($_POST['batchCari']!='')
            {
                $wher.=" and batch like '%".$_POST['batchCari']."%'";
            }
            $tanggal = substr(tanggalsystem($_POST['tglCari']),0,4).'-'.substr(tanggalsystem($_POST['tglCari']),4,2).'-'.substr(tanggalsystem($_POST['tglCari']),6,2);
            
            if($_POST['tglCari']!='')
            {
                $wher.=" and tanggal like '%".$tanggal."%'";
            }
//            exit("error: ".$wher);
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                
                $sql2="select * from ".$dbname.".bibitan_mutasi where kodetransaksi='AFB' 
                       and  kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ".$wher."  order by tanggal desc ";
//                exit("error".$sql2);
                $query2=mysql_query($sql2) or die("Error ".mysql_error($conn));
                $jlhbrs=mysql_num_rows($query2);
                if($jlhbrs!=0)
                {
                $sData="select distinct kodetransaksi, jumlah,batch,kodeorg,tanggal,post,flag,tujuan,keterangan from ".$dbname.".bibitan_mutasi  
                        where kodetransaksi='AFB' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'   ".$wher." 
                        order by tanggal desc limit ".$offset.",".$limit." ";
//                exit("error".$sData);
                $qData=mysql_query($sData) or die("Error ".mysql_error($conn));
                while($rData=mysql_fetch_assoc($qData))
                {
                    $data='';
              
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$rData['kodetransaksi']."</td>";
                    $tab.="<td>".$rData['batch']."</td>";
                    $tab.="<td>".tanggalnormal($rData['tanggal'])."</td>";
                    $tab.="<td>".$optNm[$rData['kodeorg']]."</td>";
                   
                    $tab.="<td align=right>".$rData['jumlah']."</td>";
                    $tab.="<td>".$rData['keterangan']."</td>";
                    if(($rData['post']==1)&&($rData['flag']=='manual'))
                    {
                        $data=1;
                    }//
                    elseif(($rData['flag']=='AUTO')&&($rData['post']==0))
                    {
                        $data=1;
                    }
                    elseif(($rData['post']==0)&&($rData['flag']=='manual'))
                    {
                         $data=0;          
                    }
                    if($data==0)
                    {
                        $tab.="<td  align=center colspan=2><img id='detail_edit' &nbsp; style='cursor:pointer;' title='Edit ".$rData['batch']."' class=zImgBtn onclick=\"filField3('".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."','".substr($rData['jumlah'],1)."')\" src='images/application/application_edit.png'/>";
                        $tab.="&nbsp;<img id='detail_del' style='cursor:pointer;' title='Delete ".$rData['batch']."' class=zImgBtn onclick=\"delField3('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['tujuan']."')\" src='images/application/application_delete.png'/>";
                        $tab.="&nbsp;<img id='detail_del' style='cursor:pointer;' title='Posting Data ".$rData['batch']."' class=zImgBtn onclick=\"postingData3('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['jumlah']."')\" src='images/skyblue/posting.png'/></td>";
                    }
                    else
                    {
                        $tab.="<td>".$_SESSION['lang']['posting']."</td>";   
                    }

                    $tab.="</tr>";
                }
                  $tab.="
		<tr class=rowheader><td colspan=10 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast2(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast2(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
                }
                else
                {
                    $tab.="<tr class=rowcontent><td colspan=12>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
            
            echo $tab;
        break;
         case'loadData5':
            $tanggal = substr(tanggalsystem($_POST['tglCari']),0,4).'-'.substr(tanggalsystem($_POST['tglCari']),4,2).'-'.substr(tanggalsystem($_POST['tglCari']),6,2);
            if($_POST['tglCari']!='')
            {
                $wher.=" and tanggal like '%".$tanggal."%'";
            }
            if($_POST['batchCari']!='')
            {
                $wher.=" and batch like '%".$_POST['batchCari']."%'";
            }
             if($_POST['statCari']!='')
            {
                $wher.=" and post='".$_POST['statCari']."'";
            }
//            exit("error: ".$wher);
            
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                
                $sql2="select * from ".$dbname.".bibitan_mutasi where kodetransaksi='DBT' and  
                       kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ".$wher." order by tanggal desc ";
                $query2=mysql_query($sql2) or die("Error ".mysql_error($conn));
                $jlhbrs=mysql_num_rows($query2);
                if($jlhbrs!=0)
                {
                $sData="select distinct kodetransaksi, jumlah,batch,kodeorg,tanggal,post,flag,keterangan from ".$dbname.".bibitan_mutasi  where 
                        kodetransaksi='DBT' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'  ".$wher." 
                        order by tanggal desc limit ".$offset.",".$limit." ";
//                exit("error".$sData);
                $qData=mysql_query($sData) or die("Error ".mysql_error($conn));
                while($rData=mysql_fetch_assoc($qData))
                {
                    $data='';
              
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$rData['kodetransaksi']."</td>";
                    $tab.="<td>".$rData['batch']."</td>";
                    $tab.="<td>".tanggalnormal($rData['tanggal'])."</td>";
                    $tab.="<td>".$optNm[$rData['kodeorg']]."</td>";
                   
                    $tab.="<td align=right>".$rData['jumlah']."</td>";
                    $tab.="<td>".$rData['keterangan']."</td>";
                    if(($rData['post']==1)&&($rData['flag']=='manual'))
                    {
                        $data=1;
                    }//
                    elseif(($rData['flag']=='AUTO')&&($rData['post']==0))
                    {
                        $data=1;
                    }
                    elseif(($rData['post']==0)&&($rData['flag']=='manual'))
                    {
                         $data=0;          
                    }
                    if($data==0)
                    {
                        $tab.="<td  align=center colspan=2><img id='detail_edit' &nbsp; style='cursor:pointer;' title='Edit ".$rData['batch']."' class=zImgBtn onclick=\"filField5('".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."','".$rData['jumlah']."')\" src='images/application/application_edit.png'/>";
                        $tab.="&nbsp;<img id='detail_del' style='cursor:pointer;' title='Delete ".$rData['batch']."' class=zImgBtn onclick=\"delField5('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['tujuan']."')\" src='images/application/application_delete.png'/>";
                        $tab.="&nbsp;<img id='detail_del' style='cursor:pointer;' title='Posting Data ".$rData['batch']."' class=zImgBtn onclick=\"postingData5('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['tujuan']."','".$rData['jumlah']."')\" src='images/skyblue/posting.png'/></td>";
                    }
                    else
                    {
                        $tab.="<td>".$_SESSION['lang']['posting']."</td>";   
                    }

                    $tab.="</tr>";
                }
                  $tab.="
		<tr class=rowheader><td colspan=10 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast2(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast2(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
                }
                else
                {
                    $tab.="<tr class=rowcontent><td colspan=12>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
            
            echo $tab;
        break;
        case'loadData7':
            $tanggal = substr(tanggalsystem($_POST['tglCari']),0,4).'-'.substr(tanggalsystem($_POST['tglCari']),4,2).'-'.substr(tanggalsystem($_POST['tglCari']),6,2);
            if($_POST['statCari']!='')
            {
                $wher.=" and post='".$_POST['statCari']."'";
            }
            if($_POST['batchCari']!='')
            {
                $wher.=" and batch like '%".$_POST['batchCari']."%'";
            }
            if($_POST['tglCari']!='')
            {
                $wher.=" and tanggal like '%".$tanggal."%'";
            }
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                
                $sql2="select * from ".$dbname.".bibitan_mutasi where kodetransaksi='PNB' and  
                       kodeorg like '".$_SESSION['empl']['lokasitugas']."%'   ".$wher."  order by tanggal desc ";
                $query2=mysql_query($sql2) or die("Error ".mysql_error($conn));
                $jlhbrs=mysql_num_rows($query2);
                if($jlhbrs!=0)
                {
                $sData="select distinct * from ".$dbname.".bibitan_mutasi  where kodetransaksi='PNB' and 
                        kodeorg like '".$_SESSION['empl']['lokasitugas']."%'   ".$wher."  
                        order by tanggal desc limit ".$offset.",".$limit." ";
               // exit("error".$sData);
                $qData=mysql_query($sData) or die("Error ".mysql_error($conn));
                while($rData=mysql_fetch_assoc($qData))
                {
                    $data='';
                    $no+=1;
                    if(strlen($rData['pelanggan'])=='4')
                    {
                        $pelanggan=$optNm[$rData['pelanggan']];
                    }
                    else
                    {
                         $pelanggan=$optnmSup[$rData['pelanggan']];
                    }
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$rData['kodetransaksi']."</td>";
                    $tab.="<td>".$rData['batch']."</td>";
                    $tab.="<td>".tanggalnormal($rData['tanggal'])."</td>";
                    $tab.="<td>".$optNm[$rData['kodeorg']]."</td>";  
                    $tab.="<td align=right>".$rData['jumlah']."</td>";
                    $tab.="<td>".$rData['jenistanam']."</td>";
                    $tab.="<td>".$rData['keterangan']."</td>";
                    $tab.="<td>".$rData['kodevhc']."</td>";
                    
                    
                    $tab.="<td>".$pelanggan."</td>";
                    $tab.="<td>".$optNm[$rData['afdeling']]."</td>"; 
                    $tab.="<td>".$optNmkaryawan[$rData['penanggungjawab']]."</td>";
                    if(($rData['post']==1)&&($rData['flag']=='manual'))
                    {
                        $data=1;
                    }//
                    elseif(($rData['flag']=='AUTO')&&($rData['post']==0))
                    {
                        $data=1;
                    }
                    elseif(($rData['post']==0)&&($rData['flag']=='manual'))
                    {
                         $data=0;          
                    }
                    if($data==0)
                    {
                       $tab.="<td  align=center colspan=2>";
                       // $tab.="<img id='detail_edit' &nbsp; style='cursor:pointer;' title='Edit ".$rData['batch']."' class=zImgBtn onclick=\"filField7('".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."','".substr($rData['jumlah'],1)."','".$rData['kodevhc']."','".$rData['sopir']."','".$rData['intex']."','".$rData['pelanggan']."','".$rData['lokasipengiriman']."','".$rData['penanggungjawab']."','".$rData['afdeling']."','".$rData['jenistanam']."','".$rData['rit']."')\" src='images/application/application_edit.png'/>";
                        $tab.="&nbsp;<img  style='cursor:pointer;' title='Delete ".$rData['batch']."' class=zImgBtn onclick=\"delField7('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['rit']."','".trim($rData['kodevhc'])."')\" src='images/application/application_delete.png'/>";
                        $tab.="&nbsp;<img  style='cursor:pointer;' title='Posting Data ".$rData['batch']."' class=zImgBtn onclick=\"postingData7('".$rData['tanggal']."','".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['rit']."','".trim($rData['kodevhc'])."','".$rData['jumlah']."')\" src='images/skyblue/posting.png'/>";
                        $tab.="&nbsp;<img  style='cursor:pointer;' title='PDF ".$rData['batch']."' class=resicon  src='images/pdf.jpg' onclick=\"masterPDF('bibitan_mutasi','".$rData['tanggal'].",".$rData['kodetransaksi'].",".$rData['batch'].",".$rData['kodeorg'].",".$rData['rit'].",".trim($rData['kodevhc'])."','','kebun_slavepengirimanBibitPdf',event)\" /></td>";
                    }
                    else
                    {
                        $tab.="<td><img  style='cursor:pointer;' title='Posting Data ".$rData['batch']."' class=resicon  src='images/pdf.jpg' onclick=\"masterPDF('bibitan_mutasi','".$rData['tanggal'].",".$rData['kodetransaksi'].",".$rData['batch'].",".$rData['kodeorg'].",".$rData['rit'].",".$rData['kodevhc']."','','kebun_slavepengirimanBibitPdf',event)\" /></td>";
                    }

                    $tab.="</tr>";
                }
                  $tab.="
		<tr class=rowheader><td colspan=13 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast7(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast7(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
                }
                else
                {
                    $tab.="<tr class=rowcontent><td colspan=13>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
            
            echo $tab;
        break;
        case'getKet':
          $sData="select distinct keterangan from ".$dbname.".bibitan_mutasi  where kodeorg='".$kdOrg."' and batch='".$batchVar."' and kodetransaksi='".$kodeTrans."' ";
          $qData=mysql_query($sData) or die("Error ".mysql_error($conn));
          $rData=mysql_fetch_assoc($qData);
          echo $rData['keterangan'];
        break;
	case'update1':
           if(($kdOrg=='')||($jmlhBibitan=='')||($jnsBibitan=='')||($supplierid=='')||($tglProduksi=='')||($tglTnm==''))
           {
                exit(" Error: ".$_SESSION['lang']['isifield']."");
           }
            $scek="select distinct post from ".$dbname.".bibitan_mutasi where batch='".$batchVar."' and  kodeorg='".$kdOrg."' and kodetransaksi='TMB' ";
           //exit("Error".$scek);
           $qcek=mysql_query($scek) or die("Error ".mysql_error($conn));
           $rcek=mysql_fetch_assoc($qcek);
           if($rcek['post']=='0')
           {
               $supdate="update ".$dbname.".bibitan_batch  set jenisbibit='".$jnsBibitan."',supplerid='".$supplierid."', tanggalproduksi='".$tglProduksi."',jumlahdo='".$jmlhdDo."',jumlahterima='".$jmlhTrima."',jumlahafkir='".$afkirKcmbh."',nodo='".$nodo."'
                         where batch='".$batchVar."' and jenisbibit='".$oldJenisBibit."'";//(batch, tanggal, tanggaltanam, , ) "
               if(mysql_query($supdate))
               {
                   $supdate2="update ".$dbname.".bibitan_mutasi set jumlah='".$jmlhBibitan."',keterangan='".$ket."',updateby='".$_SESSION['standard']['userid']."' where batch='".$batchVar."' and kodeorg='".$kdOrg."' and kodetransaksi='TMB' and tanggal='".$tglTnm."'";// (batch, kodeorg, tanggal, kodetransaksi, jumlah, keterangan, )
                   if(!mysql_query($supdate2))
                   {
                     echo "DB Error : ".$supdate2."\n".mysql_error($conn);
                   }
               }
               else
               {
                  echo "DB Error : ".$supdate."\n".mysql_error($conn);
               }
           }
           else
           {
               exit(" Error: ".$_SESSION['lang']['post']."");
           }
	break;
       
        case'delData':
           $scek="select distinct post from ".$dbname.".bibitan_mutasi where batch='".$tglTnm."' and kodeorg='".$kdOrg."' and tanggal='".$_POST['tanggal']."'";
           //exit("Error".$scek);
           $qcek=mysql_query($scek) or die("Error ".mysql_error($conn));
           $rcek=mysql_fetch_assoc($qcek);
           if($rcek['post']=='0')
           {
               $sDel="delete from ".$dbname.".bibitan_mutasi where kodetransaksi='".$kodeTrans."' and batch='".$batchVar."' and kodeorg='".$kdOrg."' and tanggal='".$tglTnm."' and tanggal='".$_POST['tanggal']."'";
              // exit("Error".$sDel);
               if(mysql_query($sDel))
               {
                   $sDel2="delete from ".$dbname.".bibitan_batch where batch='".$batchVar."' and jenisbibit='".$oldJenisBibit."' and tanggal='".$_POST['tanggal']."'";
                   if(!mysql_query($sDel2))
                   {
                           //ada kemungkinan tidak terhapus jika sudah banyak transaksi di mutasi
                           //echo "DB Error : ".$sDel2."\n".mysql_error($conn);
                   }
               }
               else
               {
                    echo "DB Error : ".$sDel."\n".mysql_error($conn);
               }
           }
            
        break;
         case'delData2':
           $scek="select distinct post from ".$dbname.".bibitan_mutasi where batch='".$batchVar."' and kodeorg='".$kdOrg."' and kodetransaksi='TPB' and tujuan='".$kdOrgTjn."' and tanggal='".$_POST['tanggal']."'";
          // exit("Error".$scek);
           $qcek=mysql_query($scek) or die("Error ".mysql_error($conn));
           $rcek=mysql_fetch_assoc($qcek);
           if($rcek['post']=='0')
           {
               $sDelet="delete from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrgTjn."' and kodetransaksi='TMB' and batch='".$batchVar."' and tanggal='".$_POST['tanggal']."'";
               //exit("Error".$sDelet);
              if(mysql_query($sDelet))
               {
                   $sDelete2="delete from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='TPB' and tujuan='".$kdOrgTjn."' and batch='".$batchVar."' and tanggal='".$_POST['tanggal']."'";
                   if(mysql_query($sDelete2))
                       echo"";
                   else
                       echo "DB Error : ".$sDelete2."\n".mysql_error($conn);
               }
                else
                   {
                        echo "DB Error : ".$sDelet."\n".mysql_error($conn);
                   }
           }
           else
           {
               exit(" Error:".$_SESSION['lang']['post']."");
           }
        break;
        case'delData3':
           $sDelete2="delete from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' 
            and kodetransaksi='".$kodeTrans."' and batch='" .$batchVar."' and tanggal='".$_POST['tanggal']."'";
               if(!mysql_query($sDelete2))
               { 
                   echo "DB Error : ".$sDelete2."\n".mysql_error($conn);
                   
               }
        break;
          case'delData5':
           $sDelete2="delete from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='DBT' and  batch='" .$batchVar."' and tanggal='".$_POST['tanggal']."'";
               if(!mysql_query($sDelete2))
               { 
                   echo "DB Error : ".$sDelete2."\n".mysql_error($conn);
                   
               }
        break;
         case'delData7':
           $sDeleteX="delete from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and rit='".$_POST['rit']."' and kodevhc='".$_POST['kodevhc']."' and batch='" .$batchVar."'  and tanggal='".$_POST['tanggal']."'";
             if(!mysql_query($sDeleteX))
               { 
                   echo "DB Error : ".$sDeleteX."\n".mysql_error($conn);
                   
               }
        break;
        case'postData':
           $scek="select distinct post from ".$dbname.".bibitan_mutasi where batch='".$tglTnm."' and kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."'";
           //exit("Error".$scek);
           $qcek=mysql_query($scek) or die("Error ".mysql_error($conn));
           $rcek=mysql_fetch_assoc($qcek);
           if($rcek['post']=='0')
           {
               $sDel="update ".$dbname.".bibitan_mutasi set post=1 where kodetransaksi='".$kodeTrans."' and batch='".$batchVar."' and kodeorg='".$kdOrg."' and tanggal='".$tglTnm."' and post='0'";
              // exit("Error".$sDel);
               if(!mysql_query($sDel))
               {
                   echo "DB Error : ".$sDel."\n".mysql_error($conn);
               }
           }
           else
           {
               exit(" Error:".$_SESSION['lang']['nodata']."");
           }
        break;
        case'postData2':
           $scek2="select sum(jumlah) as totalBibitan from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
           $qcek2=mysql_query($scek2) or die("Error ".mysql_error($conn));
           $rcek2=mysql_fetch_assoc($qcek2);
           if(($jmlhBibitan*-1)>$rcek2['totalBibitan'])
           {
               exit(" Error:".$_SESSION['lang']['jumlah']." ".$jmlhBibitan." ".$_SESSION['lang']['greater']." ".$_SESSION['lang']['total']." 
                   ".$_SESSION['lang']['stock']." ".$rcek2['totalBibitan']." ".$_SESSION['lang']['on']." ".$_SESSION['lang']['batch']." ".$batchVar." ".$_SESSION['lang']['lokasi']." ".$kdOrg);
           }
           
           $scek="select post from ".$dbname.".bibitan_mutasi where batch='".$batchVar."' and kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and tujuan='".$kdOrgTjn."' and tanggal='".$_POST['tanggal']."'";

           $qcek=mysql_query($scek) or die("Error ".mysql_error($conn));
           $rcek=mysql_fetch_assoc($qcek);

           if($rcek['post']=='0')
           {   //execute 2 query on one script
               $sDel="update ".$dbname.".bibitan_mutasi set post=1 where kodetransaksi='TMB' and batch='".$batchVar."' and kodeorg='".$kdOrgTjn."' and post='0' and tanggal='".$_POST['tanggal']."' and flag='AUTO';";
              // exit("Error".$sDel);
               if(!mysql_query($sDel))
               {
                   echo "DB Error : ".$sDel."\n".mysql_error($conn);
               }
               else
               {
                   $su="update ".$dbname.".bibitan_mutasi set post=1 where kodetransaksi='".$kodeTrans."' and batch='".$batchVar."' and kodeorg='".$kdOrg."' and tujuan='".$kdOrgTjn."' and post='0' and tanggal='".$_POST['tanggal']."';";
                   mysql_query($su); 
               }
           }
           else
           {
               exit(" Error:".$_SESSION['lang']['post']."");
           }
        break;
        case'postData3':
           $scek2="select sum(jumlah) as totalBibitan from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
           $qcek2=mysql_query($scek2) or die("Error ".mysql_error($conn));
           $rcek2=mysql_fetch_assoc($qcek2);
          // exit("Error".$rcek2['totalBibitan']);
           if(($jmlhBibitan*-1)>$rcek2['totalBibitan'])
           {
               exit(" Error:".$_SESSION['lang']['jumlah']." ".$jmlhBibitan." ".$_SESSION['lang']['greater']." ".$_SESSION['lang']['total']." ".$_SESSION['lang']['stock']." ".$rcek2['totalBibitan']." ".$_SESSION['lang']['on']." ".$_SESSION['lang']['batch']." ".$batchVar." ".$_SESSION['lang']['lokasi']." ".$kdOrg);
           }            
           $scek="select post from ".$dbname.".bibitan_mutasi where batch='".$batchVar."' and kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and tanggal='".$_POST['tanggal']."' and post=1";
           $qcek=mysql_query($scek) or die("Error ".mysql_error($conn));
           $rcek=mysql_num_rows($qcek);
           if($rcek=='0')
           {
               $sDel="update ".$dbname.".bibitan_mutasi set post=1 where batch='".$batchVar."' and kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and tanggal='".$_POST['tanggal']."'";
               //exit("Error".$sDel);
               if(!mysql_query($sDel))
               {
                   echo "DB Error : ".$sDel."\n".mysql_error($conn);
               }
           }
           else
           {
               exit(" Error: ".$_SESSION['lang']['post']."");
           }
        break;
       case'postData5':          
           $scek="select post from ".$dbname.".bibitan_mutasi where batch='".$batchVar."' and kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and tanggal='".$_POST['tanggal']."' and post=1";
           // exit("Error".$scek);
           $qcek=mysql_query($scek) or die("Error ".mysql_error($conn));
           $rcek=mysql_num_rows($qcek);
           //exit("Error".$rcek['post']);
           if($rcek=='0')
           {
               $sDel="update ".$dbname.".bibitan_mutasi set post=1 where batch='".$batchVar."' and kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and tanggal='".$_POST['tanggal']."'";
               //exit("Error".$sDel);
               if(!mysql_query($sDel))
               {
                   echo "DB Error : ".$sDel."\n".mysql_error($conn);
               }
           }
           else
           {
               exit(" Error:".$_SESSION['lang']['post']."");
           }
        break;        
        case'postData7':
           $scek2="select sum(jumlah) as totalBibitan from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
           $qcek2=mysql_query($scek2) or die("Error ".mysql_error($conn));
           $rcek2=mysql_fetch_assoc($qcek2);
          // exit("Error".$rcek2['totalBibitan']);
           if(($jmlhBibitan*-1)>$rcek2['totalBibitan'])
           {
              exit(" Error:".$_SESSION['lang']['jumlah']." ".$jmlhBibitan." ".$_SESSION['lang']['greater']." ".$_SESSION['lang']['total']." 
                   ".$_SESSION['lang']['stock']." ".$rcek2['totalBibitan']." ".$_SESSION['lang']['on']." ".$_SESSION['lang']['batch']." ".$batchVar." ".$_SESSION['lang']['lokasi']." ".$kdOrg);
           }  
           

           $scek="select distinct post from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and rit='".$jmlRit."' and kodevhc like '".$kdvhc."%' and batch='" .$batchVar."' and tanggal='".$_POST['tanggal']."'";
          // exit("Error".$scek);
           $qcek=mysql_query($scek) or die("Error ".mysql_error($conn));
           $rcek=mysql_fetch_assoc($qcek);
           //exit("Error".$scek);
           if($rcek['post']=='0')
           {
               $sDel="update ".$dbname.".bibitan_mutasi set post=1 where kodeorg='".$kdOrg."' and kodetransaksi='".$kodeTrans."' and rit='".$jmlRit."' and kodevhc like '".$kdvhc."%' and batch='" .$batchVar."' and post='0' and tanggal='".$_POST['tanggal']."'";
               //exit("Error".$sDel);
               if(!mysql_query($sDel))
               {
                   echo "DB Error : ".$sDel."\n".mysql_error($conn);
               }
           }
           else
           {
               exit(" Error:".$_SESSION['lang']['post']."");
           }
        break;
         
        case'getKodeorg':
            $optKdorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

            if($batchVar!='')
            {
                $sData="select distinct kodeorg from ".$dbname.".bibitan_mutasi where batch='".$batchVar."'";
               // exit("Error:".$sData);
                $qData=mysql_query($sData) or die("Error ".mysql_error($conn));
                //$rData=mysql_fetch_assoc($qData);
                //$sOrg2="select kodeorg from ".$dbname.".setup_blok where  statusblok='BBT' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by kodeorg asc";
                //echo $sOrg2;
                //$qOrg2=mysql_query($sOrg2) or die(mysql_error());
                while($rOrg2=mysql_fetch_assoc($qData))
                {
                        $optKdorg.="<option value=".$rOrg2['kodeorg']." >".$optNm[$rOrg2['kodeorg']]."</option>";
                }
                echo $optKdorg;
            }
        break;
            case'getKodeorgN':
            $optKdorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
           if($batchVar!='')
            {
                $sData="select distinct kodeorg from ".$dbname.".bibitan_mutasi where batch='".$batchVar."' and kodeorg not like '%PN%'";
                //exit("Error:".$sData);
                $qData=mysql_query($sData) or die("Error ".mysql_error($conn));
                //$rData=mysql_fetch_assoc($qData);
                //$sOrg2="select kodeorg from ".$dbname.".setup_blok where  statusblok='BBT' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by kodeorg asc";
                //echo $sOrg2;
                //$qOrg2=mysql_query($sOrg2) or die(mysql_error());
                while($rOrg2=mysql_fetch_assoc($qData))
                {
                        $optKdorg.="<option value=".$rOrg2['kodeorg']." >".$optNm[$rOrg2['kodeorg']]."</option>";
                }
                echo $optKdorg;
            }
        break;
        case'cekSmGak':
            $sData="select distinct kodeorg from ".$dbname.".bibitan_mutasi where batch='".$batchVar."'";
            $qData=mysql_query($sData) or die("Error ".mysql_error($conn));
            $rData=mysql_fetch_assoc($qData);
            //exit("Error"."__".$rData['kodeorg']."__".$kdOrg);
            if($rData['kodeorg']==$kdOrg)
            {
                echo "1";
            }
        break;
        case'saveTab2':
           if(($kdOrgTjn=='')||($batchVar=='')||($jmlhBibitan=='')||($tglTnm==''))
           {
               exit(" Error: ".$_SESSION['lang']['isifield']."");
           }
           
           $str=" select * from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrgTjn."' and kodetransaksi='TMB' and batch='".$batchVar."' and tanggal='".$tglTnm."' and post=1";
           $res=mysql_query($str);
           if(mysql_num_rows($res)>0)
           {
               exit (" Error: ".$_SESSION['lang']['exist']."");
           }
           
           $scek2="select sum(jumlah) as totalBibitan from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
           $qcek2=mysql_query($scek2) or die("Error ".mysql_error($conn));
           $rcek2=mysql_fetch_assoc($qcek2);
          // exit("Error".$rcek2['totalBibitan']);
           if($jmlhBibitan>$rcek2['totalBibitan'])
           {
               exit(" Error:".$_SESSION['lang']['jumlah']." ".$jmlhBibitan." ".$_SESSION['lang']['greater']." ".$_SESSION['lang']['total']." ".$_SESSION['lang']['stock']." ".$rcek2['totalBibitan']." ".$_SESSION['lang']['on']." ".$_SESSION['lang']['batch']." ".$batchVar." ".$_SESSION['lang']['lokasi']." ".$kdOrg);
           }
                   //$sGetData="select distinct "
                   $sDelet="delete from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrgTjn."' and kodetransaksi='TMB' and batch='".$batchVar."' and tanggal='".$tglTnm."' and flag='AUTO'";

                   if(mysql_query($sDelet))
                   {
                       $sDelete2="delete from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='TPB' and tanggal='".$tglTnm."' and tujuan='".$kdOrgTjn."' and batch='".$batchVar."'";

                       if(mysql_query($sDelete2))
                       {
                            $jmlh=$jmlhBibitan*-1;
                            $sInsert="insert into ".$dbname.".bibitan_mutasi (batch, kodeorg, tanggal, kodetransaksi, jumlah, keterangan, updateby,tujuan) 
                            values('".$batchVar."','".$kdOrg."','".$tglTnm."','TPB','".$jmlh."','".$ket."','".$_SESSION['standard']['userid']."','".$kdOrgTjn."')";
                            if(mysql_query($sInsert))
                            {
                                $sInsert2="insert into ".$dbname.".bibitan_mutasi (batch, kodeorg, tanggal, kodetransaksi, jumlah, keterangan, updateby,flag) 
                                values('".$batchVar."','".$kdOrgTjn."','".$tglTnm."','TMB','".$jmlhBibitan."','".$ket."','".$_SESSION['standard']['userid']."','AUTO')";
                                if(!mysql_query($sInsert2))
                                {
                                    echo "DB Error : ".$sInsert2."\n".mysql_error($conn);
                                }
                            }
                            else
                            {
                                echo "DB Error : ".$sInsert."\n".mysql_error($conn);
                            }
                       }
                   }

        break;
        case'saveTab3':
           if(($kdOrg=='')||($batchVar=='')||($jmlhBibitan=='')||($tglTnm==''))
           {
               exit(" Error: ".$_SESSION['lang']['isifield']."");
           }
           $str=" select * from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='AFB' and batch='".$batchVar."' and tanggal='".$tglTnm."' and post=1";
           $res=mysql_query($str);
           if(mysql_num_rows($res)>0)
           {
               exit (" Error: ".$_SESSION['lang']['exist']."");
           }
           
           $scek2="select sum(jumlah) as totalBibitan from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
           $qcek2=mysql_query($scek2) or die("Error ".mysql_error($conn));
           $rcek2=mysql_fetch_assoc($qcek2);
           if($jmlhBibitan>$rcek2['totalBibitan'])
           {
               exit(" Error:".$_SESSION['lang']['jumlah']." ".$jmlhBibitan." ".$_SESSION['lang']['greater']." ".$_SESSION['lang']['total']." ".$_SESSION['lang']['stock']." ".$rcek2['totalBibitan']." ".$_SESSION['lang']['on']." ".$_SESSION['lang']['batch']." ".$batchVar." ".$_SESSION['lang']['lokasi']." ".$kdOrg);
           }
           //$sGetData="select distinct "
         
               $sDelete2="delete from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='AFB' and batch='".$batchVar."' and tanggal='".$tglTnm."'";
               if(mysql_query($sDelete2))
               {
                      $jmlh=$jmlhBibitan*-1;

                        $sInsert2="insert into ".$dbname.".bibitan_mutasi (batch, kodeorg, tanggal, kodetransaksi, jumlah, keterangan, updateby) 
                        values('".$batchVar."','".$kdOrg."','".$tglTnm."','AFB','".$jmlh."','".$ket."','".$_SESSION['standard']['userid']."')";
                        if(!mysql_query($sInsert2))
                        {
                            echo "DB Error : ".$sInsert2."\n".mysql_error($conn);
                        }
               }
            else {
                    echo "DB Error : ".$sDelete2."\n".mysql_error($conn);
            }
        break;
        case'saveTab5':
           if(($kdOrg=='')||($batchVar=='')||($jmlhBibitan=='')||($tglTnm==''))
           {
               exit(" Error: ".$_SESSION['lang']['isifield']."");
           }
           $str=" select * from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='DBT'  and batch='".$batchVar."' and tanggal='".$tglTnm."' and post=1";
           $res=mysql_query($str);
           if(mysql_num_rows($res)>0)
           {
               exit (" Error: ".$_SESSION['lang']['exist']."");
           }           
           $scek2="select  sum(jumlah) as totalBibitan from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
           $qcek2=mysql_query($scek2) or die("Error ".mysql_error($conn));
           $rcek2=mysql_fetch_assoc($qcek2);
           if($jmlhBibitan>$rcek2['totalBibitan'])
           {
               exit(" Error:".$_SESSION['lang']['jumlah']." ".$jmlhBibitan." ".$_SESSION['lang']['greater']." ".$_SESSION['lang']['total']." ".$_SESSION['lang']['stock']." ".$rcek2['totalBibitan']." ".$_SESSION['lang']['on']." ".$_SESSION['lang']['batch']." ".$batchVar." ".$_SESSION['lang']['lokasi']." ".$kdOrg);
           }
           
               $sDelete2="delete from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and kodetransaksi='DBT'  and batch='".$batchVar."' and tanggal='".$tglTnm."'";
               if(mysql_query($sDelete2))
               {
                        $sInsert2="insert into ".$dbname.".bibitan_mutasi (batch, kodeorg, tanggal, kodetransaksi, jumlah, keterangan, updateby) 
                        values('".$batchVar."','".$kdOrg."','".$tglTnm."','DBT','".$jmlhBibitan."','".$ket."','".$_SESSION['standard']['userid']."')";
                        if(!mysql_query($sInsert2))
                        {
                            echo "DB Error : ".$sInsert2."\n".mysql_error($conn);
                        }
               }
        break;
        case'saveTab7':         
           if(($kdOrg=='')||($batchVar=='')||($jmlhBibitan=='')||($tglTnm=='')||($intexDt=='')||($kdvhc=='')||($nmSupir=='')||($assistenPnb=='')||($custId=='')||($jmlRit==''))
           {
               exit(" Error: ".$_SESSION['lang']['isifield']."");
           }

           $scek2="select sum(jumlah) as totalBibitan from ".$dbname.".bibitan_mutasi where kodeorg='".$kdOrg."' and batch='".$batchVar."' and post=1 group by kodeorg";
           $qcek2=mysql_query($scek2) or die("Error ".mysql_error($conn));
           $rcek2=mysql_fetch_assoc($qcek2);
           if($jmlhBibitan>$rcek2['totalBibitan'])
           {
               exit(" Error:".$_SESSION['lang']['jumlah']." ".$jmlhBibitan." ".$_SESSION['lang']['greater']." ".$_SESSION['lang']['total']." ".$_SESSION['lang']['stock']." ".$rcek2['totalBibitan']." ".$_SESSION['lang']['on']." ".$_SESSION['lang']['batch']." ".$batchVar." ".$_SESSION['lang']['lokasi']." ".$kdOrg);
           }

                $jmlh=$jmlhBibitan*-1;
                $sInsert2="insert into ".$dbname.".bibitan_mutasi (batch, kodeorg, tanggal, kodetransaksi, jumlah, keterangan, updateby, kodevhc, sopir, intex, pelanggan, lokasipengiriman, penanggungjawab,jenistanam,afdeling,rit) 
                values('".$batchVar."','".$kdOrg."','".$tglTnm."','PNB','".$jmlh."','".$ket."' ,'".$_SESSION['standard']['userid']."','".$kdvhc."','".$nmSupir."','".$intexDt."','".$custId."','".$detPeng."','".$assistenPnb."','".$KegiatanId."','".$kodeAfd."','".$jmlRit."')";
                if(!mysql_query($sInsert2))
                {
                    echo "DB Error : ".$sInsert2."\n".mysql_error($conn);
                }

        break;
        case'getCust':
        $optKode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        if($intexDt!='')
        {
                if($intexDt=='1')
                {
                $sOpt="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['org']['kodeorganisasi']."' and tipe='KEBUN'";
                }
                elseif($intexDt=='2')
                {
                $sOpt="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk<>'".$_SESSION['org']['kodeorganisasi']."'  and tipe='KEBUN'";
                }
                elseif($intexDt=='0')
                {
                $sOpt="select kodecustomer as kodeorganisasi,namacustomer as namaorganisasi from ".$dbname.".pmn_4customer  order by namacustomer asc";
                }
                //exit("error:".$sOpt);
                $qOpt=mysql_query($sOpt) or die("Error ".mysql_error($conn));
                while($rOpt=mysql_fetch_assoc($qOpt))
                {
                    if($kdOrg!='')
                    {//
                             $optKode.="<option value='".$rOpt['kodeorganisasi']."' ".($rOpt['kodeorganisasi']==$kdOrg?'selected':'').">".$rOpt['namaorganisasi']."</option>";
                    }
                    else
                    {
                            $optKode.="<option value='".$rOpt['kodeorganisasi']."'>".$rOpt['namaorganisasi']."</option>";
                    }
                }
        }
        
        echo $optKode;
        break;
        case'getAfd':
        $optKode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sOpt="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$kdOrg."'  order by namaorganisasi asc";
        $qOpt=mysql_query($sOpt) or die("Error ".mysql_error($conn));
        while($rOpt=mysql_fetch_assoc($qOpt))
        {
            if($kdOrg!='')
            {//
                     $optKode.="<option value='".$rOpt['kodeorganisasi']."' ".($rOpt['kodeorganisasi']==$kodeAfd?'selected':'').">".$rOpt['namaorganisasi']."</option>";
            }
            else
            {
                    $optKode.="<option value='".$rOpt['kodeorganisasi']."'>".$rOpt['namaorganisasi']."</option>";
            }
        }
        echo $optKode;
        break;
        case'getBlok':
        $optKode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sOpt="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk like '".$kdOrg."%' and tipe = 'blok' order by namaorganisasi asc";
        $qOpt=mysql_query($sOpt) or die("Error ".mysql_error($conn));
        while($rOpt=mysql_fetch_assoc($qOpt))
        {
            if($kdOrg!='')
            {//
                     $optKode.="<option value='".$rOpt['kodeorganisasi']."' ".($rOpt['kodeorganisasi']==$kodeAfd?'selected':'').">".$rOpt['namaorganisasi']."</option>";
            }
            else
            {
                    $optKode.="<option value='".$rOpt['kodeorganisasi']."'>".$rOpt['namaorganisasi']."</option>";
            }
        }
        echo $optKode;
        break;
        case'getBatch':
            $optBatch="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $sBatch="select distinct batch from ".$dbname.".bibitan_mutasi where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by batch desc";
            $qBatch=mysql_query($sBatch) or die("Error ".mysql_error($conn));
            while($rBatch=mysql_fetch_assoc($qBatch))
            {
                $optBatch.="<option value='".$rBatch['batch']."'>".$rBatch['batch']."</option>";
            }
            echo $optBatch;
        break;
	default:
	break;
}
?>