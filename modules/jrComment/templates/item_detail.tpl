{jrCore_module_url module="jrComment" assign="murl"}
<div class="block">

    <div class="title">
        <h1>{jrCore_lang module="jrComment" id=3 default="Commented on"}: {$item.comment_item_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrComment" id=11 default="Comments"}</a> &raquo; {jrCore_lang module="jrComment" id=3 default="Commented on"}: <a href="{$item.comment_url}">{$item.comment_item_title}</a>
        </div>
    </div>

    <div class="block_content">
        <div class="item">

            <div class="container">
                <div class="row">
                    <div class="col1">
                        <div class="block_image p5">
                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="small" alt=$item.user_name class="action_item_user_img iloutline"}
                        </div>
                    </div>
                    <div class="col9">
                        <div class="p5" style="margin-left:24px">
                            <span class="info" style="display:inline-block;">{$item._created|jrCore_date_format} <a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_url}</a>:</span><br>
                            <span class="normal">{$item.comment_text|jrCore_format_string:$item.profile_quota_id}</span>
                        </div>
                    </div>
                    <div class="col2 last">
                        <div class="block_config">
                            {if $_params.profile_owner_id > 0}
                                {* profile owners can delete comments *}
                                {jrCore_item_delete_button module="jrComment" profile_id=$_params.profile_owner_id item_id=$item._item_id}
                            {else}
                            {* site admins and comment owners see this button *}
                                {jrCore_item_delete_button module="jrComment" profile_id=$item._profile_id item_id=$item._item_id}
                            {/if}
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
