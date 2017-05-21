<div class="block">

    <div class="title">
        <h1>{$item.profile_name}</h1>
    </div>

    <div class="block_content">

        <div class="item">
            <div class="container">
                <div class="row">
                    <div class="col3">
                        <div class="block_image center">
                            {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="large" alt=$item.profile_name width=false height=false class="iloutline img_scale"}
                            {jrCore_module_function function="jrRating_form" type="star" module="jrProfile" index="1" item_id=$item._profile_id current=$item.profile_rating_1_average_count|default:0 votes=$item.profile_rating_1_count|default:0}
                        </div>
                    </div>
                    <div class="col9 last">
                        <div class="p5">
                            {$item.profile_bio}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {jrCore_item_detail_features module="jrProfile" item=$item}
    </div>

</div>
