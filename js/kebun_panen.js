var showPerPage = 10;

function getValue(id) {
    var tmp = document.getElementById(id);
    
    if(tmp) {
        if(tmp.options) {
            return tmp.options[tmp.selectedIndex].value;
        } else if(tmp.nodeType=='checkbox') {
            if(tmp.checked==true) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return tmp.value;
        }
    } else {
        return false;
    }
}

/* Search
 * Filtering Data
 */
function searchTrans(tipe,tipeVal) {
    var notrans = document.getElementById('sNoTrans');
    if(notrans.value=='') {
        var where = '[["'+tipe+'","'+tipeVal+'"]]';
    } else {
        var where = '[["notransaksi","'+notrans.value+'"],["'+tipe+'","'+tipeVal+'"]]';
    }
    goToPages(1,showPerPage,where);
}

/* Paging
 * Paging Data
 */
function defaultList(tipe) {
	tipe='PNN';
    goToPages(1,showPerPage,'[["tipetransaksi","'+tipe+'"]]');
}

function goToPages(page,shows,where) {
    if(typeof where != 'undefined') {
        var newWhere = where.replace(/'/g,'"');
    }
    var workField = document.getElementById('workField');
    var param = "page="+page;
    param += "&shows="+shows+"&tipe=PNN";
    if(typeof where != 'undefined') {
        param+="&where="+newWhere;
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_operasional.php?proses=showHeadList', param, respon);
}

function choosePage(obj,shows,where) {
    var pageVal = obj.options[obj.selectedIndex].value;
    goToPages(pageVal,shows,where);
}

/* Halaman Manipulasi Data
 * Halaman add, edit, delete
 */
function showAdd(tipe) {
    var workField = document.getElementById('workField');
    var param = "tipe="+tipe;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_operasional.php?proses=showAdd', param, respon);
}

function showEditFromAdd(tipe) {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('notransaksi');
    var param = "notransaksi="+trans.value;
    param+="&tipe="+tipe;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                    showDetail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_operasional.php?proses=showEdit', param, respon);
}

function showEdit(num,tipe) {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('notransaksi_'+num);
    var param = "numRow="+num+"&notransaksi="+trans.getAttribute('value');
    param+="&tipe="+tipe;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                    showDetail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_operasional.php?proses=showEdit', param, respon);
}

/* Manipulasi Data
 * add, edit, delete
 */
function addDataTable(tipe) {
    var param = "notransaksi="+getValue('notransaksi')+"&kodeorg="+getValue('kodeorg');
    param += "&tanggal="+getValue('tanggal')+"&nikmandor="+getValue('nikmandor');
    param += "&nikmandor1="+getValue('nikmandor1')+"&nikasisten="+getValue('nikasisten');
    param += "&keranimuat="+getValue('keranimuat')+"&asistenpanen="+getValue('asistenpanen');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById('notransaksi').value = con.responseText;
                    //alert('Added Data Header');
                    showEditFromAdd(tipe);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_operasional.php?proses=add&tipe='+tipe, param, respon);
}

function editDataTable(tipe) {
    var param = "notransaksi="+getValue('notransaksi')+"&kodeorg="+getValue('kodeorg');
    param += "&tanggal="+getValue('tanggal')+"&nikmandor="+getValue('nikmandor');
    param += "&nikmandor1="+getValue('nikmandor1')+"&nikasisten="+getValue('nikasisten');
    param += "&keranimuat="+getValue('keranimuat')+"&asistenpanen="+getValue('asistenpanen');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    defaultList(tipe);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_operasional.php?proses=edit', param, respon);
}

/*
 * Detail
 */

function showDetail() {
    var detailField = document.getElementById('detailField');
    var notrans = document.getElementById('notransaksi').value;
    var afdeling = getValue('kodeorg');
	var tanggal = document.getElementById('tanggal').value;
    var param = "notransaksi="+notrans+"&afdeling="+afdeling+"&tanggal="+tanggal;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    detailField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_panen_detail.php?proses=showDetail', param, respon);
}

function deleteData(num) {
    var notrans = document.getElementById('notransaksi_'+num).getAttribute('value');
    var param = "notransaksi="+notrans;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    var tmp = document.getElementById('tr_'+num);
                    tmp.parentNode.removeChild(tmp);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_operasional.php?proses=delete', param, respon);
}

/* Update No Urut di halaman absensi
 */
function updNoUrut() {
    var tabBody = document.getElementById('mTabBody');
    var nourut = document.getElementById('nourut');
    var maxNum = 0;
    
    if(tabBody.childNodes.length>0) {
        for(i=0;i<tabBody.childNodes.length;i++) {
            var tmp = document.getElementById('nourut_'+i);
            if(tmp.innerHTML > maxNum) {
                maxNum = tmp.innerHTML;
            }
        }
    }
    nourut.value = parseInt(maxNum)+1;
}

function getLaporanPanen()
{
	pt=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
                tgl1    =document.getElementById('tgl1').value;
                tgl2    =document.getElementById('tgl2').value;
		ptV	=pt.options[pt.selectedIndex].value;
		gudangV	=gudang.options[gudang.selectedIndex].value;

	param='pt='+ptV+'&gudang='+gudangV+'&tgl1='+tgl1+'&tgl2='+tgl2;
	tujuan='kebun_laporanPanen.php';
	
	if(ptV=='')
	{
		alert('Company required');
		return;
	}
	
	else if(tgl1=='' || tgl2=='')
        {
            alert('Date required');
			return;
        } 

	else if(tgl1.length!=10 || tgl2.length!=10)
        {
            alert('Date incorrect');
        }    
        else
        post_response_text(tujuan, param, respog);
	
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						showById('printPanel');
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

function dateExpired(d1) {//d1 is date to check in YYYY-MM-DD format setFullYear it is in YYYY,MM,DD. But it starts Months on 0 for Jan.
}

function days_between(tgl1, tgl2) {
 var x=tgl1.split("-");     
        var y=tgl2.split("-");
    // The number of milliseconds in one day
    var ONE_DAY = 1000 * 60 * 60 * 24
       var date1=new Date(x[2],(x[1]-1),x[0]);
  
        var date2=new Date(y[2],(y[1]-1),y[0])

    // Calculate the difference in milliseconds
    var difference_ms = Math.abs(date1.getTime() - date2.getTime())

    // Convert back to days and return
    return Math.round(difference_ms/ONE_DAY)

}

function getLaporanPanen_1()
{
    pt=document.getElementById('pt_1');
    unit=document.getElementById('unit_1');
    tgl1=document.getElementById('tgl1_1').value;
    tgl2=document.getElementById('tgl2_1').value;
    ptV	=pt.options[pt.selectedIndex].value;
    unitV=unit.options[unit.selectedIndex].value;

    param='pt='+ptV+'&unit='+unitV+'&tgl1='+tgl1+'&tgl2='+tgl2;
    tujuan='kebun_laporanPanen_tanggal.php';

    jumlahhari=days_between(tgl1,tgl2);

    if(ptV=='')
    {
        alert('Comany required');
        return;
    }
    else if(tgl1=='' || tgl2=='')
    {
        alert('Date required');
        return;
    }
    else if(jumlahhari>30){
        alert('Number of days must less than 31 days');
        return;
    }
    else if(tgl1.length!=10 || tgl2.length!=10)
    {
        alert('Date incorrect');
        return;
    }    
    else
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                        showById('printPanel_1');
                        document.getElementById('container_1').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function getLaporanPanen_2()
{
    pt=document.getElementById('pt_2');
    unit=document.getElementById('unit_2');
    pil=document.getElementById('pil_2');
    tgl1=document.getElementById('tgl1_2').value;
    tgl2=document.getElementById('tgl2_2').value;
    ptV	=pt.options[pt.selectedIndex].value;
    unitV=unit.options[unit.selectedIndex].value;
    pilV=pil.options[pil.selectedIndex].value;

    param='pt='+ptV+'&unit='+unitV+'&tgl1='+tgl1+'&tgl2='+tgl2+'&pil='+pilV;
    tujuan='kebun_laporanPanen_orang.php';

    jumlahhari=days_between(tgl1,tgl2);

    if(ptV=='')
    {
        alert('Company required');
        return;
    }
    else if(tgl1=='' || tgl2=='')
    {
        alert('Date required');
        return;
    }
    else if(jumlahhari>30){
        alert('Number of days must less than 31 days');
        return;
    }
    else if(tgl1.length!=10 || tgl2.length!=10)
    {
        alert('Date incorrect');
        return;
    }    
    else
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                        showById('printPanel_2');
                        document.getElementById('container_2').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function getLaporanPanen_3()
{
    pt=document.getElementById('pt_3');
    unit=document.getElementById('unit_3');
    tgl1=document.getElementById('tgl1_3').value;
    tgl2=document.getElementById('tgl2_3').value;
    ptV	=pt.options[pt.selectedIndex].value;
    unitV=unit.options[unit.selectedIndex].value;

    param='pt='+ptV+'&unit='+unitV+'&tgl1='+tgl1+'&tgl2='+tgl2;
    tujuan='kebun_laporanPanen_spbwb.php';

    jumlahhari=days_between(tgl1,tgl2);

    if(ptV=='')
    {
        alert('Company required');
        return;
    }
    else if(tgl1=='' || tgl2=='')
    {
        alert('Date required');
        return;
    }
//    else if(jumlahhari>30){
//        alert('Jumlah hari lebih dari 31');
//        return;
//    }
    else if(tgl1.length!=10 || tgl2.length!=10)
    {
        alert('Tanggal salah');
        return;
    }    
    else
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                        showById('printPanel_3');
                        document.getElementById('container_3').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function laporanKeExcel_1(ev,tujuan)
{
    pt=document.getElementById('pt_1');
    unit=document.getElementById('unit_1');
    tgl1=document.getElementById('tgl1_1').value;
    tgl2=document.getElementById('tgl2_1').value;
    ptV	=pt.options[pt.selectedIndex].value;
    unitV=unit.options[unit.selectedIndex].value;

    param='pt='+ptV+'&unit='+unitV+'&tgl1='+tgl1+'&tgl2='+tgl2;
    judul='Excel';
    printFile2(param,tujuan,judul,ev)	
}

function laporanKeExcel_2(ev,tujuan)
{
    pt=document.getElementById('pt_2');
    unit=document.getElementById('unit_2');
    tgl1=document.getElementById('tgl1_2').value;
    tgl2=document.getElementById('tgl2_2').value;
    ptV	=pt.options[pt.selectedIndex].value;
    unitV=unit.options[unit.selectedIndex].value;
    pil=document.getElementById('pil_2');
    pilV=pil.options[pil.selectedIndex].value;

    param='pt='+ptV+'&unit='+unitV+'&tgl1='+tgl1+'&tgl2='+tgl2+'&pil='+pilV;
    judul='Excel';
    printFile2(param,tujuan,judul,ev)	
}

function laporanKeExcel_3(ev,tujuan)
{
    pt=document.getElementById('pt_3');
    unit=document.getElementById('unit_3');
    tgl1=document.getElementById('tgl1_3').value;
    tgl2=document.getElementById('tgl2_3').value;
    ptV	=pt.options[pt.selectedIndex].value;
    unitV=unit.options[unit.selectedIndex].value;

    param='pt='+ptV+'&unit='+unitV+'&tgl1='+tgl1+'&tgl2='+tgl2;
    judul='Excel';
    printFile2(param,tujuan,judul,ev)	
}

function laporanKePDF_1(ev,tujuan)
{
    pt=document.getElementById('pt_1');
    unit=document.getElementById('unit_1');
    tgl1=document.getElementById('tgl1_1').value;
    tgl2=document.getElementById('tgl2_1').value;
    ptV	=pt.options[pt.selectedIndex].value;
    unitV=unit.options[unit.selectedIndex].value;

    param='pt='+ptV+'&unit='+unitV+'&tgl1='+tgl1+'&tgl2='+tgl2;
    judul='Portable Document Format';
    printFile(param,tujuan,judul,ev)	
}

function laporanKePDF_2(ev,tujuan)
{
    pt=document.getElementById('pt_2');
    unit=document.getElementById('unit_2');
    tgl1=document.getElementById('tgl1_2').value;
    tgl2=document.getElementById('tgl2_2').value;
    ptV	=pt.options[pt.selectedIndex].value;
    unitV=unit.options[unit.selectedIndex].value;
    pil=document.getElementById('pil_2');
    pilV=pil.options[pil.selectedIndex].value;

    param='pt='+ptV+'&unit='+unitV+'&tgl1='+tgl1+'&tgl2='+tgl2;
    judul='Portable Document Format';
    if(pilV=='fisik')
    printFile(param,tujuan,judul,ev)
    else alert('PDF report by labour only display result.')
}

function laporanKePDF_3(ev,tujuan)
{
    pt=document.getElementById('pt_3');
    unit=document.getElementById('unit_3');
    tgl1=document.getElementById('tgl1_3').value;
    tgl2=document.getElementById('tgl2_3').value;
    ptV	=pt.options[pt.selectedIndex].value;
    unitV=unit.options[unit.selectedIndex].value;

    param='pt='+ptV+'&unit='+unitV+'&tgl1='+tgl1+'&tgl2='+tgl2;
    judul='Portable Document Format';
    printFile(param,tujuan,judul,ev)	
}

function bersih_1()
{
    document.getElementById('printPanel_1').style.display='none';
    document.getElementById('container_1').innerHTML='';
}

function bersih_2()
{
    document.getElementById('printPanel_2').style.display='none';
    document.getElementById('container_2').innerHTML='';
}

function bersih_3()
{
    document.getElementById('printPanel_3').style.display='none';
    document.getElementById('container_3').innerHTML='';
}

function fisikKePDF(ev,tujuan)
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
                tgl1    =document.getElementById('tgl1').value;
                tgl2    =document.getElementById('tgl2').value;
		pt		=pt.options[pt.selectedIndex].value;
		gudang	=gudang.options[gudang.selectedIndex].value;
	judul='Report PDF';	
	param='pt='+ptV+'&gudang='+gudangV+'&tgl1='+tgl1+'&tgl2='+tgl2;
	printFile(param,tujuan,judul,ev)	
}
function fisikKeExcel(ev,tujuan)
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
                tgl1    =document.getElementById('tgl1').value;
                tgl2    =document.getElementById('tgl2').value;
		pt		=pt.options[pt.selectedIndex].value;
		gudang	=gudang.options[gudang.selectedIndex].value;
	judul='Report Ms.Excel';	
	param='pt='+ptV+'&gudang='+gudangV+'&tgl1='+tgl1+'&tgl2='+tgl2;
	printFile(param,tujuan,judul,ev)	
}
function fisikKeExcel2(ev,tujuan)
{
	tgl    =document.getElementById('tanggal').value;
	kdOrg  =document.getElementById('kdOrg').value;
	judul='Report Ms.Excel';	
	param='tgl='+tgl+'&kdOrg='+kdOrg+'&proses=excelDetail';
	printFile2(param,tujuan,judul,ev)	
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}


/* Posting Data
 */
function postingData(numRow) {
    var notrans = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    var param = "notransaksi="+notrans;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    //alert('Posting Berhasil');
                    //javascript:location.reload(true);
                    x=document.getElementById('tr_'+numRow);
                    x.cells[8].innerHTML='';
                    x.cells[9].innerHTML='';
                    x.cells[10].innerHTML="<img class=\"zImgOffBtn\" title=\"Posting\" src=\"images/skyblue/posted.png\">";
 
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(confirm('Akan dilakukan posting untuk transaksi '+notrans+
        '\nData tidak dapat diubah setelah ini. Anda yakin?')) {
        post_response_text('kebun_slave_panen_posting.php', param, respon);
    }
}

function updTahunTanam() {
    var tgl=document.getElementById('tanggal').value;
    var nik = document.getElementById('ftPrestasi_kodeorg').firstChild;
    var tahuntanam = document.getElementById('ftPrestasi_tahuntanam').firstChild;
    var param = "kodeorg="+nik.options[nik.selectedIndex].value+'&tanggal='+tgl;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    
                    var res = con.responseText.split("####");
                    tahuntanam.value = res[0];
                    document.getElementById('bjraktual').value=res[1];
                    updUpah();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_panen_detail.php?proses=updTahunTanam', param, respon);
}

function updBjr() {
    var nik = document.getElementById('ftPrestasi_kodeorg').firstChild;
    var notransaksi = document.getElementById('notransaksi').value;
    var hasilkerja = document.getElementById('ftPrestasi_hasilkerja').firstChild.value;
    var hasilkerjakg = document.getElementById('ftPrestasi_hasilkerjakg').firstChild;
    var param = "kodeorg="+nik.options[nik.selectedIndex].value+"&notransaksi="+notransaksi+"&hasilkerja="+hasilkerja;
//    alert(param);
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    var res = con.responseText;
                    hasilkerjakg.value = res;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_panen_detail.php?proses=updBjr', param, respon);
}

function updUpah() {
    var nik = document.getElementById('ftPrestasi_nik').firstChild;
    var upahkerja = document.getElementById('ftPrestasi_upahkerja').firstChild;  
    var trf= document.getElementById('ftPrestasi_tarif').firstChild;
    var blk= document.getElementById('ftPrestasi_kodeorg').firstChild;
    var tanggal=document.getElementById('tanggal').value;  
    var bjrak= document.getElementById('bjraktual').value;
    var hslkrj =document.getElementById('hasilkerja').value;
    var param = "nik="+nik.options[nik.selectedIndex].value+'&tanggal='+tanggal+'&jmlhJjg='+hslkrj;
    param+= "&tarif="+trf.options[trf.selectedIndex].value+'&bjraktual='+bjrak+'&blok='+blk.options[blk.selectedIndex].value;
    

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
					upahkerja.value = 0;
					document.getElementById('norma').value=0;
                    document.getElementById('upahpremi').value=0;
                    document.getElementById('hasilkerjakg').value=0;
                } else {
                    //=== Success Response
                    var isi= con.responseText.split("####");
                    upahkerja.value = parseFloat(isi[0]).toFixed(2) ;
                    document.getElementById('norma').value=isi[1];
                    document.getElementById('upahpremi').value=isi[2];
                    document.getElementById('hasilkerjakg').value=isi[3];
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_panen_detail.php?proses=updUpah', param, respon);
}
function updDenda(tipDenda){
    strUrl2='';
    var tanggal=document.getElementById('tanggal').value;  
    var bjrak= document.getElementById('bjraktual').value;
    var blk= document.getElementById('ftPrestasi_kodeorg').firstChild;
    var trf= document.getElementById('ftPrestasi_tarif').firstChild;
    var dend= document.getElementById('rupiahpenalty').value;
    var param = 'tanggal='+tanggal+'&tipeDenda='+tipDenda+'&totDenda='+dend+'&blok='+blk.options[blk.selectedIndex].value;
        param+= "&tarif="+trf.options[trf.selectedIndex].value+'&bjraktual='+bjrak;
        for(d=1;d<8;d++){
            try{
                 if(strUrl2!=''){
                     strUrl2+='&isiDt['+d+']='+document.getElementById('penalti'+d).value;
                 }else{
                     strUrl2+='&isiDt['+d+']='+document.getElementById('penalti'+d).value;
                 }
                }
                catch(e){}
             }
         param+=strUrl2;
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById('rupiahpenalty').value=parseFloat(con.responseText).toFixed(2);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_panen_detail.php?proses=updDenda', param, respon);
}
function detailPDF(numRow,ev) {
    // Prep Param
    var notransaksi = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    param = "proses=pdf&tipe=PNN"+"&notransaksi="+notransaksi;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='kebun_slave_operasional_print_detail_panen.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}


function printPDF(ev,tipe) {
    // Prep Param
    param = "proses=pdf&tipe=PNN";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='kebun_slave_operasional_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}
function getKbn()
{
    //alert('masuk');
    pt=document.getElementById('pt').options[document.getElementById('pt').selectedIndex].value;
    param='pt='+pt+'&proses=getKbn';
    tujuan='kebun_slave_2panen.php';
     function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                   document.getElementById('gudang').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text(tujuan, param, respon);
    
}

function getKbn_1()
{
//    alert('masuk');
    pt=document.getElementById('pt_1').options[document.getElementById('pt_1').selectedIndex].value;
    param='pt='+pt+'&proses=getKbn';
    tujuan='kebun_slave_2panen.php';
     function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                   document.getElementById('unit_1').innerHTML = con.responseText;
                   bersih_1();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(tujuan, param, respon);    
}

function getKbn_2()
{
//    alert('masuk');
    pt=document.getElementById('pt_2').options[document.getElementById('pt_2').selectedIndex].value;
    param='pt='+pt+'&proses=getKbn';
    tujuan='kebun_slave_2panen.php';
     function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                   document.getElementById('unit_2').innerHTML = con.responseText;
                   bersih_2();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text(tujuan, param, respon);
    
}

function getKbn_3()
{
//    alert('masuk');
    pt=document.getElementById('pt_3').options[document.getElementById('pt_3').selectedIndex].value;
    param='pt='+pt+'&proses=getKbn';
    tujuan='kebun_slave_2panen.php';
     function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                   document.getElementById('unit_3').innerHTML = con.responseText;
                   bersih_3();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text(tujuan, param, respon);
    
}

function zDetail(ev,tujuan,passParam)
{
	var passP = passParam.split('##');
	var param = "";
	 for(i=0;i<passP.length;i++) {
       // var tmp = document.getElementById(passP[i]);
	   	a=i;
        param += "&"+passP[a]+"="+passP[i+1];
    }
	param+='&proses=getDetail';
	judul="Detail ";
	//alert(param);
	printFile(param,tujuan,judul,ev)
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='800';
   height='550';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
function printFile2(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='450';
   height='350';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog2(title,content,width,height,ev); 	
}

function filterKaryawan(val)
{
       
if(val!='null')
   param='afd='+val+'&tipe=afdeling';
else
    {        
     val=document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value; 
     param='afd='+val+'&tipe=unit';
    }
       post_response_text('kebun_slave_panen_detail.php?proses=gatKarywanAFD', param, respon);     


     function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById('nik').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }   
}

/**
 * allPtKaryawan
 * Show semua karyawan di dalam 1 PT
 * @param	string	idElement	ID dari element yang akan direplace optionnya
 * @param	object	check		Object Checkbox
 */
function allPtKaryawan(idElement, check) {
	kodeorg=document.getElementById('kodeorg').value;
	if(check.checked==true) {
		param='kodeorg='+kodeorg+'&tipe=all';
	} else {
		param='kodeorg='+kodeorg+'&tipe=default';
	}
	post_response_text('kebun_slave_operasional_karyawan.php?proses=getAllPt', param, respon);     
	
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById(idElement).innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function validat(tipe,tipeVal,ev)
{
  key=getKey(ev);
  if(key==13){
    searchTrans(tipe,tipeVal);
  } else {
  return tanpa_kutip(ev);	
  }	
}