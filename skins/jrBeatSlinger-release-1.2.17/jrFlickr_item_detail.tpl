{jrCore_module_url module="jrFlickr" assign="murl"}
{assign var="_data" value=$item.flickr_data|json_decode:TRUE}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrBeatSlinger_breadcrumbs module="jrFlickr" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrFlickr" item=$item}
    </div>
</div>

<div class="col8">
    <div class="box">
        {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrFlickr" profile_url=$profile_url}
        <span</span>
        <div class="box_body">
            <div class="wrap detail_section">
                <div class="media">
                    <div>
                        <a href="{jrCore_server_protocol}://www.flickr.com/photos/{$_data.owner.nsid}/{$_data.attributes.id}" target="_blank">
                            <img src="{jrCore_server_protocol}://farm{$_data.attributes.farm}.staticflickr.com/{$_data.attributes.server}/{$_data.attributes.id}_{$_data.attributes.secret}.jpg" width="100%" alt="{$item.flickr_title}">
                        </a>
                        <br>
                    </div>
                    {if !empty($item.flickr_caption)}
                        <div class="caption">
                            {$item.flickr_caption}
                        </div>
                    {/if}
                </div>

                {* bring in module features *}
                {if jrUser_is_logged_in()}
                    <div class="action_feedback">
                        {jrBeatSlinger_feedback_buttons module="jrFlickr" item=$item}
                        {if jrCore_module_is_active('jrRating')}
                            <div class="rating" id="jrFlickr_{$item._item_id}_rating">{jrCore_module_function
                                function="jrRating_form"
                                type="star"
                                module="jrFlickr"
                                index="1"
                                item_id=$item._item_id
                                current=$item.flickr_rating_1_average_count|default:0
                                votes=$item.flickr_rating_1_number|default:0}</div>
                        {/if}
                        {jrCore_item_detail_features module="jrFlickr" item=$item}
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>

<div class="col4 last">
    <div class="box">
        {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrFlickr" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrFlickr"
                    profile_id=$item.profile_id
                    order_by='_created RANDOM'
                    pagebreak=8
                    template="chart_flickr.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>




