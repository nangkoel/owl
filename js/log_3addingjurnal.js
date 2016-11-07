/**
* @author repindra.ginting
*/
function getPrd(){
	prd=document.getElementById('kdOrg');
	prd=prd.options[prd.selectedIndex].value;
	param='kdOrg='+prd+'&proses=getPeriode';
	post_response_text('log_slave_3addingjurnal.php', param, respon);
	function respon(){
		if (con.readyState == 4) {

			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
                    document.getElementById('periodeGdng').innerHTML=con.responseText;
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
function getTptrk(){
	prd=document.getElementById('kdOrg');
	prd=prd.options[prd.selectedIndex].value;
	prde=document.getElementById('periodeGdng');
	prde=prde.options[prde.selectedIndex].value;
	param='kdOrg='+prd+'&proses=getTp'+'&periodeGdng='+prde;
	post_response_text('log_slave_3addingjurnal.php', param, respon);
	function respon(){
		if (con.readyState == 4) {

			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
                    document.getElementById('tpTransaksi').innerHTML=con.responseText;
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
function setSloc(x){
gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;

if (gudang != '') {
        if (x == 'simpan') {
                document.getElementById('sloc').disabled = true;
                document.getElementById('btnsloc').disabled = true;
                tujuan = 'log_slave_getUnposted.php';
                param = 'gudang=' + gudang;
                post_response_text(tujuan, param, respog);
        }
        else {
                document.getElementById('sloc').disabled = false;
                document.getElementById('sloc').options[0].selected=true;
                document.getElementById('btnsloc').disabled = false;
        }	

}	
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        //alert(con.responseText);
                                        document.getElementById('unconfirmaedlist').innerHTML = con.responseText;
                                    getDocumentList(gudang);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}



function getDocumentList(gudang)
{
        param='gudang='+gudang;
        tujuan = 'log_slave_getDaftarDokumen.php';
        post_response_text(tujuan, param, respog);
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('containerlist').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
}

function cariUnconfirmed(num)
{
tex=trim(document.getElementById('txtunpost').value);
gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
if(gudang =='')
{
        alert('Storage Location  is obligatory')
}
else
{
        param='gudang='+gudang;
        param+='&page='+num;
        if(tex!='')
                param+='&tex='+tex;
        tujuan = 'log_slave_getUnposted.php';
        post_response_text(tujuan, param, respog);			
}
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('unconfirmaedlist').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
}

function cariDokumen(num)
{
tex=trim(document.getElementById('txtbabp').value);
gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
if(gudang =='')
{
        alert('Storage Location  is obligatory')
}
else
{
        param='gudang='+gudang;
        param+='&page='+num;
        if(tex!='')
                param+='&tex='+tex;
        tujuan = 'log_slave_getDaftarDokumen.php';
        post_response_text(tujuan, param, respog);			
}
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('containerlist').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
}

function previewDocument(tipe,notransaksi,ev)
{
param='notransaksi='+notransaksi;
switch (tipe){
        case 1:
                tujuan = 'log_slave_print_bapb_pdf.php?'+param;						
                break;
        case 2:
                tujuan = 'log_slave_print_retur_pdf.php?'+param;						
                break;
        case 3:
                tujuan = 'log_slave_print_received_pdf.php?'+param;
                break;
        case 5:
                tujuan = 'log_slave_print_bast_pdf.php?'+param;
                break;
        case 7:
        tujuan = 'log_slave_print_mutasi_pdf.php?'+param;
                break;			
        default : alert("Unknown document type");
}

//display window
title=notransaksi;
width='700';
height='400';
content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
showDialog1(title,content,width,height,ev);	
}

function previewPosting(tipe,notransaksi,gudang,ev)
{
param='notransaksi='+notransaksi+'&tipe='+tipe+'&gudang='+gudang;
        tujuan = 'log_slave_posting_gudang.php';
        //if (confirm('Posting ' + notransaksi + ', Are you sure..?')) {
                post_response_text(tujuan, param, respog);
                lockScreen('wait');
        //} 				
        function respog(){
                if (con.readyState == 4) {
                        unlockScreen();
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                            title=notransaksi;
                                            width='700';
                                            height='400';
                                            content=con.responseText;
                                            //alert(content);
                                            showDialog1(title,content,width,height,ev);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	   
}

function prosesPosting(maxRow, tipetrx){

    if(confirm('Are you sure?'))
    {
        doPostingmaterial(maxRow,tipetrx,1);  
    }
    else
        {
        closeDialog();
        }
}

function doPostingmaterial(maxRow,tipetrx,currentRow){
    tipetransaksi=tipetrx;
    gudang = document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
	notransaksi=trim(document.getElementById('notrans_'+currentRow).innerHTML);   
    tanggal=trim(document.getElementById('tglTrans_'+currentRow).innerHTML);    
    kodebarang=trim(document.getElementById('kdBrg_'+currentRow).innerHTML);  
    satuan=trim(document.getElementById('sat_'+currentRow).innerHTML);  
    jumlah=trim(document.getElementById('jmlh_'+currentRow).innerHTML);  
	if(parseInt(jumlah)==0){
		alert("Jumlah Tidak Boleh Kosong/ 0");
		return;
	}
    kodept=trim(document.getElementById('pt_'+currentRow).innerHTML); 
    try{
    kodeblok=trim(document.getElementById('blok_'+currentRow).innerHTML);
    }
    catch(e)
    {
        kodeblok='';
    }
    gudangx='';
    untukunit='';
    untukpt='';
    supplier='';
    nopo='';
    hargasatuan='0';
    kodekegiatan='';
    kodemesin='';
switch(tipetrx)
{
        case '3':
                gudangx=trim(document.getElementById('gdngx_'+currentRow).innerHTML);
                hargasatuan=trim(document.getElementById('hrgsat_'+currentRow).innerHTML);
                break;	 
        case '5':
                untukpt=trim(document.getElementById('Upt_'+currentRow).innerHTML); 
                untukunit=trim(document.getElementById('UUnit_'+currentRow).innerHTML);
                kodekegiatan=trim(document.getElementById('kegId_'+currentRow).innerHTML); 
                kodemesin=trim(document.getElementById('vhcId_'+currentRow).innerHTML); 
                break;
        case '2':
                untukunit=trim(document.getElementById('Upt_'+currentRow).innerHTML);
                kodekegiatan=trim(document.getElementById('kegId_'+currentRow).innerHTML); 
                kodemesin=trim(document.getElementById('vhcId_'+currentRow).innerHTML); 
                break;                
        case '7':
                gudangx=trim(document.getElementById('gdngx_'+currentRow).innerHTML); 
                break;				
        case '1':
                supplier=trim(document.getElementById('suppId_'+currentRow).innerHTML);
                nopo=trim(document.getElementById('nopo_'+currentRow).innerHTML);
                hargasatuan=trim(document.getElementById('hrgsat_'+currentRow).innerHTML);
                break;
        case '6':
                hargasatuan=trim(document.getElementById('hrgsat_'+currentRow).innerHTML);
                supplier=trim(document.getElementById('suppId_'+currentRow).innerHTML);
                nopo=trim(document.getElementById('nopo_'+currentRow).innerHTML);                
                break;
}
//periksa tanggal=====================================================================
    //gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
    x=tanggal;
    _start=document.getElementById('dt_start').value;
    _end=document.getElementById('dt_end').value;
    while (x.lastIndexOf("-") > -1) {
            x = x.replace("-", "");
    }
    while (x.lastIndexOf("-") > -1) {
        x=x.replace("/","");
    }

    curdateY=x.substr(4,4).toString();
    curdateM=x.substr(2,2).toString();
    curdateD=x.substr(0,2).toString();
    curdate=curdateY+curdateM+curdateD;	
    curdate=parseInt(curdate);
    if (curdate < parseInt(_start) || curdate > parseInt(_end)) {
        alert('Date out of range')
    }        
    else{        
    //====================================================================================         
    if((tipetransaksi== 3 || tipetransaksi==7) && gudangx=='')
    {
        alert('Data component (Source or Destination) is missing');
    }
    else if(tipetransaksi== 5 && untukpt=='')
    {
        alert('Data component (Destination Company) is missing');
    }
    else{
        document.getElementById('row_'+currentRow).style.backgroundColor='orange';
        param='tipetransaksi='+tipetransaksi+'&tanggal='+tanggal;
        param+='&kodebarang='+kodebarang+'&satuan='+satuan+'&jumlah='+jumlah;
        param+='&kodept='+kodept+'&gudangx='+gudangx+'&untukpt='+untukpt;
        param+='&gudang='+gudang+'&kodeblok='+kodeblok+'&notransaksi='+notransaksi;
        param+='&nopo='+nopo+'&supplier='+supplier+'&hargasatuan='+hargasatuan+'&untukunit='+untukunit;
        param+='&kodekegiatan='+kodekegiatan+'&kodemesin='+kodemesin;
        tujuan='log_slave_3addingJurnalPost.php';
        post_response_text(tujuan, param, respog);
        //lockScreen('wait');
    }
    }
function respog(){
    if (con.readyState == 4) {

        if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                        document.getElementById('row_'+currentRow).style.backgroundColor='red';
                }
                else {
                        document.getElementById('row_'+currentRow).style.backgroundColor='green';
                        currentRow+=1;
                        if(currentRow>maxRow)
                        {
                                //setPosting(gudang,notransaksi,1);//beri flag 1 pada kolom post		
								documen.getElementById('printContainer').innerHTML="";
								alert("done");
                        }  
                        else
                        {
                                doPostingmaterial(maxRow,tipetrx,currentRow);
                        }
                }
        }
        else {
                busy_off();
                error_catch(con.status);
                
        }
    }   
}	  
}

function setPosting(gudang,notransaksi,status)
{
param='notransaksi='+notransaksi+'&status='+status+'&gudang='+gudang;
tujuan='log_slave_ubahFlagPosting.php';
post_response_text(tujuan, param, respog);
function respog(){
    if (con.readyState == 4) {

            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                            document.getElementById('indukrow'+currentRow).style.backgroundColor='red';
                        unlockScreen();
                    }
                    else {
                                    setSloc('simpan');
                                    unlockScreen();
                                    alert('Done');
                                    closeDialog();						 
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