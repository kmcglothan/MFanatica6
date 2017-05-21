/**
 * If any of the DELETE checkboxes are checked make sure the user knows
 * they are deleting the whole item, not just the field value.
 * @returns {*}
 */
function jrBatchEdit_confirm_delete()
{
    var a = false;
    $("input[type='checkbox']").each(function() {
        if ($(this).is(":checked")) {
            a = true;
        }
    });
    if (a) {
        return confirm("Are you sure you want to delete these items?  All data will be removed!");
    }
    return true;
}

/**
 * Check all the delete checkboxes
 */
function jrBatchEdit_delete_checkbox_all(){
    if($('#delete_all').prop('checked')){
        $(".delete_id").prop('checked','checked');
    }else{
        $(".delete_id").prop('checked','');
    }
}