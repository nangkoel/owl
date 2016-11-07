/*
 Function addData
 Fungsi untuk menambah data ke table
 field = list dari field dengan format '##field1##field2'
 table = nama table
 page = halaman tujuan (slave page)
*/
function addData(field,id,table,page,freeze) {
    if(confirm("You'll add new data\n Are you sure?")) {
        var fieldJs = field.split("##");
        var idJs = id.split("##");
        var param = "tableName=" + table;
        var mTable = document.getElementById('masterTable');
        
        // Get Available numRow
        var numRow = 0;
        var tmpRow = document.getElementById('tr_'+numRow);
        while(tmpRow) {
            numRow++;
            tmpRow = document.getElementById('tr_'+numRow);
        }
        param += "&numRow="+numRow;
        
        // Get Parameter
        var idField = "";
        var idVal = "";
        for(i=1;i<fieldJs.length;i++) {
            tmpField = document.getElementById(fieldJs[i]);
            if(tmpField.options) {
                param += "&" + fieldJs[i] + "=" + tmpField.options[tmpField.selectedIndex].value;
                for(j=1;j<idJs.length;j++){
                    if(fieldJs[i] == idJs[j]) {
                            idField += "##" + fieldJs[i];
                            idVal += "##" + tmpField.options[tmpField.selectedIndex].value;
                    }
                }
            } else if(tmpField.getAttribute('type')=='checkbox') {
                    if(tmpField.checked) {
                            var tmpVal = '1';
                    } else {
                            var tmpVal = '0';
                    }
                    param += "&" + fieldJs[i] + "=" + tmpVal;
                    for(j=1;j<idJs.length;j++){
                            if(fieldJs[i] == idJs[j]) {
                                    idField += "##" + fieldJs[i];
                                    idVal += "##" + tmpVal;
                            }
                    }
            } else {
                    param += "&" + fieldJs[i] + "=" + document.getElementById(fieldJs[i]).value;
                    for(j=1;j<idJs.length;j++){
                            if(fieldJs[i] == idJs[j]) {
                                    idField += "##" + fieldJs[i];
                                    idVal += "##" + tmpField.value;
                            }
                    }
            }
        }
        param += "&idField="+idField+"&idVal="+idVal;
        if(typeof freeze!='undefined') {
                param += "&freeze="+freeze;
        }
        
        // Post to Slave
        if(page==null) {
                page = "slave_master_addR";
        }
        
        // Catch Result
        function respon(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        mTable = document.getElementById('mTabBody');
                                        mTable.innerHTML += con.responseText;
                                        //eval(con.responseText);
                                        clearData(field);
                                        //location.reload(true);
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
        
        post_response_text(page+'.php', param, respon);
    }
}

/* Function showDetail
 * Fungsi untuk pop up form detail
 * I : id table, primary key table
 * P : Ajax menyiapkan keseluruhan halaman detail
 * O : Halaman edit detail shift
 */
function showDetail(num,idStr,event) {
    var IDs = idStr.split('##');
    
    for(i=1;i<IDs.length;i++) {
        tmp = document.getElementById(IDs[i]+"_"+num);
        if(i==1) {
            var param = IDs[i]+"="+tmp.innerHTML;
        } else {
            param += "&"+IDs[i]+"="+tmp.innerHTML;
        }
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    showDialog1('Edit Detail',con.responseText,'800','300',event);
                    var dialog = document.getElementById('dynamic1');
                    dialog.style.top = '137px';
                    dialog.style.left = '30px%';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_5shift.php?proses=showDetail', param, respon);
}