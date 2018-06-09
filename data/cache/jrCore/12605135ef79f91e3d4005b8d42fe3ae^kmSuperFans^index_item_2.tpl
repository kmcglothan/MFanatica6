{if isset($_items)}
    {jrCore_module_url module="jrAudio" assign="murl"}

    <div class="row">

    {foreach from=$_items item="item"}
        {if $item.list_rank == 1}

            <div class="col6">
                <div style="padding: 5px;">
                    <div class="featured_item">
                        <div class="cover_image">

                            {if $item.profile_header_image_size > 0}
                                <a href="{$jamroom_url}/{$item.profile_url}"  title="{jrCore_lang skin="kmSuperFans" id="34" default="Click to view"}">
                                    {if jrCore_is_mobile_device()}
                                        {$crop = "2:1"}
                                    {else}
                                        {$crop = "4:1"}
                                    {/if}
                                    {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_header_image" item_id=$item._profile_id size="1280" class="img_scale" crop=$crop alt=$item.profile_name _v=$item.profile_header_image_time}
                                </a>
                            {else}
                                <a href="{$jamroom_url}/{$item.profile_url}"  title="{jrCore_lang skin="kmSuperFans" id="34" default="Click to view"}">
                                    {if jrCore_is_mobile_device()}
                                        {jrCore_image image="profile_header_image.jpg" width="1140" class="img_scale" height="auto"}
                                    {else}
                                        {jrCore_image image="profile_header_image_large.jpg" width="1140" class="img_scale" height="auto"}
                                    {/if}
                                </a>
                            {/if}

                            <div class="profile_info">
                                <div class="wrap">
                                    <div class="table">
                                        <div class="table-row">
                                            <div class="table-cell profile-image">
                                                <div class="profile_image">
                                                    {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="xxlarge" crop="auto" class="img_scale img_shadow" alt=$item.profile_name width=false height=false}
                                                </div>
                                            </div>
                                            <div class="table-cell">
                                                <div class="profile_name">
                                                    {$item.profile_name|truncate:55}<br>
                                                    <span><a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_url}</a> </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="wrap">

                            {if $_conf.kmSuperFans_require_price_2 == 'on'}
                                {$s1 = "audio_file_item_price > 0"}
                            {/if}
                            {if jrCore_module_is_active('jrCombinedAudio') && $_conf.kmSuperFans_list_2_soundcloud == 'on'}
                                {jrCombinedAudio_get_active_modules assign="mods"}
                                {if strlen($mods) > 0}
                                    {jrSeamless_list modules=$mods  profile_id=$item._profile_id order_by="_created numerical_desc" limit="5" template="index_item_audio_large.tpl"}
                                {elseif jrUser_is_admin()}
                                    No active audio modules found!
                                {/if}
                            {else}
                                {jrCore_list module="jrAudio" profile_id=$item._profile_id search=$s1 order_by="audio_display_order desc" limit="5" template="index_item_audio_large.tpl"}
                            {/if}

                        </div>
                    </div>
                </div>
            </div>
        {else}
            <div class="col3">
                <div style="padding: 5px;">
                    <div class="featured_item">
                        <div class="cover_image">

                            {if $item.profile_header_image_size > 0}
                                <a href="{$jamroom_url}/{$item.profile_url}"  title="{jrCore_lang skin="kmSuperFans" id="34" default="Click to view"}">
                                    {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_header_image" item_id=$item._profile_id size="1280" class="img_scale" crop="2:1" alt=$item.profile_name _v=$item.profile_header_image_time}
                                </a>
                            {else}
                                <a href="{$jamroom_url}/{$item.profile_url}"  title="{jrCore_lang skin="kmSuperFans" id="34" default="Click to view"}">
                                    {jrCore_image image="profile_header_image.jpg" width="1140" class="img_scale" height="auto" crop="3:1"}
                                </a>
                            {/if}

                            <div class="profile_info">
                                <div class="wrap">
                                    <div class="table">
                                        <div class="table-row">
                                            <div class="table-cell profile-image">
                                                <div class="profile_image">
                                                    {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="xxlarge" crop="auto" class="img_scale img_shadow" alt=$item.profile_name width=false height=false}
                                                </div>
                                            </div>
                                            <div class="table-cell">
                                                <div class="profile_name">
                                                    {$item.profile_name|truncate:55}<br>
                                                    <span><a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_url}</a> </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="wrap">
                            {if $_conf.kmSuperFans_require_price_2 == 'on'}
                                {$s2 = "audio_file_item_price > 0"}
                            {/if}
                            {if jrCore_module_is_active('jrCombinedAudio') && $_conf.kmSuperFans_list_2_soundcloud == 'on'}
                                {jrCombinedAudio_get_active_modules assign="mods"}
                                {if strlen($mods) > 0}
                                    {jrSeamless_list modules=$mods  profile_id=$item._profile_id order_by="*_display_order desc" limit="5" template="index_item_audio.tpl"}
                                {elseif jrUser_is_admin()}
                                    No active audio modules found!
                                {/if}
                            {else}
                                {jrCore_list module="jrAudio" profile_id=$item._profile_id search=$s1 order_by="audio_display_order desc" limit="5" template="index_item_audio.tpl"}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        {/if}

        {if $item.list_rank == 3}
            </div><div class="row">
        {/if}

    {/foreach}
    </div>
{else}
    <div class="no-items">
        <h1>{jrCore_lang skin="kmSuperFans" id="62" default="No items found"}</h1>
        {jrCore_module_url module="jrCore" assign="core_url"}
        <button class="form_button" style="display: block; margin: 2em auto;" onclick="jrCore_window_location('{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}/section=List+2')">{jrCore_lang skin="kmSuperFans" id="64" default="Edit Configuration"}</button>
    </div>
{/if}


