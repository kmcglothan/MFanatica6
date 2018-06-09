{jrCore_module_url module="jrBundle" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">

            {jrCore_item_detail_buttons module="jrBundle" field="bundle" quantity_max="1" price=$item.bundle_item_price no_bundle="true" item=$item}

        </div>
        <h1>{$item.bundle_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrBundle" id=1 default="Item Bundles"}</a> &raquo; {$item.bundle_title}
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
                <ul class="sortable" style="list-style:none outside none;padding-left:0;">
                    {foreach $item.bundle_items as $bundle_item}
                        {if !empty($item.bundle_templates[$bundle_item.bundle_module])}

                            <li data-id="{$bundle_item.bundle_module}-{$bundle_item._item_id}">
                            <div id="{$bundle_item.bundle_module}{$bundle_item._item_id}" style="position:relative">
                                {jrCore_include template=$item.bundle_templates[$bundle_item.bundle_module] module=$bundle_item.bundle_module bundle_id=$item._item_id}
                            </div>
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
                .sortable > li {
                    cursor: move;
                    list-style: outside none none;
                }
                li.sortable-placeholder {
                    border: 1px dashed #BBB;
                    background: none;
                    height: 100px;
                    margin: 12px;
                }
                .col4 {
                }
                .item {
                    clear: both;
                }
            </style>

            <script>
                $(function() {
                    $('.sortable').sortable().bind('sortupdate', function() {
                        var o = $('ul.sortable li').map(function(){
                            return $(this).data("id");
                        }).get();
                        $.post(core_system_url + '/' + jrBundle_url + "/order_update/id={$item._item_id}/__ajax=1", {
                            bundle_order: o
                        });
                    });
                });
            </script>

        {/if}

        {/if}

        {* bring in module features *}
        {jrCore_item_detail_features module="jrBundle" item=$item}

    </div>

</div>
