<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';

?>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/budget_by_umum.js"></script>
<?php
 
//pilihan tipebudget
$tipebudget=substr($_SESSION['empl']['lokasitugas'],3,1);
if($tipebudget=='M')$tipebudget='MILL'; else
if($tipebudget=='E')$tipebudget='ESTATE'; else $tipebudget=$_SESSION['empl']['tipelokasitugas'];
$kodeorg=substr($_SESSION['empl']['lokasitugas'],0,4);

//pilihan kodebudget
    $str="select kodebudget,nama from ".$dbname.".bgt_kode
        where kodebudget like 'UMUM%'
        ";
    $optkodebudget="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $optkodebudget.="<option value='".$bar->kodebudget."'>".$bar->nama."</option>";
    }

//pilihan tahunbudget
    $str="select distinct tahunbudget from ".$dbname.".bgt_budget
        where tipebudget='".$tipebudget."' and kodeorg like '".$kodeorg."%' and kodebudget like 'UMUM%'
            order by tahunbudget desc
            ";
    $opttahunbudget="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $opttahunbudget.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
    }
    
//pilihan kodeakun    
    if($_SESSION['language']=='ID'){
        $dd='namaakun as namaakun';
    }else{
        $dd='namaakun1 as namaakun';
    }
    $str="select noakun,".$dd." from ".$dbname.".keu_5akun
                    where detail=1 and tipeakun = 'Biaya' order by noakun
                    ";
    $optakun="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $optakun.="<option value='".$bar->noakun."'>".$bar->noakun." - ".$bar->namaakun."</option>";
            $akun[$bar->noakun]=$bar->namaakun;
    }
    
  $optVhc="";
  $str="select kodevhc from ".$dbname.".vhc_5master where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%' order by kodevhc";
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {
      $optVhc.="<option value='".$bar->kodevhc."'>".$bar->kodevhc."</option>";
  }
    
//atas
OPEN_BOX('',"<b>".$_SESSION['lang']['biayaumum']."</b>");
echo"<table cellspacing=1 border=0>
    <tr><td>".$_SESSION['lang']['tipeanggaran']." </td><td>:</td><td>
        <input type=text class=myinputtext id=tipebudget name=tipebudget onkeypress=\"return angka_doang(event);\" maxlength=2 disabled=true style=width:150px; value=\"".$tipebudget."\"/></td></tr>
    <tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
        <input type=text class=myinputtext id=tahunbudget name=tahunbudget onkeypress=\"return angka_doang(event);\" maxlength=4 style=width:150px; /></td></tr>
    <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
        <select id=kodebudget onchange=\"updateTab();\" name=kodebudget style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optkodebudget."</select></td></tr>
    <tr><td>".$_SESSION['lang']['jenisbiaya']."</td><td>:</td><td>
        <select name=jenisbiaya id=jenisbiaya style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optakun."</select></td></tr>
  <tr><td>".$_SESSION['lang']['abkend']."</td>
      <td>:</td>
      <td><select  id=kodevhc style='width:150px;' onchange=\"kalikanRp()\" >
           <option value=''>".$optVhc."</option>
          </select>
      </td>
    </tr>    

   <tr><td>".$_SESSION['lang']['jamperthn']."</td><td>:</td><td>
        <input type=text class=myinputtextnumber name=jamperthn id=jamperthn onkeypress=\"return angka_doang(event);\" onblur=kalikanRp()></td></tr>
    
<tr><td>".$_SESSION['lang']['jumlahpertahun']." </td><td>:</td><td>
        <input type=text class=myinputtext id=jumlahbiaya name=jumlahbiaya onkeypress=\"return angka_doang(event);\" maxlength=20 style=width:150px; /></td></tr>
    <tr><td>".$_SESSION['lang']['keterangan']." </td><td>:</td><td>
        <input type=text class=myinputtext id=ketUmum name=ketUmum onkeypress=\"return tanpa_kutip(event);\" maxlength=45 style=width:150px; /></td></tr>
    <tr><td colspan=3>
        <button class=mybutton id=simpan name=simpan onclick=simpan()>".$_SESSION['lang']['save']."</button>
        <input type=hidden id=tersembunyi name=tersembunyi value=tersembunyi >
    </td></tr></table>";

//tab0
$frm[0].="<fieldset id=tab0><legend>".$_SESSION['lang']['list']."</legend>    
<div style=overflow:auto;width:100%;height:300px; id=container0>";
    $frm[0].=$_SESSION['lang']['budgetyear']." : <select name=pilihtahun0 id=pilihtahun0 onchange=\"updateTab();\"><option value=''>".$_SESSION['lang']['all']."</option>".$opttahunbudget."</select>";
    
    $frm[0].="<table id=container9 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['index']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['noakun']."</td>
            <td align=center>".$_SESSION['lang']['namaakun']."</td>
            <td align=center>".$_SESSION['lang']['keterangan']."</td>
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
            <td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
     </thead>
     <tbody>";
    $str="select * from ".$dbname.".bgt_budget
        where kodebudget like 'UMUM%' and tipebudget = '".$tipebudget."' and kodeorg = '".$kodeorg."'
            order by tahunbudget desc, noakun";
    $res=mysql_query($str);
    $no=1;
    while($bar= mysql_fetch_object($res))
    {
    $frm[0].="<tr class=rowcontent>
            <td align=center>".$bar->kunci."</td>
            <td align=center>".$bar->tahunbudget."</td>
            <td align=center>".$bar->kodeorg."</td>
            <td align=center>".$bar->tipebudget."</td>
            <td align=center>".$bar->kodebudget."</td>
            <td align=center>".$bar->noakun."</td>
            <td align=left>".$akun[$bar->noakun]."</td>
             <td align=left>".UCFIRST($bar->keterangan)."</td>
            <td align=right>".number_format($bar->rupiah)."</td>";
            if($bar->tutup==0)
            $frm[0].="<td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(".$bar->kunci.")\" title=\"Hapus\"></td>";
            else
            $frm[0].="<td align=center>&nbsp;</td>";
       $hkef.="
       </tr>";
    $no+=1;
    }
//    echo $hkef;        


    $frm[0].= "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";


$frm[0].="</div>
    ";
$frm[0].="</fieldset>";

//tab 1
$frm[1].="<fieldset id=tab1><legend>".$_SESSION['lang']['sebaran']."</legend>
    <div style=overflow:auto;width:100%;height:300px; id=container1>";
    $frm[1].=$_SESSION['lang']['budgetyear']." : <select name=pilihtahun1 id=pilihtahun1 onchange=\"updateTabs();\"><option value=''>".$_SESSION['lang']['all']."</option>".$opttahunbudget."</select>";
//    $hkef.=$_SESSION['lang']['budgetyear']." : <select name=pilihtahun1 id=pilihtahun1 onchange=\"updateTabs();\"><option value=''>".$_SESSION['lang']['all']."</option>".$opttahunbudget."</select>";
    $frm[1].="<input type=hidden id=hidden1 name=hidden1 value=\"\">";

    $arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sept","10"=>"Okt","11"=>"Nov","12"=>"Des");
    $frm[1].="
        <table><tr>";
    foreach($arrBln as $brsBulan =>$listBln)
    {
        $frm[1].="<td>".$listBln."</td>";
    } 
    $frm[1].="</tr>";
    $frm[1].="<tr>
            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss1 value=1></td>
            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss2 value=1></td>
            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss3 value=1></td>
            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss4 value=1></td>
            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss5 value=1></td>
            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss6 value=1></td>
            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss7 value=1></td>
            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss8 value=1></td>
            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss9 value=1></td>
            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss10 value=1></td>
            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss11 value=1></td>
            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss12 value=1></td>
            <td><img src=images/clear.png onclick=bersihkanDonk() style='height:30px;cursor:pointer' title='bersihkan'></td>
        </tr>
        </table>";        

    $frm[1].="<table id=container6 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td></td>
            <td align=center>No</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['noakun']."</td>
            <td align=center>".$_SESSION['lang']['namaakun']."</td>
            <td align=center>Jan</td>
            <td align=center>Feb</td>
            <td align=center>Mar</td>
            <td align=center>Apr</td>
            <td align=center>May</td>
            <td align=center>Jun</td>
            <td align=center>Jul</td>
            <td align=center>Aug</td>
            <td align=center>Sep</td>
            <td align=center>Oct</td>
            <td align=center>Nov</td>
            <td align=center>Dec</td>
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
            <td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
     </thead>
     <tbody>";
//    $str="select * from ".$dbname.".bgt_budget_detail
//        where kodeorg = '".$mesin."' and kodeorg like '".substr($_SESSION['empl']['lokasitugas'],0,4)."%'";
    $str="select a.*, b.tutup from ".$dbname.".bgt_budget_detail a
        left join ".$dbname.".bgt_budget b on a.kunci=b.kunci
        where a.kodebudget like 'UMUM%' and a.tipebudget = '".$tipebudget."' and a.kodeorg = '".$kodeorg."'
            order by a.tahunbudget desc, a.noakun";
    $res=mysql_query($str);
    $no=1;
    while($bar= mysql_fetch_object($res))
    {
       $bar->tutup==0?$rpt=" onclick=\"sebaran(".$bar->kunci.",event)\" title='Sebaran ".$kodeorg." ".$akun[$bar->noakun]."' style='cursor:pointer;'":$rpt=" ";
    $frm[1].="<tr class=rowcontent style='cursor:pointer;' id=baris".$no.">
            <td><input type=checkbox onclick=sebarkanBoo('".$bar->kunci."',".$no.",this,".$bar->rupiah.",".$bar->jumlah."); title='Sebarkan sesuai proporsi diatas'></td>
            <td align=center ".$rpt.">".$no."</td>
            <td align=center ".$rpt.">".$bar->tahunbudget."</td>
            <td align=center ".$rpt.">".$bar->kodebudget."</td>
            <td align=center ".$rpt.">".$bar->tipebudget."</td>
            <td align=right ".$rpt.">".$bar->noakun."</td>
            <td align=left ".$rpt.">".$akun[$bar->noakun]."</td>
            <td align=right ".$rpt.">".number_format($bar->rp01)."</td>
            <td align=right ".$rpt.">".number_format($bar->rp02)."</td>
            <td align=right ".$rpt.">".number_format($bar->rp03)."</td>
            <td align=right ".$rpt.">".number_format($bar->rp04)."</td>
            <td align=right ".$rpt.">".number_format($bar->rp05)."</td>
            <td align=right ".$rpt.">".number_format($bar->rp06)."</td>
            <td align=right ".$rpt.">".number_format($bar->rp07)."</td>
            <td align=right ".$rpt.">".number_format($bar->rp08)."</td>
            <td align=right ".$rpt.">".number_format($bar->rp09)."</td>
            <td align=right ".$rpt.">".number_format($bar->rp10)."</td>
            <td align=right ".$rpt.">".number_format($bar->rp11)."</td>
            <td align=right ".$rpt.">".number_format($bar->rp12)."</td>
            <td align=right ".$rpt.">".number_format($bar->rupiah)."</td>";
            if($bar->tutup==0)
            $frm[1].="
            <td align=center>
                <input type=\"image\" id=search src=images/search.png class=dellicon title=".$_SESSION['lang']['sebaran']." onclick=\"sebaran(".$bar->kunci.",event)\";>
            </td>";
            else
            $frm[1].="<td align=center>&nbsp;</td>";
       $hkef.="
       </tr>";
    $no+=1;
    }


    $frm[1].= "</tbody>
     <tfoot>
     </tfoot>		 
     </table>
    </div>";
$frm[1].="</fieldset>";

//tab2
$frm[2].="<fieldset id=tab2><legend>".$_SESSION['lang']['close']."</legend>";
$frm[2].="<table cellspacing=1 border=0><thead>
    </thead>
    <tr>
    <td>".$_SESSION['lang']['budgetyear']." : <select name=pilihtahun2 id=pilihtahun2 onchange=\"updateTabs2();\"><option value=''>".$_SESSION['lang']['all']."</option>".$opttahunbudget."</select>
    </td><td>
        <button class=mybutton id=display2 name=display2 onclick=persiapantutup2()>".$_SESSION['lang']['list']."</button>
    </td><td>
        <button class=mybutton id=tutup2 name=tutup2 onclick=tutup2(1) disabled=true>".$_SESSION['lang']['close']."</button>
        <input type=hidden id=hidden2 name=hidden2 value=>
    </td></tr></table>";
$frm[2].="</fieldset>";
//box dalam tab3, daftar table
$frm[2].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
<div id=container2></div>    
    ";
$frm[2].="</fieldset>";

//========================
//tab title
$hfrm[0]=$_SESSION['lang']['list'];
$hfrm[1]=$_SESSION['lang']['sebaran'];
$hfrm[2]=$_SESSION['lang']['close'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//===============================================	
?>

<?php
CLOSE_BOX();

echo close_body();
?>