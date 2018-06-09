<section class="features">
    <div class="row animatedParent">
        <h2 class="animated fadeInUp">{$_conf.jrMSkin_list_1_headline}</h2>
    </div>
    <div class="row">
        <div id="list" class="clearfix">
            {$prefix = jrCore_db_get_prefix("`$_conf.jrMSkin_list_1_type`")}
            {$require_image = "`$prefix`_image"}
            {if strlen($_conf.jrMSkin_list_1_ids) > 0}
                {$s1 = "_item_id in `$_conf.jrMSkin_list_1_ids`"}
            {/if}
            {jrCore_list module=$_conf.jrMSkin_list_1_type search=$s1 order_by=$_conf.jrMSkin_list_1_order limit=10 pagebreak=5 page=1 template="index_item_1.tpl" require_image=$require_image}
        </div>
    </div>
    <div class="row">
        <div id="list2" class="clearfix">
            {jrCore_list module=$_conf.jrMSkin_list_1_type search=$s1 order_by=$_conf.jrMSkin_list_1_order limit=10 pagebreak=5 page=2 template="index_item_1b.tpl" require_image=$require_image}
        </div>
    </div>

    {if $_conf.jrMSkin_list_1_more_show == 'on'}
        <button onclick="jrCore_window_location('{$_conf.jrMSkin_list_1_more_url}')" class="see_more">{$_conf.jrMSkin_list_1_more_text}</button>
    {/if}
</section>

<div class="index_border">
    <div class="bottom black">
       <span class="down">
           <a href="#"></a>
       </span>
    </div>
    <div class="inner"></div>
</div>

<section class="features">
    <div class="row animatedParent">
        <h2 class="animated fadeInUp">{$_conf.jrMSkin_list_2_headline}</h2>
    </div>
    <div class="row">
        <div id="list" class="clearfix">
            {$prefix = jrCore_db_get_prefix("`$_conf.jrMSkin_list_2_type`")}
            {$require_image = "`$prefix`_image"}
            {if strlen($_conf.jrMSkin_list_2_ids) > 0}
                {$s2 = "_item_id in `$_conf.jrMSkin_list_2_ids`"}
            {/if}
            {jrCore_list module=$_conf.jrMSkin_list_2_type search=$s2 order_by=$_conf.jrMSkin_list_2_order limit=12 template="index_item_2.tpl" require_image=$require_image}
        </div>
    </div>

    {if $_conf.jrMSkin_list_2_more_show == 'on'}
        <button onclick="jrCore_window_location('{$_conf.jrMSkin_list_2_more_url}')" class="see_more">{$_conf.jrMSkin_list_2_more_text}</button>
    {/if}


    <div class="bottom black">
       <span class="down">
           <a href="#"></a>
       </span>
    </div>
</section>

{if !jrCore_is_mobile_device()}
    <section class="divider">
        <div class="banner"></div>
        <div>
            <div class="row clearfix">
                <div class="col7">
                    <div class="wrap">
                        <h2>Go {$_conf.jrCore_system_name} Pro</h2>
                        <h3>{jrCore_lang skin="jrMSkin" id=87 default="Retain up to 100% of your earnings and get loads of sales."}</h3>
                        <a href="#" class="see_more">{jrCore_lang skin="jrMSkin" id=88 default="Learn More"}</a>
                    </div>
                </div>
                <div class="col5">
                    <div class="wrap">
                        <p class="pro_header">{$_conf.jrCore_system_name} {jrCore_lang skin="jrMSkin" id=89 default="PRO offer:"}</p>
                        <ul class="feature_list">
                            <li class="checked">{jrCore_lang skin="jrMSkin" id=90 default="Unlimited storage space"}</li>
                            <li class="checked">{jrCore_lang skin="jrMSkin" id=91 default="100% of sales receipts"}</li>
                            <li class="checked">{jrCore_lang skin="jrMSkin" id=92 default="Social network sharing"}</li>
                            <li class="checked">{jrCore_lang skin="jrMSkin" id=93 default="Upload videos"}</li>
                            <li class="checked">{jrCore_lang skin="jrMSkin" id=94 default="Profile banners"}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
{else}
    <div class="index_border">
        <div class="inner"></div>
    </div>
{/if}

<section class="features">
    <div class="row animatedParent">
        <h2 class="animated fadeInUp">{$_conf.jrMSkin_list_3_headline}</h2>
    </div>
    <div class="row">
        <div id="list" class="clearfix" style="max-width:940px; margin: auto; padding: 0 1em;">
            {$prefix = jrCore_db_get_prefix("`$_conf.jrMSkin_list_3_type`")}
            {$require_image = "`$prefix`_image"}
            {if strlen($_conf.jrMSkin_list_3_ids) > 0}
                {$s3 = "_item_id in `$_conf.jrMSkin_list_3_ids`"}
            {/if}
            {jrCore_list module=$_conf.jrMSkin_list_3_type search=$s3 order_by=$_conf.jrMSkin_list_3_order limit=20 template="index_item_3.tpl" require_image=$require_image}
        </div>
    </div>

    {if $_conf.jrMSkin_list_3_more_show == 'on'}
        <button onclick="jrCore_window_location('{$_conf.jrMSkin_list_3_more_url}')" class="see_more">{$_conf.jrMSkin_list_3_more_text}</button>
    {/if}

</section>

<div class="index_border">
    <div class="inner"></div>
    <div class="bottom black">
       <span class="down up">
           <a href="#"></a>
       </span>
    </div>
</div>

<div class="features">
    <div class="row">
        {$_conf.jrMSkin_bottom_text}
    </div>
</div>