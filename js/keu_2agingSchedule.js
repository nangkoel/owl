/**
 * @author repindra.ginting
 */

function getUsiaHutang()
{
	pt=document.getElementById('pt');
        pil=document.getElementById('pilDt');
	gudang  =document.getElementById('gudang');
	tanggal =document.getElementById('tanggal').value;
	tanggalpivot =document.getElementById('tanggalpivot').value;
		ptV		=pt.options[pt.selectedIndex].value;
                pilD		=pil.options[pil.selectedIndex].value;
		gudangV	=gudang.options[gudang.selectedIndex].value;

	param='pt='+ptV+'&gudang='+gudangV+'&tanggal='+tanggal+'&tanggalpivot='+tanggalpivot+'&pilDt='+pilD;
	tujuan='keu_laporanUsiaHutang.php';
	post_response_text(tujuan, param, respog);
//	alert(tujuan+param);
	
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

function fisikKeExcel(ev,tujuan)
{
	pt	=document.getElementById('pt');
         pil=document.getElementById('pilDt');
	gudang  =document.getElementById('gudang');
	tanggal =document.getElementById('tanggal').value;
	tanggalpivot =document.getElementById('tanggalpivot').value;
		pt		=pt.options[pt.selectedIndex].value;
                 pilD		=pil.options[pil.selectedIndex].value;
		gudang	=gudang.options[gudang.selectedIndex].value;
//		periode	=periode.options[periode.selectedIndex].value;
	judul='Report Ms.Excel';	
	param='pt='+pt+'&gudang='+gudang+'&tanggal='+tanggal+'&tanggalpivot='+tanggalpivot+'&pilDt='+pilD;
	printFile(param,tujuan,judul,ev)	
}

function fisikKePDF(ev,tujuan)
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	tanggal =document.getElementById('tanggal').value;
	tanggalpivot =document.getElementById('tanggalpivot').value;
		pt		=pt.options[pt.selectedIndex].value;
		gudang	=gudang.options[gudang.selectedIndex].value;
//		periode	=periode.options[periode.selectedIndex].value;
	judul='Report PDF';	
	param='pt='+pt+'&gudang='+gudang+'&tanggal='+tanggal+'&tanggalpivot='+tanggalpivot;
	printFile(param,tujuan,judul,ev)	
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='900';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function lihattagihan(noinvoice,ev)
{
   param='noinvoice='+noinvoice;
   tujuan='keu_slave_laporanusiahutang.php'+"?"+param;  
   width='600';
   height='100';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog2('Data Tagihan '+noinvoice,content,width,height,ev); 
	
}


function ambilAnak(pt)
{
	param='pt='+pt+'&method=getUnit';
	tujuan='keu_laporanUsiaHutang.php';
	post_response_text(tujuan, param, respog);
	
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('gudang').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
	
}

function firstDateofYear(){
    date('d-m-Y');
}

function printVp(numRow,ev){
    var novp = document.getElementById('novp_'+numRow).innerHTML;
//    alert(novp);
    param = "proses=pdf&novp="+novp;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='keu_slave_vp_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}