<style type="text/css">
.CodeMirror {
    line-height: 1.2;
    width: 100%;
    margin: 0;
    padding: 0;
}
#template-compare-view {
    margin: 0 auto;
    max-width: 1110px;
}
</style>

<div id="template-compare-view">{* difference view loads into here *}</div>

<input type="hidden" name="template_body" id="template_body" value=""/>
{if isset($skin)}
    <input type="hidden" name="skin" value="{$skin}"/>
{else}
    <input type="hidden" name="module_url" value="{$module_url}"/>
{/if}
{if isset($template_id)}
    <input type="hidden" name="template_id" value="{$template_id}"/>
{/if}
{if isset($template_name)}
    <input type="hidden" name="template_name" value="{$template_name}"/>
{/if}

<script type="text/javascript">
    $(document).ready(function () {
        var v = $('#template-compare-view');
    	v.mergely({
            width: 1110,
            height: 500, // containing div must be given a height
            ignorews: true,
    		cmsettings: {
                lineNumbers: true,
                mode: 'smarty'
            },
    		lhs: function(setValue) {
    			setValue({$code_left});
    		},
    		rhs: function(setValue) {
    			setValue({$code_right});
    		}
    	});
        var cm = v.mergely('cm', 'rhs');
        cm.setOption('readOnly', true);
    });

    function jrCore_compare_get_modified_template() {
        var tpl = $('#template-compare-view').mergely('get', 'lhs');
        $('#template_body').val(tpl)
    }

</script>
