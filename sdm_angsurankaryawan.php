<?php //@Copy nangkoelframework
require_once('master_validation.php');

include('lib/nangkoelib.php');
require_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/sdm_payrollHO.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?php
include('master_mainMenu.php');
//+++++++++++++++++++++++++++++++++++++++++++++

$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');

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

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
  $str1="select * from ".$dbname.".datakaryawan
      where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") 
          and alokasi=1
          order by namakaryawan";
  // $str2="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where lenght(kodeorganisasi)='4'";	  	  
}
else if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{//and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
  $str1="select * from ".$dbname.".datakaryawan
 	 where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") 
	  and tipekaryawan!=0 
	  and lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
	  order by namakaryawan";   	  
}

else
{
   $str1="select * from ".$dbname.".datakaryawan
      where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") 
          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
          order by namakaryawan";	
	// $str2="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".substr($_SESSION['empl']['lokasitugas'],0,4)."'";	  
}
//echo $str2;
$res1=mysql_query($str1,$conn);
$opt1='';
while($bar1=mysql_fetch_object($res1))
{
        $opt1.="<option value=".$bar1->karyawanid.">".$bar1->namakaryawan." -- ".$bar1->nik." -- ".$bar1->lokasitugas."[".$nmOrg[$bar1->lokasitugas]."]</option>";
}

 $str2="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi in 
   		 (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
$optOrg="<option value=0>".$_SESSION['lang']['all']."</option>";;
$res2=mysql_query($str2,$conn);
while($bar2=mysql_fetch_assoc($res2))
{
	$optOrg.="<option value=".$bar2['kodeorganisasi'].">".$bar2['namaorganisasi']."</option>";
}


if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
	$sortOrg="
			<tr>
				<td>
					<table>
						<tr>
							<td>Sortir Organisasi</td>
							<td>:</td>
							<td><select id=kdOrg style=\"width:150px;\" onchange=getKar()>".$optOrg."</select></td>
						</tr>
					</table>						
				</td>
			</tr>";
}

$opt3='';
for($z=-12;$z<=64;$z++){
        $da=mktime(0,0,0,date('m')-$z,'1',date('Y'));
        $opt3.="<option value='".date('Y-m',$da)."'>".date('m-Y',$da)."</option>";
}  
        OPEN_BOX('','<b>'.$_SESSION['lang']['angsuran'].'</b>');
                echo"<div id=EList>";
                echo OPEN_THEME($_SESSION['lang']['angsuran'].':');
                echo"<table>";
					 
					 echo $sortOrg;
						
				
						echo"<tr>
							<td>";
         echo"<table class=data>
		 
		
		 
                      <thead>
                          <tr>
                            <td align=center><b>".$_SESSION['lang']['namakaryawan']."</b></td>
                                <td align=center><b>".$_SESSION['lang']['jennisangsuran']."</b></td>
                                <td align=center><b>".$_SESSION['lang']['total']." ".$_SESSION['lang']['nilaihutang']."<br>(Rp.)</b></td>
                                <td align=center>".$_SESSION['lang']['bulanawal']."<br>".$_SESSION['lang']['potongan']."</td>
                                <td align=center>".$_SESSION['lang']['jumlah']."<br>(".$_SESSION['lang']['bulan'].")</td>
                                <td align=center>".$_SESSION['lang']['status']."</td>
                          </tr> 
                          </thead>
                          <tbody>
                          <tr class=rowcontent>
                          <td><select id=userid>".$opt1."</select></td>
                          <td><select id=idx>".$opt."</select></td>
                          <td><input type=text id=total class=myinputtextnumber size=13 maxlength=14 onkeypress=\"return angka_doang(event);\" onblur=change_number(this)></td>
                          <td><select id=start>".$opt3."</select></td>
                          <td><input type=text id=lama class=myinputtextnumber size=4 maxlength=3 onkeypress=\"return angka_doang(event);\" value=0></td>
                          <td><select id=active><option value=1>Active</option>
                          <option value=0>Not Active</option></select>
                          <input type=hidden value='insert' id=method>
                          </td>
                          </tr>
                          </body>
                          <tfoot></tfoot>
                      </table>
                          <center>
                            <button class=mybutton onclick=saveAngsuran()>".$_SESSION['lang']['save']."</button>
                            <button class=mybutton onclick=cancelAngsuran()>".$_SESSION['lang']['cancel']."</button>
                          </center>
                          ";
         if($_SESSION['language']=='ID'){       
         echo"</td><td>
                             <fieldset style='text-align:left;width:300px;'>
                                   <legend><b><img src=images/info.png align=left height=25px valign=asmiddle>[Info]</b></legend>
                                   <p>Satu karyawan hanya dapat memiliki satu setiap jenis angsuran.
                                      Jika angsuran sudah ada dan diinput dengan tipe yang  sama maka angsuran lama akan ditimpah. Untuk menambah komponen angsuran
                                          gunakan menu <b>Payroll Component</b> dengan syarat, awal nama komponen harus '<b>Angsuran</b>'. 
                                   </p>
                                   </fieldset>		      
                      </td></tr>
                          </table>";
         }else{
          echo"</td><td>
                             <fieldset style='text-align:left;width:300px;'>
                                   <legend><b><img src=images/info.png align=left height=25px valign=asmiddle>[Info]</b></legend>
                                   <p>Each employee can only has one type of loan.
                                        If the installments already exist and in the same type of input with the old installment will be overwritten. 
                                        If there is a new component, please register on the setup menu <b>Payroll Component</b> with condition:  component name must be preceded by the word '<b>Angsuran</b>'. 
                                   </p>
                                   </fieldset>		      
                      </td></tr>
                          </table>";            
         }     
                echo CLOSE_THEME();
                echo"<hr><div id=laporan style='width:100%; height:340px;overflow:scroll;'>
                     List Angsuran:";
         echo"<table class=sortable width=100% border=0 cellspacing=1>
                      <thead>
                          <tr class=rowheader>
                            <td align=center>No.</td>
                                <td align=center>".$_SESSION['lang']['nik']."</td>
                           		<td align=center>".$_SESSION['lang']['namakaryawan']."</td>
								<td align=center>".$_SESSION['lang']['lokasitugas']."</td>
                                <td align=center>".$_SESSION['lang']['jennisangsuran']."</td>
                                <td align=center>".$_SESSION['lang']['total']." ".$_SESSION['lang']['nilaihutang']."<br>(Rp.)</td>
                                <td align=center>".$_SESSION['lang']['bulanawal']."</td>
                                <td align=center>".$_SESSION['lang']['sampai']."</td>
                                <td align=center>".$_SESSION['lang']['jumlah']."<br>(".$_SESSION['lang']['bulan'].")</td>
                                <td align=center>".$_SESSION['lang']['potongan']."/".$_SESSION['lang']['bulan'].".<br>(Rp.)</td>				
                                <td align=center>".$_SESSION['lang']['status']."</td>
                                <td align=center></td>
                          </tr> 
                          </thead>
                          <tbody id=tbody>";
        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
        {			    
                /*$str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                      where a.karyawanid=u.karyawanid
                          (a.tipekaryawan=0 or a.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                      order by namakaryawan";*/
				$str="select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.".sdm_angsuran a left join ".$dbname.".datakaryawan u on a.karyawanid=u.karyawanid
					  where u.tipekaryawan=0 or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."'
                      order by namakaryawan";	  
					  
					  
        }
		else  if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
		{
			$str="select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.".sdm_angsuran a left join ".$dbname.".datakaryawan u on a.karyawanid=u.karyawanid
					  where u.tipekaryawan!=0 and 
					  u.lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
					  order by namakaryawan";	
		}
        else
        {
			$str="select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.".sdm_angsuran a left join ".$dbname.".datakaryawan u on a.karyawanid=u.karyawanid
					  where u.tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                      order by namakaryawan";	 
               /* $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                      where a.karyawanid=u.karyawanid
                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                      order by namakaryawan";*/		
        }
	//	print_r($_SESSION['empl']);
		//echo $str;
		
                $res=mysql_query($str,$conn);
                $no=0;
                while($bar=mysql_fetch_object($res))
                {			  
                   $no+=1;
                   echo"<tr class=rowcontent>
                            <td class=firsttd>".$no."</td>
                            <td>".$bar->nik."</td>
                                <td>".$bar->namakaryawan."</td>
								<td>".$bar->lokasitugas." -- ".$nmOrg[$bar->lokasitugas]." </td>
                                <td>".$arr[$bar->jenis]."</td>
                                <td align=right>".number_format($bar->total,2,'.',',')."</td>
                                <td align=center>".$bar->start."</td>
                                <td align=center>".$bar->end."</td>
                                <td align=right>".$bar->jlhbln."</td>
                                <td align=right>".number_format($bar->bulanan,2,'.',',')."</td>				
                                <td align=center>".($bar->active==1?"Active":"Not Active")."</td>
                                        <td>
                             <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAngsuran('".$bar->karyawanid."','".$bar->jenis."','".$bar->total."','".$bar->start."','".$bar->jlhbln."','".$bar->active."');\">
                             &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delAngsuran('".$bar->karyawanid."','".$bar->jenis."');\">		
                                        </td>				
                          </tr>"; 			
                }	  	  
                echo"</body>
                          <tfoot></tfoot>
                      </table>";  	  			 
                echo"</div>";
                echo"</div>";
        CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>