{if jrCore_module_is_active('jrComment')}

    {if isset($_post.option) && $_post.option == 'videos'}
        {assign var="com_mod" value="jrVideo"}
        {assign var="search_tag" value="video"}
    {else}
        {assign var="com_mod" value="jrAudio"}
        {assign var="search_tag" value="audio"}
    {/if}
    {jrCore_list module=$com_mod order_by="`$search_tag`_comment_count NUMERICAL_DESC" search1="`$search_tag`_comment_count > 0" limit="5" template="reviews_row.tpl"}

{else}

    {if jrUser_is_master() || jrUser_is_admin()}
        <div class="body_5 page" style="padding:5px; margin-bottom:5px; margin-right:10px;">
            <div class="highlight-txt center">
                Install and Activate the<br>
                jrComment Module!<br>
                <a href="{$jamroom_url}/core/admin/global">Control Panel</a>
            </div>
        </div>
    {/if}

{/if}
