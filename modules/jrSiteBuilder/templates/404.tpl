<div class="error-404">
    <h1>404 template not found</h1><br/>
    The template is set for this widget, but that template was not found in the skin. <br/>
    <br/>
    {if strlen($tpl_location) > 2}
        Add a template to:
        <br/>
        {$tpl_location}
    {/if}
    {if strlen($_params.template) > 2}
        The name used was:
        <br/>
        {$_params.template}
    {/if}


    {if strlen($jr_template) > 2}
        template: {$jr_template}
        <br/>
        {$tpl_location}
    {/if}

    {if strlen($jr_template_directory) > 2}
        directory: {$jr_template_directory}
        <br/>
    {/if}
</div>