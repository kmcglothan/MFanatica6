{* show the structure of the widget *}

<div class="page_section_header">
    Template Code
</div>
<div id="template_structure" class="widget-template-code fixed-width"></div>

<script type="text/javascript">

    $(document).ready(function()
    {
        var module = $("#list_module").val();
        if (module.length > 1) {
            jrCore_update_template_structure();
        }
    });

    $("#jrSiteBuilder_modify_widget_form").change(function()
    {
        var active = $('#list_module').val();
        if (typeof window.sbamodule === "undefined") {
            window.sbamodule = active;
        }
        if (active == window.sbamodule) {
            jrCore_update_template_structure();
        }
        else {
            window.sbamodule = active;
        }
    });

    function jrCore_update_template_structure()
    {
        var module = $("#list_module").val();
        var order_by = $("#list_order_by_key").val();
        var direction = $("#list_order_by_dir").val();
        var limit = $("#list_limit").val();
        var pagebreak = $("#list_pagebreak").val();
        var group_by = $("#list_group_by").val();
        var template = $("#list_template").val();
        var searches = $("select[name='list_search_key[]']");
        var searches_op = $("select[name='list_search_op[]']");
        var searches_val = $("input[name='list_search_val[]']");
        var operator = '';

        var structure = '{ldelim}jrCore_list';
        if (module != null && module.length > 0) {
            structure += ' module="' + module + '"';
        }
        $.each(searches, function(index, thing)
        {
            if (thing.value.length > 2 && searches_val[index].value.length > 0) {
                switch (searches_op[index].value) {
                    case 'eq':
                        operator = '= ' + searches_val[index].value;
                        break;
                    case 'neq':
                        operator = '!= ' + searches_val[index].value;
                        break;
                    case 'lt':
                        operator = '< ' + searches_val[index].value;
                        break;
                    case 'gt':
                        operator = '> ' + searches_val[index].value;
                        break;
                    case 'like':
                        if (searches_val[index].value.indexOf('%') > -1) {
                            operator = 'like ' + searches_val[index].value;
                        }
                        else {
                            operator = 'like %' + searches_val[index].value + '%';
                        }
                        break;
                    case 'not_like':
                        if (searches_val[index].value.indexOf('%') > -1) {
                            operator = 'not_like ' + searches_val[index].value;
                        }
                        else {
                            operator = 'not_like %' + searches_val[index].value + '%';
                        }
                        break;
                    case 'bw':
                        if (searches_val[index].value.indexOf('%') > -1) {
                            operator = 'like ' + searches_val[index].value;
                        }
                        else {
                            operator = 'like ' + searches_val[index].value + '%';
                        }
                        break;
                    case 'ew':
                        if (searches_val[index].value.indexOf('%') > -1) {
                            operator = 'like ' + searches_val[index].value;
                        }
                        else {
                            operator = 'like %' + searches_val[index].value;
                        }
                        break;
                    default:
                        operator = searches_op[index].value + ' ' + searches_val[index].value;
                        break;
                }
                structure += ' search' + (index + 1) + '="' + thing.value + ' ' + operator + '"';
            }
        });

        if (order_by != null && order_by.length > 2) {
            structure += ' order_by="' + order_by + ' ' + direction + '"';
        }

        if (limit != null && limit > 0) {
            structure += ' limit="' + limit + '"';
        }

        if (pagebreak != null && pagebreak > 0) {
            structure += ' pagebreak="' + pagebreak + '" pager="true" page=$_post.p ';
        }

        if (group_by != null && group_by.length > 2) {
            structure += ' group_by="' + group_by + '"';
        }

        if (template != null && template.length > 2 && template != 'item_list.tpl' && template != 'custom') {
            structure += ' template="' + template + '" tpl_dir="' + module + '"';
        }
        structure += '{rdelim}';
        $('#template_structure').html(structure);

    }

</script>