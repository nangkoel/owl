function batal()
{
	document.location.reload();
}

function batal2()
{
	document.location.reload();
}


maxf=0
sekarang=1;
function saveAll(maxRow)
{     

	document.getElementById('tPreview').disabled=true;
	document.getElementById('tExcel').disabled=true;
	document.getElementById('tBatal').disabled=true;
      	 maxf=maxRow;
	    loopsave(1,maxRow);
}

function loopsave(currRow,maxRow)
{
	not=trim(document.getElementById('not'+currRow).innerHTML);
	hk=trim(document.getElementById('hk'+currRow).innerHTML);
	hs=trim(document.getElementById('hs'+currRow).innerHTML);
        jjg=trim(document.getElementById('jjg'+currRow).innerHTML);
	
	    param='not='+not+'&hk='+hk+'&hs='+hs+'&jjg='+jjg;
		param+="&proses=savedata";
		
		//alert(param);return;
		tujuan = 'kebun_slave_save_3cekbkm.php';
		post_response_text(tujuan, param, respog);
		document.getElementById('row'+currRow).style.backgroundColor='cyan';
		//lockScreen('wait');
	
	function respog(){
		if (con.readyState == 4) {
			
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('row'+currRow).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					document.getElementById('row'+currRow).style.display='none';
                    currRow+=1;
					sekarang=currRow;
                    if(currRow>maxRow)
					{
						alert('Done');
						document.location.reload();	
						unlockScreen();
						document.getElementById('infoDisplay').innerHTML='';
					}  
					else
					{
						loopsave(currRow,maxRow);
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




////////////////////////////////////
////////////////////////////////////
////////////////////////////////////
////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////SKS
maxfx=0
sekarangx=1;
function saveAllx(maxRowx)
{     

	document.getElementById('tPreviewx').disabled=true;
	document.getElementById('tExcelx').disabled=true;
	document.getElementById('tBatalx').disabled=true;
	
      	 maxfx=maxRowx;
	    loopsavex(1,maxRowx);
		
}

function loopsavex(currRowx,maxRowx)
{
	notx=trim(document.getElementById('notx'+currRowx).innerHTML);
	
	
	    param='notx='+notx;
		param+="&proses=savedatax";
		
		//alert(param);return;
		tujuan = 'kebun_slave_save_3cekbkm.php';
		post_response_text(tujuan, param, respog);
		document.getElementById('rowx'+currRowx).style.backgroundColor='green';
		//lockScreen('wait');
	
	function respog(){
		if (con.readyState == 4) {
			
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('rowx'+currRowx).style.backgroundColor='red';
				   unlockScreenx();
				}
				else {
					document.getElementById('rowx'+currRowx).style.display='none';
                    currRowx+=1;
					sekarangx=currRowx;
                    if(currRowx>maxRowx)
					{
						alert('Done');
						document.location.reload();	
						unlockScreenx();
						document.getElementById('infoDisplay').innerHTML='';
					}  
					else
					{
						loopsavex(currRowx,maxRowx);
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
