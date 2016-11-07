function unit()
	{
		_tanggal=document.getElementById('tgllap').value;
		_unit=document.getElementById('unitcode').options[document.getElementById('unitcode').selectedIndex].value;
		if(_tanggal.length<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else
		if(document.getElementById('unitcode').options[document.getElementById('unitcode').selectedIndex].value == 0) {
			alert('Unit Belum Dipilih');
			document.getElementById('unitcode').options[document.getElementById('unitcode').focus()];
		}
		else
			window.open('lpt_per_unit_result.php?tanggal='+_tanggal+'&unit='+_unit,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}
function divisiZ()
	{
		_tanggal=document.getElementById('tgllap').value;
		_unit=document.getElementById('unitcode').options[document.getElementById('unitcode').selectedIndex].value;
		_div=document.getElementById('divcode').options[document.getElementById('divcode').selectedIndex].value;
		if(_tanggal.length<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else
		if(document.getElementById('unitcode').options[document.getElementById('unitcode').selectedIndex].value == 0) {
			alert('Unit Belum Dipilih');
			document.getElementById('unitcode').options[document.getElementById('unitcode').focus()];
		}
		else
		if(document.getElementById('divcode').options[document.getElementById('divcode').selectedIndex].value == 0) {
			alert('Divisi Belum Dipilih');
			document.getElementById('divcode').options[document.getElementById('divcode').focus()];
		}
		else
			window.open('lpt_per_divisi_result.php?tanggal='+_tanggal+'&unit='+_unit+'&div='+_div,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}
function perJamInt()
	{
		_tanggal=document.getElementById('tgllap').value;
		if(_tanggal.length<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else
			window.open('lpt_monitor_int_result.php?tanggal='+_tanggal,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}
function perTglInt()
	{
		_tanggal=document.getElementById('tgllap').value;
		if(_tanggal.length<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else
			window.open('lpt_per_tgl_int_result.php?tanggal='+_tanggal,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}
function perBln()
	{
		_periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].text;
		//alert(_periode);
		window.open('lpt_per_bln_int_result.php?periode='+_periode,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}
function perTglEks()
	{
		_tanggal=document.getElementById('tgllap').value;
		_tanggal2=document.getElementById('tgllap2').value;
		trp=document.getElementById('supplier');
		trp=trp.options[trp.selectedIndex].value;
		if(_tanggal.length<9 || _tanggal2<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else
			window.open('lpt_per_tgl_eks_result.php?tanggal='+_tanggal+'&tanggal2='+_tanggal2+'&trp='+trp,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}
		
function perJamEks()
	{
		_tanggal=document.getElementById('tgllap').value;
		if(_tanggal.length<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else
			window.open('lpt_monitor_eks_result.php?tanggal='+_tanggal,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}
function lptLain()
	{
		_tanggal=document.getElementById('tgllap').value;
		_product=document.getElementById('product').options[document.getElementById('product').selectedIndex].value;
		if(_tanggal.length<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else
		if(document.getElementById('product').options[document.getElementById('product').selectedIndex].value == 0) {
			alert('Product Belum Dipilih');
			document.getElementById('product').options[document.getElementById('product').focus()];
		}
		else
			window.open('lpt_lain_result.php?tanggal='+_tanggal+'&product='+_product,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}
function rubahX(key,field)
{
	param='key='+key+'&field='+field;
	//alert(param);
	if (field=='Unit'){
		hubungkan_post('load_sipb.php',param,response_product);
	}
	
}
function response_product()
{
	if(con.readyState==4)
     {
        if(con.status==200)
        {
		   //alert(con.responseText);
		   //document.getElementById('SIPBNO').innerHTML=con.responseText;
		   ss=con.responseText.split(",");
		   document.getElementById('SIPBNO').innerHTML=ss[0];
		   
		   unlock();
		}
        else
        {
		  unlock();
          error_catch(con.status);
        }
     }
}
function lptRealisasi()
	{
		_product=document.getElementById('product').options[document.getElementById('product').selectedIndex].value;
		_sipb=document.getElementById('SIPBNO').options[document.getElementById('SIPBNO').selectedIndex].text;
		if(document.getElementById('product').options[document.getElementById('product').selectedIndex].value == 0) {
			alert('Product Belum Dipilih');
			document.getElementById('product').options[document.getElementById('product').focus()];
		}
		else
			window.open('realisasi_sipb_excel.php?product='+_product+'&sipb='+_sipb,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}
function lptNonCpo()
	{
		_tanggal=document.getElementById('tgllap').value;
		_product=document.getElementById('product').options[document.getElementById('product').selectedIndex].value;
		if(_tanggal.length<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else
		if(document.getElementById('product').options[document.getElementById('product').selectedIndex].value == 0) {
			alert('Product Belum Dipilih');
			document.getElementById('product').options[document.getElementById('product').focus()];
		}
		else
			window.open('lpt_non_cpo_result.php?tanggal='+_tanggal+'&product='+_product,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}

	function perTglAll()
	{
		_tanggal=document.getElementById('tgllap').value;
		productcode=document.getElementById('product').options[document.getElementById('product').selectedIndex].value;
		productname=document.getElementById('product').options[document.getElementById('product').selectedIndex].text;
        if(productcode=='')
		{
			alert('Pilih produk');
		} 
		else if(_tanggal.length<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else
			window.open('lpt_per_tgl_all_result.php?tanggal='+_tanggal+'&code='+productcode+'&name='+productname,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}
	
function totalPengeluaran()
	{
		_tanggal=document.getElementById('tgllap').value;
		_product=document.getElementById('product').options[document.getElementById('product').selectedIndex].value;
		if(_tanggal.length<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else
		if(document.getElementById('product').options[document.getElementById('product').selectedIndex].value == 0) {
			alert('Product Belum Dipilih');
			document.getElementById('product').focus();
		}
		else
			window.open('rpt_per_tgl_excel.php?tanggal='+_tanggal+'&product='+_product,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}

function PengirimanHarianPerTgl()
	{
		_tanggal=document.getElementById('tgllap').value;
		_product=document.getElementById('product').options[document.getElementById('product').selectedIndex].value;
		if(_tanggal.length<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else
		if(document.getElementById('product').options[document.getElementById('product').selectedIndex].value == 0) {
			alert('Product Belum Dipilih');
			document.getElementById('product').focus();
		}
		else
			window.open('rpt_pengiriman_harian_per_tgl_excel.php?tanggal='+_tanggal+'&product='+_product,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}

function perTglEksSupplier()
{
		_tanggal=document.getElementById('tgllap').value;
		_kodesupplier=document.getElementById('kodesupplier').options[document.getElementById('kodesupplier').selectedIndex].value;
		_namasupplier=document.getElementById('kodesupplier').options[document.getElementById('kodesupplier').selectedIndex].text;
		if(_tanggal.length<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else if(_kodesupplier==0)
		{
			alert('Pilih Supplier');
		}
		else
		 document.getElementById('ifamku').src="lpt_per_tgl_per_suppl_eks_result.php?tanggal="+_tanggal+'&trpcode='+_kodesupplier+'&namasupplier='+_namasupplier;
	
}

function totalPerUnitPerTglInt()
{
		_tanggal=document.getElementById('tgllap').value;
		if(_tanggal.length<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else
		{
		   param='tanggal='+_tanggal;
			tujuan = 'lpt_per_total_per_tgl_per_unit.php';
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
									document.getElementById('tabcontainer').innerHTML=con.responseText;
								}
							}
							else {
								busy_off();
								error_catch(con.status);
							}
				      }	
			 }		
}

function printBuktiPengiriman()
{
	product=document.getElementById('product').options[document.getElementById('product').selectedIndex].value;
	tanggal=document.getElementById('tgllap').value;
	window.open('dw_printBuktiPengirimanPdf.php?tanggal='+tanggal+'&product='+product);
	
}	

function displayExisting()
{
	product=document.getElementById('product').options[document.getElementById('product').selectedIndex].value;
	tanggal=document.getElementById('tgllap').value;
	param = 'product='+product+'&tanggal='+tanggal;
	hubungkan_post('wb_slave_getExisting.php', param, respogx);	
		function respogx()
		{
		  if(con.readyState==4)
		     {
		        if(con.status==200)
		        {
		         x=con.responseText;
				 if(x.lastIndexOf("rror") > -1) {
				   sta=false;
				   alert('Error'+con.responseText);
				 }
				 else
				 {
				 	document.getElementById('containex').innerHTML=con.responseText;
				 }
				}
		        else
		        {
				  unlock();
		          error_catch(con.status);
		        }
		     }
		}

}

function simpanBukti(tiket,urut)
{
	tiket	=tiket;
	nosegel	=document.getElementById('nosegel'+urut).value;
	air		=document.getElementById('air'+urut).value;
	kotoran	=document.getElementById('kotoran'+urut).value;
	ffa		=document.getElementById('ffa'+urut).value;
	nobuku	=document.getElementById('nobuku'+urut).value;
	kapabrik=document.getElementById('kapabrik'+urut).value;
	manager	=document.getElementById('manager'+urut).value;
	bongkar	=document.getElementById('bongkar'+urut).value;
    
	if(air=='')
	   air=0;
	if(kotoran=='')
	   kotoran=0;
	if(ffa=='')
	   ffa=0;
	         
	param = 'tiket='+tiket+'&nosegel='+nosegel;
	param+= '&air='+air+'&kotoran='+kotoran;
	param+= '&ffa='+ffa+'&nobuku='+nobuku;
	param+= '&kapabrik='+kapabrik+'&manager='+manager+'&bongkar='+bongkar;
	
	hubungkan_post('wb_slave_saveBukti.php', param, respogx);
		
		function respogx()
		{
		  if(con.readyState==4)
		     {
		        if(con.status==200)
		        {
		         x=con.responseText;
				 if(x.lastIndexOf("rror") > -1) {
				   alert('Error'+con.responseText);
				 }
				 else
				 {
				 	//alert('Saved');
					document.getElementById('row'+tiket).style.backgroundColor='darkgreen';
				 }
				}
		        else
		        {
				  unlock();
		          error_catch(con.status);
		        }
		     }
		}
			
}
function printBuktiP(tiket,urut)
{
  window.open('dw_printBuktiPengirimanPdf.php?notiket='+tiket,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
}
function perBlnperUnit()
	{
		_periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
		_unit=document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value;
		if(_periode=='' || _unit==''){
			alert('Data di isi');
			document.getElementById('periode').focus();
		}
		else
			window.open('lpt_per_bln_per_unit_result.php?periode='+_periode+'&unit='+_unit,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}

function perJamAll()
	{
		_tanggal=document.getElementById('tgllap').value;
		if(_tanggal.length<9){
			alert('Tanggal Belum di isi');
			document.getElementById('tgllap').focus();
		}
		else
			window.open('lpt_monitor_all_result.php?tanggal='+_tanggal,'newin','toolbar=yes,addressbar=no,resizable=yes,scrollbars=yes');	
	}