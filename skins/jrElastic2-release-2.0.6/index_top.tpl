<section class="index">
    <div class="overlay"></div>
    <div class="row" style="position: relative; z-index: 1; max-width: 1180px">
        <div class="col8">
            <div class="index_welcome">
                <h1>{$_conf.jrElastic2_welcome_title}</h1>

                <ul class="elastic_list">
                    <li>{jrCore_lang skin="jrElastic2" id=12 default="Highly Configurable"}</li>
                    <li>{jrCore_lang skin="jrElastic2" id=13 default="Responsive Design"}</li>
                    <li>{jrCore_lang skin="jrElastic2" id=14 default="Site Builder"}</li>
                    <li>{jrCore_lang skin="jrElastic2" id=15 default="Template Editor"}</li>
                    <li>{jrCore_lang skin="jrElastic2" id=18 default="Scalable"}</li>
                    <li>{jrCore_lang skin="jrElastic2" id=19 default="Expandable"}</li>
                </ul>
                <br>
                <ul class="social clearfix">
                    {if strlen($_conf.jrElastic2_facebook_url) > 0 && $_conf.jrElastic2_facebook_url != '0'}
                        <li><a href="{$_conf.jrElastic2_facebook_url}" class="social-facebook" target="_blank"></a></li>
                    {/if}
                    {if strlen($_conf.jrElastic2_twitter_url) > 0 && $_conf.jrElastic2_twitter_url != '0'}
                        <li><a href="{$_conf.jrElastic2_twitter_url}" class="social-twitter" target="_blank"></a></li>
                    {/if}
                    {if strlen($_conf.jrElastic2_google_url) > 0 && $_conf.jrElastic2_google_url != '0'}
                        <li><a href="{$_conf.jrElastic2_google_url}" class="social-google" target="_blank"></a></li>
                    {/if}
                    {if strlen($_conf.jrElastic2_linkedin_url) > 0 && $_conf.jrElastic2_linkedin_url != '0'}
                        <li><a href="{$_conf.jrElastic2_linkedin_url}" class="social-linkedin" target="_blank"></a></li>
                    {/if}
                    {if strlen($_conf.jrElastic2_youtube_url) > 0 && $_conf.jrElastic2_youtube_url != '0'}
                        <li><a href="{$_conf.jrElastic2_youtube_url}" class="social-youtube" target="_blank"></a></li>
                    {/if}
                </ul>

            </div>
        </div>
        <div class="col4">
            {if !jrUser_is_logged_in()}
                <div class="index_form">
                    <div class="wrap clearfix">
                        {if jrCore_module_is_active('jrOneAll') && isset($_conf.jrOneAll_domain) && strlen($_conf.jrOneAll_domain) > 2 }
                            <script src="{$_conf.jrOneAll_domain}/socialize/library.js?v=1.8.4" type="text/javascript"></script>
                        {/if}
                        {jrCore_array name='widget_data' key='type' value="login"}
                        {jrCore_array name='widget' key='widget_data' value=$widget_data}
                        {jrUser_widget_login_display($widget)}
                    </div>
                </div>
            {/if}
        </div>
    </div>

</section>