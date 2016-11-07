<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<link rel=stylesheet type='text/css' href='style/orgchart.css'>
<script   language=javascript1.2 src='js/menusetting.js'></script>
<script   language=javascript1.2 src='js/sdm_orgchart.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX();
$optNmKar=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
echo OPEN_THEME('Functional Structure:');
echo "<div class=maincontent>
      <fieldset class=legend><legend>Functional Structure:</legend>
      ".$_SESSION['lang']['strukturremark']."
          </fildset>
          ";	  


//================================  
//ambil alokasi biaya
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
      tipe='PT' order by namaorganisasi";
$res=mysql_query($str);
$optalokasi="<option value=''></option>";
while($bark=mysql_fetch_object($res))
{
        $optalokasi.="<option value='".$bark->kodeorganisasi."'>".$bark->namaorganisasi."</option>";
}	  
//======================================   

//=========================================
   $str="select * from ".$dbname.".sdm_strukturjabatan where induk='' or induk='0'";
   $res=mysql_query($str);
   echo"<ul>";
   if(mysql_num_rows($res)>0){
           while($bar=mysql_fetch_object($res))
           {
                  echo"<li class=mmgr>
                       <img title=expand class=arrow src='images/foldc_.png'  height=22px onclick=show_sub('gr".$bar->kodestruktur."',this);>";
                  echo "<b class=elink id='el".$bar->kodestruktur."' style='height:22px;font:20' onclick=\"javascript:activeOrg=this.id;orgVal='".$bar->induk."';getCurrent('".$bar->kodestruktur."');setpos('inputorg',event);\"  style='height:22px' title='Click to change'>".$bar->kodestruktur.": ".$optNmKar[$bar->karyawanid]."</b>";
                  //++++++++++++++++++++++++++
                    $str1="select * from ".$dbname.".sdm_strukturjabatan where induk='".$bar->kodestruktur."'";
                        $res1=mysql_query($str1);
                        echo"<ul id=gr".$bar->kodestruktur." style='display:none'>";
                        echo"<div id=main".$bar->kodestruktur.">";
                                while($bar1=mysql_fetch_object($res1))
                                {
                                        echo"<li class=mmgr>
                                             <img title=expand class=arrow src='images/foldc_.png' height=21px   onclick=show_sub('gr".$bar1->kodestruktur."',this);>";
                                        echo "<b class=elink id='el".$bar1->kodestruktur."'  onclick=\"javascript:activeOrg=this.id;orgVal='".$bar1->induk."';getCurrent('".$bar1->kodestruktur."');setpos('inputorg',event);\" title='Click to change'>".$bar1->kodestruktur.": ".$optNmKar[$bar1->karyawanid]."</b>";
                                  //++++++++++++++++++++++++++
                                    $str2="select * from ".$dbname.".sdm_strukturjabatan where induk='".$bar1->kodestruktur."'";
                                        $res2=mysql_query($str2);
                                                echo"<ul id=gr".$bar1->kodestruktur." style='display:none;'>";
                                                echo"<div id=main".$bar1->kodestruktur.">";						
                                                while($bar2=mysql_fetch_object($res2))
                                                {
                                                        echo"<li class=mmgr>
                                                             <img title=expand class=arrow src='images/foldc_.png' height=19px  onclick=show_sub('gr".$bar2->kodestruktur."',this);>";
                                                        echo "<b class=elink id='el".$bar2->kodestruktur."'  onclick=\"javascript:activeOrg=this.id;orgVal='".$bar2->induk."';getCurrent('".$bar2->kodestruktur."');setpos('inputorg',event);\" title='Click to change'>".$bar2->kodestruktur.": ".$optNmKar[$bar2->karyawanid]."</b>";
                                                  //++++++++++++++++++++++++++
                                                    $str3="select * from ".$dbname.".sdm_strukturjabatan where induk='".$bar2->kodestruktur."'";
                                                        $res3=mysql_query($str3);
                                                                echo"<ul id=gr".$bar2->kodestruktur." style='display:none;'>";
                                                                echo"<div id=main".$bar2->kodestruktur.">";						
                                                                while($bar3=mysql_fetch_object($res3))
                                                                {
                                                                        echo"<li class=mmgr>
                                                                             <img title=expand class=arrow src='images/foldc_.png' height=17px   onclick=show_sub('gr".$bar3->kodestruktur."',this);>";
                                                                        echo "<b class=elink id='el".$bar3->kodestruktur."'  onclick=\"javascript:activeOrg=this.id;orgVal='".$bar3->induk."';getCurrent('".$bar3->kodestruktur."');setpos('inputorg',event);\" title='Click to change'>".$bar3->kodestruktur.": ".$optNmKar[$bar3->karyawanid]."</b>";
                                                                  //++++++++++++++++++++++++++
                                                                    $str4="select * from ".$dbname.".sdm_strukturjabatan where induk='".$bar3->kodestruktur."'";
                                                                        $res4=mysql_query($str4);
                                                                                echo"<ul id=gr".$bar3->kodestruktur." style='display:none;'>";
                                                                                echo"<div id=main".$bar3->kodestruktur.">";						
                                                                                while($bar4=mysql_fetch_object($res4))
                                                                                {
                                                                                        echo"<li class=mmgr>
                                                                                             <img title=expand class=arrow src='images/foldc_.png' height=15px   onclick=show_sub('gr".$bar4->kodestruktur."',this);>";
                                                                                        echo "<b class=elink id='el".$bar4->kodestruktur."'  onclick=\"javascript:activeOrg=this.id;orgVal='".$bar4->induk."';getCurrent('".$bar4->kodestruktur."');setpos('inputorg',event);\" title='Click to change'>".$bar4->kodestruktur.": ".$optNmKar[$bar4->karyawanid]."</b>";
                                                                                  //++++++++++++++++++++++++++
                                                                                    $str5="select * from ".$dbname.".sdm_strukturjabatan where induk='".$bar4->kodestruktur."'";
                                                                                        $res5=mysql_query($str5);
                                                                                                echo"<ul id=gr".$bar4->kodestruktur." style='display:none;'>";
                                                                                                echo"<div id=main".$bar4->kodestruktur.">";						
                                                                                                while($bar5=mysql_fetch_object($res5))
                                                                                                {
                                                                                                        echo"<li class=mmgr>
                                                                                                             <img title=expand class=arrow src='images/foldc_.png' height=17px   onclick=show_sub('gr".$bar5->kodestruktur."',this);>";
                                                                                                        echo "<b class=elink id='el".$bar5->kodestruktur."'  onclick=\"javascript:activeOrg=this.id;orgVal='".$bar5->induk."';getCurrent('".$bar5->kodestruktur."');setpos('inputorg',event);\" title='Click to change'>".$bar5->kodestruktur.": ".$optNmKar[$bar5->karyawanid]."</b>";
                                                                                                  //++++++++++++++++++++++++++
                                                                                                    $str6="select * from ".$dbname.".sdm_strukturjabatan where induk='".$bar5->kodestruktur."'";
                                                                                                        $res6=mysql_query($str6);
                                                                                                                echo"<ul id=gr".$bar5->kodestruktur." style='display:none;'>";
                                                                                                                echo"<div id=main".$bar5->kodestruktur.">";						
                                                                                                                while($bar6=mysql_fetch_object($res6))
                                                                                                                {
                                                                                                                        echo"<li class=mmgr>
                                                                                                                             <img title=expand class=arrow src='images/foldc_.png' height=17px   onclick=show_sub('gr".$bar6->kodeorganisasi."',this);>";
                                                                                                                        echo "<b class=elink id='el".$bar6->kodestruktur."'  onclick=\"javascript:activeOrg=this.id;orgVal='".$bar6->induk."';getCurrent('".$bar6->kodeorganisasi."');setpos('inputorg',event);\" title='Click to change'>".$bar6->kodestruktur.": ".$optNmKar[$bar6->karyawanid]."</b>";
                                                                                                                  //++++++++++++++++++++++++++
                                                                                                                    $str7="select * from ".$dbname.".sdm_strukturjabatan where induk='".$bar6->kodestruktur."'";
                                                                                                                        $res7=mysql_query($str7);
                                                                                                                                echo"<ul id=gr".$bar6->kodestruktur." style='display:none;'>";
                                                                                                                                echo"<div id=main".$bar6->kodestruktur.">";						
                                                                                                                                while($bar7=mysql_fetch_object($res7))
                                                                                                                                {
                                                                                                                                        echo"<li class=mmgr>
                                                                                                                                             <img title=expand class=arrow src='images/foldc_.png' height=17px   onclick=show_sub('gr".$bar7->kodestruktur."',this);>";
                                                                                                                                        echo "<b class=elink id='el".$bar7->kodestruktur."'  onclick=\"javascript:activeOrg=this.id;orgVal='".$bar7->induk."';getCurrent('".$bar7->kodestruktur."');setpos('inputorg',event);\" title='Click to change'>".$bar7->kodestruktur.": ".$optNmKar[$bar7->karyawanid]."</b>";
                                                                                                                                  //++++++++++++++++++++++++++
                                                                                                                                    $str8="select * from ".$dbname.".sdm_strukturjabatan where induk='".$bar7->kodestruktur."'";
                                                                                                                                        $res8=mysql_query($str8);
                                                                                                                                                echo"<ul id=gr".$bar7->kodestruktur." style='display:none;'>";
                                                                                                                                                echo"<div id=main".$bar7->kodestruktur.">";						
                                                                                                                                                while($bar8=mysql_fetch_object($res8))
                                                                                                                                                {
                                                                                                                                                        echo"<li class=mmgr>
                                                                                                                                                             <img title=expand  src='images/menu/arrow_10.gif'>";
                                                                                                                                                        echo "<b class=elink id='el".$bar8->kodestruktur."'  onclick=\"javascript:activeOrg=this.id;orgVal='".$bar8->induk."';getCurrent('".$bar8->kodestruktur."');setpos('inputorg',event);\" title='Click to change'>".$bar8->kodestruktur.": ".$optNmKar[$bar8->karyawanid]."</b>";
                                                                                                                                                    echo"</li>"; 
                                                                                                                                                }			
                                                                                                                                        echo"</div>";
                                                                                                                                        echo"<li class=mmgr>	
                                                                                                                                        <a id='".$bar7->kodestruktur."_new' class=elink title='Create Child'  onclick=\"javascript:orgVal='".$bar7->kodestruktur."';clos=9;activeOrg='".$bar7->kodestruktur."_new';setpos('inputorg',event);clearForm();\">New Org<a>
                                                                                                                                        </li>";
                                                                                                                                    echo"</ul>";
                                                                                                                                 //============================================	
                                                                                                                                    echo"</li>"; 
                                                                                                                                }			
                                                                                                                        echo"</div>";
                                                                                                                        echo"<li class=mmgr>	
                                                                                                                        <a id='".$bar6->kodestruktur."_new' class=elink title='Create Child'  onclick=\"javascript:orgVal='".$bar6->kodestruktur."';clos=8;activeOrg='".$bar6->kodestruktur."_new';setpos('inputorg',event);clearForm();\">New Org<a>
                                                                                                                        </li>";
                                                                                                                    echo"</ul>";
                                                                                                                 //============================================	
                                                                                                                    echo"</li>"; 
                                                                                                                }			
                                                                                                        echo"</div>";
                                                                                                        echo"<li class=mmgr>	
                                                                                                        <a id='".$bar5->kodestruktur."_new' class=elink title='Create Child'  onclick=\"javascript:orgVal='".$bar5->kodestruktur."';clos=7;activeOrg='".$bar5->kodestruktur."_new';setpos('inputorg',event);clearForm();\">New Org<a>
                                                                                                        </li>";
                                                                                                    echo"</ul>";
                                                                                                 //============================================	
                                                                                                    echo"</li>"; 
                                                                                                }			
                                                                                        echo"</div>";
                                                                                        echo"<li class=mmgr>	
                                                                                        <a id='".$bar4->kodestruktur."_new' class=elink title='Create Child'  onclick=\"javascript:orgVal='".$bar4->kodestruktur."';clos=6;activeOrg='".$bar4->kodestruktur."_new';setpos('inputorg',event);clearForm();\">New Org<a>
                                                                                        </li>";
                                                                                    echo"</ul>";
                                                                                 //============================================	
                                                                                    echo"</li>"; 
                                                                                }			
                                                                        echo"</div>";
                                                                        echo"<li class=mmgr>	
                                                                        <img id='".$bar3->kodestruktur."_new' class=elink title='Create Child'   src='images/plus.png'".
                                                                        "style='width:10px;height:10px;cursor:pointer' onclick=\"javascript:orgVal='".$bar3->kodestruktur."';clos=5;activeOrg='".$bar3->kodestruktur."_new';setpos('inputorg',event);clearForm();\">
                                                                        </li>";
                                                                    echo"</ul>";
#									<a id='".$bar3->kodeorganisasi."_new' class=elink title='Create Child'  onclick=\"javascript:orgVal='".$bar3->kodeorganisasi."';clos=5;activeOrg='".$bar3->kodeorganisasi."_new';setpos('inputorg',event);clearForm();\">New Org<a>
                                                                 //============================================	
                                                                    echo"</li>"; 
                                                                }			
                                                        echo"</div>";
                                                        echo"<li class=mmgr>	
                                                        <img id='".$bar2->kodestruktur."_new' class=elink title='Create Child'  src='images/plus.png'".
                                                        "style='width:12px;height:12px;cursor:pointer' onclick=\"javascript:orgVal='".$bar2->kodestruktur."';clos=4;activeOrg='".$bar2->kodestruktur."_new';setpos('inputorg',event);clearForm();\">
                                                        </li>";
                                                    echo"</ul>";
#							<a id='".$bar2->kodeorganisasi."_new' class=elink title='Create Child'  onclick=\"javascript:orgVal='".$bar2->kodeorganisasi."';clos=4;activeOrg='".$bar2->kodeorganisasi."_new';setpos('inputorg',event);clearForm();\">New Org<a>
                                                 //============================================	
                                                    echo"</li>"; 
                                                }			
                                        echo"</div>";
                                        echo"<li class=mmgr>	
                                        <img id='".$bar1->kodestruktur."_new' class=elink title='Create Child' src='images/plus.png'".
                                        "style='width:14px;height:14px;cursor:pointer' onclick=\"javascript:orgVal='".$bar1->kodestruktur."';clos=3;activeOrg='".$bar1->kodestruktur."_new';setpos('inputorg',event);clearForm();\">
                                        </li>";
                                    echo"</ul>";
#					<a id='".$bar1->kodeorganisasi."_new' class=elink title='Create Child'  onclick=\"javascript:orgVal='".$bar1->kodeorganisasi."';clos=3;activeOrg='".$bar1->kodeorganisasi."_new';setpos('inputorg',event);clearForm();\">New Org<a>
                                 //============================================										  

                                    echo"</li>"; 
                                }			
                        echo"</div>";
                        echo"<li class=mmgr>	
                        <img id='".$bar->kodestruktur."_new' class=elink title='Create Child' src='images/plus.png'".
                        "style='width:16px;height:16px;cursor:pointer' onclick=\"javascript:orgVal='".$bar->kodestruktur."';clos=2;activeOrg='".$bar->kodestruktur."_new';setpos('inputorg',event);clearForm();\">
                        </li>";
                    echo"</ul>";
#			<a id='".$bar->kodeorganisasi."_new' class=elink title='Create Child'  onclick=\"javascript:orgVal='".$bar->kodeorganisasi."';clos=2;activeOrg='".$bar->kodeorganisasi."_new';setpos('inputorg',event);clearForm();\">New<a>
#$headControl = "<img id='addHeaderId' title='Tambah Header' src='images/plus.png'".
#  "style='width:20px;height:20px;cursor:pointer' onclick='addHeader(event)' />&nbsp;";
                 //============================================										  
                  echo"</li>";
           }
   }
   else//if head office not yet exist
   {
                echo"<li class=mmgr>	
                <a id=HQ class=elink title='Create New HQ'  onclick=\"javascript:orgVal='';clos=1;activeOrg='HQ';setpos('inputorg',event);clearForm();\">New Entity<a>
                </li>";
   }
   echo"</ul>";
echo "</div>";

echo CLOSE_THEME();
CLOSE_BOX();
$optkary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sdata="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan where tipekaryawan=0 and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") order by namakaryawan asc";
$qdata=mysql_query($sdata) or die(mysql_error($conn));
while($rdata=  mysql_fetch_assoc($qdata)){
    $optkary.="<option value='".$rdata['karyawanid']."'>".$rdata['namakaryawan']."</option>";
}
$optjbtn.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sjbtn="select distinct * from ".$dbname.".sdm_5jabatan order by namajabatan asc";
$qjbnt=mysql_query($sjbtn) or die(mysql_error($conn));
while($rjbtn=  mysql_fetch_assoc($qjbnt)){
    $optjbtn.="<option value='".$rjbtn['kodejabatan']."'>".$rjbtn['namajabatan']."</option>";
}
 echo"<div id=inputorg style='display:none;position:absolute'>".OPEN_THEME($_SESSION['lang']['orgentry'])."
                <table>
                <tr>
                   <td>".$_SESSION['lang']['kodestruktur']."</td><td><input type=text class=myinputtext id=kdStruktur maxlength=10 style='width:150px;' onkeypress=\"return charAndNum(event);\"></td>
                </tr>
                <tr>
                   <td>".$_SESSION['lang']['namakaryawan']."</td>
                   <td><select id=karyId style='width:150px;'>".$optkary."</select></td>
                </tr>	
                <tr>
                   <td>".$_SESSION['lang']['kodejabatan']."</td>
                   <td><select id=kdJbtn style='width:150px;'>".$optjbtn."</select></td>
                </tr>
                <tr>
                   <td>".$_SESSION['lang']['email']."</td>
                   <td><input type=text class=myinputtext id=maildt style='width:150px;' onkeypress=\"return tanpa_kutip(event);\"></td>
                </tr>
                <tr>
                   <td>".$_SESSION['lang']['detail']."</td><td><select id=detailDt><option value=1>".$_SESSION['lang']['yes']."</option><option value=0>".$_SESSION['lang']['no']."</option></select></td>
                </tr>		
                <tr>
                   <td>".$_SESSION['lang']['pt']."</td><td><select id=alokasi style='width:150px;'>".$optalokasi."</select></td>
                </tr>

                </table>
                <input type=button class=mybutton value='".$_SESSION['lang']['save']."' onclick=saveOrg()>
                <input type=button class=mybutton value='".$_SESSION['lang']['close']."' onclick=\"hideById('inputorg');clearForm();\">
                ".CLOSE_THEME()."
                </div>";
echo close_body();
?>
