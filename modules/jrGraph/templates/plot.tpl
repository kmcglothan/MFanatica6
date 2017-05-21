{if isset($_post.standalone)}
    <!doctype html>
<html>
<head>
    <script type="text/javascript" src="{jrCore_javascript_src}"></script>
</head>
<body>
{/if}

<style type="text/css">
    /* correction for basic_options graph*/
    .legend table {
        width: auto;
        border-width: 5px;
        border-spacing: 5px;
    }
</style>

<script type="text/javascript">
    {$flot.extra_js}
    $(document).ready(function () {
        $.plot("#g{$unique_id}", {$flot.data|json_encode}, {$flot.options|json_encode});
    });
</script>

{$flot.extra_html}




<div id="g{$unique_id}" class="graph-holder" style="width:{$width};height:{$height}"></div>
<div id="l{$unique_id}" class="graph-legend"></div>
<div id="t{$unique_id}" class="p5 notice graph-tooltip" style="position:absolute;display:none"></div>

{if isset($_post.standalone)}
</body>
</html>
{/if}
