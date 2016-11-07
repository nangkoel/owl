// JavaScript Document

function getDt()
{
	idRem=document.getElementById('lksiServer').value;
	param='idRemote='+idRem+'&proses=getDataLokasi';
//	alert(param);
	tujuan='pabrik_slave_3uploadtimbangan.php';
	post_response_text(tujuan, param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                  //$arr="##dbnm##prt##pswrd##ipAdd#period##kdBrg";
				 // alert(con.responseText);
					ar= con.responseText.split("###");
					document.getElementById('ipAdd').value=ar[0];
					document.getElementById('prt').value=ar[1];
					document.getElementById('dbnm').value=ar[2];
					document.getElementById('usrName').value=ar[3];
					document.getElementById('pswrd').value=ar[4];
					
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function lockForm()
{
	document.getElementById('preview').disabled=true;
	document.getElementById('period').disabled=true;
	//document.getElementById('kdBrg').disabled=true;
	document.getElementById('lksiServer').disabled=true;
}
function uploadData(maxRow,varIsi)
{
//
	if(confirm('This will take some time, are you sure..?'))
	{
		   loopClosingFisik(1,maxRow,varIsi);
		   lockForm();
	}
	else
	{
		document.getElementById('preview').disabled=false;
		return;
	}
}

function loopClosingFisik(currRow,maxRow,passParam)
{
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
	idTimbangan=document.getElementById('isiData_'+currRow).innerHTML;
	tglData=document.getElementById('tglData_'+currRow).innerHTML;
	custData=document.getElementById('custData_'+currRow).innerHTML;
	kbn=document.getElementById('kbn_'+currRow).innerHTML;
	pabrik=document.getElementById('pabrik_'+currRow).innerHTML;
	kdBrg=document.getElementById('kdBrg_'+currRow).innerHTML;
	spbno=document.getElementById('spbno_'+currRow).innerHTML;
	sibno=document.getElementById('sibno_'+currRow).innerHTML;
	thnTnm=document.getElementById('thnTnm_'+currRow).innerHTML;
	thnTnm2=document.getElementById('thnTnm2_'+currRow).innerHTML;
	thnTnm3=document.getElementById('thnTnm3_'+currRow).innerHTML;	
	jmlhjjg=document.getElementById('jmlhjjg_'+currRow).innerHTML;	
	jmlhjjg2=document.getElementById('jmlhjjg2_'+currRow).innerHTML;	
	jmlhjjg3=document.getElementById('jmlhjjg3_'+currRow).innerHTML;	
	brndln=document.getElementById('brndln_'+currRow).innerHTML;	
	nodo=document.getElementById('nodo_'+currRow).innerHTML;	
	kdVhc=document.getElementById('kdVhc_'+currRow).innerHTML;	
	spir=document.getElementById('spir_'+currRow).innerHTML;	
	jmMasuk=document.getElementById('jmMasuk_'+currRow).innerHTML;	
	jmKeluar=document.getElementById('jmKeluar_'+currRow).innerHTML;	
	brtBrsih=document.getElementById('brtBrsih_'+currRow).innerHTML;	
	brtMsk=document.getElementById('brtMsk_'+currRow).innerHTML;	
	brtOut=document.getElementById('brtOut_'+currRow).innerHTML;	
	usrNm=document.getElementById('usrNm_'+currRow).innerHTML;	
	kntrkNo=document.getElementById('kntrkNo_'+currRow).innerHTML;
	param+='&proses=uploadData';
	param+='&idTimbangan='+idTimbangan+'&tglData='+tglData+'&custData='+custData+'&kbn='+kbn+'&pabrik='+pabrik;
	param+='&kdBrg='+kdBrg+'&spbno='+spbno+'&sibno='+sibno+'&thnTnm='+thnTnm+'&thnTnm2='+thnTnm2+'&thnTnm3='+thnTnm3;
	param+='&jmlhjjg='+jmlhjjg+'&jmlhjjg2='+jmlhjjg2+'&jmlhjjg3='+jmlhjjg3+'&brndln='+brndln+'&kdVhc='+kdVhc+'&spir='+spir;
	param+='&jmMasuk='+jmMasuk+'&jmKeluar='+jmKeluar+'&brtBrsih='+brtBrsih+'&brtMsk='+brtMsk+'&brtOut='+brtOut+'&usrNm='+usrNm;
	param+='&kntrkNo='+kntrkNo;
	tujuan = 'pabrik_slave_3uploadtimbangan.php';
	post_response_text(tujuan, param, respog);
	document.getElementById('row_'+currRow).style.backgroundColor='orange';
	lockScreen('wait');
	function respog(){
		if (con.readyState == 4) {
			
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('row_'+currRow).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					//alert(con.responseText);
					//return;
					if(con.responseText==1)
					{
						document.getElementById('row_'+currRow).style.backgroundColor='green';
						currRow+=1;
					}
					else if(con.responseText==0)
					{
						document.getElementById('row_'+currRow).style.backgroundColor='red';
						currRow+=1;
					}
					else
					{
						alert("Error");
						tutupProses();
						//unlockScreen();
						
					}
					if(currRow>maxRow)
					{
						document.getElementById('preview').disabled=false;
						tutupProses('simpan');
					}  
					else
					{
						loopClosingFisik(currRow,maxRow,dtAll);
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
function unLockForm()
{
	//##dbnm##prt##pswrd##ipAdd##period##kdBrg##usrName##lksiServer
	document.getElementById('preview').disabled=false;
	document.getElementById('period').disabled=false;
	//document.getElementById('kdBrg').disabled=false;
	document.getElementById('lksiServer').disabled=false;
	document.getElementById('dbnm').value='';
	document.getElementById('prt').value='';
	document.getElementById('pswrd').value='';
	document.getElementById('ipAdd').value='';
	document.getElementById('period').value='';
	//document.getElementById('kdBrg').value='';
	document.getElementById('usrName').value='';
	document.getElementById('lksiServer').value='';
	document.getElementById('printContainer').innerHTML='';
}

function tutupProses(x)
{
	period=document.getElementById('preview');
	if(period.disabled!=true)
	{
		if (x == 'simpan') {
			unlockScreen();
			alert("Data Telah Terupload");
			unLockForm();
			document.getElementById('printContainer').innerHTML='';
		}
		else
		{
			unlockScreen();
		}
	}
	/*gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
//set value display periode
    tglstart=document.getElementById(gudang+'_start').value;
	tglend=document.getElementById(gudang+'_end').value;
	tglstart=tglstart.substr(6,2)+"-"+tglstart.substr(4,2)+"-"+tglstart.substr(0,4);
	tglend=tglend.substr(6,2)+"-"+tglend.substr(4,2)+"-"+tglend.substr(0,4);
	document.getElementById('displayperiod').innerHTML=tglstart+" - "+tglend;
	
	if (gudang != '') {
		if (x == 'simpan') {
			document.getElementById('sloc').disabled = true;
			document.getElementById('btnsloc').disabled = true;
			tujuan = 'log_slave_tutupBukuFisik.php';
			param = 'gudang=' + gudang+'&awal='+document.getElementById(gudang+'_start').value+'&akhir='+document.getElementById(gudang+'_end').value;
			if (confirm('make it permanent and closing period '+document.getElementById('displayperiod').innerHTML+', are you sure..?')) {
				post_response_text(tujuan, param, respog);
			}
			else
			{
				unlockScreen();
			}
			
		}
		else {
			document.getElementById('sloc').disabled = false;
			document.getElementById('sloc').options[0].selected=true;
			document.getElementById('btnsloc').disabled = false;
			//kosongkan();
		}	
		
	}
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						document.getElementById('infoDisplay').innerHTML=con.responseText;
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						alert('Closing of '+ gudang + ' successful, please Relogin!');
						logout();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}		*/
}

	/*tglstart=document.getElementById(gudang+'_start').value;
	tglend=document.getElementById(gudang+'_end').value;
	periode		=trim(document.getElementById('period'+currRow).innerHTML);
	pt			=trim(document.getElementById('pt'+currRow).innerHTML);
	gudang		=trim(document.getElementById('gudang'+currRow).innerHTML);
	kodebarang	=trim(document.getElementById('kodebarang'+currRow).innerHTML);
    if(pt=='' || periode=='' || gudang=='' || kodebarang=='')
	{
		alert("Data inconsistent");
	}	
    else
	{  
	    param='pt='+pt+'&periode='+periode+'&gudang='+gudang+'&kodebarang='+kodebarang;
		param+='&awal='+tglstart+'&akhir='+tglend;
		tujuan = 'log_slave_saveTutupBukuFisik.php';
		post_response_text(tujuan, param, respog);
		document.getElementById('row'+currRow).style.backgroundColor='orange';
		lockScreen('wait');
		isiData_
	}*/

/*function uploadData(passParam)
{
	//tbl=document.getElementById('ListData').childNodes;
	//tbl.length;
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
	str='';
	i=1;
	while(tmp=document.getElementById('isiData_'+i))
	{
		if(str!='')
		{ str+="&idTimbangan[]="+tmp.innerHTML;}
		else
		{str+="&idTimbangan[]="+tmp.innerHTML;}
		i++;
	}
	//alert(str);
	param+='&proses=uploadData';
	param+=str;
	tujuan='pabrik_slave_3uploadtimbangan.php';
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
				 	//  	document.getElementById('btnUpload').disabled=false;
                  	//loadData();
				  	eval('var status='+con.responseText);
					for(i in status) {
						tmp = document.getElementById(i);
						if(status[i]==1) {
							//tmp.setAttribute('bgColor','#00CC66');
							//tmp.style.background-color= '#00CC66';
							tmp.style.backgroundColor='green';
							}
					}
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	if(confirm("Apakah Anda Yakin Untuk Mengupload Data"))
	{
		post_response_text(tujuan, param, respon);
		document.getElementById('btnUpload').disabled=true;
	}
	else
	{
		return;
	} 
}
*/
/*function uploadData(passParam)
{
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
	
	param+='proses=uploadData';
	tujuan='pabrik_slave_3uploadtimbangan.php';
  // alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
				   	document.getElementById('btnUpload').disabled=false;
					/*fileTarget='pabrik_slave_3uploadtimbangan';
					idcon='printContainer';
					zPreview(fileTarget,param,idcon);
                  //loadData();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	if(confirm("Apakah Anda Yakin Untuk Mengupload Data"))
	{
		post_response_text(tujuan, param, respon);
		document.getElementById('btnUpload').disabled=true;
	}
	else
	{
		return;
	} 
}*/
