/**
 * @author repindra.ginting
 */
function simpanTipeKar()
{
	kode=document.getElementById('kode').value;
	nama=document.getElementById('nama').value;
	met=document.getElementById('method').value;
	if(trim(kode)=='')
	{
		alert('Code is empty');
		document.getElementById('kode').focus();
	}
	else
	{
		kode=trim(kode);
		nama=trim(nama);
		param='kode='+kode+'&nama='+nama+'&method='+met;
		tujuan='sdm_slave_save_tipekaryawan.php';
        post_response_text(tujuan, param, respog);		
	}
	
	function respog()
	{
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
							//alert(con.responseText);
							document.getElementById('container').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function fillField(kode,nama)
{
	document.getElementById('kode').value=kode;
    document.getElementById('kode').disabled=true;
	document.getElementById('nama').value=nama;
	document.getElementById('method').value='update';
}

function cancelTipeKar()
{
    document.getElementById('kode').disabled=false;
	document.getElementById('kode').value='';
	document.getElementById('nama').value='';
	document.getElementById('method').value='insert';		
}
