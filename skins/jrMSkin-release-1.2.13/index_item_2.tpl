{if isset($_items)}
    {foreach from=$_items item="item"}
        {jrMSkin_process_item item=$item module=$_conf.jrMSkin_list_2_type assign="_item"}
        <div class="col2 index_item">
            <div class="wrap">
                <div style="position: relative;">

                    <a href="{$_item.url}">
                        {jrCore_module_function
                        function="jrImage_display"
                        module=$_item.module
                        type=$_item.image_type
                        item_id=$_item._item_id
                        size="xxlarge"
                        crop="2:3"
                        class="img_scale"
                        alt=$_item.title
                        width=false
                        height=false
                        }</a>

                    <div class="hover">
                        <div class="middle">
                            <div class="wrap">
                                <span class="title">{$_item.title}</span>
                                {if $_item.module != 'jrProfile'}
                                    <span>by {$item.profile_name}</span><br>
                                {else}
                                    <span style="text-transform: capitalize;">{$item.quota_jrProfile_name}</span><br>
                                {/if}
                                <button onclick="jrCore_window_location('{$_item.url}')">{$_item.read_more}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
{else}
    <div class="no-items" style="width: 100%;">
        <h1>{jrCore_lang skin="jrMSkin" id="147" default="No items found"}</h1>
        {jrCore_module_url module="jrCore" assign="core_url"}
        <button class="form_button" style="display: block; margin: 2em auto;" onclick="jrCore_window_location('{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}/section=list+2')">{jrCore_lang skin="jrMSkin" id="148" default="Edit Configuration"}</button>
    </div>
{/if}