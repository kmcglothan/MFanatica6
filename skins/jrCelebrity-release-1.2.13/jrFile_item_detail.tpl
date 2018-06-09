{jrCore_module_url module="jrFile" assign="murl"}

{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrCelebrity_breadcrumbs module="jrFile" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrFile" item=$item  field="file_file"}
    </div>
</div>

<div class="col8">
    <div class="box">
        {jrCelebrity_sort template="icons.tpl" nav_mode="jrFile" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap detail_section">

                <div class="list_item">
                    <div class="wrap clearfix">
                        <div class="row">
                            <div class="col4">
                                <div class="image">
                                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.file_title_url}">{jrCore_module_function function="jrImage_display" module="jrFile" type="file_image" item_id=$item._item_id size="xlarge" crop="auto" class="iloutline img_scale" alt=$item.file_title width=false height=false}</a>
                                </div>
                            </div>
                            <div class="col8">
                                <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.file_title_url}">{$item.file_title}</a></h3><br>

                        <span class="info_c">
                        {if empty($item.file_file_item_price) && empty($item.file_file_item_bundle)}
                            <a href="{$jamroom_url}/{$murl}/download/file_file/{$item._item_id}">{$item.file_file_name}</a>
                        {else}
                            {$item.file_file_name}
                        {/if}
                        </span>
                                <br>{jrCore_module_function function="jrRating_form" type="star" module="jrFile" index="1" item_id=$item._item_id current=$item.file_rating_1_average_count|default:0 votes=$item.file_rating_1_count|default:0}

                                <div class="data clearfix">
                                    <span>{$item.file_comment_count|jrCore_number_format} {jrCore_lang skin="jrCelebrity" id="109" default="Comments"}</span>
                                    <span>{$item.file_like_count|jrCore_number_format} {jrCore_lang skin="jrCelebrity" id="110" default="Likes"}</span>
                                </div>
                            </div>
                        </div>
                    </div>


                {* bring in module features *}
                <div class="action_feedback" style="padding: 0">
                    {jrCelebrity_feedback_buttons module="jrFile" item=$item}
                    {if jrCore_module_is_active('jrRating')}
                        <div class="rating" id="jrFile_{$item._item_id}_rating">{jrCore_module_function
                            function="jrRating_form"
                            type="star"
                            module="jrFile"
                            index="1"
                            item_id=$item._item_id
                            current=$item.file_rating_1_average_count|default:0
                            votes=$item.file_rating_1_number|default:0}</div>
                    {/if}
                    {jrCore_item_detail_features module="jrFile" item=$item}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        {jrCelebrity_sort template="icons.tpl" nav_mode="jrFile" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrCelebrity" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {* jrCore_list
                    module="jrFile"
                    profile_id=$item.profile_id
                    order_by='_created RANDOM'
                    pagebreak=8
                    template="chart_file.tpl" *}
                </div>
            </div>
        </div>
    </div>
</div>

