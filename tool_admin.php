<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/tool_admin.js'></script>
<?php
$arr="##listTransaksi##pilUn_1##unitId##method";
include('master_mainMenu.php');
OPEN_BOX();
$opt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$pil=array("1"=>$_SESSION['lang']['kasbank'],"3"=>$_SESSION['lang']['kontrak'],"4"=>$_SESSION['lang']['tbm']."/".$_SESSION['lang']['tm']."/".$_SESSION['lang']['panen'],"5"=>$_SESSION['lang']['traksi'],"6"=>$_SESSION['lang']['suratPengantarBuah']);
foreach($pil as $dtl=>$vw)
{
    $opt.="<option value='".$dtl."'>".$vw."</option>";
}
$optUnit2=$optUnit=$optPrd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPeriode="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error($conn));
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPrd.="<option value='".$rPeriode['periode']."'>".$rPeriode['periode']."</option>";
}
$sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)=4 order by namaorganisasi asc";
$qUnit=mysql_query($sUnit) or die(mysql_error($conn));
while($rUnit=mysql_fetch_assoc($qUnit))
{
    $optUnit.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['kodeorganisasi']." - ".$rUnit['namaorganisasi']."</option>";
    if(substr($rUnit['kodeorganisasi'],3,1)=='E')
    {
    $optUnit2.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['kodeorganisasi']." - ".$rUnit['namaorganisasi']."</option>";
    }
}
$frm[0]="<table><tr><td valign=top><fieldset style=width:350px;>
     <legend>Unposting</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['notransaksi']."</td>
	   <td><textarea id=listTransaksi name=listTransaksi></textarea></td>
	 </tr>
     
         <tr>
	   <td hidden>".$_SESSION['lang']['unit']."</td>
	   <td hidden>
           <select id=unitId style=width:150px>".$optUnit."</select>
           </td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['jenis']."</td>
	   <td>
           <select id=pilUn_1 style=width:150px onchange=getInfo('".$_SESSION['language']."')>".$opt."</select>
           </td>
	 </tr>
	 </table>
	 <button class=mybutton id=tmblDt onclick=saveFranco('tool_slave_admin','".$arr."')>".$_SESSION['lang']['proses']."</button>
     </fieldset><input type=hidden id=method value=getData />";

if($_SESSION['empl']['bagian']=="IT"){
    $frm[0].="<fieldset style=width:350px;float:left;>
     <legend>".$_SESSION['lang']['ganti']." ".$_SESSION['lang']['blok']."</legend>
	 <table>
	 <tr>
                       <td>".$_SESSION['lang']['kebun']."</td>
                       <td><select id=kebuncoy style=width:150px onchange=getBlok(this.options[this.selectedIndex].value)>".$optUnit2."</select></td>
	  </tr>
                      <tr>
                       <td>".$_SESSION['lang']['bloklm']."</td>
	   <td><select id=bloklama style=width:150px onchange=updateBlokBaru(this.options[this.selectedIndex].value)><option value=''></option></select></td></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['blokbr']."</td>
	   <td><input type='text' class='myinputtext' id='blokbaru' size='10' maxlength='10' style=\"width:100px;\"/></td>
	 </tr>
	 </table>
	 <button class=mybutton id=tombolganti onclick=gantiblok('tool_slave_admin')>".$_SESSION['lang']['proses']."</button>
     </fieldset><input type=hidden id=method2 value=blokganti />
    ";

    $str="select  kodeorganisasi from ".$dbname.".organisasi where tipe not like '%GUDANG%'  and length(kodeorganisasi)=4 order by kodeorganisasi";
    $res=mysql_query($str);
    $optOpenClose1="<option value=''></option>";
    while($bar=mysql_fetch_object($res))
    {
        $optOpenClose1.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi."</option>";
    }

    $frm[0].="<fieldset><legend>Open/Close Periode For Accounting</legend>
                 <select id=openclose><option value='OPEN'>Open</option><option value='CLOSE'>Close</option></select>
                 Unit:<select id=unitopenclose onchange=getPeriode(this.options[this.selectedIndex].value)>".$optOpenClose1."</select>
                 From<span id=periodeopenclose></span>
                 <button class=mybutton onclick=prosesDong() id=buttonDong style='display:none;'>Proses!</button>
             </fieldset>";
    $optj="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $optjen=array("1"=>$_SESSION['lang']['penerimaanbarang'],"3"=>$_SESSION['lang']['terimamutasi'],"5"=>$_SESSION['lang']['pengeluaranbarang'],"7"=>$_SESSION['lang']['mutasi']);
    foreach($optjen as $dtl=>$vw)
    {
        $optj.="<option value='".$dtl."'>".$vw."</option>";
    }
    $arr2="##listTransaksi2##pilUn_5##method3";
    if($_SESSION['language']=='ID'){
        $aks="getInfo2('ID')";
    }
    else
    {
        $aks='';
    }

    $frm[0].="<fieldset><legend>Unposting Warehouse</legend>
             <table>
             <tr>
               <td>".$_SESSION['lang']['notransaksi']."</td>
               <td><textarea id=listTransaksi2 name=listTransaksi2></textarea></td>
             </tr>

             <tr>
               <td>".$_SESSION['lang']['jenis']."</td>
               <td>
               <select id=pilUn_5 style=width:150px onchange=".$aks.">".$optj."</select>
               </td>
             </tr>
             </table>
             <button class=mybutton id=tmblDt onclick=saveFranco2('tool_slave_admin','".$arr2."')>".$_SESSION['lang']['proses']."</button>
         </fieldset><input type=hidden id=method3 value=getData2 />";

    $frm[0].="</td><td valign=top><fieldset style=width:350px;><legend>".$_SESSION['lang']['info']."</legend><div id=infoTip style=align:justify><script>getInfo('".$_SESSION['language']."')</script>";
    $frm[0].="</div></fieldset></td></tr></table>";


    CLOSE_BOX();

} else {
    if($_SESSION['empl']['bagian']=="ACC" or $_SESSION['empl']['bagian']=="FIN" or $_SESSION['empl']['bagian']=="FAT"){
        $frm[0].="</td><td valign=top><fieldset style=width:350px;><legend>".$_SESSION['lang']['info']."</legend><div id=infoTip style=align:justify>";
        $frm[0].="</div></fieldset></td></tr></table>";
    }
}
$frm[0].="<div id=listData style=display:none>";
OPEN_BOX();
$frm[0].="<fieldset style=width:450px;><legend>".$_SESSION['lang']['list']."</legend><div id=container>";
	
$frm[0].="</div></fieldset>";

#===========================================================================================

$frm[1]='';
if($_SESSION['empl']['bagian']=="IT"){
    
    $frm[1].="<fieldset><legend>Choose data type:</legend>
                      <span>Data type:<select id=udatatype onclick=getFormUplaod(this.options[this.selectedIndex].value)>
                                                    <option value=''>Please choose..</option>
                                                    <option value='ACCBAL'>ACCBAL:Previous Accounting Balance</option>
                                                    <option value='JOURNAL'>JOURNAL:Journal regarding transaction history</option>
                                                    <option value='JOURNALMEMO'>JOURNALMEMO:Journal memorial regarding amortization or any</option>
                                                    <option value='INV'>INV:Previous inventory balance</option>
                                                    <option value='PO'>PO:Outstanding PO</option>
                                                    </select>
                     </fieldset>                               
                    <fieldset><legend>Form</legend>
                     <div id=uForm style='display:none'>
                     	
                                         <span id=sample></span><br><br>
                                         (File type support only CSV).
                                        <form id=frm name=frm enctype=multipart/form-data method=post action=tool_slave_uploadData.php target=frame>	
                                        <input type=hidden name=jenisdata id=jenisdata value=''>
                                        <input type=hidden name=MAX_FILE_SIZE value=1024000>
                                        File:<input name=filex type=file id=filex size=25 class=mybutton>
                                        Field separated by<select name=pemisah>
                                        <option value=','>, (comma)</option>
                                        <option value=';'>; (semicolon)</option>
                                        <option value=':'>: (two dots)</option>
                                        <option value='/'>/ (devider)</option>
                                        </select>
                                        <input type=button class=mybutton  value=".$_SESSION['lang']['save']." title='Submit this File' onclick=submitFile()>
                                    </form>
 
                                    <iframe frameborder=0 width=800px height=200px name=frame>
                                    </iframe>
                     </div>
                    </fieldset>";
}
else{
    if($_SESSION['empl']['bagian']=="ACC" or $_SESSION['empl']['bagian']=="FIN" or $_SESSION['empl']['bagian']=="FAT"){
        $frm[1].="<fieldset><legend>Choose data type:</legend>
                      <span>Data type:<select id=udatatype onclick=getFormUplaod(this.options[this.selectedIndex].value)>
                                                    <option value=''>Please choose..</option>
                                                    <option value='JOURNALMEMO'>JOURNALMEMO:Journal memorial regarding amortization or any</option>
                                                    </select>
                     </fieldset>                               
                    <fieldset><legend>Form</legend>
                     <div id=uForm style='display:none'>
                     	
                                         <span id=sample></span><br><br>
                                         (File type support only CSV).
                                        <form id=frm name=frm enctype=multipart/form-data method=post action=tool_slave_uploadData.php target=frame>	
                                        <input type=hidden name=jenisdata id=jenisdata value=''>
                                        <input type=hidden name=MAX_FILE_SIZE value=1024000>
                                        File:<input name=filex type=file id=filex size=25 class=mybutton>
                                        Field separated by<select name=pemisah>
                                        <option value=','>, (comma)</option>
                                        <option value=';'>; (semicolon)</option>
                                        <option value=':'>: (two dots)</option>
                                        <option value='/'>/ (devider)</option>
                                        </select>
                                        <input type=button class=mybutton  value=".$_SESSION['lang']['save']." title='Submit this File' onclick=submitFile()>
                                    </form>
 
                                    <iframe frameborder=0 width=800px height=200px name=frame>
                                    </iframe>
                     </div>
                    </fieldset>";
    } else {
        $frm[1]='Not authorized'; 
    }
}
#============================================================================================
$hfrm[0]='Unposting';
$hfrm[1]='Upload';
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,900);

CLOSE_BOX();
echo"</div>";
echo close_body();
?>