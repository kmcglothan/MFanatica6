{jrCore_module_url module="jrPhotoAlbum" assign="murl"}


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
        <span class="action_user_name"><a href="{$jamroom_url}/{$item.profile_url}"
                                          title="{$item.profile_name|jrCore_entity_string}">{$item.profile_url}</a></span>

        {if $item.action_mode == 'create'}
            <span class="action_desc"><a
                        href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.photoalbum_title_url}/all">
                    {jrCore_lang module="jrPhotoAlbum" id="12" default="Created a new Photo Album"}.
                </a></span>
            <br>
        {else}
            <span class="action_desc"><a
                        href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.photoalbum_title_url}/all">
                    {jrCore_lang module="jrPhotoAlbum" id="13" default="Updated a Photo Album"}.
                </a></span>
            <br>
        {/if}

        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>

    </div>
</div>
<div class="media">
    <div style="padding: 0.5em; position: relative" class="clearfix">
        {$_item = jrCore_db_get_item('jrPhotoAlbum', $item.action_item_id)}
        {$num = 1}
        {if isset($_item.photoalbum_items)}
            {$_list_count = $_item.photoalbum_count}

            {foreach $_item.photoalbum_items as $_i}
                {if $_list_count == 1}
                    {$class = "single"}
                    {$aspect = "16:9"}
                    {$size = "xxxlarge"}
                {elseif $_list_count == 2}
                    {$aspect = "8:9"}
                    {$class = "double"}
                    {$size = "xxlarge"}
                {elseif $_list_count == 3}
                    {$aspect = "5.3:9"}
                    {$class = "triple"}
                    {$size = "xxlarge"}
                {else}
                    {$class = "quads"}
                    {$aspect = "16:9"}
                    {$size = "xlarge"}
                {/if}

                {if $num > 4}
                    {assign var="class" value="hidden"}
                {/if}
                <div class="list-item photo {$class}">
                    <div>
                        <div>
                            {jrCore_module_url module="jrGallery" assign="murl"}
                            <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$_i._item_id}/1280"
                               data-lightbox="images_{$_i.gallery_title_url}"
                               title="{$_i.gallery_caption|default:$_i.gallery_image_name|jrGallery_title_name:$_i.gallery_caption}">
                                {jrCore_module_function
                                function="jrImage_display"
                                module="jrGallery"
                                type="gallery_image"
                                item_id=$_i._item_id
                                size=$size
                                crop=$aspect
                                alt=$_i.gallery_alt_text
                                width=false
                                height=false}</a>
                            {if $num == 4}
                                <div class="list-info full">
                                    {math equation="x-y" x=$_list_count y=4 assign="m"}
                                    <span>+{$m}</span>
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>
                {math equation="x+y" x=$num y=1 assign="num"}
            {/foreach}
        {/if}

    </div>
</div>






