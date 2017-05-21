{jrCore_module_url module="jrFAQ" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="item">

            {if jrUser_is_master()}
            <div class="block_config">
                <a href="{jamroom_url}/faq/update/id={$item._item_id}">{jrCore_icon icon="gear"}</a>&nbsp;
                <a href="{jamroom_url}/faq/delete/id={$item._item_id}">{jrCore_icon icon="trash"}</a>
            </div>
            {/if}

            <h2><a onclick="$('#faq_{$item._item_id}').slideToggle(250);">{$item.faq_question}</a></h2>
            <br>
            <div id="faq_{$item._item_id}" class="form_help" style="display:none;">
                <table class="form_help_drop">
                    <tr>
                        <td class="form_help_drop_left">
                            <div style="max-height:300px;overflow:auto;">
                                {$item.faq_answer}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    {/foreach}
{/if}
