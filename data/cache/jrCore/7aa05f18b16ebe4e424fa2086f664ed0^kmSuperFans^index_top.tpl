<div class="index_top">
    <div class="row">
        <div class="col12">
            <div class="index_slide">

                {if jrCore_is_mobile_device()}
                    {jrCore_image image="index_top_mobile.jpg" width="800" height="auto"}
                {else}
                    {jrCore_image image="index_top.jpg" width="1280" height="auto"}
                {/if}

                <div class="slide_info">
                    <div class="wrap">
                        <ul class="social clearfix">
                            {if strlen($_conf.kmSuperFans_facebook_url) > 0 && $_conf.kmSuperFans_facebook_url != "0"}
                                <li><a href="{$_conf.kmSuperFans_facebook_url}" class="social-facebook" target="_blank"></a></li>
                            {/if}
                            {if strlen($_conf.kmSuperFans_twitter_url) > 0 && $_conf.kmSuperFans_twitter_url != "0"}
                                <li><a href="{$_conf.kmSuperFans_twitter_url}" class="social-twitter" target="_blank"></a></li>
                            {/if}
                            {if strlen($_conf.kmSuperFans_google_url) > 0 && $_conf.kmSuperFans_google_url != "0"}
                                <li><a href="{$_conf.kmSuperFans_google_url}" class="social-google" target="_blank"></a></li>
                            {/if}
                            {if strlen($_conf.kmSuperFans_linkedin_url) > 0 && $_conf.kmSuperFans_linkedin_url != "0"}
                                <li><a href="{$_conf.kmSuperFans_linkedin_url}" class="social-linkedin" target="_blank"></a></li>
                            {/if}
                            {if strlen($_conf.kmSuperFans_youtube_url) > 0 && $_conf.kmSuperFans_youtube_url != "0"}
                                <li><a href="{$_conf.kmSuperFans_youtube_url}" class="social-youtube" target="_blank"></a></li>
                            {/if}
                        </ul>
                        <span class="large white">{jrCore_lang skin="kmSuperFans" id=51 default="Welcome to"} </span> <span class="large">{$_conf.jrCore_system_name}</span><br>
                        <span>{jrCore_lang skin="kmSuperFans" id=79 default="Member of the "}</span>
                        <span class="white">{jrCore_lang skin="kmSuperFans" id=80 default="MUSICREWARDS"}</span>
                        <span>{jrCore_lang skin="kmSuperFans" id=81 default="Network"}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>