/* Function addModeForm
 * Fungsi untuk mengubah form header menjadi mode tambah
 * O : form header mode tambah
 */
function validPeriod(id) {
    var startPeriod = document.getElementById('startPeriod').value;
    var endPeriod = document.getElementById('endPeriod').value;
    var currDate = document.getElementById(id).value;
    var tmpCurr = currDate.split('-');
    
    // Get Tgl, Bln, Tahun
    var tglStart = startPeriod.substring(6,8);
    var blnStart = startPeriod.substring(4,6);
    var thnStart = startPeriod.substring(0,4);
    
    var tglEnd = endPeriod.substring(6,8);
    var blnEnd = endPeriod.substring(4,6);
    var thnEnd = endPeriod.substring(0,4);
    
    var tglCurr = tmpCurr[0];
    var blnCurr = tmpCurr[1];
    var thnCurr = tmpCurr[2];
    
    // Make to JS Date
    var jsCurr = new Date(thnCurr,blnCurr,tglCurr);
    var jsStart = new Date(thnStart,blnStart,tglStart);
    var jsEnd = new Date(thnEnd,blnEnd,tglEnd);
    
    var vPeriod = false;
    
    if(jsCurr<=jsEnd && jsCurr>=jsStart) {
        vPeriod = true;
    }
    
    return vPeriod;
}

/* Function addModeForm
 * Fungsi untuk mengubah form header menjadi mode tambah
 * O : form header mode tambah
 */
function addModeForm() {
    var kodejurnal = document.getElementById('kodejurnal');
    var nojurnal = document.getElementById('nojurnal');
    var tanggal = document.getElementById('tanggal');
    var noreferensi = document.getElementById('noreferensi');
    var matauang = document.getElementById('matauang');
    var saveBtn = document.getElementById('saveButton');
    var fieldForm = document.getElementById('fieldFormHeader');
    
    // Remove Disabled
    kodejurnal.removeAttribute('disabled');
    nojurnal.removeAttribute('disabled');
    tanggal.removeAttribute('disabled');
    noreferensi.removeAttribute('disabled');
    matauang.removeAttribute('disabled');
    saveBtn.removeAttribute('disabled');
    
    // Set Attr
    tanggal.setAttribute('onmousemove','setCalendar(this.id)');
    saveBtn.setAttribute('onclick','addDataHeader()');
    fieldForm.firstChild.firstChild.innerHTML = 'Form Header : Tambah Data';
}

/* Function addDataHeader
 * Fungsi untuk menambah data header
 * O : form header mode tambah
 */
function addDataHeader() {
    var nojurnal = document.getElementById('nojurnal');
    var kodejurnal = document.getElementById('kodejurnal');
    var tanggal = document.getElementById('tanggal');
    var noref = document.getElementById('noreferensi');
    var matauang = document.getElementById('matauang');
    var fieldForm = document.getElementById('fieldFormHeader');
    
    // Empty = Not Valid
    if(tanggal.value=='') {
        alert('Tanggal harus diisi');
        exit;
    }
    
    if(validPeriod('tanggal')) {
        var param = "kodejurnal="+getOptionsValue(kodejurnal);
        param += "&tanggal="+tanggal.value;
        param += "&noreferensi="+noref.value;
        param += "&matauang="+getOptionsValue(matauang);
        
        function respon() {
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    } else {
                        // Success Response
                        // Pass Journal No
                        nojurnal.value = con.responseText;
                        
                        // Change Form to Edit Mode
                        fieldForm.firstChild.firstChild.innerHTML = 'Form Header : Ubah Data';
                        nojurnal.setAttribute('disabled','disabled');
                        
                        // Tambah Row Header
                        addHeaderRow();
                        
                        // Show Detail
                        showDetail();
                    }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
        
        post_response_text('keu_slave_jurnal_header.php?proses=add', param, respon);
    } else {
        alert("Tanggal tidak boleh diluar Periode Aktif");
    }
    
}

/* Function addHeaderRow
 * Fungsi untuk menambah row baru hasil penambahan header
 * O : Row baru pada table header
 */
function addHeaderRow() {
    var bodyHeader = document.getElementById('bodyListHeader');
    var nojurnal = document.getElementById('nojurnal');
    var kodejurnal = document.getElementById('kodejurnal');
    var tanggal = document.getElementById('tanggal');
    
    // Search Available numRow
    var numRow = 0;
    while(document.getElementById('tr_'+numRow)) {
        numRow++;
    }
    
    // Prep row
    var kodeVal = kodejurnal.options[kodejurnal.selectedIndex].value;
    var theRow = "<tr onclick='editNDetail()' class='rowcontent'>";
    theRow += "<td id='kodejurnal_"+numRow+"'>"+kodeVal+"</td>";
    theRow += "<td id='nojurnal_"+numRow+"'>"+kodeVal+nojurnal.value+"</td>";
    theRow += "<td id='tanggal_"+numRow+"'>"+tanggal.value+"</td>";
    theRow += "</tr>";
    
    // Insert Row
    bodyHeader.innerHTML += theRow;
}

/* Function showDetail
 * Fungsi untuk menambah row baru hasil penambahan header
 * O : Row baru pada table header
 */
function showDetail() {
    
}