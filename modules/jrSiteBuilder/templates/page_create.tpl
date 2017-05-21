{jrCore_page_title title="Create new Page?"}

{jrCore_include template="header.tpl"}

<div class="container">

    <div class="row">
        <div class="col12 last">
            <div class="block center">
                <div class="title" style="padding:30px;">

                    <h1>This page does not exist</h1>
                    <br><br>
                    {if strlen($create_notice) > 0}
                        {$create_notice}
                    {/if}
                    Would you like to create this page in Site Builder?
                    <br><br>
                    <input type="button" class="form_button" value="Yes - Create This Page" onclick="jrSiteBuilder_create_and_edit_page()">

                </div>
            </div>
        </div>
    </div>

</div>

{jrCore_include template="footer.tpl"}
