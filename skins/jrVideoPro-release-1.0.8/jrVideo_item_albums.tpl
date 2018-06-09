{jrCore_module_url module="jrVideo" assign="murl"}

{if !isset($_post._2)}

    {* We're showing a list of existing albums *}

    <div class="block">

        <div class="title">
            <div class="block_config">
                {jrCore_bundle_index_buttons module="jrVideo" profile_id=$_profile_id create_action="`$murl`/create_album" create_alt=34}
            </div>
            <h1>{jrCore_lang module="jrVideo" id="34" default="Albums"}</h1>
            <div class="breadcrumbs">

                <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo;
                {if jrCore_module_is_active('jrCombinedVideo') && $quota_jrCombinedVideo_allowed == 'on'}
                    <a href="{$jamroom_url}/{$profile_url}/{jrCore_module_url module="jrCombinedVideo"}">{jrCore_lang module="jrCombinedVideo" id=1 default="Videos"}</a>
                {else}
                    <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrVideo" id="35" default="Video"}</a>
                {/if}
                &raquo; {jrCore_lang module="jrVideo" id="34" default="Albums"}

            </div>
        </div>

        {capture name="row_template" assign="template"}
            {literal}
                {if isset($_items) && is_array($_items)}
                {jrCore_module_url module="jrVideo" assign="murl"}
                {foreach from=$_items item="item"}
                <div class="item">

                    <div class="container">
                        <div class="row">
                            <div class="col2">
                                <div class="block_image">
                                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.video_album_url}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="small" crop="auto" alt=$item.video_title title=$item.video_title class="iloutline" width=false height=false}</a>
                                </div>
                            </div>
                            <div class="col5">
                                <div class="p5">
                                    <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.video_album_url}">{$item.video_album}</a></h2>
                                </div>
                            </div>
                            <div class="col5 last">
                                <div class="block_config">
                                    {jrFoxyCartBundle_get_album module="jrVideo" profile_id=$item._profile_id name=$item.video_album assign="album"}
                                    {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrFoxyCartBundle" field="bundle" quantity_max="1" price=$album.bundle_item_price no_bundle="true" item=$album}
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>

                </div>
                {/foreach}
                {/if}
            {/literal}
        {/capture}

        <div class="block_content">

            {jrCore_list module="jrVideo" profile_id=$_profile_id order_by="_created desc" group_by="video_album_url" pagebreak="6" page=$_post.p template=$template pager=true}

        </div>

    </div>

{else}
    {jrProfile_disable_header}
    {jrProfile_disable_sidebar}
    {* Show our video items in this album *}
    {capture name="row_template" assign="template"}
    {literal}

        {if isset($_items) && is_array($_items)}
        {jrCore_module_url module="jrVideo" assign="murl"}

        <div class="profile_minimal">
        <div class="profile_info">
            <div class="wrap">
                <div class="table">
                    <div class="table-row">
                        <div class="table-cell profile-image">
                            <div class="profile_image">
                                {jrCore_module_function
                                function="jrImage_display"
                                module="jrProfile"
                                type="profile_image"
                                item_id=$_items.0._profile_id
                                size="xxlarge"
                                crop="auto"
                                class="img_scale img_shadow"
                                alt=$_items.0.profile_name
                                width=false
                                height=false}
                            </div>
                        </div>
                        <div class="table-cell">
                            <div class="profile_name">
                                {$_items[0]['profile_name']|truncate:55}<br>
                                <span><a href="{$jamroom_url}/{$_items[0]['profile_url']}">@{$_items[0]['profile_url']}</a> </span>
                            </div>
                        </div>
                        <div class="table-cell action_buttons">
                            {jrCore_lang id=5 skin="jrVideoPro" default="Follow" assign="Follow"}
                            {jrFollower_button profile_id=$_items.0._profile_id title=$follow}
                            {jrFoxyCartBundle_get_album module="jrVideo" profile_id=$_items.0._profile_id name=$_items.0.video_album assign="album"}
                            {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrFoxyCartBundle" field="bundle" quantity_max="1" price=$album.bundle_item_price no_bundle="true" item=$album}
                            {jrCore_item_create_button module="jrVideo" profile_id=$_items.0._profile_id action="`$murl`/create_album" icon="star2" alt="45"}
                            {jrCore_item_create_button module="jrVideo" profile_id=$_items.0._profile_id}
                            {jrCore_item_delete_button module="jrVideo" profile_id=$_items.0._profile_id action="`$murl`/delete_album/`$_items.0.video_album_url`" alt="52" prompt="53"}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section id="video_play">
        <div class="wrap">
            {assign var="ap" value="`$_conf.jrCore_active_skin`_auto_play"}
            {assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
            {assign var="player_type" value=$_conf.$skin_player_type}
            {assign var="player" value="jrVideo_`$player_type`"}
            {if isset($player_type) && strlen($player_type) > 0}
                {jrCore_media_player type=$player module="jrVideo" field="video_file" search1="_profile_id = `$_items.0._profile_id`" search2="video_album = `$_items.0.video_album`" order_by="video_file_track numerical_asc" limit="50" autoplay=$_conf.jrVideoPro_auto_play}
            {else}
                {jrCore_media_player module="jrVideo" field="video_file" search1="_profile_id = `$_items.0._profile_id`" search2="video_album = `$_items.0.video_album`" order_by="video_file_track numerical_asc" limit="50" autoplay=$_conf.jrVideoPro_auto_play}
            {/if}
        </div>
    </section>
    <section class="detail_box">
        <div class="wrap">
            <div class="row">
                <div class="col12">
                    {jrCore_lang skin="jrVideo" id=67 default="My Videos" assign="my_vids"}
                    <h1><a href="{$jamroom_url}/{$_items.0.profile_url}/{$murl}/albums/{$_items.0.video_album_url}">{$_items.0.video_album|default:$my_vids}</a></h1>
                </div>
            </div>
        </div>
    </section>
    <section class="videos" style="margin: 0; padding: 0;">
       <div class="wrap">
           <ul class="sortable list" style="list-style:none outside none;padding-left:0;">
               {foreach from=$_items item="item" name="loop"}
               <li data-id="{$item._item_id}" >
                   <div class="item">

                       <div class="container">
                           <div class="row">
                               <div class="col1">
                                   <div class="p5">
                                       {$item@iteration}
                                   </div>
                               </div>
                               <div class="col3">
                                   <div class="p5">
                                       <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title_url}">{$item.video_title}</a></h3>
                                   </div>
                               </div>
                               <div class="col3">
                                   <div class="p5">
                                       {jrCore_module_function function="jrRating_form" type="star" module="jrVideo" index="1" item_id=$item._item_id current=$item.video_rating_1_average_count|default:0 votes=$item.video_rating_1_number|default:0}
                                   </div>
                               </div>
                               <div class="col5 last">
                                   <div class="block_config">
                                       {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrVideo" field="video_file" item=$item}
                                       {jrCore_module_function function="jrFoxyCartBundle_button" module="jrVideo" field="video_file" item=$item}
                                       {jrCore_module_function function="jrPlaylist_button" playlist_for="jrVideo" item_id=$item._item_id title="Add To Playlist"}
                                       {jrCore_item_update_button module="jrVideo" profile_id=$item._profile_id item_id=$item._item_id style="width:100px"}
                                       {jrCore_item_delete_button module="jrVideo" profile_id=$item._profile_id item_id=$item._item_id style="width:100px;margin:6px 0"}
                                   </div>
                                   <div class="clear"></div>
                               </div>
                           </div>
                       </div>

                   </div>
               </li>
               {/foreach}
           </ul>
       </div>
    </section>
    {/if}

    {/literal}
    {/capture}

    {$album_url = jrCore_url_string($_post._2)}
    {jrCore_list module="jrVideo" profile_id=$_profile_id search2="video_album_url = `$album_url`" order_by="video_file_track numerical_asc" limit="50" template=$template}

    {* We want to allow the item owner to re-order *}
    {if jrProfile_is_profile_owner($_profile_id)}

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
            border: 1px dashed #DDD;
            background: none;
            height: 60px;
            margin: 12px;
        }
        </style>

        <script type="text/javascript">
            $(function() {
                $('.sortable').sortable().bind('sortupdate', function(e,u) {
                    var o = $('ul.sortable li').map(function(){
                        return $(this).data("id");
                    }).get();
                    $.post(core_system_url + '/' + jrVideo_url + "/order_update/__ajax=1", {
                        video_file_track: o
                    });
                });
            });
        </script>

    {/if}
    <div class="videos">
        <div class="wrap">
            <div class="row">
                <div class="head">
                    <span>{jrCore_lang skin="jrVideoPro" id=55 default="More From"} {$profile_name}</span>
                    <div class="see-all">
                        <a href="{$jamroom_url}/{$profile_url}/{$murl}/">{jrCore_lang skin="jrVideoPro" id=70 default="See All"}</a>
                    </div>
                </div>
            </div>
            <div class="list_wrap">
                <div class="row">
                    <div class="index_list clearfix page_1">
                        <div>{jrCore_list
                            module="jrVideo"
                            order_by="_item_id numerical_asc"
                            limit="18"
                            profile_id=$_profile_id
                            template="index_item_1.tpl"
                            require_image="video_image"
                            }
                        </div>
                    </div>
                </div>
                <a class="list_nav previous"></a>
                <a class="list_nav next"></a>
            </div>
            <br>
            <div class="row">
                <div class="head">
                    <span>{jrCore_lang skin="jrVideoPro" id=56 default="New Series From"} {$profile_name}</span>
                    <div class="see-all">
                        <a href="{$jamroom_url}/{$profile_url}/{$murl}/albums/">{jrCore_lang skin="jrVideoPro" id=70 default="See All"}</a>
                    </div>
                </div>
            </div>
            <div class="list_wrap">
                <div class="row">
                    <div class="index_list clearfix page_1">
                        <div>{jrCore_list
                            module="jrVideo"
                            order_by="_item_id numerical_asc"
                            limit="18"
                            profile_id=$_profile_id
                            group_by="video_album_url"
                            template="index_item_1.tpl"
                            require_image="video_image"
                            }
                        </div>
                    </div>
                </div>
                <a class="list_nav previous"></a>
                <a class="list_nav next"></a>
            </div>
            <br>
            <div class="row">
                <div class="head">
                    <span>{jrCore_lang skin="jrVideoPro" id=57 default="You May Also Like"}</span>
                    <div class="see-all">
                        <a href="{$jamroom_url}/{$murl}/">{jrCore_lang skin="jrVideoPro" id=70 default="See All"}</a>
                    </div>
                </div>
            </div>
            <div class="list_wrap">
                <div class="row">
                    <div class="index_list clearfix page_1">
                        <div>{jrCore_list
                            module="jrVideo"
                            order_by="_item_id numerical_asc"
                            limit="18" search="_profile_id != `$_profile_id`"
                            template="index_item_1.tpl"
                            require_image="video_image"
                            }
                        </div>
                    </div>
                </div>
                <a class="list_nav previous"></a>
                <a class="list_nav next"></a>
            </div>
        </div>
    </div>
{/if}

