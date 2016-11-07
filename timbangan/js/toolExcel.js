/**
 * @author {nangkoel gutul et nangkoel@gmail.com}
 */
function submitExcel2007()
{
	obj=document.getElementById('filex');
	ftype=obj.value.substr((obj.value.length-4),4).toLowerCase();
	if(ftype=='.xls' || ftype=='xlsx')
	{
		frm.submit();
		parent.document.getElementById('result').style.display='';
	}
	else
	{
		alert('Filetype not supported');
	}	
}
