{jrCore_module_url module="jrSoundCloud" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrBeatSlinger_breadcrumbs module="jrSoundCloud" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrSoundCloud" item=$item  field="soundcloud_file"}
    </div>
</div>

<div class="col8">
    <div class="box">
        {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrSoundCloud" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap detail_section">
                {jrSoundCloud_embed item_id=$item._item_id auto_play=$_conf.jrBeatSlinger_auto_play}
                <div class="detail_box">
                    <div class="basic-info">
                        <div class="trigger"><span>{jrCore_lang skin="jrBeatSlinger" id="115" default="Basic Info"}</span></div>
                        <div class="item" style="display: none; padding: 0;">
                            <div style="display: table; width: 100%">
                                <div class="header">
                                    <div>{jrCore_lang skin="jrBeatSlinger" id=46 default="Artist"}</div>
                                    <div>{jrCore_lang skin="jrBeatSlinger" id=39 default="Genre"}</div>
                                    <div>{jrCore_lang skin="jrBeatSlinger" id=40 default="Created"}</div>
                                    <div>{jrCore_lang skin="jrBeatSlinger" id=38 default="Plays"}</div>
                                </div>
                                <div class="details">
                                    <div>{$item.soundcloud_artist}</div>
                                    <div>{$item.soundcloud_genre|default:"none"}</div>
                                    <div>{$item._created|jrCore_date_format:"relative"}</div>
                                    <div>{$item.soundcloud_stream_count|jrCore_number_format}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {if strlen($item.soundcloud_description) > 0}
                        <div class="description">
                            <div class="trigger"><span>{jrCore_lang skin="jrBeatSlinger" id="47" default="Description"}</span></div>
                            <div class="item" style="display: none;">
                                {$item.soundcloud_description}
                            </div>
                        </div>
                    {/if}
                </div>


                {* bring in module features *}
                <div class="action_feedback">
                    {jrBeatSlinger_feedback_buttons module="jrSoundCloud" item=$item}
                    {if jrCore_module_is_active('jrRating')}
                        <div class="rating" id="jrSoundCloud_{$item._item_id}_rating">{jrCore_module_function
                            function="jrRating_form"
                            type="star"
                            module="jrSoundCloud"
                            index="1"
                            item_id=$item._item_id
                            current=$item.soundcloud_rating_1_average_count|default:0
                            votes=$item.soundcloud_rating_1_number|default:0}</div>
                    {/if}
                    {jrCore_item_detail_features module="jrSoundCloud" item=$item}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrSoundCloud" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrBeatSlinger" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrSoundCloud"
                    profile_id=$item.profile_id
                    order_by='_created RANDOM'
                    pagebreak=8
                    template="chart_soundcloud.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>
