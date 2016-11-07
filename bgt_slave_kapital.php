<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

  switch($_POST['proses'])
  {
      case 'simpanHeader':
           $str="insert into ".$dbname.".bgt_kapital (tahunbudget, kodeunit, jeniskapital, keterangan, jumlah, hargasatuan, hargatotal, tutup,updateby,lokasi)
                 values(".$_POST['tahunbudget'].",'".$_POST['kodeorg']."','".$_POST['jeniskapital']."','".$_POST['keterangan']."',
                 ".$_POST['jumlah'].",".$_POST['harga'].",".$_POST['total'].",0,".$_SESSION['standard']['userid'].",'".$_POST['lokasi']."');";
          if(mysql_query($str))
          {
             $str="select a.*,b.namatipe,
                 (a.k01+a.k02+a.k03+a.k04+a.k05+a.k06+a.k07+a.k08+a.k09+a.k10+a.k11+a.k12) as sebaran
                 from ".$dbname.".bgt_kapital a left join
                   ".$dbname.".sdm_5tipeasset b on a.jeniskapital=b.kodetipe
                   where kodeunit='".$_SESSION['empl']['lokasitugas']."'
                   order by tahunbudget desc limit 100";
             $res=mysql_query($str);
             $no=0;
             while($bar=mysql_fetch_object($res))
             {
                
                $bar->tutup==0?$rtp=" title=\"Sebaran\" onclick=\"sebaran(".$bar->kunci.",event)\" style='cursor:pointer'":$rtp=''; 
                $no+=1;
                 echo"<tr class=rowcontent>
                    <td ".$rtp.">".$no."</td>
                    <td ".$rtp.">".$bar->tahunbudget."</td>
                    <td ".$rtp.">".$bar->kodeunit."</td>
                    <td ".$rtp.">".$bar->lokasi."</td>                         
                    <td ".$rtp.">".$bar->namatipe."</td>
                    <td ".$rtp.">".$bar->keterangan."</td>
                    <td ".$rtp.">".number_format($bar->jumlah,0)."</td>
                    <td ".$rtp.">".number_format($bar->hargasatuan,0)."</td>
                    <td ".$rtp.">".number_format($bar->hargatotal,0)."</td>";
                    if(round($bar->sebaran)<round($bar->hargatotal))
                     echo"<td>Not.OK</td>";
                    else
                      echo"<td>OK</td>"; 
            
                 if($bar->tutup==1)
                   echo"<td></td>";
                 else
                    echo"<td align=center style='cursor:pointer;'>
                          <img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteData('".$bar->kunci."')\" src='images/application/application_delete.png'/>
                          <input id=\"search\" src=\"images/search.png\" class=\"dellicon\" title=\"Sebaran\" onclick=\"sebaran(".$bar->kunci.",event)\" type=\"image\"></td>";      
                 echo"</tr> ";  
             }
          }
          else
          {
              echo "Error:".addslashes(mysql_error($conn).$str);
          }   
      break;    
  case 'delete':
      $str="delete from ".$dbname.".bgt_kapital where kunci=".$_POST['kunci'];
      if(mysql_query($str))
          {
            
            $bar->tutup==0?$rtp=" title=\"Sebaran\" onclick=\"sebaran(".$bar->kunci.",event)\" style='cursor:pointer'":$rtp='';
             $str="select a.*,b.namatipe,
                 (a.k01+a.k02+a.k03+a.k04+a.k05+a.k06+a.k07+a.k08+a.k09+a.k10+a.k11+a.k12) as sebaran
                 from ".$dbname.".bgt_kapital a left join
                   ".$dbname.".sdm_5tipeasset b on a.jeniskapital=b.kodetipe
                   where kodeunit='".$_SESSION['empl']['lokasitugas']."'
                   order by tahunbudget desc limit 100";
             $res=mysql_query($str);
             $no=0;
             while($bar=mysql_fetch_object($res))
             {
                $no+=1;
                 echo"<tr class=rowcontent>
                    <td ".$rtp.">".$no."</td>
                    <td ".$rtp.">".$bar->tahunbudget."</td>
                    <td ".$rtp.">".$bar->kodeunit."</td>
                    <td ".$rtp.">".$bar->lokasi."</td>                          
                    <td ".$rtp.">".$bar->namatipe."</td>   
                    <td ".$rtp.">".$bar->keterangan."</td>
                    <td align=right ".$rtp.">".number_format($bar->jumlah,0)."</td>
                    <td align=right ".$rtp.">".number_format($bar->hargasatuan,0)."</td>
                    <td align=right ".$rtp.">".number_format($bar->hargasatuan,0)."</td>";
                    if(round($bar->sebaran)<round($bar->hargatotal))
                     echo"<td>Not.OK</td>";
                    else
                      echo"<td>OK</td>"; 
                 if($bar->tutup==1)
                   echo"<td></td>";
                 else
                    echo"<td align=center style='cursor:pointer;'>
                          <img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteData('".$bar->kunci."')\" src='images/application/application_delete.png'/>
                           <input id=\"search\" src=\"images/search.png\" class=\"dellicon\" title=\"Sebaran\" onclick=\"sebaran(".$bar->kunci.",event)\" type=\"image\">     
                          </td>";

                 echo"</tr> ";  
             }
          }
          else
          {
              echo "Error:".addslashes(mysql_error($conn));
          }        
      break;
   case 'sebaran':
       $str="select * from ".$dbname.".bgt_kapital where kunci=".$_POST['kunci'];
       $res=mysql_query($str);
       while($bar=mysql_fetch_object($res))
       {
           $kunci=$bar->kunci;
           $total=$bar->hargatotal;
           $k01=$bar->k01;
           $k02=$bar->k02;
           $k03=$bar->k03;
           $k04=$bar->k04;
           $k05=$bar->k05;
           $k06=$bar->k06;
           $k07=$bar->k07;
           $k08=$bar->k08;
           $k09=$bar->k09;
           $k10=$bar->k10;
           $k11=$bar->k11;
           $k12=$bar->k12;
           $krata=$total/12;
       }

           echo"<table class=sortable cellspacing=1 border=0>
                <thead>
                <thead>
                   <tr class=rowheader><td>".$_SESSION['lang']['bulan']."</td><td>%</td><td>".number_format($total,2)."</td></tr>
                </thead>
                </thead>
                <tbody>
                <tr class=rowcontent>";
           if(($k01+$k02+$k03+$k04+$k05+$k06+$k07+$k08+$k09+$k10+$k11+$k12)<1)
           {
               for($x=1;$x<13;$x++){
                   $z=str_pad($x, 2, "0", STR_PAD_LEFT);
                    echo"<tr class=rowcontent><td>".$z."</td>
                          <td><input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" id=persen".$x." size=3 onblur=ubahNilai(".$total.") value=".number_format(($krata/$total*100),2,'.','')."></td>
                          <td><input id=k".$x." type=text class=myinputtextnumber onkeypress=\"return angka_doang(event)\" value='".$krata."' size=15></td></tr>";
               }
                 
            }
            else
            {
               for($x=1;$x<13;$x++){
                   $z=str_pad($x, 2, "0", STR_PAD_LEFT);
                    echo"<tr class=rowcontent><td>".$z."</td>
                        <td><input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" id=persen".$x." size=3 onblur=ubahNilai(".$total.") value=".number_format((${"k".$z}/$total*100),2,'.','')."></td>
                        <td><input id=k".$x." type=text class=myinputtextnumber onkeypress=\"return angka_doang(event)\" value='".${"k".$z}."' size=15></td></tr>";
               }  
            }   
            echo "<tr class=rowcontent><td colspan=3 align=center>
<img id='detail_add' title='Simpan' class=zImgBtn onclick=simpanSebaran('".$total."','".$kunci."') src='images/save.png'/ style='cursor:pointer;'>&nbsp;&nbsp;<img id='detail_add' title='Clear Form' class=zImgBtn  width='16' height='16'  onclick=\"clearForm()\" src='images/clear.png'/ style='cursor:pointer;'>";
//<button class=mybutton onclick=simpanSebaran('".$total."','".$kunci."') >".$_SESSION['lang']['save']."</button></td></tr>";  
           echo"</tr>
                </tbody>
                <tfoot>
                </tfoot>
               </table>";
      break; 
   case 'updatesebaran':
       $zz=$_POST['k01']+$_POST['k02']+$_POST['k03']+$_POST['k04']+$_POST['k05']+$_POST['k06']+$_POST['k07']+$_POST['k08']+$_POST['k09']+$_POST['k10']+$_POST['k11']+$_POST['k12'];
       if(floor($zz)>$_POST['total'])
         exit("Error: Sebaran lebih besar dari total (".$_POST['total']."<".$zz.")");
       else
       {   
           $str="update ".$dbname.".bgt_kapital set
             k01=".$_POST['k01'].",
             k02=".$_POST['k02'].",
             k03=".$_POST['k03'].",
             k04=".$_POST['k04'].",
             k05=".$_POST['k05'].",
             k06=".$_POST['k06'].",
             k07=".$_POST['k07'].",
             k08=".$_POST['k08'].",
             k09=".$_POST['k09'].",
             k10=".$_POST['k10'].",
             k11=".$_POST['k11'].",
             k12=".$_POST['k12'].",
             updateby=".$_SESSION['standard']['userid']."    
             where kunci=".$_POST['kunci'];
      if(mysql_query($str))
          {
          }
          else
          {
              echo "Error:".addslashes(mysql_error($conn));
          }  
       }
      break; 
  case'tutup':
      $str="update ".$dbname.".bgt_kapital set tutup=1,updateby=".$_SESSION['standard']['userid']." 
          where kodeunit='".$_SESSION['empl']['lokasitugas']."' and tahunbudget='".$_POST['tahun']."'";
      if(mysql_query($str))
          {
          }
          else
          {
              echo "Error:".addslashes(mysql_error($conn));
          }  
             
      break;
      
  }

?>