{jrCore_module_url module="jrFlickr" assign="murl"}

<div class="action_info">
    <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}')">
        {jrCore_module_function
        function="jrImage_display"
        module="jrUser"
        type="user_image"
        item_id=$item._user_id
        size="icon"
        crop="auto"
        alt=$item.user_name
        }
    </div>
    <div class="action_data">
        <div class="action_delete">
            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
        </div>
        <span class="action_user_name"><a href="{$jamroom_url}/{$item.action_data.profile_url}" title="{$item.action_data.profile_name|jrCore_entity_string}">{$item.profile_url}</a></span>

        {if $item.action_mode == 'create'}
            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.action_data.flickr_title_url}">
                    {jrCore_lang module="jrFlickr" id="14" default="Posted a new Flickr image"}.</a></span><br>

        {elseif $item.action_mode == 'search'}

            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.action_data.flickr_title_url}">
                    {jrCore_lang module="jrFlickr" id="15" default="Posted new Flickr images"}.</a></span><br>

        {else}

            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.action_data.flickr_title_url}">
                    {jrCore_lang module="jrFlickr" id="16" default="Updated a Flickr image"}.</a></span><br>

        {/if}

        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>

    </div>
</div>
<div class="item_media">
    <div class="wrap" style="padding: 0.5em;">
        {if $item.action_mode == 'create'}


            <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.flickr_title_url}" title="{$item.action_data.flickr_title|jrCore_entity_string}">
                {assign var="_data" value=$item.action_data.flickr_data|json_decode:true}
                <img src="{jrCore_server_protocol}://farm{$_data.attributes.farm}.staticflickr.com/{$_data.attributes.server}/{$_data.attributes.id}_{$_data.attributes.secret}_m.jpg" style="width:24%" alt="{$item.action_data.flickr_title|jrCore_entity_string}" title="{$item.action_data.flickr_title|jrCore_entity_string}">
            </a>

        {elseif $item.action_mode == 'search'}

            {math equation="x + 4" x=$item.action_data._created assign="x"}
            {jrCore_list module="jrFlickr" search1="_created >= `$item.action_data._created`" search2="_created <= `$x`" search3="_profile_id = `$item.action_data._profile_id`" template='null' order_by="_created numerical_asc" limit="4" assign="preview"}
            {if isset($preview[0]) && is_array($preview[0])}
                <div class="row">
                    {foreach $preview as $_i}
                        {assign var="_data" value=$_i.flickr_data|json_decode:true}
                        <div class="col3">
                            <div class="p5">
                                <img src="{jrCore_server_protocol}://farm{$_data.attributes.farm}.staticflickr.com/{$_data.attributes.server}/{$_data.attributes.id}_{$_data.attributes.secret}_m.jpg" class="img_scale" alt="{$_i.flickr_title|jrCore_entity_string}" title="{$_i.flickr_title|jrCore_entity_string}">
                            </div>
                        </div>
                    {/foreach}
                </div>
            {/if}

        {else}

            <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.flickr_title_url}" title="{$item.action_data.flickr_title|jrCore_entity_string}">
                {assign var="_data" value=$item.action_data.flickr_data|json_decode:true}
                <img src="{jrCore_server_protocol}://farm{$_data.attributes.farm}.staticflickr.com/{$_data.attributes.server}/{$_data.attributes.id}_{$_data.attributes.secret}_m.jpg" style="width:24%" alt="{$item.action_data.flickr_title|jrCore_entity_string}" title="{$item.action_data.flickr_title|jrCore_entity_string}">
            </a>

        {/if}
    </div>
</div>
