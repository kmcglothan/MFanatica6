{jrCore_module_url module="jrFAQ" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="item">

            <h2>{$item.faq_category}</h2>
            {capture name="row_template" assign="faq_list_row"}
                {literal}
                    {jrCore_module_url module="jrFAQ" assign="murl"}
                    {if isset($_items)}
                        {foreach from=$_items item="item"}
                        <div id="{$item._item_id}-{$item.faq_question_url}" class="item">
                            <div class="block_config">
                                {jrCore_item_list_buttons module="jrFAQ" item=$item}
                            </div>
                            &nbsp;&nbsp;{$item@iteration}.&nbsp;<a onclick="$('#faq_{$item._item_id}').slideToggle(250);"><h3>{$item.faq_question}</h3></a>&nbsp;&dArr;
                        <div class="clear"></div>
                        </div>
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
                        {/foreach}
                    {/if}
                {/literal}
            {/capture}

            {jrCore_list module="jrFAQ" profile_id=$item._profile_id order_by="faq_display_order numerical_asc" search1="faq_category = `$item.faq_category`" limit="50" template=$faq_list_row}
        </div>

    {/foreach}
{/if}
