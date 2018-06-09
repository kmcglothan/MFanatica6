<style type="text/css">
    .CodeMirror {
        line-height: 1.2;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    #template-compare-view {
        margin: 0 auto;
        width: 100%;
    }
</style>

<div id="template-compare-view">{* difference view loads into here *}</div>

<input type="hidden" name="template_body" id="template_body" value="">
{if isset($skin)}
    <input type="hidden" name="skin" value="{$skin}">
{else}
    <input type="hidden" name="module_url" value="{$module_url}">
{/if}
{if isset($template_id)}
    <input type="hidden" name="template_id" value="{$template_id}">
{/if}
{if isset($template_name)}
    <input type="hidden" name="template_name" value="{$template_name}">
{/if}

<script type="text/javascript">
function jrCore_compare_get_height()
{
    return ($('body').outerHeight() - $('#template-compare-view').offset().top - $('#footer').outerHeight() - $('.form_submit_section').outerHeight() - 30);
}
function jrCore_compare_cp_resize()
{
    $('#template-compare-view').height(jrCore_compare_get_height());
}
function jrCore_compare_get_modified_template()
{
    var tpl = $('#template-compare-view').mergely('get', 'lhs');
    $('#template_body').val(tpl)
}
$(document).ready(function()
{
    var v = $('#template-compare-view');
    v.mergely({
        width: v.width(),
        height: jrCore_compare_get_height(),
        ignorews: true,
        cmsettings: {
            lineNumbers: true,
            mode: 'smarty',
            lcs: false
        },
        lhs: function(s)
        {
            s({$code_left});
        },
        rhs: function(s)
        {
            s({$code_right});
        }
    });
    var cm = v.mergely('cm', 'rhs');
    cm.setOption('readOnly', true);
    jrCore_compare_cp_resize();
});
</script>
