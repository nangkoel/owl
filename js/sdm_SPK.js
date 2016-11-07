function parsel(tex) {
	xml=tex.toString();
    xmlobject = (new DOMParser()).parseFromString(xml, "text/xml");	
    notr=xmlobject.getElementsByTagName('notransaksi')[0].firstChild.nodeValue;
    notr=notr.replace("*","");
    karyawanid=xmlobject.getElementsByTagName('karyawanid')[0].firstChild.nodeValue;
    karyawanid=karyawanid.replace("*","");
    penandatangan=xmlobject.getElementsByTagName('penandatangan')[0].firstChild.nodeValue;
    penandatangan=penandatangan.replace("*","");
    tanggal=xmlobject.getElementsByTagName('tanggal')[0].firstChild.nodeValue;
    tanggal=tanggal.replace("*","");

    	jk=document.getElementById('karyawanid');
                for(x=0;x<jk.length;x++)
                {
                        if(jk.options[x].value==karyawanid)
                        {
                                jk.options[x].selected=true;
                        }
                }

        jk=document.getElementById('penandatangan');
                for(x=0;x<jk.length;x++)
                {
                        if(jk.options[x].value==penandatangan)
                        {
                                jk.options[x].selected=true;
                        }
                }	

    document.getElementById('notr').value=notr;
    document.getElementById('tanggal').value=tanggal;
    document.getElementById('method').value=method;
    tabAction(document.getElementById('tabFRM0'),0,'FRM',1);
}

function  filterK(){
    lokasitugas=document.getElementById('lokasitugas');
    lokasitugas=lokasitugas.options[lokasitugas.selectedIndex].value;
    tipekaryawan=document.getElementById('tipekaryawan');
    tipekaryawan=tipekaryawan.options[tipekaryawan.selectedIndex].value;
    
                param='lokasitugas='+lokasitugas+'&tipekaryawan='+tipekaryawan;
                tujuan = 'sdm_slave_getSPKList.php';
                post_response_text(tujuan, param, respog);
                
                
         function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                      document.getElementById('karyawanid').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }   
}

function simpanSPK() {
	notr = document.getElementById('notr').value;
	karyawanid = document.getElementById('karyawanid');
	karyawanid = karyawanid.options[karyawanid.selectedIndex].value;
	penandatangan = document.getElementById('penandatangan');
	penandatangan = penandatangan.options[penandatangan.selectedIndex].value;
	tanggal = document.getElementById('tanggal').value;
	method = document.getElementById('method').value;

	if (karyawanid == '' || penandatangan == '' || tanggal == '') {
		alert('Employee/Karyawan, Signet/Penandatangan & Doc.Date/Tanggal Surat are obligatory(berhubungan satu dan lainnya)');
	} else {
		param = 'notr='+notr + '&karyawanid='+karyawanid;
		param+= '&penandatangan='+penandatangan + '&tanggal='+tanggal;
		param+= '&method='+method;

		if (confirm('Saving, are you sure..?')) {
			tujuan = 'sdm_slave_saveSPK.php';
			post_response_text(tujuan,param,respog);
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
                                        if (con.responseText=='') {
                                        alert('Saved');
                                        //clearForm();
                                        loadList();
                                        } else {
                                            alert(con.responseText);
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

function cariSPK(num)
{
        tex=trim(document.getElementById('txtbabp').value);
                param='&page='+num;
                if(tex!='')
                        param+='&tex='+tex;
                tujuan = 'sdm_slave_getSPKList.php';

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

function loadList()
{      num=0;
                param='&page='+num;
                tujuan = 'sdm_slave_getSPKList.php';
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

function previewSPK(notr,ev)
{
        param='notr='+notr;
        tujuan = 'sdm_slave_printSPK_pdf.php?'+param;	
 //display window
   title=notr;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);

}

function editSPK(notr,nokaryawan)
{

                param='karyawanid='+nokaryawan+'&notr='+notr;
                tujuan = 'sdm_slave_getSPForEdit.php';
                post_response_text(tujuan, param, respog);		


        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        parsel(con.responseText);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }		
}