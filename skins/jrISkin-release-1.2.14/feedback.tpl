{jrCore_module_url module="jrUser" assign="uurl"}

{if isset($data.like_profile_url)}
    {$profile_url = $data.like_profile_url}
{elseif isset($data.rating_profile_url)}
    {$profile_url = $data.rating_profile_url}
{elseif isset($data.profile_url)}
    {$profile_url = $data.profile_url}
{else}
    {$profile_url = $item.profile_url}
{/if}

{if !isset($item_id)}
    {$item_id = $item._item_id}
{/if}

<div class="item clearfix">

    {if strlen($module) > 0 && jrCore_module_is_active('jrLike')}

        {if is_array($item.action_data)}
            {$like_module = $item.action_module}
            {$like_item_id = $item.action_item_id}
            {$like_item_data = $item.action_data}
        {else}
            {$like_module = $module}
            {$like_item_id = $item._item_id}
            {$like_item_data = $item}
        {/if}

        {if $item.action_module == 'jrLike' || $item.action_module == 'jrRating' || $item.action_module == 'jrComment'}

            {* likes go to the item *}
            {$like_module = $item.action_original_module}
            {$like_item_id = $item.action_original_item_id}
            {$like_item_data = $item.action_original_data}

        {/if}

        {if $module == 'jrAction'}
            {$like_item_data = $item}
        {/if}


        {if $_conf.jrLike_like_option == 'all' || $_conf.jrLike_like_option == 'like'}
            {jrLike_button item=$like_item_data module=$like_module action="like" item_id=$like_item_id}
        {/if}

        {if $_conf.jrLike_like_option == 'all' || $_conf.jrLike_like_option == 'dislike'}
            {jrLike_button item=$like_item_data module=$like_module action="dislike" item_id=$like_item_id}
        {/if}
    {/if}

    {if jrCore_module_is_active('jrComment')}
        <div class="like_button_box">
            {if jrUser_is_logged_in()}
                {if isset($comment_url) && strlen($comment_url) > 0}

                    {* comment module has constructed URL *}
                    <a href="{$comment_url}">

                {else}

                    {* If this action is an item detail feature we point to the item unless it is another item detail feature *}
                    {if $item.action_module == 'jrLike' || $item.action_module == 'jrRating' || $item.action_module == 'jrComment'}

                        {if $item.action_original_module == 'jrLike' || $item.action_original_module == 'jrRating' || $item.action_original_module == 'jrComment'}

                            {jrCore_module_url module='jrAction' assign="curl"}
                            {if isset($item.action_data.profile_url)}
                                <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$curl}/{$item._item_id}">
                            {else}
                                <a href="{$jamroom_url}/{$item.profile_url}/{$curl}/{$item._item_id}">
                            {/if}

                        {else}

                            {jrCore_module_url module=$item.action_original_module assign="curl"}
                            {if isset($item.action_original_title_url)}
                                <a href="{$jamroom_url}/{$item.action_original_data.profile_url}/{$curl}/{$item.action_original_data._item_id}/{$item.action_original_title_url}">
                            {else}
                                <a href="{$jamroom_url}/{$item.action_original_data.profile_url}/{$curl}/{$item.action_original_data._item_id}">
                            {/if}

                        {/if}

                    {elseif $item.action_module == 'jrGroupDiscuss'}

                        {jrCore_module_url module=$item.action_module assign="curl"}
                        <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$curl}/{$item.action_data._item_id}/{$item.action_data.discuss_title_url}">

                    {elseif strlen($item.action_module) > 0}

                        {jrCore_module_url module=$item.action_module assign="curl"}
                        <a href="{$jamroom_url}/{$item.profile_url}/{$curl}/{$item._item_id}">

                    {else}
                            {jrCore_module_url module=$module assign="curl"}
                            <a href="{$jamroom_url}/{$item.profile_url}/{$curl}/{$item._item_id}">
                    {/if}
                {/if}
            {else}
                <a href="{$jamroom_url}/{$uurl}/login?r=1">
            {/if}
            {jrCore_lang module="jrComment" id="2" default="Post Your Comment" assign='com'}
            {jrCore_image image="comment.png" width="24" height="24" class="like_button_img" alt='Comment' title=$com}</a>
            <span><a>{$comment_count|jrCore_number_format}</a></span>
        </div>
    {/if}

    {if isset($disable_share)}
        <div class="like_button_box">
            {jrCore_lang module="jrAction" id=35 default="You have Shared this with your Followers" assign="title"}
            {jrCore_image image="share_disabled.png" width=24 height=24 class="like_button_img" alt=$title title=$title}
            <span><a>{$item.action_original_data.action_share_count|jrCore_number_format}</a></span>
        </div>
    {elseif !isset($item.action_shared)}
        {$share_id = $item_id}
        {$share_module = $module}
        {if isset($item.action_original_item_id) && is_numeric($item.action_original_item_id)}
            {$share_id = $item.action_original_item_id}
            {$share_module = $item.action_original_module}
        {/if}
        {if $share_module == 'jrComment' && is_array($item.action_original_data)}
            {$share_module = $item.action_original_data.comment_module}
            {$share_id = $item.action_original_data.comment_item_id}
        {/if}
        <div class="like_button_box">
            {if jrUser_is_logged_in()}
            <a onclick="jrAction_share('{$share_module}','{$share_id}')">
            {else}
            <a href="{$jamroom_url}/{$uurl}/login">
            {/if}
            {jrCore_lang module="jrAction" id=34 default="Share This with your Followers" assign="title"}
            {jrCore_image image="share.png" width="24" height="24" class="like_button_img" alt=$title title=$title}</a>
            <span><a>{$item.action_data.action_share_count|jrCore_number_format}</a> </span>
        </div>
    {/if}

    {if jrCore_module_is_active('jrTags') && $timeline != true}
        <div class="like_button_box">
            {if jrUser_is_logged_in()}
            <a onclick="jrISkin_open_div('#{$module}_{$item._item_id}_tag', '#tag_text')">
            {else}
            <a href="{$jamroom_url}/{$uurl}/login">
            {/if}
                {jrCore_lang module="jrTags" id="2" default="Tag" assign='tag'}
                {jrCore_image image="tag.png" width="24" height="24" class="like_button_img" alt='Tags' title=$tag}
            </a>
            <span><a>{$tag_count|jrCore_number_format}</a></span>
        </div>
    {/if}

    {if jrCore_module_is_active('jrRating') && $timeline != true}
        <div class="like_button_box">
            {if jrUser_is_logged_in()}
            <a onclick="jrISkin_open_div('#{$module}_{$item._item_id}_rating')">
                {else}
                <a href="{$jamroom_url}/{$uurl}/login">
                    {/if}
                    {jrCore_lang module="jrRating" id="1" default="Rate" assign='rate'}
                    {jrCore_image image="star.png" width="24" height="24" class="like_button_img" alt='Rate' title=$rate}</a>
                <span><a>
                    {$rating_count|jrCore_number_format}
                </a></span>
        </div>
    {/if}

</div>