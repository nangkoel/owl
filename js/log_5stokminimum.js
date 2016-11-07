
// JavaScript Document
function searchBarang(title,content,ev)
{
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
	//alert('asalll');
}

function findBarang()
{
	txt=trim(document.getElementById('namabrg').value);
	if(txt=='')
	{
		alert('Text is obligatory');
	}
	else
	{
		param='txtfind='+txt;
		tujuan='log_slave_get_barang.php';
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
							document.getElementById('containerBarang').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  	
}

function setKodeBarang(kelompok,kode,nama,satuan)
{
	 document.getElementById('namadisabled').value=nama;
         document.getElementById('sat').innerHTML=satuan;
	 document.getElementById('kodebarang').innerHTML=kode;
	 closeDialog();
}

function cariBast(num)
{
    kdgudang=document.getElementById('optcari').value;
    nmbarang=document.getElementById('txtcari').value;
		param='method=loadData&kdOrg='+kdgudang+'&nmcari='+nmbarang;
		param+='&page='+num;
		tujuan = 'log_slave_5stokminimum.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//displayList();
						
						document.getElementById('container').innerHTML=con.responseText;
						//loadData();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}

function loadData()
{
    kdgudang=document.getElementById('optcari').options[document.getElementById('optcari').selectedIndex].value;
    nmbarang=document.getElementById('txtcari').value;
    param='method=loadData&kdOrg='+kdgudang+'&nmcari='+nmbarang;
    tujuan='log_slave_5stokminimum';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}

function simpan() {
    kdOrg=document.getElementById('kdOrg').value;
    kodebarang=document.getElementById('kodebarang').innerHTML;
    minstok=document.getElementById('minstok').value;
    maxstok=document.getElementById('maxstok').value;
    method=trim(document.getElementById('method').value);

    param='kdOrg='+kdOrg+'&kodebarang='+kodebarang+'&maxstok='+maxstok+'&minstok='+minstok+'&method='+method;
    tujuan = 'log_slave_5stokminimum.php';
    post_response_text(tujuan, param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    loadData();
                    cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(fileTarget+'.php', param, respon);
}

function fillField(kodegudang,kodebarang,minstok,maxstok,namabarang)
{
    document.getElementById('kdOrg').value=kodegudang;
    document.getElementById('kodebarang').innerHTML=kodebarang;
    document.getElementById('namadisabled').value=namabarang;
    document.getElementById('minstok').value=minstok;
    document.getElementById('maxstok').value=maxstok;
    document.getElementById('method').value="update";
}

function cancelIsi()
{
    document.getElementById('kdOrg').value='';
    document.getElementById('kodebarang').innerHTML='';
    document.getElementById('namadisabled').value='';
    document.getElementById('minstok').value='0';
    document.getElementById('maxstok').value='0';
    document.getElementById('method').value="insert";
}

function del(kodegudang,kodebarang)
{
	param='method=delete'+'&kodept='+kdOrg+'&kodebarang='+kodebarang;
	//alert(param);
	tujuan='log_slave_5stokminimum.php';
	post_response_text(tujuan, param, respog);	
	function respog()
	{
		  if(con.readyState==4)
		  {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else 
					{
						loadData();
                                                cancelIsi();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}
function carinmbarang(ev)
{
  key=getKey(ev);
  if(key==13){
        loadData();
        document.getElementById('txtcari').select();
  } else {
  return tanpa_kutip(ev);	
  }	
}
