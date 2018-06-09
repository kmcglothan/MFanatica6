
<section class="features animatedParent">
    <h2 class="animated fadeInUp">{$_conf.jrISkin_headline_1}</h2>
    <div class="list_wrap">
        {if $_conf.jrISkin_list_1_active == 'on'}
            {$prefix = jrCore_db_get_prefix("`$_conf.jrISkin_list_1_type`")}
            {$require_image = "`$prefix`_image"}
            <div class="index_list clearfix page_1" style="padding: 5px 5px 0;">
                {if strlen($_conf.jrISkin_list_1_ids) > 0}
                    <div>{jrCore_list
                        module=$_conf.jrISkin_list_1_type
                        search="_item_id in `$_conf.jrISkin_list_1_ids`"
                        order_by=$_conf.jrISkin_list_1_order
                        limit="16"
                        template="index_item_1.tpl"
                        require_image=$require_image
                        }
                    </div>
                    {jrCore_list
                    module=$_conf.jrISkin_list_1_type
                    search="_item_id in `$_conf.jrISkin_list_1_ids`"
                    order_by=$_conf.jrISkin_list_1_order
                    limit="16"
                    template='blank.tpl'
                    pagebreak="4"
                    pager='true'
                    pager_template="index_list_pager.tpl"
                    require_image=$require_image
                    }
                {else}
                    <div>{jrCore_list
                        module=$_conf.jrISkin_list_1_type
                        order_by=$_conf.jrISkin_list_1_order
                        limit="16"
                        template="index_item_1.tpl"
                        require_image=$require_image
                        }</div>
                    {jrCore_list
                    module=$_conf.jrISkin_list_1_type
                    order_by=$_conf.jrISkin_list_1_order
                    limit="16"
                    template='blank.tpl'
                    pagebreak="4"
                    pager='true'
                    pager_template="index_list_pager.tpl"
                    require_image=$require_image
                    }
                {/if}
            </div>
        {/if}
        {if $_conf.jrISkin_list_2_active == 'on'}
            {$prefix = jrCore_db_get_prefix("`$_conf.jrISkin_list_2_type`")}
            {$require_image = "`$prefix`_image"}
            <div class="index_list clearfix page_1" style="padding: 0 5px 5px;">
                {if strlen($_conf.jrISkin_list_2_ids) > 0}
                    <div>{jrCore_list
                        module=$_conf.jrISkin_list_2_type
                        order_by=$_conf.jrISkin_list_2_order
                        search="_item_id in `$_conf.jrISkin_list_2_ids`"
                        limit="16"
                        template="index_item_2.tpl"
                        require_image=$require_image
                        }
                    </div>
                    {jrCore_list
                    module=$_conf.jrISkin_list_2_type
                    order_by=$_conf.jrISkin_list_2_order
                    search="_item_id in `$_conf.jrISkin_list_2_ids`"
                    limit="16" template='blank.tpl'
                    pagebreak="4"
                    pager='true'
                    pager_template="index_list_pager.tpl"
                    require_image=$require_image
                    }
                {else}
                    <div>{jrCore_list
                        module=$_conf.jrISkin_list_2_type
                        order_by=$_conf.jrISkin_list_2_order
                        limit="16"
                        template="index_item_2.tpl"
                        require_image=$require_image
                        }
                    </div>
                    {jrCore_list
                    module=$_conf.jrISkin_list_2_type
                    order_by=$_conf.jrISkin_list_2_order
                    limit="16"
                    template='blank.tpl'
                    pagebreak="4"
                    pager='true'
                    pager_template="index_list_pager.tpl"
                    require_image=$require_image
                    }
                {/if}
            </div>
        {/if}
        {if $_conf.jrISkin_see_more_1_active == "on"}
            <button class="index_button" onclick="jrCore_window_location('{$_conf.jrISkin_see_more_1_url}')">{$_conf.jrISkin_see_more_1_text}</button>
        {/if}
    </div>
    <div class="down">
        <a href="#"></a>
    </div>
</section>


<section class="features dark animatedParent">
    <h2 class="animated fadeInUp">{$_conf.jrISkin_headline_2}</h2>
    <div class="list_wrap">
        {if $_conf.jrISkin_list_3_active == 'on'}
            {$prefix = jrCore_db_get_prefix("`$_conf.jrISkin_list_3_type`")}
            {$require_image = "`$prefix`_image"}
            <div class="index_list clearfix page_1" style="padding: 5px 5px 0;">
                {if strlen($_conf.jrISkin_list_3_ids) > 0}
                    <div>{jrCore_list
                        module=$_conf.jrISkin_list_3_type require_image=$require_image
                        order_by=$_conf.jrISkin_list_3_order
                        search="_item_id in `$_conf.jrISkin_list_3_ids`"
                        limit="16"
                        template="index_item_3.tpl"
                        require_image=$require_image
                        }
                    </div>
                    {jrCore_list
                    module=$_conf.jrISkin_list_3_type
                    order_by=$_conf.jrISkin_list_3_order
                    search="_item_id in `$_conf.jrISkin_list_3_ids`"
                    limit="16"
                    template='blank.tpl'
                    pagebreak="4"
                    pager='true'
                    pager_template="index_list_pager.tpl"
                    require_image=$require_image
                    }
                {else}
                    <div>{jrCore_list
                        module=$_conf.jrISkin_list_3_type
                        order_by=$_conf.jrISkin_list_3_order
                        limit="16"
                        template="index_item_3.tpl"
                        require_image=$require_image
                        }
                    </div>
                    {jrCore_list
                    module=$_conf.jrISkin_list_3_type
                    order_by=$_conf.jrISkin_list_3_order
                    limit="16"
                    template='blank.tpl'
                    pagebreak="4"
                    pager='true'
                    pager_template="index_list_pager.tpl"
                    require_image=$require_image
                    }
                {/if}
            </div>
        {/if}
        {if $_conf.jrISkin_list_4_active == 'on'}
            {$prefix = jrCore_db_get_prefix("`$_conf.jrISkin_list_4_type`")}
            {$require_image = "`$prefix`_image"}
            <div class="index_list clearfix page_1" style="padding: 0 5px 5px;">
                {if strlen($_conf.jrISkin_list_4_ids) > 0}
                    <div>{jrCore_list
                        module=$_conf.jrISkin_list_4_type
                        order_by=$_conf.jrISkin_list_4_order
                        search="_item_id in `$_conf.jrISkin_list_4_ids`"
                        limit="16"
                        template="index_item_4.tpl"
                        require_image=$require_image
                        }
                    </div>
                    {jrCore_list
                    module=$_conf.jrISkin_list_4_type
                    order_by=$_conf.jrISkin_list_4_order
                    search="_item_id in `$_conf.jrISkin_list_4_ids`"
                    limit="16"
                    template='blank.tpl'
                    pagebreak="4"
                    pager='true'
                    pager_template="index_list_pager.tpl"
                    require_image=$require_image
                    }
                {else}
                    <div>{jrCore_list
                        module=$_conf.jrISkin_list_4_type
                        order_by=$_conf.jrISkin_list_4_order
                        limit="16"
                        template="index_item_4.tpl"
                        require_image=$require_image
                        }
                    </div>
                    {jrCore_list
                    module=$_conf.jrISkin_list_4_type
                    order_by=$_conf.jrISkin_list_4_order
                    limit="16"
                    template='blank.tpl'
                    pagebreak="4"
                    pager='true'
                    pager_template="index_list_pager.tpl"
                    require_image=$require_image
                    }
                {/if}
            </div>
        {/if}
        {if $_conf.jrISkin_see_more_2_active == "on"}
            <button class="index_button" onclick="jrCore_window_location('{$_conf.jrISkin_see_more_2_url}')">{$_conf.jrISkin_see_more_2_text}</button>
        {/if}
    </div>
    <div class="down">
        <a href="#"></a>
    </div>
</section>

<section class="features animatedParent">
    <h2 class="animated fadeInUp">{$_conf.jrISkin_headline_3}</h2>
    <div class="list_wrap">
        {if $_conf.jrISkin_list_5_active == 'on'}
            {$prefix = jrCore_db_get_prefix("`$_conf.jrISkin_list_5_type`")}
            {$require_image = "`$prefix`_image"}
            <div class="index_list clearfix page_1" style="padding: 5px 5px 0;">
                {if strlen($_conf.jrISkin_list_5_ids) > 0}
                    <div>{jrCore_list
                        module=$_conf.jrISkin_list_5_type
                        search="_item_id in `$_conf.jrISkin_list_5_ids`"
                        order_by=$_conf.jrISkin_list_5_order
                        limit="16"
                        template="index_item_5.tpl"
                        require_image=$require_image
                        }
                    </div>
                    {jrCore_list
                    module=$_conf.jrISkin_list_5_type
                    search="_item_id in `$_conf.jrISkin_list_5_ids`"
                    order_by=$_conf.jrISkin_list_5_order
                    limit="16"
                    template='blank.tpl'
                    pagebreak="4"
                    pager='true'
                    pager_template="index_list_pager.tpl"
                    require_image=$require_image
                    }
                {else}
                    <div>{jrCore_list
                        module=$_conf.jrISkin_list_5_type
                        order_by=$_conf.jrISkin_list_5_order
                        limit="16"
                        template="index_item_5.tpl"
                        require_image=$require_image
                        }</div>
                    {jrCore_list
                    module=$_conf.jrISkin_list_5_type
                    order_by=$_conf.jrISkin_list_5_order
                    limit="16"
                    template='blank.tpl'
                    pagebreak="4"
                    pager='true'
                    pager_template="index_list_pager.tpl"
                    require_image=$require_image
                    }
                {/if}
            </div>
        {/if}
        {if $_conf.jrISkin_list_6_active == 'on'}
            {$prefix = jrCore_db_get_prefix("`$_conf.jrISkin_list_6_type`")}
            {$require_image = "`$prefix`_image"}
            <div class="index_list clearfix page_1" style="padding: 0 5px 5px;">
                {if strlen($_conf.jrISkin_list_6_ids) > 0}
                    <div>{jrCore_list
                        module=$_conf.jrISkin_list_6_type
                        order_by=$_conf.jrISkin_list_6_order
                        search="_item_id in `$_conf.jrISkin_list_6_ids`"
                        limit="16"
                        template="index_item_6.tpl"
                        require_image=$require_image
                        }
                    </div>
                    {jrCore_list
                    module=$_conf.jrISkin_list_6_type
                    order_by=$_conf.jrISkin_list_6_order
                    search="_item_id in `$_conf.jrISkin_list_6_ids`"
                    limit="16" template='blank.tpl'
                    pagebreak="4"
                    pager='true'
                    pager_template="index_list_pager.tpl"
                    require_image=$require_image
                    }
                {else}
                    <div>{jrCore_list
                        module=$_conf.jrISkin_list_6_type
                        order_by=$_conf.jrISkin_list_2_order
                        limit="16"
                        template="index_item_6.tpl"
                        require_image=$require_image
                        }
                    </div>
                    {jrCore_list
                    module=$_conf.jrISkin_list_6_type
                    order_by=$_conf.jrISkin_list_6_order
                    limit="16"
                    template='blank.tpl'
                    pagebreak="4"
                    pager='true'
                    pager_template="index_list_pager.tpl"
                    require_image=$require_image
                    }
                {/if}
            </div>
        {/if}
        {if $_conf.jrISkin_see_more_1_active == "on"}
            <button class="index_button" onclick="jrCore_window_location('{$_conf.jrISkin_see_more_3_url}')">{$_conf.jrISkin_see_more_3_text}</button>
        {/if}
    </div>
    <div class="down up">
        <a href="#"></a>
    </div>
</section>