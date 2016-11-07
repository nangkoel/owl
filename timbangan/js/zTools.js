/* Function autoFill
 * Fungsi untuk mengisi element dengan suatu nilai
 * I : element,nilai
 * O : element dengan value val
 */
function autoFill(id,val) {
    // Check if element exist
    if(!id) {
        alert('DOM Definition Error');
        exit;
    }
    
    if(id.options) {
        // Options Element
        var index = 0;
        for(i=0;i<id.options.length;i++) {
            if(id.options[i].value==val) {
                id.selectedIndex = i;
                break;
            }
        }
    } else if(id.getAttribute('type')=='checkbox') {
        // Options Checkbox
        if(val==0) {
            id.checked = true;
        } else {
            id.checked = false;
        }
    } else {
        // Options Text
        id.value = val;
    }
}