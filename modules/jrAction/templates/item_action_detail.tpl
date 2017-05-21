<div class="container">
    <div class="row">

        <div class="col2">
            <div class="action_item_media">
                {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" alt=$item.user_name class="action_item_user_img img_shadow img_scale"}
            </div>
        </div>
        <div class="col10 last" style="position:relative">

            <div class="action_item_desc">

                <span class="action_item_title"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a> {jrCore_lang module="jrAction" id=42 default="posted"}</span>
                <span class="action_item_actions"> &bull; {$item._created|jrCore_date_format:"relative"}</span>

                <br>

                <div class="action_item_text">
                    {if strlen($item.action_text) > 0}
                    {$item.action_text}
                    {else}
                    {$item.action_html}
                    {/if}
                </div>

            </div>
        </div>

    </div>
</div>
