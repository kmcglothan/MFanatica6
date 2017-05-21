{jrCore_module_url module="jrGallery" assign="murl"}

{if isset($_items) && is_array($_items)}
    <div class="container">

        <table class="page_table">
            <tr class="page_table_row">
                <td class="page_table_cell center" style="width:25%">
                    {$imgsize = jrCore_get_cookie('imgsizg')}

                    {jrCore_lang module="jrGallery" id="26" default="Size"}: <select id="imgsizg" class="form_select" style="width: auto;" onchange="jrSetCookie('imgsizg', JSON.stringify($(this).val()));">
                        {foreach $image_sizes as $pixels => $desc}
                            {if strlen($imgsize) > 0 && $imgsize == $pixels}
                                <option value="{$pixels}" selected="selected">{$desc} - {$pixels}px</option>
                            {elseif strlen($imgsize) === 0 && $pixels == 256}
                                <option value="{$pixels}" selected="selected">{$desc} - {$pixels}px</option>
                            {else}
                                <option value="{$pixels}">{$desc} - {$pixels}px</option>
                            {/if}
                        {/foreach}
                    </select>
                </td>
                <td class="page_table_cell center" style="width:25%">
                    {$imgposg = jrCore_get_cookie('imgposg')}

                    {jrCore_lang module="jrGallery" id="28" default="Position"}: <select id="imgposg" class="form_select" style="width: auto;" onchange="jrSetCookie('imgposg', JSON.stringify($(this).val()));">
                        <option value="" {if $imgposg == ""}selected="selected"{/if}>{jrCore_lang module="jrGallery" id="30" default="normal"}</option>
                        <option value="left"  {if $imgposg == "left"}selected="selected"{/if}>{jrCore_lang module="jrGallery" id="31" default="float left"}</option>
                        <option value="right" {if $imgposg == "right"}selected="selected"{/if}>{jrCore_lang module="jrGallery" id="32" default="float right"}</option>
                        <option value="stretch" {if $imgposg == "stretch"}selected="selected"{/if}>{jrCore_lang module="jrGallery" id="44" default="stretch"}</option>
                    </select>
                </td>
                <td class="page_table_cell center" style="width:25%">
                    {$imgposg = jrCore_get_cookie('imgmarg')}

                    {jrCore_lang module="jrGallery" id="52" default="Margin"}: <select id="imgmarg" class="form_select" style="width:auto" onchange="jrSetCookie('imgmarg', JSON.stringify($(this).val()));">
                        <option value="0" {if $imgposg == "0"}selected="selected"{/if}>none</option>
                        <option value="1" {if $imgposg == "1"}selected="selected"{/if}>1px</option>
                        <option value="2" {if $imgposg == "2"}selected="selected"{/if}>2px</option>
                        <option value="3" {if $imgposg == "3"}selected="selected"{/if}>3px</option>
                        <option value="4" {if $imgposg == "4"}selected="selected"{/if}>4px</option>
                        <option value="5" {if $imgposg == "5"}selected="selected"{/if}>5px</option>
                        <option value="6" {if $imgposg == "6"}selected="selected"{/if}>6px</option>
                        <option value="8" {if $imgposg == "8"}selected="selected"{/if}>8px</option>
                        <option value="10" {if $imgposg == "10"}selected="selected"{/if}>10px</option>
                        <option value="12" {if $imgposg == "12"}selected="selected"{/if}>12px</option>
                        <option value="15" {if $imgposg == "15"}selected="selected"{/if}>15px</option>
                        <option value="18" {if $imgposg == "18"}selected="selected"{/if}>18px</option>
                        <option value="20" {if $imgposg == "20"}selected="selected"{/if}>20px</option>
                        <option value="30" {if $imgposg == "30"}selected="selected"{/if}>30px</option>
                        <option value="40" {if $imgposg == "40"}selected="selected"{/if}>40px</option>
                        <option value="50" {if $imgposg == "50"}selected="selected"{/if}>50px</option>
                    </select>
                </td>
                <td class="page_table_cell center" style="width:25%">
                    {$aspect = jrCore_get_cookie('aspect')}
                    <button class="form_button" onclick="jrGallery_toggle_aspect('{$aspect}')">{jrCore_lang module="jrGallery" id=53 default="Toggle Crop"}</button>
                </td>
            </tr>
            <tr class="page_table_row_alt">
                <td colspan="4">

                    {if !$aspect || $aspect == 'cropped'}

                        {* square images *}
                        <input type="hidden" name="aspect" id="aspect" value="cropped">
                        {foreach $_items as $key => $item}
                            <div style="float:left;padding:3px;text-align:center">
                                <a onclick="jrGallery_insert_image('/{$murl}/image/gallery_image/{$item._item_id}', '{$item.gallery_image_name|addslashes}')" title="{$item.gallery_image_name}">
                                    {jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="medium" class="jrgallery_update_image" crop="auto" alt=$item.gallery_image_name width=148 height=148 title=$item.gallery_image_name}
                                </a><br>
                                {if isset($item.gallery_image_title) && strlen($item.gallery_image_title) > 0}{$item.gallery_image_title|truncate:20:"...":false}{else}{$item.gallery_image_name|truncate:20:"...":true}{/if}<br>
                                <a onclick="jrEmbed_load_module('jrGallery', 1, 'profile_url:{$item.profile_url}');">@{$item.profile_name}</a><br>
                                <small>{$item.gallery_image_width} x {$item.gallery_image_height}px</small>
                            </div>
                        {/foreach}

                    {else}

                        {* non-cropped images *}
                        <input type="hidden" name="aspect" id="aspect" value="original">
                        {foreach $_items as $key => $item}
                            <div style="float:left;padding:3px;text-align:center;max-width:152px">
                                <a onclick="jrGallery_insert_image('/{$murl}/image/gallery_image/{$item._item_id}', '{$item.gallery_image_name|addslashes}')" title="{$item.gallery_image_name}">
                                    {jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="medium" class="jrgallery_update_image img_scale" alt=$item.gallery_image_name title=$item.gallery_image_name}
                                </a><br>
                                {if isset($item.gallery_image_title) && strlen($item.gallery_image_title) > 0}{$item.gallery_image_title|truncate:20:"...":false}{else}{$item.gallery_image_name|truncate:20:"...":true}{/if}<br>
                                <a onclick="jrEmbed_load_module('jrGallery', 1, 'profile_url:{$item.profile_url}');">@{$item.profile_name}</a><br>
                                <small>{$item.gallery_image_width} x {$item.gallery_image_height}px</small>
                            </div>
                        {/foreach}

                    {/if}
                </td>
            </tr>
        </table>

    </div>

{else}

    <div class="container">
        <table class="page_table">
            <tr class="page_table_row">
                <td class="page_table_cell center" colspan="2">{jrCore_lang module="jrGallery" id="45" default="no gallery images were found"}</td>
            </tr>
        </table>
    </div>

{/if}

<div id="galpnum" style="display:none">{$info.this_page}</div>
