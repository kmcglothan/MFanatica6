{if !isset($_post._2)}
    {jrCore_page_title title="`$profile_name` - {jrCore_lang module="jrVideo" id="35" default="Video"}"}
    {jrCore_module_url module="jrVideo" assign="murl"}
    {jrProfile_disable_header}
    {jrProfile_disable_sidebar}
    <div class="page_nav clearfix">
        <div class="breadcrumbs">
            {jrCore_include template="profile_header_minimal.tpl"}
            {jrMogul_breadcrumbs module="jrVideo" profile_url=$profile_url profile_name=$profile_name page="group"}
        </div>
        <div class="action_buttons">
            {jrCore_bundle_index_buttons module="jrVideo" profile_id=$_profile_id create_action="`$murl`/create_album" create_alt=45}
        </div>
    </div>
    <div class="col8">
        <div class="box">
            {jrMogul_sort template="icons.tpl" nav_mode="jrVideo" profile_url=$profile_url}
            <span>{jrCore_lang module="jrAudio" id="34" default="Albums"} by {$profile_name}</span>
            <div class="box_body">
                <div class="wrap">
                    <div id="list">{jrCore_list module="jrVideo" profile_id=$_profile_id order_by="_created desc" group_by="video_album_url" pagebreak=20 page=$_post.p pager=true template="albums_list_video.tpl"}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col4 last">
        <div class="box">
            <ul id="actions_tab">
                <li class="solo" id="album_tab">
                    <a href="#"></a>
                </li>
            </ul>
            <span>{jrCore_lang skin="jrMogul" id="111" default="You May Also Like"}</span>
            <div class="box_body">
                <div class="wrap">
                    <div id="list" class="sidebar">
                        {jrCore_list
                        module="jrVideo"
                        search="_profile_id != `$_profile_id`"
                        order_by='_created RANDOM'
                        pagebreak=10
                        group_by="video_album_url"
                        template="chart_video.tpl"}
                    </div>
                </div>
            </div>
        </div>
    </div>
{else}

    {* Show our video items in this album *}
    {capture name="row_template" assign="template"}
    {literal}
        {jrCore_page_title title="`$_items[0]['video_album']` - `$_items[0]['profile_name']` inside"}
        {jrCore_module_url module="jrVideo" assign="murl"}
        {jrProfile_disable_header}
        {jrProfile_disable_sidebar}
        <div class="page_nav clearfix">
            <div class="breadcrumbs">
                {jrCore_include template="profile_header_minimal.tpl"}
                {jrMogul_breadcrumbs module="jrVideo" profile_url=$_items[0].profile_url page="group" item=$_items[0]}
            </div>
            <div class="action_buttons">
                {jrVideo_download_album_button items=$_items}
                {jrFoxyCartBundle_get_album module="jrVideo" profile_id=$_items.0._profile_id
                name=$_items.0.video_album assign="album"}
                {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrFoxyCartBundle" field="bundle"
                quantity_max="1" price=$album.bundle_item_price no_bundle="true" item=$album}
                {jrCore_item_create_button module="jrVideo" profile_id=$_items.0._profile_id
                action="`$murl`/create_album" icon="star2" alt="35"}
                {jrCore_item_update_button module="jrVideo" profile_id=$_items.0._profile_id
                action="`$murl`/update_album/`$_items.0.video_album_url`" alt="60"}
                {jrCore_item_delete_button module="jrVideo" profile_id=$_items.0._profile_id
                action="`$murl`/delete_album/`$_items.0.video_album_url`" alt="56" prompt="57"}
                    <span class="sprite_icon sprite_icon_30" title="Edit Playlist Order" id="edit_button">
                            <span class="sprite_icon_30 sprite_icon_30_img sprite_icon_30_settings">&nbsp;</span>
                        </span>
            </div>
        </div>
       <div class="col8">
           <div class="box">
               {jrMogul_sort template="icons.tpl" nav_mode="jrVideo" profile_url=$_items[0].profile_url}
               <span>{$_items[0].video_album}</span>
               <div class="box_body">
                   <div class="wrap detail_section">
                       <div class="media">
                           {if isset($_items.0.video_active) && $_items.0.video_active == 'off' &&
                           isset($_items.0.quota_jrVideo_video_conversions) &&
                           $_items.0.quota_jrVideo_video_conversions == 'on'}
                           <p class="center waiting">{jrCore_lang module="jrVideo" id="38" default="This video file is currently being processed and will appear here when complete."}</p>
                           {elseif $_items.0.video_file_extension == 'flv'}
                           {jrCore_media_player
                           module="jrVideo"
                           field="video_file"
                           search1="_profile_id = `$_items.0._profile_id`"
                           search2="video_album = `$_items.0.video_album`"
                           order_by="video_file_track numerical_asc"
                           limit="50"
                           }
                           {/if}
                       </div>
                       <div class="detail_box">
                           <div class="header">
                               <div style="width:5%;">#</div>
                               <div style="width:75%;">{jrCore_lang skin="jrMogul" id=42 default="Title"}</div>
                               <div style="width:15%;">{jrCore_lang skin="jrMogul" id=43 default="Buy"}</div>
                               <div style="width:5%;">{jrCore_lang skin="jrMogul" id=44 default="Add"}</div>
                           </div>
                           <div class="media">
                               <ul class="sortable list" style="list-style:none outside none;padding:0;">
                                   {foreach from=$_items item="item" name="loop"}
                                   <li data-id="{$item._item_id}">
                                       <div style="display: table; width: 100%" class="list-text">
                                           <div style="display: table-row; width: 100%">
                                               <div style="display: table-cell; width: 5%"> {$item@iteration}</div>
                                               <div style="display: table-cell; width: 65%"><a
                                                           href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title_url}">{$item.video_title|truncate:50}</a>
                                               </div>
                                               <div style="display: table-cell;width: 30%; text-align: right">
                                                   {jrCore_module_function function="jrFoxyCart_add_to_cart"
                                                   module="jrVideo" field="video_file" item=$item}
                                                   {jrCore_module_function function="jrFoxyCartBundle_button"
                                                   module="jrVideo" field="video_file" item=$item}
                                                   {jrCore_module_function function="jrPlaylist_button"
                                                   playlist_for="jrVideo" item_id=$item._item_id title="Add To
                                                   Playlist"}
                                               </div>
                                           </div>
                                       </div>
                                   </li>
                                   {/foreach}
                               </ul>
                           </div>
                       </div>
                       {* bring in module features *}
                       <div class="action_feedback">
                           {jrMogul_feedback_buttons module="jrVideo" item=$_items.0}
                           {if jrCore_module_is_active('jrRating')}
                           <div class="rating" id="jrAudio_{$item._items.0}_rating">{jrCore_module_function
                               function="jrRating_form"
                               type="star"
                               module="jrAudio"
                               index="1"
                               item_id=$item._items.0
                               current=$item.audio_rating_1_average_count|default:0
                               votes=$item.audio_rating_1_number|default:0}</div>
                           {/if}
                           {jrCore_item_detail_features module="jrVideo" item=$_items.0}
                       </div>
                   </div>
               </div>
           </div>
       </div>
        <div class="col4 last">
            <div class="box">
                {jrMogul_sort template="icons.tpl" nav_mode="jrVideo" profile_url=$profile_url}
                <span>{jrCore_lang skin="jrMogul" id="111" default="You May Also Like"}</span>
                <div class="box_body">
                    <div class="wrap">
                        <div id="list" class="sidebar">
                            {jrCore_list
                            module="jrVideo"
                            search="_profile_id != `$_items[0]._profile_id`"
                            order_by='_created RANDOM'
                            pagebreak=10
                            template="chart_video.tpl"}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/literal}
    {/capture}
    {$album_url = jrCore_url_string($_post._2)}
    {jrCore_list module="jrVideo" profile_id=$_profile_id search2="video_album_url = `$album_url`" order_by="video_file_track numerical_asc" limit="50" template=$template}
    {* We want to allow the item owner to re-order *}
    {if jrProfile_is_profile_owner($_profile_id)}
        <style type="text/css">
            .sortable {
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
                cursor: move;
                list-style: outside none none;
                padding: 0 8px;
            }
            .sortable li:last-child > .list-text {
                border-bottom: medium none;
            }
            li.sortable-placeholder {
                border: 1px dashed #BBB;
                background: none;
                height: 60px;
                margin: 12px;
            }
        </style>
        <script type="text/javascript">
            $(function () {
                $('#edit_button').click(function (e) {
                    $('#album_list').toggle();
                });
                $('.sortable').sortable().bind('sortupdate', function (e, u) {
                    var o = $('ul.sortable li').map(function () {
                        return $(this).data("id");
                    }).get();
                    $.post(core_system_url + '/' + jrVideo_url + "/order_update/__ajax=1", {
                        video_file_track: o
                    });
                });
            });
        </script>
    {/if}
{/if}