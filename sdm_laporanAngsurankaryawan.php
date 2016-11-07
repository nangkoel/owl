<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/sdm_payrollHO.js'></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?php
include('master_mainMenu.php');
//+++++++++++++++++++++++++++++++++++++++++++++
$str="select * from ".$dbname.".sdm_ho_component
      where name like '%Angs%'";
$res=mysql_query($str,$conn);
$arr=Array();
$opt='';
while($bar=mysql_fetch_object($res))
{
        $opt.="<option value=".$bar->id.">".$bar->name."</option>";
        $arr[$bar->id]=$bar->name;
}
$opt3='';
for($z=-12;$z<=64;$z++)
{
        $da=mktime(0,0,0,date('m')-$z,'1',date('Y'));
        $opt3.="<option value='".date('Y-m',$da)."'>".date('m-Y',$da)."</option>";
}
        OPEN_BOX('',"<b>".$_SESSION['lang']['angsuran']."</b>");
                echo"<div id=EList>";
                echo OPEN_THEME($_SESSION['lang']['angsuran']);
               if($_SESSION['language']=='EN'){
                echo"<br>(Display installment on:<select id=bln onchange=showAngsuran(this.options[this.selectedIndex].value)><option value=''></option>".$opt3."</select>)
                     || (Display installment which:<select id=lunas  onchange=showAngsuran(this.options[this.selectedIndex].value)><option value=''></option>
                         <option value=lunas>Settled</option>
                         <option value=blmlunas>Not yet settled</option>
                         <option value=active>Active</option>
                         <option value=notactive>Not Active</option></select>)";         
                echo"<hr><br>Below installment that :<b><span id=caption>not yet settled</span></b>
                     <image src=images/pdf.jpg class=resicon title='PDF' onclick=angsuranPDF(event)>
                         <input type=hidden id=val value=''>
                         ";                 
               } else{
                echo"<br>(Tampilkan Angsuran Bulan:<select id=bln onchange=showAngsuran(this.options[this.selectedIndex].value)><option value=''></option>".$opt3."</select>)
                     || (Tampilkan Angsuran Yang<select id=lunas  onchange=showAngsuran(this.options[this.selectedIndex].value)><option value=''></option>
                         <option value=lunas>Sudah Lunas</option>
                         <option value=blmlunas>Belum Lunas</option>
                         <option value=active>Active</option>
                         <option value=notactive>Not Active</option></select>)	 
                         ";
                echo"<hr><br>Berikut Angsuran Karyawan :<b><span id=caption>Belum Lunas</span></b>
                     <image src=images/pdf.jpg class=resicon title='PDF' onclick=angsuranPDF(event)>
                         <input type=hidden id=val value=''>
                         ";                 
               }
                        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          (u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                      order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                      order by namakaryawan";		
                        }
	  		     
                echo"<table class=sortable width=900px border=0 cellspacing=1>
                      <thead>
                          <tr class=rowheader>
                            <td align=center>No.</td>
                                <td align=center>".$_SESSION['lang']['karyawanid']."</td>
                            <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
                                <td align=center>".$_SESSION['lang']['jennisangsuran']."</td>
                                <td align=center>".$_SESSION['lang']['total']." ".$_SESSION['lang']['nilaihutang']."<br>(Rp.)</td>
                                <td align=center>".$_SESSION['lang']['bulanawal']."</td>
                                <td align=center>".$_SESSION['lang']['sampai']."</td>
                                <td align=center>".$_SESSION['lang']['jumlah']."<br>(".$_SESSION['lang']['bulan'].")</td>
                                <td align=center>".$_SESSION['lang']['potongan']."/".$_SESSION['lang']['bulan'].".<br>(Rp.)</td>				
                                <td align=center>".$_SESSION['lang']['status']."</td>
                          </tr> 
                          </thead>
                          <tbody id=tbody>";
                $res=mysql_query($str,$conn);
                echo mysql_error($conn);
                $no=0;
                while($bar=mysql_fetch_object($res))
                {			  
                   $no+=1;
                   echo"<tr class=rowcontent>
                            <td class=firsttd>".$no."</td>
                            <td>".$bar->karyawanid."</td>
                                <td>".$bar->namakaryawan."</td>
                                <td>".$arr[$bar->jenis]."</td>
                                <td align=right>".number_format($bar->total,2,'.',',')."</td>
                                <td align=center>".$bar->start."</td>
                                <td align=center>".$bar->end."</td>
                                <td align=right>".$bar->jlhbln."</td>
                                <td align=right>".number_format($bar->bulanan,2,'.',',')."</td>				
                                <td align=center>".($bar->active==1?"Active":"Not Active")."</td>
                          </tr>"; 
                  $ttl+=$bar->bulanan;	  			
                }
                echo"</tbody>
                          <tfoot></tfoot>
                          </table>";  	  			 
                echo"</div>";
                echo CLOSE_THEME();		
        CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>