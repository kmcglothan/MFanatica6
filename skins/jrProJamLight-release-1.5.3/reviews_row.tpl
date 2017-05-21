{if isset($_params.module)}
    {assign var="active_mod" value=$_params.module}
{/if}
{if isset($_items)}
    {jrCore_module_url module=$active_mod assign="murl"}
    {foreach from=$_items item="item"}
        {if $_params.module == 'jrVideo'}
            {assign var="media_title_url" value=$item.video_title_url}
            {assign var="media_title" value=$item.video_title}
            {assign var="com_count" value=$item.video_comment_count}
        {else}
            {assign var="media_title_url" value=$item.audio_title_url}
            {assign var="media_title" value=$item.audio_title}
            {assign var="com_count" value=$item.audio_comment_count}
        {/if}
        <div class="body_5 page" style="padding:5px; margin-bottom:5px; margin-right:10px;">
            <h3 style="font-weight:normal;">
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$media_title_url}">{$media_title|truncate:35:"...":false}</a>
            </h3>
            <div style="font-size:12px;">{$item._created|jrCore_date_format}</div>
            <div style="font-size:11px;"><span class="highlight-txt"><i>By:</i></span>&nbsp;<a href="{$item.profile_url}"><span class="capital">{$item.profile_name}</span></a></div>
            {if jrCore_module_is_active('jrComment')}
                <br>
                <div class="float-right" style="padding-right:5px;">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$media_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$com_count|default:0}</a>
                </div>
                <div class="clear"></div>
            {/if}
        </div>
    {/foreach}
{else}
    <div class="body_5 page" style="padding:5px; margin-bottom:5px; margin-right:10px;">
        {jrCore_lang skin=$_conf.jrCore_active_skin id="166" default="Sorry! There are no reviews to list."}
    </div>
{/if}
