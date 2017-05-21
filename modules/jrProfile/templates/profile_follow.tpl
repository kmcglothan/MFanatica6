<div class="container">

    <div class="row">
        <div class="col12 last">
            <div class="profile_name_box">

                <div class="block_config" style="margin-top:3px">
                    {jrCore_module_function function="jrFollower_button" profile_id=$_profile_id title="Follow This Profile"}
                </div>

                <a href="{$jamroom_url}/{$profile_url}"><h1 class="profile_name">{$profile_name}</h1></a>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col3">
            <div class="block">
                <div class="block_image">
                    <a href="{$jamroom_url}/{$profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$_profile_id size="large" crop="auto" class="iloutline img_scale" alt=$profile_name title=$profile_name width=false height=false}</a>
                </div>
            </div>
        </div>
        <div class="col9 last">
            <div class="block">

                <h1>{jrCore_lang module="jrProfile" id=24 default="This profile is private, and is only visible to followers."}</h1>

                {if !empty($profile_bio)}
                    <br>
                    <br>
                    {$profile_bio|jrCore_format_string:$profile_quota_id}
                {/if}

            </div>
        </div>
    </div>

</div>


