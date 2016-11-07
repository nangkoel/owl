<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
$frm[0]='';
$frm[1]='';
$frm[2]='';
?>
<script language=javascript src=js/zMaster.js></script>
<script language=javascript src=js/zSearch.js></script>
<script language=javascript src=js/zTools.js></script>
<script>
   
    /* Function zPreview
 * Fungsi untuk preview sebuah report
 * I : target file, parameter yang akan dilempar, id container
 * O : report dalam bentuk HTML
 */
function zPreview(fileTarget,passParam,idCont) {
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
  // alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var res = document.getElementById(idCont);
                    res.innerHTML = con.responseText;
                    document.getElementById('detailData').style.display='none';
                    document.getElementById('awal').style.display='block';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php?proses=preview', param, respon);

}
function zExcel(ev,tujuan,passParam)
{
	judul='Report Excel';
	//alert(param);
	var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
	param+='&proses=excel';
	//alert(param);
	printFile(param,tujuan,judul,ev)
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param; 
   width='700';
   height='250';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
}

/* Posting Data
 */
function postingData(numRow) {
//    alert("masuk sini");
//    return;
    var notrans = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    var param = "notransaksi="+notrans;

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                   // alert('Posting Berhasil');
                   // javascript:location.reload(true);
                   document.getElementById('rowDt_'+numRow).style.display='none';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    if(confirm('Akan dilakukan posting untuk transaksi '+notrans+
        '\nData tidak dapat diubah setelah ini. Anda yakin?')) {
        post_response_text('kebun_slave_operasional_posting.php', param, respon);
    }
}
function detailData(numRow,ev,tipe)
{
    var notransaksi = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    param = "proses=html&tipe="+tipe+"&notransaksi="+notransaksi;
        title="Data Detail";
        showDialog1(title,"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='kebun_slave_operasional_print_detail.php?"+param+"'></iframe>",'800','400',ev);
        var dialog = document.getElementById('dynamic1');
        dialog.style.top = '50px';
        dialog.style.left = '15%';
}
function getPeriode()
{
    var kdOrg = document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
    var param = "kdOrg="+kdOrg+'&proses=getPeriode';
    post_response_text('kebun_slave_3updategajibjr.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                   // alert('Posting Berhasil');
                   // javascript:location.reload(true);
                   document.getElementById('thnId').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }


        
    
}

function postingDat(maxRow)
{
//
	if(confirm('Anda Yakin Ingin Mengupdate Semua Data ..?'))
	{
		   loopClosingFisik(1,maxRow);
		   // lockForm();
	}
	else
	{
		document.getElementById('revTmbl').disabled=false;
		return;
	}
}

function loopClosingFisik(currRow,maxRow){
        notrans = document.getElementById('notransaksi_'+currRow).innerHTML;
        kdorg = document.getElementById('kodeblok_'+currRow).innerHTML;
        tgl = document.getElementById('tanggal_'+currRow).innerHTML;
        nik = document.getElementById('karyawanid_'+currRow).value;
        upah = document.getElementById('updUpah_'+currRow).innerHTML;
        insentif = document.getElementById('updInsentif_'+currRow).innerHTML;
        denda = document.getElementById('updDenda_'+currRow).innerHTML;
        hasilKg = document.getElementById('hasilKg_'+currRow).innerHTML;
        bjrAkt = document.getElementById('brjAktual_'+currRow).innerHTML;
        param = "notransaksi="+notrans+'&proses=updateData';
        param+="&nik="+nik+"&tanggal="+tgl+"&upah="+upah+"&insentif="+insentif;
        param+="&denda="+denda+"&hasilKg="+hasilKg+"&brjAktual="+bjrAkt+"&kodeorg="+kdorg;
        post_response_text('kebun_slave_3updategajibjr.php', param, respon);
	document.getElementById('rowDt_'+currRow).style.backgroundColor='orange';
       
	//lockScreen('wait');
	function respon(){
		if (con.readyState == 4) {

			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('rowDt_'+currRow).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					//alert(con.responseText);
					//return;
                                        
                                        err=con.responseText;
                                        if(err=="1"){
                                            document.getElementById('rowDt_'+currRow).style.backgroundColor='red';
                                        }else{
                                            document.getElementById('rowDt_'+currRow).style.backgroundColor='green';
                                        }
                                        currRow+=1;
					if(currRow>maxRow)
					{
						document.getElementById('revTmbl').disabled=false;
						tutupProses('simpan');
					}
					else
					{
						param='';
                                                loopClosingFisik(currRow,maxRow);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
				unlockScreen();
			}
		}
	}

}
function tutupProses(x)
{
	period=document.getElementById('revTmbl');
	if(period.disabled!=true)
	{
		if (x == 'simpan') {
			//unlockScreen();
			alert("Data Telah Terupdate");
			//unlockForm();
			document.getElementById('printContainer').innerHTML='';
		}
		else
		{
			unlockScreen();
		}
	}
}


function postingDat2(maxRow)
{
	if(confirm('Anda Yakin Ingin Mengupdate Semua Data ..?')){
            strUrl2 = '';
		 for(i=1;i<=maxRow;i++){
                    try{

                            if(strUrl2 != ''){					
                                //+'&kdbrg[]='+encodeURIComponent(trim(document.getElementById('rkdbrg_'+i).value))
                                    strUrl2 +='&notrans[]='+trim(document.getElementById('notransaksi2_'+i).innerHTML)
                                    +'&kdorg[]='+encodeURIComponent(trim(document.getElementById('kodeblok2_'+i).innerHTML))
                                    +'&tgl[]='+encodeURIComponent(trim(document.getElementById('tanggal2_'+i).innerHTML))
                                    +'&nik[]='+document.getElementById('karyawanid2_'+i).value
                                    +'&hasilKg2[]='+encodeURIComponent(trim(document.getElementById('hasilKg2_'+i).value))
                                    +'&updUpah[]='+encodeURIComponent(trim(document.getElementById('updUpah2_'+i).value))
                                    +'&updHk[]='+encodeURIComponent(trim(document.getElementById('hkData_'+i).value));
                            }
                            else{
                                    strUrl2 +='&notrans[]='+trim(document.getElementById('notransaksi2_'+i).innerHTML)
                                    +'&kdorg[]='+encodeURIComponent(trim(document.getElementById('kodeblok2_'+i).innerHTML))
                                    +'&tgl[]='+encodeURIComponent(trim(document.getElementById('tanggal2_'+i).innerHTML))
                                    +'&nik[]='+document.getElementById('karyawanid2_'+i).value
                                    +'&hasilKg2[]='+encodeURIComponent(trim(document.getElementById('hasilKg2_'+i).value))
                                    +'&updUpah[]='+encodeURIComponent(trim(document.getElementById('updUpah2_'+i).value))
                                    +'&updHk[]='+encodeURIComponent(trim(document.getElementById('hkData_'+i).value));

                           }
                    }
                        catch(e){}

                }
	}
        param='proses=updateData';
        param+=strUrl2;
        post_response_text('kebun_slave_3updategajibjr2.php', param, respon);
//        alert(param);
//        return;
	//lockScreen('wait');
	function respon(){
		if (con.readyState == 4) {

			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('rowDt2_'+currRow).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					//alert(con.responseText);
					//return;
                                        alert("Done")
                                        document.getElementById('printContainer2').innerHTML="";
                                        document.getElementById('kdKegiatan').focus();
                                       
				}
			}
			else {
				busy_off();
				error_catch(con.status);
				unlockScreen();
			}
		}
	}
}





function postingDat3(maxRow)
{
	if(confirm('Anda Yakin Ingin Mengupdate Semua Data ..?')){
            strUrl2 = '';
		 for(i=1;i<=maxRow;i++){
                    try{

                            if(strUrl2 != ''){					
                                //+'&kdbrg[]='+encodeURIComponent(trim(document.getElementById('rkdbrg_'+i).value))
                                    strUrl2 +='&notrans[]='+trim(document.getElementById('notransaksidt_'+i).innerHTML)
                                    +'&tgl[]='+encodeURIComponent(trim(document.getElementById('tgldt_'+i).innerHTML))
                                    +'&nik[]='+document.getElementById('nik_'+i).value
                                    +'&updUpah[]='+encodeURIComponent(trim(document.getElementById('premiApdt_'+i).value));
                            }
                            else{
                                    strUrl2 +='&notrans[]='+trim(document.getElementById('notransaksidt_'+i).innerHTML)
                                    +'&tgl[]='+encodeURIComponent(trim(document.getElementById('tgldt_'+i).innerHTML))
                                    +'&nik[]='+document.getElementById('nik_'+i).value
                                    +'&updUpah[]='+encodeURIComponent(trim(document.getElementById('premiApdt_'+i).value));

                           }
                    }
                        catch(e){}

                }
	}
        param='proses=updateData';
        param+=strUrl2;
        post_response_text('kebun_slave_3updategajibjr3.php', param, respon);
//        alert(param);
//        return;
	//lockScreen('wait');
	function respon(){
		if (con.readyState == 4) {

			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('rowDt2_'+currRow).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					//alert(con.responseText);
					//return;
                                        alert("Done")
                                        document.getElementById('printContainer3').innerHTML="";
                                       
				}
			}
			else {
				busy_off();
				error_catch(con.status);
				unlockScreen();
			}
		}
	}
}
function getKegiatan(){
	tgl1=document.getElementById('tanggal1b').value;
	tgl2=document.getElementById('tanggal2b').value;
	kdorg=document.getElementById('kdOrgb');
	kdorg=kdorg.options[kdorg.selectedIndex].value;
	if(tgl1==''||tgl2==''){
		alert("Tanggal Tidak Boleh Kosong");
		return;
	}
	param='proses=getKegiatan'+'&tanggal1b='+tgl1+'&tanggal2b='+tgl2;
	param+='&kdOrgb='+kdorg;
    post_response_text('kebun_slave_3updategajibjr2.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                   document.getElementById('kdKegiatan').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
</script>
<?php
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$sPeriodeCari="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_aktifitas
//                 where kodeorg='".$_SESSION['empl']['lokasitugas']."' and jurnal=0 order by substr(tanggal,1,7) desc";
////echo $sPeriodeCari;
//$qPeriodeCari=mysql_query($sPeriodeCari) or die(mysql_error());
//while($rPeriodeCari=mysql_fetch_assoc($qPeriodeCari))
//{
//   $optPeriode.="<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode']."</option>";
//}
$optOrg2=$optOrg=$optPeriode;
if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    $sOrg="select distinct substr(kodeorganisasi,1,4) as kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
      tipe='KEBUN' and induk='".$_SESSION['org']['kodeorganisasi']."' order by namaorganisasi asc";
    $sOrg2="select distinct substr(kodeorganisasi,1,4) as kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
            tipe='TRAKSI'  order by namaorganisasi asc";
}else{
    $sOrg="select distinct substr(kodeorganisasi,1,4) as kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
      tipe='KEBUN' and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi asc";
	  if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
         $sOrg2="select distinct substr(kodeorganisasi,1,4) as kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
			tipe='TRAKSI' and left(kodeorganisasi,4) in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by namaorganisasi asc";
	  }else{
		$sOrg2="select distinct substr(kodeorganisasi,1,4) as kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
               tipe='TRAKSI' and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi asc";
	
	  }
}
    
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";
}
$qOrg2=mysql_query($sOrg2) or die(mysql_error($conn));
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
    $optOrg2.="<option value='".$rOrg2['kodeorganisasi']."'>".$rOrg2['namaorganisasi']."</option>";
}
$optTrak="<option value=''>".$_SESSION['lang']['all']."</option>";
$trk=array("0"=>"TB","1"=>"BBT","2"=>"TBM","3"=>"TM","4"=>"PNN");
foreach($trk as $dtrk =>$lstr)
{
    $optTrak.="<option value='".$lstr."'>".$lstr."</option>";
}

$optTipe="<option value=''>All</option>";
$optTipe.="<option value='/BBT/'>BBT</option>";
$optTipe.="<option value='/TB/'>TB</option>";
$optTipe.="<option value='/TBM/'>TBM</option>";
$optTipe.="<option value='/TM/'>TM</option>";
$optTipe.="<option value='/PNN/'>PNN</option>";

$arr="##kdOrg##tanggal1##tanggal2";
$arr2="##kdOrgb##tanggal1b##tanggal2b##kdKegiatan";
$arr3="##kdOrgT##tanggal1T##tanggal2T##kdKegiatanT";


$frm[0].="<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style=\"float: left;\">
<legend><b>Update Data Inputan Panen</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['kodeorg']."</label></td><td><select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px\">".$optOrg."</select></td></tr>
    <td><label>".$_SESSION['lang']['tanggal']."</label></td>
    <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal1\" name=\"tanggal1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['tanggal']." 2</label></td>
    <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal2\" name=\"tanggal2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>
</tr>
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\"><button onclick=\"zPreview('kebun_slave_3updategajibjr','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button></td></tr>
</table>
</fieldset>
</div>


<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id=awal>
    <div id='printContainer' style='overflow:auto;height:350px;max-width:1220px;'>

    </div>
</div>
<div id=detailData style=display:none>
<div id=isiData>
</div>
</div>
</fieldset>";
$optKegiatanNm=  makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optKegiatan.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if($_SESSION['empl']['tipelokasitugas']!='HOLDING'){
    $sKegiatan="select distinct kodekegiatan,regional from ".$dbname.".kebun_5psatuan";
}else{
    $sKegiatan="select distinct kodekegiatan,regional from ".$dbname.".kebun_5psatuan where regional='".$_SESSION['empl']['regional']."'";
}
$qKegiatan=  mysql_query($sKegiatan) or die(mysql_error($conn));
while($rKegiatan=  mysql_fetch_assoc($qKegiatan)){
    if($_SESSION['empl']['tipelokasitugas']!='HOLDING'){
        $optKegiatan.="<option value='".$rKegiatan['kodekegiatan']."'>".$rKegiatan['kodekegiatan']."-".$optKegiatanNm[$rKegiatan['kodekegiatan']]."</option>";
    }else{
        $optKegiatan.="<option value='".$rKegiatan['kodekegiatan']."'>".$rKegiatan['kodekegiatan']."-".$optKegiatanNm[$rKegiatan['kodekegiatan']]."-".$rKegiatan['regional']."</option>";
    }
}

$frm[1].="<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style=\"float: left;\">
<legend><b>Update Data Inputan Perawatan Menggunakan JJG</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr>
    <td><label>".$_SESSION['lang']['tanggal']." 1</label></td>
    <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal1b\" name=\"tanggal1b\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['tanggal']." 2</label></td>
    <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal2b\" name=\"tanggal2b\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>
</tr>
<tr><td><label>".$_SESSION['lang']['kodeorg']."</label></td><td><select id=\"kdOrgb\" name=\"kdOrgb\" style=\"width:150px\" onchange=getKegiatan()>".$optOrg."</select></td>

<tr>
    <td><label>".$_SESSION['lang']['kegiatan']."</label></td>
    <td><select id=kdKegiatan style=width:150px>".$optKegiatan."</select></td>
</tr>
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\"><button onclick=\"zPreview('kebun_slave_3updategajibjr2','".$arr2."','printContainer2')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button></td></tr>
</table>
</fieldset>
</div>


<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id=awal>
    <div id='printContainer2' style='overflow:auto;height:350px;max-width:1220px;'>

    </div>
</div>
<div id=detailData style=display:none>
<div id=isiData>
</div>
</div>
</fieldset>";

$optKegiatan2.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    $sKegiatan="select distinct kodekegiatan,regional,namakegiatan from ".$dbname.".vhc_kegiatan ";
}else{
        $sKegiatan="select distinct kodekegiatan,regional,namakegiatan from ".$dbname.".vhc_kegiatan "
             . "where regional='".$_SESSION['empl']['regional']."'";
}


$qKegiatan=  mysql_query($sKegiatan) or die(mysql_error($conn));
while($rKegiatan=  mysql_fetch_assoc($qKegiatan)){
        $optKegiatan2.="<option value='".$rKegiatan['kodekegiatan']."'>".$rKegiatan['kodekegiatan']."-".$rKegiatan['namakegiatan']."-".$rKegiatan['regional']."</option>";
}
$frm[2].="<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style=\"float: left;\">
<legend><b>Update Traksi JJG</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['kodeorg']."</label></td><td><select id=\"kdOrgT\" name=\"kdOrgT\" style=\"width:150px\">".$optOrg2."</select></td></tr>
    <td><label>".$_SESSION['lang']['tanggal']."</label></td>
    <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal1T\" name=\"tanggal1T\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['tanggal']." 2</label></td>
    <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal2T\" name=\"tanggal2T\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /><input type=hidden id=kdKegiatanT value='' /></td>
</tr>
<!--<tr>
    <td><label>".$_SESSION['lang']['kegiatan']."</label></td>
    <td><select id=kdKegiatanT style=width:150px>".$optKegiatan2."</select></td>
</tr>-->
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\"><button onclick=\"zPreview('kebun_slave_3updategajibjr3','".$arr3."','printContainer3')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button></td></tr>
</table>
</fieldset>
</div>


<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id=awal>
    <div id='printContainer3' style='overflow:auto;height:350px;max-width:1220px;'>

    </div>
</div>
<div id=detailData style=display:none>
<div id=isiData>
</div>
</div>
</fieldset>";
//========================
$hfrm[0]="Update Panen";
$hfrm[1]="Update Perawatan";
$hfrm[2]="Update Traksi JJG";
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,170,1050);
//===============================================

CLOSE_BOX();
echo close_body();
?>