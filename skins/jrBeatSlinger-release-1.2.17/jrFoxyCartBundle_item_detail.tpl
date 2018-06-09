{jrCore_module_url module="jrFoxyCartBundle" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrBeatSlinger_breadcrumbs module="jrFoxyCartBundle" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrFoxyCartBundle" field="bundle" quantity_max="1" price=$item.bundle_item_price no_bundle="true" item=$item}
    </div>
</div>


<div class="col8">
    <div class="box">
        {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrFoxyCartBundle" profile_url=$profile_url}
        <span>{$item.bundle_title}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list">
                    {if $item.bundle_count > 0}

                        {if !empty($item.bundle_description)}
                            {$item.bundle_description|jrCore_format_string:$item.profile_quota_id}
                        {/if}

                        <ul class="sortable list" style="list-style:none outside none;padding-left:0;">
                            {foreach $item.bundle_items as $bundle_item}
                                {if !empty($item.bundle_templates[$bundle_item.bundle_module])}

                                    <li data-id="{$bundle_item.bundle_module}-{$bundle_item._item_id}">
                                        {if $bundle_item.audio_bundle_only == 'on'}
                                            {* this item is only available in this bundle *}
                                            <div class="bundle_only">
                                                <i>{jrCore_lang module="jrFoxyCartBundle" id="39" default="Available only as part of this bundle!"}</i>
                                            </div>
                                            <div id="{$bundle_item.bundle_module}{$bundle_item._item_id}" class="item" style="margin-top:0;border:2px solid #ccff99">
                                                {include file=$item.bundle_templates[$bundle_item.bundle_module] bundle_id=$item._item_id}
                                            </div>

                                        {else}

                                            <div id="{$bundle_item.bundle_module}{$bundle_item._item_id}">
                                                {include file=$item.bundle_templates[$bundle_item.bundle_module] bundle_id=$item._item_id}
                                            </div>

                                        {/if}
                                    </li>

                                {/if}
                            {/foreach}
                        </ul>

                        {* We want to allow the item owner to re-order *}
                    {if jrProfile_is_profile_owner($item._profile_id)}

                        <style type="text/css">
                            .sortable{
                                margin: auto;
                                padding: 0;
                                -webkit-touch-callout: none;
                                -webkit-user-select: none;
                                -khtml-user-select: none;
                                -moz-user-select: none;
                                -ms-user-select: none;
                                user-select: none;
                            }
                            .sortable li {
                                list-style: none;
                                cursor: move;
                            }
                            li.sortable-placeholder {
                                border: 1px dashed #BBB;
                                background: none;
                                height: 100px;
                                margin: 12px;
                            }
                        </style>

                        <script>
                            $(function() {
                                $('.sortable').sortable().bind('sortupdate', function(event,ui) {
                                    //Triggered when the user stopped sorting and the DOM position has changed.
                                    var o = $('ul.sortable li').map(function(){
                                        return $(this).data("id");
                                    }).get();
                                    $.post(core_system_url + '/' + jrFoxyCartBundle_url + "/order_update/id={$item._item_id}/__ajax=1", {
                                        bundle_order: o
                                    });
                                });
                            });
                        </script>

                    {/if}

                    {/if}
                </div>
                <div style="clear: both"></div>
                {* bring in module features *}
                <div class="action_feedback">
                    {jrCore_item_detail_features module="jrFoxyCartBundle" item=$item}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col4 last">
    <div class="box">
        {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrFoxyCartBundle" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrBeatSlinger" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrFoxyCartBundle"
                    profile_id=$_profile_id
                    order_by='_created RANDOM'
                    pagebreak=8
                    template="chart_bundle.tpl"}

                </div>
            </div>
        </div>
    </div>
</div>

