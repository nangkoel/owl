function hapusDph(x)
{
        if(confirm('Anda yakin menghapus DPH No.'+x+' ?'))
        {
          url='pchs_hapus_dph.php?no_dph='+x;
          document.body.style.cursor='progress';
          document.getElementById('progress').style.display='';
          //document.write(url);
          request(url);
        }
}
function request(sUrl)
{
        var oScript = document.createElement("script");
        oScript.src = sUrl;
        document.body.appendChild(oScript);
}
function selesai_hapus()
{
      window.location.reload();
}
function hapus_gagal()
{
          document.body.style.cursor='default';
          document.getElementById('progress').style.display='none';
          alert('Gagal menghapus DPH');
}
