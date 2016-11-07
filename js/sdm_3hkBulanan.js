maxf=0
sekarang=1;
function saveAll(maxRow)
{     
    maxf=maxRow;
    loopsave(1,maxRow);
}


function batal()
{
	document.getElementById('per').value='';	
	document.getElementById('printContainer').innerHTML='';
        //document.reload();
        //document.location.reload();
        
}



function loopsave(currRow,maxRow)
{
	periode=trim(document.getElementById('periode'+currRow).innerHTML);
	karyawanid=trim(document.getElementById('karyawanid'+currRow).innerHTML);
	hk=trim(document.getElementById('hk'+currRow).innerHTML);
        hkAbs=trim(document.getElementById('hkAbs'+currRow).innerHTML);
        unit=document.getElementById('unit').value;
	
	if(periode=='' || karyawanid=='' || hk=='')
	{
		alert("Data tidak lengkap");return;
	}	
    else
	{  
	    param='periode='+periode+'&karyawanid='+karyawanid+'&hk='+hk+'&unit='+unit+'&hkAbs='+hkAbs;
		param+="&proses=savedata";
		
		//alert(param);
		tujuan = 'sdm_slave_3hkBulanan.php';
		post_response_text(tujuan, param, respog);
		document.getElementById('row'+currRow).style.backgroundColor='cyan';
		//lockScreen('wait');
	}
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
                                                batal();
						
						//document.getElementById('infoDisplay').innerHTML='';
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