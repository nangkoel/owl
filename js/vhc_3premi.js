function batal()
{
	document.location.reload();
}


///////////////////////DT//////////////////////////
maxfDt=0
sekarangDt=1;
function saveAllDt(maxRowDt)
{     

	document.getElementById('tPreviewDt').disabled=true;
	document.getElementById('tExcelDt').disabled=true;
	//document.getElementById('tBatalDt').disabled=true;
	document.getElementById('kdorgDt').disabled=true;
	document.getElementById('perDt').disabled=true;
	
      	 maxfDt=maxRowDt;
	    loopsaveDt(1,maxRowDt);
}

function loopsaveDt(currRowDt,maxRowDt)
{
	
	kdorg=document.getElementById('kdorgDt').value;
	per=document.getElementById('perDt').value;
	karyawanid=trim(document.getElementById('karyawanid'+currRowDt).value);
	premi=trim(document.getElementById('premi'+currRowDt).value);
	
	    param='karyawanid='+karyawanid+'&kdorg='+kdorg+'&per='+per+'&premi='+premi;
		param+="&proses=savedataDt";
		
	//	alert(param);return;
		tujuan = 'vhc_slave_save_3premi.php';
		post_response_text(tujuan, param, respog);
		document.getElementById('row'+currRowDt).style.backgroundColor='cyan';
		//lockScreen('wait');
	
	function respog(){
		if (con.readyState == 4) {
			
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('row'+currRowDt).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					document.getElementById('row'+currRowDt).style.display='none';
                    currRowDt+=1;
					sekarangDt=currRowDt;
                    if(currRowDt>maxRowDt)
					{
						alert('Done');
						document.location.reload();	
						unlockScreen();
						document.getElementById('infoDisplay').innerHTML='';
					}  
					else
					{
						loopsaveDt(currRowDt,maxRowDt);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
                               // document.getElementById('lanjut').style.display='';
				//unlockScreen();
			}
		}
	}		
	
}
////////////////////////////////tutup DT///////////////////////////////////////


//////////////////////////////AB///////////////////////////////////////////////
maxfAb=0
sekarangAb=1;
function saveAllAb(maxRowAb)
{     

	document.getElementById('tPreviewAb').disabled=true;
	document.getElementById('tExcelAb').disabled=true;
	//document.getElementById('tBatalAb').disabled=true;
	document.getElementById('kdorgAb').disabled=true;
	document.getElementById('perAb').disabled=true;
	
      	 maxfAb=maxRowAb;
	    loopsaveAb(1,maxRowAb);
}

function loopsaveAb(currRowAb,maxRowAb)
{
	
	kdorg=document.getElementById('kdorgAb').value;
	per=document.getElementById('perAb').value;
	karyawanid=trim(document.getElementById('karyawanid'+currRowAb).value);
	premi=trim(document.getElementById('premi'+currRowAb).value);
	
	    param='karyawanid='+karyawanid+'&kdorg='+kdorg+'&per='+per+'&premi='+premi;
		param+="&proses=savedataAb";
		
		//alert(param);return;
		tujuan = 'vhc_slave_save_3premi.php';
		post_response_text(tujuan, param, respog);
		document.getElementById('row'+currRowAb).style.backgroundColor='cyan';
		//lockScreen('wait');
	
	function respog(){
		if (con.readyState == 4) {
			
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('row'+currRowAb).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					document.getElementById('row'+currRowAb).style.display='none';
                    currRowAb+=1;
					sekarangAb=currRowAb;
                    if(currRowAb>maxRowAb)
					{
						alert('Done');
						document.location.reload();	
						unlockScreen();
						document.getElementById('infoDisplay').innerHTML='';
					}  
					else
					{
						loopsaveAb(currRowAb,maxRowAb);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
                               // document.getElementById('lanjut').style.display='';
				//unlockScreen();
			}
		}
	}		
	
}
/////////////////////////////////////////////TUTUP AB////////////////////////////////////////////////

///////////////////////Cu//////////////////////////
maxfCu=0
sekarangCu=1;
function saveAllCu(maxRowCu)
{     

	document.getElementById('tPreviewCu').disabled=true;
	document.getElementById('tExcelCu').disabled=true;
	//document.getElementById('tBatalCu').disabled=true;
	document.getElementById('kdorgCu').disabled=true;
	document.getElementById('perCu').disabled=true;
	
      	 maxfCu=maxRowCu;
	    loopsaveCu(1,maxRowCu);
}

function loopsaveCu(currRowCu,maxRowCu)
{
	
	kdorg=document.getElementById('kdorgCu').value;
	per=document.getElementById('perCu').value;
	karyawanid=trim(document.getElementById('karyawanid'+currRowCu).value);
	premi=trim(document.getElementById('premi'+currRowCu).value);
	
	    param='karyawanid='+karyawanid+'&kdorg='+kdorg+'&per='+per+'&premi='+premi;
		param+="&proses=savedataCu";
		
	//	alert(param);return;
		tujuan = 'vhc_slave_save_3premi.php';
		post_response_text(tujuan, param, respog);
		document.getElementById('row'+currRowCu).style.backgroundColor='cyan';
		//lockScreen('wait');
	
	function respog(){
		if (con.readyState == 4) {
			
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('row'+currRowCu).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					document.getElementById('row'+currRowCu).style.display='none';
                    currRowCu+=1;
					sekarangCu=currRowCu;
                    if(currRowCu>maxRowCu)
					{
						alert('Done');
						document.location.reload();	
						unlockScreen();
						document.getElementById('infoDisplay').innerHTML='';
					}  
					else
					{
						loopsaveCu(currRowCu,maxRowCu);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
                               // document.getElementById('lanjut').style.display='';
				//unlockScreen();
			}
		}
	}		
	
}
////////////////////////////////tutup Cu///////////////////////////////////////



