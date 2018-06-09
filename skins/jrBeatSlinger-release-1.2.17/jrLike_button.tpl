{if $action == 'like'}

    <div id="l{$unique_id}" class="like_button_box">

        {if $like_status == 'like'}

            {* this user can "like" the item *}
            {jrCore_lang module="jrLike" id=4 default="Like" assign="title"}
            {if $_conf.jrLike_require_login == 'on' && !jrUser_is_logged_in()}
                {jrCore_module_url module="jrUser" assign="uurl"}
                <a href="{$jamroom_url}/{$uurl}/login">{jrCore_image module="jrLike" image="like.png" width="24" height="24" class="like_button_img" alt=$title title=$title}</a>
            {else}
                <a onclick="jrLike_action('{$module_url}', '{$item._item_id}', 'like', '{$unique_id}');">{jrCore_image module="jrLike" image="like.png" width="24" height="24" class="like_button_img" alt=$title title=$title}</a>
            {/if}


        {elseif $like_status == 'liked'}

            {* user already liked this *}
            {jrCore_lang module="jrLike" id=6 default="You Liked This!" assign="title"}
            {if $_conf.jrLike_require_login == 'on' && !jrUser_is_logged_in()}
                {jrCore_module_url module="jrUser" assign="uurl"}
                <a href="{$jamroom_url}/{$uurl}/login">{jrCore_image module="jrLike" image="liked.png" width="24" height="24" class="like_button_img" alt=$title title=$title}</a>
            {else}
                <a onclick="jrLike_action('{$module_url}', '{$item._item_id}', 'like', '{$unique_id}');">{jrCore_image module="jrLike" image="liked.png" width="24" height="24" class="like_button_img" alt=$title title=$title}</a>
            {/if}

        {else}

            {* user not allowed liked this *}
            {jrCore_lang module="jrLike" id=4 default="Like" assign="title"}
            {jrCore_image module="jrLike" image="`$like_status`.png" width="24" height="24" class="like_button_img" alt=$title title=$title}

        {/if}

        {if $like_count > 0}
            <span id="lc{$unique_id}" class="like_count"><a onclick="jrLike_get_like_users(this,'{$module}','{$item._item_id}','like','{$unique_id}')">{$like_count|number_format}</a></span>
        {else}
            <span id="lc{$unique_id}" class="like_count">0</span>
        {/if}

    </div>

{elseif $action == 'dislike'}

    <div id="d{$unique_id}" class="dislike_button_box">

        {if $dislike_status == 'dislike'}

            {* this user is allowed to dislike this *}
            {jrCore_lang module="jrLike" id=5 default="Dislike" assign="title"}
            {if $_conf.jrLike_require_login == 'on' && !jrUser_is_logged_in()}
                {jrCore_module_url module="jrUser" assign="uurl"}
                <a href="{$jamroom_url}/{$uurl}/login">{jrCore_image module="jrLike" image="`$dislike_status`.png" width="24" height="24" class="dislike_button_img" alt=$title title=$title}</a>
            {else}
                <a onclick="jrLike_action('{$module_url}', '{$item._item_id}', 'dislike', '{$unique_id}');">{jrCore_image module="jrLike" image="`$dislike_status`.png" width="24" height="24" class="dislike_button_img" alt=$title title=$title}</a>
            {/if}

        {elseif $dislike_status == 'disliked'}

            {* user already disliked this *}
            {jrCore_lang module="jrLike" id=7 default="You Disliked This!" assign="title"}
            {if $_conf.jrLike_require_login == 'on' && !jrUser_is_logged_in()}
                {jrCore_module_url module="jrUser" assign="uurl"}
                <a href="{$jamroom_url}/{$uurl}/login">{jrCore_image module="jrLike" image="`$dislike_status`.png" width="24" height="24" class="dislike_button_img" alt=$title title=$title}</a>
            {else}
                <a onclick="jrLike_action('{$module_url}', '{$item._item_id}', 'dislike', '{$unique_id}');">{jrCore_image module="jrLike" image="`$dislike_status`.png" width="24" height="24" class="dislike_button_img" alt=$title title=$title}</a>
            {/if}

        {else}

            {* user not allowed to dislike *}
            {jrCore_lang module="jrLike" id=5 default="Dislike" assign="title"}
            {jrCore_image module="jrLike" image="`$dislike_status`.png" width="24" height="24" class="dislike_button_img" alt=$title title=$title}

        {/if}

        {if $dislike_count > 0}
            <span id="dc{$unique_id}" class="dislike_count"><a onclick="jrLike_get_like_users(this,'{$module}','{$item._item_id}','dislike','{$unique_id}')">{$dislike_count|number_format}</a></span>
        {else}
            <span id="dc{$unique_id}" class="dislike_count">0</span>
        {/if}

    </div>
{/if}

<div id="likers-{$unique_id}" class="search_box likers_box">
    <div id="liker_list_{$unique_id}" class="liker_list"></div>
    <div class="clear"></div>
    <div style="position:absolute;right:6px;bottom:6px">
        <a class="simplemodal-close">{jrCore_icon icon="close" size="16"}</a>
    </div>
</div>

<div id="like-state-{$unique_id}" style="display:none"></div>
