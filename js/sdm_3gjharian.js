function batal()
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
	document.getElementById('kdorg').disabled=true;
	document.getElementById('per').disabled=true;
	
      	 maxf=maxRow;
	    loopsave(1,maxRow);
}

function loopsave(currRow,maxRow)
{
	karyawanid=trim(document.getElementById('karyawanid'+currRow).innerHTML);
	kdorg=trim(document.getElementById('kdorg'+currRow).innerHTML);
	tgl=trim(document.getElementById('tgl'+currRow).innerHTML);
	upah=trim(document.getElementById('upah'+currRow).innerHTML);
	
	    param='karyawanid='+karyawanid+'&kdorg='+kdorg+'&tgl='+tgl+'&upah='+upah;
		param+="&proses=savedata";
		
		//alert(param);return;
		tujuan = 'sdm_slave_save_3gjharian.php';
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

///////////////////////////////////////////////////////////////////////////////////////////////////////////////SKS
maxfx=0
sekarangx=1;
function saveAllx(maxRowx)
{     

	document.getElementById('tPreviewx').disabled=true;
	document.getElementById('tExcelx').disabled=true;
	document.getElementById('tBatalx').disabled=true;
	document.getElementById('kdorgx').disabled=true;
	document.getElementById('perx').disabled=true;
	
      	 maxfx=maxRowx;
	    loopsavex(1,maxRowx);
		
}

function loopsavex(currRowx,maxRowx)
{
	karyawanid=trim(document.getElementById('karyawanidx'+currRowx).innerHTML);
	kdorg=trim(document.getElementById('kdorgx'+currRowx).innerHTML);
	tgl=trim(document.getElementById('tglx'+currRowx).innerHTML);
	upah=trim(document.getElementById('upahx'+currRowx).innerHTML);
	
	    param='karyawanid='+karyawanid+'&kdorg='+kdorg+'&tgl='+tgl+'&upah='+upah;
		param+="&proses=savedata";
		
		//alert(param);return;
		tujuan = 'sdm_slave_save_3gjharianSks.php';
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
