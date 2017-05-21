{jrCore_module_url module="jrPlaylist" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrBeatSlinger_breadcrumbs module="jrPlaylist" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrPlaylist" item=$item}
    </div>
</div>

<div class="col8">
    <div class="box">
        {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrPlaylist" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap detail_section">
                <div class="media">
                    {if $item.playlist_count > 0}
                        {jrCore_media_player module="jrPlaylist" item=$item autoplay=false}
                    {else}
                        <div class="item">{jrCore_lang module="jrPlaylist" id="44" default="This playlist is empty and no longer exists"}</div>
                    {/if}
                </div>
                <div class="detail_box">

                    <div class="basic-info">
                        <div class="trigger"><span>{jrCore_lang skin="jrBeatSlinger" id="155" default="Tracks"}</span></div>
                        <div class="item" style="display: none; padding: 0; margin: 5px auto 0;">
                            <section>
                                <ul class="sortable list" style="list-style:none outside none;padding-left:0;">
                                    {foreach $item.playlist_items as $playlist_item}
                                        <li data-id="{$playlist_item.playlist_module}-{$playlist_item._item_id}">
                                            {include file=$item.playlist_templates[$playlist_item.playlist_module] playlist_id=$item._item_id}
                                        </li>
                                    {/foreach}
                                </ul>
                            </section>
                        </div>
                    </div>
                </div>

                {* bring in module features *}
                <div class="action_feedback">
                    {jrBeatSlinger_feedback_buttons module="jrPlaylist" item=$item}
                    {if jrCore_module_is_active('jrRating')}
                        <div class="rating" id="jrAudio_{$item._item_id}_rating">{jrCore_module_function
                            function="jrRating_form"
                            type="star"
                            module="jrAudio"
                            index="1"
                            item_id=$item._item_id
                            current=$item.audio_rating_1_average_count|default:0
                            votes=$item.audio_rating_1_number|default:0}</div>
                    {/if}
                    {jrCore_item_detail_features module="jrPlaylist" item=$item}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrPlaylist" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrBeatSlinger" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrAudio"
                    order_by='_created RANDOM'
                    pagebreak=8
                    template="chart_playlist.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>



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
            border-bottom: dashed 1px rgba(0,0,0,0.1);
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
            $('#edit_button').click(function(e){
                $('.sortable.list').toggle();
            });
            $('.sortable').sortable().bind('sortupdate', function(event,ui) {
                //Triggered when the user stopped sorting and the DOM position has changed.
                var o = $('ul.sortable li').map(function(){
                    return $(this).data("id");
                }).get();
                $.post(core_system_url + '/' + jrPlaylist_url + "/order_update/id={$item._item_id}/__ajax=1", {
                    playlist_order: o
                });
            });
        });
    </script>
{/if}