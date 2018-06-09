{jrCore_include template="header.tpl" show_main_box=1}

<div class="container">

    {if $_conf.jrProximaAlpha_ft_1_active == 'on'}
    <div class="row">
        <div class="col7">
            <div id="feature-box-1" class="feature-box feature-box-1">
                <h2 class="feature-headline feature-headline-1">{$_conf.jrProximaAlpha_ft_1_headline}</h2>
                <span class="feature-text feature-text-1">{$_conf.jrProximaAlpha_ft_1_text}</span>
            </div>
        </div>
        <div class="col5 last">
            <div class="feature-img">
                {jrCore_image skin="jrProximaAlpha" image="feature-img-1.png" class="img_scale" alt=$_conf.jrProximaAlpha_ft_1_headline|jrCore_entity_string}
            </div>
        </div>
    </div>
    {/if}


    {if $_conf.jrProximaAlpha_ft_2_active == 'on'}
    <hr class="feature-divider">
    <div class="row">
        <div class="col5">
            <div class="feature-img">
                {jrCore_image skin="jrProximaAlpha" image="feature-img-2.png" class="img_scale" alt=$_conf.jrProximaAlpha_ft_1_headline|jrCore_entity_string}
            </div>
        </div>
        <div class="col7 last">
            <div id="feature-box-2" class="feature-box feature-box-2">
                <h2 class="feature-headline feature-headline-2">{$_conf.jrProximaAlpha_ft_2_headline}</h2>
                <span class="feature-text feature-text-2">{$_conf.jrProximaAlpha_ft_2_text}</span>
            </div>
        </div>
    </div>
    {/if}


    {if $_conf.jrProximaAlpha_ft_3_active == 'on'}
    <hr class="feature-divider">
    <div class="row">
        <div class="col7">
            <div id="feature-box-3" class="feature-box feature-box-3">
                <h2 class="feature-headline feature-headline-3">{$_conf.jrProximaAlpha_ft_3_headline}</h2>
                <span class="feature-text feature-text-3">{$_conf.jrProximaAlpha_ft_3_text}</span>
            </div>
        </div>
        <div class="col5 last">
            <div class="feature-img">
                {jrCore_image skin="jrProximaAlpha" image="feature-img-3.png" class="img_scale" alt=$_conf.jrProximaAlpha_ft_3_headline|jrCore_entity_string}
            </div>
        </div>
    </div>
    {/if}

</div>

{jrCore_include template="footer.tpl" show_main_box=1}

