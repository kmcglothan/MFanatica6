{jrCore_module_url module="jrProfile" assign="murl"}
{if isset($_items)}

    {foreach from=$_items item="item"}
        <div class="item">
            <div class="clearfix" style="position: relative">
                {if strlen($item.profile_image_size) > 0}
                    <div class="media_image">
                        <a href="{$jamroom_url}/{$item.profile_url}"
                           title="{$item.profile_title|jrCore_entity_string}">
                            {jrCore_module_function
                            function="jrImage_display"
                            module="jrProfile"
                            type="profile_image"
                            item_id=$item._profile_id
                            size="xlarge"
                            class="img_scale"
                            alt=$item.profile_name
                            crop="auto"
                            }
                        </a>
                    </div>
                {/if}
                <div class="middle">
                    <div>
                        <span class="title">{$item.profile_name|truncate:60}</span>
                        <br>
                        {jrCore_module_function function="jrFollower_button" class="follow" profile_id=$item._profile_id title="Follow"}
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}