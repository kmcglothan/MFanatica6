<div class="slides">
    {$slider_count = 0}
    {if jrCore_is_mobile_device()}
        {if $_conf.jrVideoPro_slide_1_active != 'off'}
            <a href="{$_conf.jrVideoPro_slide_1_url}">{jrCore_image image="1m.jpg" width="1440px" height="auto"}</a>
            {math equation="x + y" x=$slider_count y=1 assign="slider_count"}
        {/if}
        {if $_conf.jrVideoPro_slide_2_active != 'off'}
            <a href="{$_conf.jrVideoPro_slide_2_url}">{jrCore_image image="2m.jpg" width="1440px" height="auto"}</a>
            {math equation="x + y" x=$slider_count y=1 assign="slider_count"}
        {/if}
        {if $_conf.jrVideoPro_slide_3_active != 'off'}
            <a href="{$_conf.jrVideoPro_slide_3_url}">{jrCore_image image="3m.jpg" width="1440px" height="auto"}</a>
            {math equation="x + y" x=$slider_count y=1 assign="slider_count"}
        {/if}
        {if $_conf.jrVideoPro_slide_4_active != 'off'}
            <a href="{$_conf.jrVideoPro_slide_4_url}">{jrCore_image image="4m.jpg" width="1440px" height="auto"}</a>
            {math equation="x + y" x=$slider_count y=1 assign="slider_count"}
        {/if}
        {if $_conf.jrVideoPro_slide_5_active != 'off'}
            <a href="{$_conf.jrVideoPro_slide_5_url}">{jrCore_image image="5m.jpg" width="1440px" height="auto"}</a>
            {math equation="x + y" x=$slider_count y=1 assign="slider_count"}
        {/if}
        {if $_conf.jrVideoPro_slide_6_active != 'off'}
            <a href="{$_conf.jrVideoPro_slide_6_url}">{jrCore_image image="6m.jpg" width="1440px" height="auto"}</a>
            {math equation="x + y" x=$slider_count y=1 assign="slider_count"}
        {/if}
    {else}
        {if $_conf.jrVideoPro_slide_1_active != 'off'}
            <a href="{$_conf.jrVideoPro_slide_1_url}">{jrCore_image image="1.jpg" width="1440px" height="auto"}</a>
            {math equation="x + y" x=$slider_count y=1 assign="slider_count"}
        {/if}
        {if $_conf.jrVideoPro_slide_2_active != 'off'}
            <a href="{$_conf.jrVideoPro_slide_2_url}">{jrCore_image image="2.jpg" width="1440px" height="auto"}</a>
            {math equation="x + y" x=$slider_count y=1 assign="slider_count"}
        {/if}
        {if $_conf.jrVideoPro_slide_3_active != 'off'}
            <a href="{$_conf.jrVideoPro_slide_3_url}">{jrCore_image image="3.jpg" width="1440px" height="auto"}</a>
            {math equation="x + y" x=$slider_count y=1 assign="slider_count"}
        {/if}
        {if $_conf.jrVideoPro_slide_4_active != 'off'}
            <a href="{$_conf.jrVideoPro_slide_4_url}">{jrCore_image image="4.jpg" width="1440px" height="auto"}</a>
            {math equation="x + y" x=$slider_count y=1 assign="slider_count"}
        {/if}
        {if $_conf.jrVideoPro_slide_5_active != 'off'}
            <a href="{$_conf.jrVideoPro_slide_5_url}">{jrCore_image image="5.jpg" width="1440px" height="auto"}</a>
            {math equation="x + y" x=$slider_count y=1 assign="slider_count"}
        {/if}
        {if $_conf.jrVideoPro_slide_6_active != 'off'}
            <a href="{$_conf.jrVideoPro_slide_6_url}">{jrCore_image image="6.jpg" width="1440px" height="auto"}</a>
            {math equation="x + y" x=$slider_count y=1 assign="slider_count"}
        {/if}
    {/if}
</div>

{if $slider_count > 1}
    <script type="text/javascript">
        $(document).ready(function(){
            jrVideoPro_initSlide();
        });
    </script>
{else}
    <script type="text/javascript">
        $(document).ready(function(){
            $('.slides').show();
        });
    </script>
{/if}