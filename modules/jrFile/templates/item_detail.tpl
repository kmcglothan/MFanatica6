{jrCore_module_url module="jrFile" assign="murl"}
<div class="block">

    <div class="title">

        <div class="block_config">

            {jrCore_item_detail_buttons module="jrFile" field="file_file" item=$item}

        </div>
        <h1>{$item.file_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrFile" id="11" default="files"}</a> &raquo; {$item.file_title}
        </div>

    </div>

    <div class="block_content">

        <div class="item">

            <div class="container">
                <div class="row">
                    <div class="col2">
                        <div class="block_image">
                            {jrCore_module_function function="jrImage_display" module="jrFile" type="file_image" item_id=$item._item_id size="medium" class="iloutline img_scale" alt=$item.file_title width=false height=false}
                        </div>
                    </div>
                    <div class="col8">
                        <div class="p5">
                            <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.file_title_url}">{$item.file_title}</a></h3><br>
                            <span class="info">{jrCore_lang module="jrFile" id="14" default="size"}:</span> <span class="info_c">{$item.file_file_size|jrCore_format_size}</span><br>
                            {jrCore_module_function function="jrRating_form" type="star" module="jrFile" index="1" item_id=$item._item_id current=$item.file_rating_1_average_count|default:0 votes=$item.file_rating_1_number|default:0}
                        </div>
                    </div>
                    <div class="col2 last">
                        <div class="block_config">
                            {if empty($item.file_file_item_price) && empty($item.file_file_item_bundle)}
                                {jrCore_lang module="jrFile" id="10" default="Download" assign="alt"}
                                <a href="{$jamroom_url}/{$murl}/download/file_file/{$item._item_id}">{jrCore_file_type_image extension=$item.file_file_extension width=32 height=32 alt="`$alt` `$item.file_file_name`" class="download_img"}</a>
                            {/if}
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>

        </div>

        {* bring in module features *}
        {jrCore_item_detail_features module="jrFile" item=$item}

    </div>

</div>
