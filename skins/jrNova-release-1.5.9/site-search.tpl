{assign var="selected" value="search"}
{assign var="no_inner_div" value="true"}
{jrCore_include template="header.tpl"}

<div class="container">

    {* FEATURED TABS *}
    <div class="row">

        <div class="col12 last">

            <div class="inner center p10 mb8">

                {jrCore_lang module="jrSearch" id="1" default="Search Site" assign="st"}
                <h1>{$st}</h1><br>
                <br>
                {jrSearch_form class="form_text" value=$st style="width:50%"}

            </div>

        </div>

    </div>


</div>

{jrCore_include template="footer.tpl"}

