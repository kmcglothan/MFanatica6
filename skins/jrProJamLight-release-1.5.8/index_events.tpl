{* ROW TEMPLATE *}
{capture name="row_template" assign="template"}
    {literal}
        {if isset($_items)}
        {jrCore_module_url module="jrEvent" assign="murl"}
        {foreach from=$_items item="item"}
        <div class="body_5 page" style="padding:5px; margin-bottom:5px;">
            <div style="display:table;">
                <div style="display:table-row;height:42px;">
                    <div style="display:table-cell;text-align:center;vertical-align:top;">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{jrCore_module_function function="jrImage_display" module="jrEvent" type="event_image" item_id=$item._item_id size="medium" crop="auto" width="35" height="35" alt=$item.event_title title=$item.event_title class="iloutline" style="max-width:140px;"}</a>                            </div>
                    <div style="display:table-cell;width:99%;text-align:left;vertical-align:top;padding-left:5px;">
                        <h3 style="font-weight:normal;">
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{if strlen($item.event_title) > 30}{$item.event_title|truncate:30:"...":false}{else}{$item.event_title}{/if}</a>
                        </h3>
                        <div style="font-size:12px;">{$item.event_date|jrCore_date_format}</div>
                        <div style="font-size:11px;"><span class="highlight-txt">{$item.event_location|truncate:30:"...":false}</span></div>
                        {if jrCore_module_is_active('jrComment')}
                        <br>
                        <div class="float-right" style="padding-right:5px;">
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.event_comment_count|default:0}</a>
                        </div>
                        <div class="clear"></div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
        {/foreach}
        {else}
        <div class="body_5 page" style="padding:5px;margin-bottom:5px;margin-right:0;">
            {jrCore_lang skin=$_conf.jrCore_active_skin id="183" default="No Upcoming Events!"}
        </div>
        {/if}
    {/literal}
{/capture}

{* EVENT LIST FUNCTION *}
{if isset($option) && $option === 'upcoming'}
    {assign var="order_by" value="event_date NUMERICAL_ASC"}
{elseif isset($option) && $option === 'newest'}
    {assign var="order_by" value="_created asc"}
{elseif isset($option) && $option === 'featured'}
    {assign var="order_by" value="event_rating_1_average_count NUMERICAL_DESC"}
{/if}

{if isset($option) && $option === 'upcoming'}
    {jrCore_list module="jrEvent" search="event_date >= `$smarty.now`" order_by=$order_by limit="5" template=$template}
{else}
    {jrCore_list module="jrEvent" order_by=$order_by limit="5" template=$template}
{/if}
