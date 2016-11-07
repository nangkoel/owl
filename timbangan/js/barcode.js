/**
 * @author {alex.hutagalung at afhronaldo(at)yahoo(dot)com}
 */
function show_progressx()
{
        document.getElementById('progress').style.display='';
        document.getElementById('btn').disabled=true;
}
function hide_progressx()
{
        document.getElementById('progress').style.display='none';
        document.getElementById('btn').disabled=false;
}
function gogo()
{
        _var=document.getElementById('subject').options[document.getElementById('subject').selectedIndex].value;
		
            show_progressx();
			param = 'subject=' + _var;
			post_response_text('barcode_masterdata_proses.php', param, respo);
		
		
}
function respo()
{
     if(con.readyState==4)
     {
        if(con.status==200)
        {
          //alert(con.responseText);
          document.getElementById('temp').innerHTML=con.responseText;
           hide_progressx();
           }
        else
        {
          error_catch(con.status);
            hide_progressx();
        }
     }
}

function saveMasterWil(){
    wil_code = document.getElementById('wil_code').value;
    wil_name = document.getElementById('wil_name').value;
    wil_manager = document.getElementById('wil_manager').value;
    //num  = document.getElementById('number').value;
	if (trim(wil_code) != '' && wil_name != '' && wil_manager != '') {
        if (confirm('Are you sure..?')) {
            param = 'code=' + wil_code + '&name=' + wil_name + '&manager=' + wil_manager;
			//param+= '&num='+num;
            post_response_text('barcode_saveMasterWil.php', param, respon);
        }
    }
    else {
        alert('Semua Field Harus Diisi');
    }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (con.responseText.lastIndexOf('Gagal') > -1 || con.responseText.lastIndexOf('ror') > -1 || con.responseText.lastIndexOf('rning') > -1) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('result').innerHTML = con.responseText;
                    clearMasterWil();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearMasterWil(){
    document.getElementById('note').innerHTML = 'Create ';
    document.getElementById('wil_code').value = '';
	document.getElementById('wil_name').value = '';
	document.getElementById('wil_manager').value='';
	document.getElementById('wil_code').disabled=false;
	//document.getElementById('code').disabled=false;
}

function changeMasterWil(code, name, manager){

    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('wil_code').value = code;
	document.getElementById('wil_name').value = name;
	document.getElementById('wil_manager').value = manager;
	//document.getElementById('number').value=urut;
	document.getElementById('wil_code').disabled=true;
	//document.getElementById('code').disabled=true;
}

function deleteMasterWil(code,name)
{
	param='code='+code+'&name='+name+'&del=true';
	if(confirm('Are you sure..?'))
	   post_response_text('barcode_saveMasterWil.php', param, respon);
       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('result').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}


function saveMasterComp(){
    comp_code = document.getElementById('comp_code').value;
    comp_name = document.getElementById('comp_name').value;
    
	if (trim(comp_code) != '' && comp_name != '') {
        if (confirm('Are you sure..?')) {
            param = 'code=' + comp_code + '&name=' + comp_name;
			post_response_text('barcode_saveMasterComp.php', param, respon);
        }
    }
    else {
        alert('Semua Field Harus Diisi');
    }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (con.responseText.lastIndexOf('Gagal') > -1 || con.responseText.lastIndexOf('ror') > -1 || con.responseText.lastIndexOf('rning') > -1) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('result').innerHTML = con.responseText;
                    clearMasterComp();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearMasterComp(){
    document.getElementById('note').innerHTML = 'Create ';
    document.getElementById('comp_code').value = '';
	document.getElementById('comp_name').value = '';
	document.getElementById('comp_code').disabled=false;
	//document.getElementById('code').disabled=false;
}

function changeMasterComp(code, name){

    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('comp_code').value = code;
	document.getElementById('comp_name').value = name;
	document.getElementById('comp_code').disabled=true;
	}

function deleteMasterComp(code)
{
	param='code='+code+'&del=true';
	if(confirm('Are you sure..?'))
	   post_response_text('barcode_saveMasterComp.php', param, respon);
       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('result').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}


function saveMasterUnit()
{
    opt_wil = document.getElementById('opt_wil').value;
    opt_comp = document.getElementById('opt_comp').value;
	unit_code = document.getElementById('unit_code').value;
	unit_name = document.getElementById('unit_name').value;
	manager = document.getElementById('manager_name').value;
	kasie = document.getElementById('kasie_name').value;
	
    
	if (trim(opt_wil) != '' && opt_comp != '' && unit_code != '' && unit_name != '' && manager != '' && kasie != '') {
        if (confirm('Are you sure..?')) {
            param = 'wil=' + opt_wil + '&comp=' + opt_comp + '&code=' + unit_code + '&name=' + unit_name + '&manager=' + manager + '&kasie=' + kasie;
			post_response_text('barcode_saveMasterUnit.php', param, respon);
        }
    }
    else {
        alert('Semua Field Harus Diisi');
    }
     function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (con.responseText.lastIndexOf('Gagal') > -1 || con.responseText.lastIndexOf('ror') > -1 || con.responseText.lastIndexOf('rning') > -1) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('result').innerHTML = con.responseText;
                    clearMasterUnit();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearMasterUnit(){
    document.getElementById('note').innerHTML = 'Create ';
    document.getElementById('opt_wil').value = '';
	document.getElementById('opt_comp').value = '';
	document.getElementById('unit_code').value = '';
	document.getElementById('unit_name').value = '';
	document.getElementById('manager_name').value = '';
	document.getElementById('kasie_name').value = '';
	document.getElementById('unit_code').disabled=false;
	document.getElementById('opt_wil').disabled=false;
	document.getElementById('opt_comp').disabled=false;
	//document.getElementById('code').disabled=false;
}

function changeMasterUnit(unit,wilayah,company,name,manager,kasie){
	document.getElementById('unit_code').disabled=false;
	document.getElementById('opt_wil').disabled=false;
	document.getElementById('opt_comp').disabled=false;
    document.getElementById('note').innerHTML = 'Edit ';
	document.getElementById('opt_wil').value = wilayah;
	document.getElementById('opt_comp').value = company;
	document.getElementById('unit_code').value = unit;
	document.getElementById('unit_name').value = name;
	document.getElementById('manager_name').value = manager;
	document.getElementById('kasie_name').value = kasie;
	document.getElementById('opt_wil').disabled=true;
	document.getElementById('opt_comp').disabled=true;
	document.getElementById('unit_code').disabled=true;
	}

function deleteMasterUnit(code,wilayah,company)
{
	param='code='+code+'&wilayah='+wilayah+'&company='+company+'&del=true';
	if(confirm('Are you sure..?'))
	   post_response_text('barcode_saveMasterUnit.php', param, respon);
       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('result').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function saveMasterDiv()
{
    opt_unit = document.getElementById('opt_unit').value;
  	div_code = document.getElementById('div_code').value;
	division_code = document.getElementById('division_code').value;
	division_name = document.getElementById('division_name').value;
	assistant = document.getElementById('assistant_name').value;
		
    
	if (trim(opt_unit) != '' && div_code != '' && division_code != '' && division_name != '' && assistant != '') {
        if (confirm('Are you sure..?')) {
            param = 'unit=' + opt_unit + '&div=' + div_code + '&division_code=' + division_code + '&name=' + division_name + '&assistant=' + assistant;
			post_response_text('barcode_saveMasterDiv.php', param, respon);
        }
    }
    else {
        alert('Semua Field Harus Diisi');
    }
     function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (con.responseText.lastIndexOf('Gagal') > -1 || con.responseText.lastIndexOf('ror') > -1 || con.responseText.lastIndexOf('rning') > -1) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('result').innerHTML = con.responseText;
                    clearMasterDiv();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearMasterDiv(){
    document.getElementById('note').innerHTML = 'Create ';
    document.getElementById('opt_unit').value = '';
	document.getElementById('div_code').value = '';
	document.getElementById('division_code').value = '';
	document.getElementById('division_name').value = '';
	document.getElementById('assistant_name').value = '';
	document.getElementById('opt_unit').disabled=false;
	document.getElementById('div_code').disabled=false;
	
}

function changeMasterDiv(unit,code,division,name,assistant){
	document.getElementById('opt_unit').disabled=false;
	document.getElementById('div_code').disabled=false;
	document.getElementById('note').innerHTML = 'Edit ';
	document.getElementById('opt_unit').value = unit;
	document.getElementById('div_code').value = code;
	document.getElementById('division_code').value = division;
	document.getElementById('division_name').value = name;
	document.getElementById('assistant_name').value = assistant;
	document.getElementById('opt_unit').disabled=true;
	document.getElementById('div_code').disabled=true;
	}

function deleteMasterDiv(unit,code)
{
	param='unit='+unit+'&div='+code+'&del=true';
	if(confirm('Are you sure..?'))
	   post_response_text('barcode_saveMasterDiv.php', param, respon);
       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('result').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}