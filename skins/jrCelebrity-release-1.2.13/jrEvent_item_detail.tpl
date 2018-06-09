{jrCore_module_url module="jrEvent" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrCelebrity_breadcrumbs module="jrEvent" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrEvent" item=$item}
    </div>
</div>

<div class="col8">
    <div class="box">
        {jrCelebrity_sort template="icons.tpl" nav_mode="jrEvent" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap detail_section">
                <div class="media">
                    <div class="wrap">
                        <div style="position:relative;">
                            {if strlen($item.event_image_size) > 0}
                                <div>
                                    {jrCore_module_function
                                    function="jrImage_display"
                                    module="jrEvent"
                                    type="event_image"
                                    item_id=$item._item_id
                                    size="xxxlarge"
                                    class="img_scale"
                                    crop="2:1"
                                    alt=$item.event_title
                                    }
                                </div>
                            {/if}

                        </div>
                        <br>
                        <span class="title">{$item.event_title}</span>
                        <span class="location">{$item.event_location}</span>
                        <span class="date">{$item.event_date|jrCore_date_format:"%A %B %e %Y, %l:%M %p"}</span><br>
                        <div class="media_text">
                            {$item.event_description}
                            <p><span class="attending">{jrCore_lang module="jrEvent" id="38" default="Attendees"} : {$item.event_attendee_count|default:0}</span></p>

                            {xxAttending_users event_id=$item._item_id}

                            <div style="clear: both"></div>
                        </div>
                    </div>
                </div>
                {* bring in module features *}
                <div class="action_feedback">
                    {jrCelebrity_feedback_buttons module="jrEvent" item=$item}
                    {if jrCore_module_is_active('jrRating')}
                        <div class="rating" id="jrEvent_{$item._item_id}_rating">{jrCore_module_function
                            function="jrRating_form"
                            type="star"
                            module="jrEvent"
                            index="1"
                            item_id=$item._item_id
                            current=$item.event_rating_1_average_count|default:0
                            votes=$item.event_rating_1_number|default:0}</div>
                    {/if}
                    {jrCore_item_detail_features module="jrEvent" item=$item}
                </div>
            </div>
        </div>
    </div>
</div>


<div class="col4 last">
    <div class="box">
        {jrCelebrity_sort template="icons.tpl" nav_mode="jrEvent" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrCelebrity" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrEvent"
                    profile_id=$item.profile_id
                    order_by='_created RANDOM'
                    pagebreak=8
                    require_image="event_image"
                    template="chart_event.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>



