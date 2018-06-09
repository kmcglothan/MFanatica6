{if $_conf.jrMaestro_list_1_active == 'on'}
    <section class="features">
        <div class="row animatedParent">
            <h2 class="animated fadeInUp">{$_conf.jrMaestro_list_1_headline}</h2>
        </div>
        <div class="row animatedParent animateOnce">
            <div class="wrap animated fadeInUp">
                <div id="list" class="clearfix animatedParent animateOnce">
                    {$prefix = jrCore_db_get_prefix("`$_conf.jrMaestro_list_1_type`")}
                    {$require_image = "`$prefix`_image"}
                    {if strlen($_conf.jrMaestro_list_1_ids) > 0}
                        {$s1 = "_item_id in `$_conf.jrMaestro_list_1_ids`"}
                    {/if}
                    {jrCore_list module=$_conf.jrMaestro_list_1_type search1=$s1 order_by=$_conf.jrMaestro_list_1_order limit=8 template="index_item_1.tpl" require_image=$require_image}
                </div>
            </div>
        </div>

        {if $_conf.jrMaestro_list_1_more_show == 'on'}
            <button onclick="jrCore_window_location('{$_conf.jrMaestro_list_1_more_url}')" class="see_more">{$_conf.jrMaestro_list_1_more_text}</button>
        {/if}
        <div class="down">
            <a href="#"></a>
        </div>
    </section>
{/if}

{if $_conf.jrMaestro_list_2_active == 'on'}

    {$class = ','|explode:"one,two,three,four,five"}
    {assign var=unique_id value=0|mt_rand:4}
    {if $_conf.jrMaestro_randomize == 'off'}
        {$unique_id = $_conf.jrMaestro_default_bg}
    {/if}

    <section class="features dark {$class[$unique_id]}">
       <div class="overlay"></div>
        <div class="row animatedParent">
            <h2 class="animated fadeInUp">{$_conf.jrMaestro_list_2_headline}</h2>
        </div>
        <div class="row animatedParent animateOnce">
            <div class="wrap animated fadeInUp">
                <div id="list" class="clearfix">
                    {$prefix = jrCore_db_get_prefix("`$_conf.jrMaestro_list_2_type`")}
                    {$require_image = "`$prefix`_image"}
                    {if strlen($_conf.jrMaestro_list_2_ids) > 0}
                        {$s2 = "_item_id in `$_conf.jrMaestro_list_2_ids`"}
                    {/if}
                    {jrCore_list module=$_conf.jrMaestro_list_2_type search1=$s2 order_by=$_conf.jrMaestro_list_2_order limit=5 template="index_item_2.tpl" require_image=$require_image}
                </div>
            </div>
        </div>

        {if $_conf.jrMaestro_list_2_more_show == 'on'}
            <button onclick="jrCore_window_location('{$_conf.jrMaestro_list_2_more_url}')" class="see_more">{$_conf.jrMaestro_list_2_more_text}</button>
        {/if}
        <div class="down">
            <a href="#"></a>
        </div>
    </section>
{/if}

{if $_conf.jrMaestro_list_3_active == 'on'}
    <section class="features">
        <div class="row animatedParent">
            <h2 class="animated fadeInUp">{$_conf.jrMaestro_list_3_headline}</h2>
        </div>
        <div class="row animatedParent animateOnce">
            <div class="wrap animated fadeInUp">
                <div id="list" class="clearfix">
                    {$prefix = jrCore_db_get_prefix("`$_conf.jrMaestro_list_3_type`")}
                    {$require_image = "`$prefix`_image"}
                    {if strlen($_conf.jrMaestro_list_3_ids) > 0}
                        {$s3 = "_item_id in `$_conf.jrMaestro_list_3_ids`"}
                    {/if}
                    {jrCore_list module=$_conf.jrMaestro_list_3_type search1=$s3 order_by=$_conf.jrMaestro_list_3_order limit=5 template="index_item_3.tpl" require_image=$require_image}
                </div>
            </div>
        </div>

        {if $_conf.jrMaestro_list_3_more_show == 'on'}
            <button onclick="jrCore_window_location('{$_conf.jrMaestro_list_3_more_url}')" class="see_more">{$_conf.jrMaestro_list_3_more_text}</button>
        {/if}
        <div class="down up">
            <a href="#"></a>
        </div>
    </section>
{/if}

