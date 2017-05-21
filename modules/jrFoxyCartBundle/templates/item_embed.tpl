{jrCore_module_url module="jrFoxyCartBundle" assign="murl"}
<div>
<table class="item" style="width:80%;margin-left:0">
    <tr>
        <td rowspan="2" style="width:5%;padding-right:12px">
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.bundle_title_url}">{jrCore_image module="jrFoxyCartBundle" image="embed_bundle.png" width="32" height="32" alt="embed"}</a>
        </td>
        <td>
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.bundle_title_url}"><h3>{$item.bundle_title}</h3></a>
        </td>
        <td rowspan="2" style="width:20%;white-space:nowrap;padding-right:12px">
            {jrCore_module_function function="jrFoxyCart_add_to_cart" module='jrFoxyCartBundle' field='bundle' item=$item}
        </td>
    </tr>
    <tr>
        <td class="sublabel" style="width:95%">
            {jrCore_lang module="jrFoxyCartBundle" id="36" default="item count"}: {$item.bundle_count}
        </td>
    </tr>
</table>
</div>