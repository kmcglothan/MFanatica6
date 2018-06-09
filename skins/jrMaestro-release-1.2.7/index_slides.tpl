<section class="index">
    <div class="largeScreen">
        <div id="slides" style="display: none;">
            {if $_conf.jrMaestro_slide_1_active != 'off'}
                {jrCore_image image="slide_1.jpg" width="1903" height="793" class="img_scale"}
            {/if}
            {if $_conf.jrMaestro_slide_2_active != 'off'}
                {jrCore_image image="slide_2.jpg" width="1903" height="793" class="img_scale"}
            {/if}
            {if $_conf.jrMaestro_slide_3_active != 'off'}
                {jrCore_image image="slide_3.jpg" width="1903" height="793" class="img_scale"}
            {/if}
            {if $_conf.jrMaestro_slide_4_active != 'off'}
                {jrCore_image image="slide_4.jpg" width="1903" height="793" class="img_scale"}
            {/if}
            {if $_conf.jrMaestro_slide_5_active != 'off'}
                {jrCore_image image="slide_5.jpg" width="1903" height="793" class="img_scale"}
            {/if}
        </div>
    </div>
    <div class="smallScreen">
        <div id="slides" style="display: none;">
            {if $_conf.jrMaestro_slide_1_active != 'off'}
                {jrCore_image image="slide_1_mobile.jpg" width="793" height="1115" class="img_scale"}
            {/if}
            {if $_conf.jrMaestro_slide_2_active != 'off'}
                {jrCore_image image="slide_2_mobile.jpg" width="793" height="1115" class="img_scale"}
            {/if}
            {if $_conf.jrMaestro_slide_3_active != 'off'}
                {jrCore_image image="slide_3_mobile.jpg" width="793" height="1115" class="img_scale"}
            {/if}
            {if $_conf.jrMaestro_slide_4_active != 'off'}
                {jrCore_image image="slide_4_mobile.jpg" width="793" height="1115" class="img_scale"}
            {/if}
            {if $_conf.jrMaestro_slide_5_active != 'off'}
                {jrCore_image image="slide_5_mobile.jpg" width="793" height="1115" class="img_scale"}
            {/if}
        </div>
    </div>
    <div class="texts">
        <div class="wrap">
            {if $_conf.jrMaestro_slide_1_active != 'off'}
                <div id="1">
                    <h1>{$_conf.jrMaestro_slide_1_headline}</h1>
                    <p>{$_conf.jrMaestro_slide_1_text}</p>
                    <a class="read_more" href="{$_conf.jrMaestro_slide_1_url}">{jrCore_lang skin="jrMaestro" id=71 default="Read More"}</a>
                </div>
            {/if}
            {if $_conf.jrMaestro_slide_2_active != 'off'}
                <div id="2">
                    <h1>{$_conf.jrMaestro_slide_2_headline}</h1>
                    <p>{$_conf.jrMaestro_slide_2_text}</p>
                    <a class="read_more" href="{$_conf.jrMaestro_slide_2_url}">{jrCore_lang skin="jrMaestro" id=71 default="Read More"}</a>
                </div>
            {/if}
            {if $_conf.jrMaestro_slide_3_active != 'off'}
                <div id="3">
                    <h1>{$_conf.jrMaestro_slide_3_headline}</h1>
                    <p>{$_conf.jrMaestro_slide_3_text}</p>
                    <a class="read_more" href="{$_conf.jrMaestro_slide_3_url}">{jrCore_lang skin="jrMaestro" id=71 default="Read More"}</a>
                </div>
            {/if}
            {if $_conf.jrMaestro_slide_4_active != 'off'}
                <div id="4">
                    <h1>{$_conf.jrMaestro_slide_4_headline}</h1>
                    <p>{$_conf.jrMaestro_slide_4_text}</p>
                    <a class="read_more" href="{$_conf.jrMaestro_slide_4_url}">{jrCore_lang skin="jrMaestro" id=71 default="Read More"}</a>
                </div>
            {/if}
            {if $_conf.jrMaestro_slide_5_active != 'off'}
                <div id="5">
                    <h1>{$_conf.jrMaestro_slide_5_headline}</h1>
                    <p>{$_conf.jrMaestro_slide_5_text}</p>
                    <a class="read_more" href="{$_conf.jrMaestro_slide_5_url}">{jrCore_lang skin="jrMaestro" id=71 default="Read More"}</a>
                </div>
            {/if}
        </div>
    </div>
    <div class="down">
        <a href="#"></a>
    </div>
</section>