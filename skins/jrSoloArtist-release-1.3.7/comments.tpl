<div class="block">
    <div class="title mb10">
        <h1>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="latest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="53" default="Comments"}:</h1>
    </div>
    <div class="block_content">
        {foreach from=$_items item="item"}
            <div class="container">
                <div class="row">
                    <div class="col2">
                        <div class="item p5 center">
                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="small" alt=$item.user_name class="action_item_user_img iloutline"}
                        </div>
                    </div>
                    <div class="col8">
                        <div class="item p5">
                            {if $item.profile_url == $_conf.jrSoloArtist_main_profile_url|replace:' ':'-'}
                                <span class="media_title" style="display:inline-block;">{$item._created|jrCore_date_format} <a href="{$jamroom_url}">@{$item.profile_name}</a>:</span><br>
                            {else}
                                <span class="media_title" style="display:inline-block;">{$item._created|jrCore_date_format} <a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_name}</a>:</span><br>
                            {/if}
                            <span class="normal">{jrCore_lang module="jrComment" id="3" default="Commented on"}: <a href="{$item.comment_url}">{$item.comment_item_title}</a></span><br>
                            <span class="normal">{$item.comment_text|truncate:100:"...":false|jrCore_format_string|jrCore_convert_at_tags}</span>
                        </div>
                    </div>
                    <div class="col2 last">
                        <div class="item p5 center">
                            {jrCore_item_delete_button module="jrComment" profile_id=$item._profile_id item_id=$item._item_id}
                        </div>
                    </div>
                </div>
            </div>
            <div class="divider" style="width:90%;margin:15px auto;">&nbsp;</div>
        {/foreach}
    </div>
</div>

