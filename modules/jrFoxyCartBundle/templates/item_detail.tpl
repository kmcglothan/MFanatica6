{jrCore_module_url module="jrFoxyCartBundle" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">

            {jrCore_item_detail_buttons module="jrFoxyCartBundle" field="bundle" quantity_max="1" price=$item.bundle_item_price no_bundle="true" item=$item}

        </div>
        <h1>{$item.bundle_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrFoxyCartBundle" id="1" default="Item Bundles"}</a> &raquo; {$item.bundle_title}
        </div>
    </div>

    <div class="block_content">

        {if $item.bundle_count > 0}

            {if !empty($item.bundle_description)}
            <div class="item">
                <div class="p5">
                    {$item.bundle_description|jrCore_format_string:$item.profile_quota_id}
                </div>
            </div>
            {/if}

            <section>
                <ul class="sortable list" style="list-style:none outside none;padding-left:0;">
                    {foreach $item.bundle_items as $bundle_item}
                        {if !empty($item.bundle_templates[$bundle_item.bundle_module])}

                            <li data-id="{$bundle_item.bundle_module}-{$bundle_item._item_id}">
                            {if $bundle_item.audio_bundle_only == 'on'}
                                {* this item is only available in this bundle *}
                                <div class="bundle_only">
                                    <i>{jrCore_lang module="jrFoxyCartBundle" id="39" default="Available only as part of this bundle!"}</i>
                                </div>
                                <div id="{$bundle_item.bundle_module}{$bundle_item._item_id}">
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
            </section>

        {* We want to allow the item owner to re-order *}
        {if jrProfile_is_profile_owner($item._profile_id)}

            <style type="text/css">
                .sortable{
                    margin: auto;
                    padding: 0;
                    -webkit-touch-callout: none;
                    -webkit-user-select: none;
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

        {* bring in module features *}
        {jrCore_item_detail_features module="jrFoxyCartBundle" item=$item}

    </div>

</div>
