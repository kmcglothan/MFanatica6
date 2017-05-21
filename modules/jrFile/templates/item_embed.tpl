{jrCore_module_url module="jrFile" assign="murl"}
<div>
    <table class="item" style="width:80%;margin-left:0">
        <tr>
            <td rowspan="2" style="width:5%;padding-right:12px">
                <a href="{$jamroom_url}/{$murl}/download/file_file/{$item._item_id}">{jrCore_file_type_image extension=$item.file_file_extension width=32 height=32 alt=$item.file_file_name}</a>
            </td>
            <td>
                <a href="{$jamroom_url}/{$murl}/download/file_file/{$item._item_id}">{$item.file_title}</a>
            </td>
        </tr>
        <tr>
            <td class="sublabel" style="width:95%">{$item.file_file_name}, {$item.file_file_size|jrCore_format_size}
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.file_title_url}">&infin;</a>
            </td>
        </tr>
    </table>
</div>

