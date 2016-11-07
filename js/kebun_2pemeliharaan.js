// JavaScript Document
function cariNoGudang(title,ev)
{
                 // kosongkan();
                  //setSloc('simpan');
        content= "<div>";
        content+="<fieldset>Nama Kegiatan:<input type=text id=noGudang class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=goCariGudang()>Go</button> </fieldset>";
        content+="<div id=containercari style=\"height:250px;width:470px;overflow:scroll;\"></div></div>";
   //display window
       title=title+' kegiatan:';
         width='500';
         height='300';
         showDialog1(title,content,width,height,ev);	
}



function goCariGudang()
{

                noGudang=trim(document.getElementById('noGudang').value);
                if(noGudang.length<4)
                   alert('Text too short');
                else
                {   
				//param='proses=goCariGudang'+'&noGudang='+noGudang;
               
               param='&noGudang='+noGudang;
               tujuan='kebun_slave_2pemeliharaan';
	post_response_text(tujuan+'.php?proses=goCariGudang', param, respog);
               
        
             		
            }
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('containercari').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}


function goPickGudang(noGudang)
{
        document.getElementById('kegiatan').value=noGudang;
		closeDialog();
}








function viewDetail1(kodekegiatan,kodeorg,bulan,ev)
{ 
   param='kodekegiatan='+kodekegiatan+'&kodeorg='+kodeorg+'&bulan='+bulan;
   
   tujuan='kebun_slave_2pemeliharaan1_detail.php'+"?"+param;  
   width='800';
   height='400';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Pekerjaan '+kodekegiatan+' '+kodeorg+' '+bulan,content,width,height,ev); 
	
}

function getAfd()
{
	kdOrg=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
//	kdUnit=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
	param='&kdOrg='+kdOrg;
	tujuan='kebun_slave_2pemeliharaan';
	post_response_text(tujuan+'.php?proses=getAfdAll', param, respon);
//	alert(tujuan+'.php?proses=getAfd'+param);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
						document.getElementById('kdAfd').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
}

function getAfd1()
{
	kdOrg=document.getElementById('kdOrg1').options[document.getElementById('kdOrg1').selectedIndex].value;
//	kdUnit=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
	param='&kdOrg='+kdOrg;
	tujuan='kebun_slave_2pemeliharaan';
	post_response_text(tujuan+'.php?proses=getAfdAll', param, respon);
//	alert(tujuan+'.php?proses=getAfd'+param);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
						document.getElementById('kdAfd1').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
}

function Clear0()
{
	document.getElementById('tgl1').value='';
	document.getElementById('tgl2').value='';
//	document.getElementById('tgl1').disabled=false;
//	document.getElementById('tgl2').disabled=false;
	document.getElementById('kdOrg').value=''; //getAfd();
	document.getElementById('kdAfd').innerHTML='';
	document.getElementById('kdAfd').value='';
	document.getElementById('kegiatan').value='';
	document.getElementById('printContainer').innerHTML='';
}

function Clear1()
{
	document.getElementById('tahun1').value='';
//	document.getElementById('tgl1').disabled=false;
//	document.getElementById('tgl2').disabled=false;
	document.getElementById('kdOrg1').value=''; //getAfd1();
	document.getElementById('kdAfd1').innerHTML='';
	document.getElementById('kdAfd1').value='';
	document.getElementById('kegiatan1').value='';
	document.getElementById('printContainer1').innerHTML='';
}

function viewDetail(notransaksi,ev)
{
   param='notransaksi='+notransaksi;
   tujuan='kebun_slave_2pemeliharaandetail.php'+"?"+param;  
   width='400';
   height='200';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Pemeliharaan '+notransaksi,content,width,height,ev); 
	
}

function viewDetail2(notransaksi,kdOrg,tanggal,ev)
{
   param='notransaksi='+notransaksi+'&kdOrg='+kdOrg+'&tanggal='+tanggal;
   tujuan='kebun_slave_2pemeliharaanbarang.php'+"?"+param;  
   width='400';
   height='200';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Pemeliharaan '+notransaksi,content,width,height,ev); 
	
}

function detailExcel(ev,tujuan)
{
    width='200';
   height='100';
//   alert(tujuan);
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Pemeliharaan Excel',content,width,height,ev); 
}