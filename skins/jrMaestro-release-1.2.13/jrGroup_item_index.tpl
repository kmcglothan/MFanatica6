{jrCore_module_url module="jrGroup" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrMaestro_breadcrumbs module="jrGroup" profile_url=$profile_url profile_name=$profile_name page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrGroup" profile_id=$_profile_id}
    </div>
</div>


<div class="box">
    {jrMaestro_sort template="icons.tpl" nav_mode="jrGroup" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap">
            {* see if we have any featured groups *}
            {capture name="row_template" assign="tpl"}
            {literal}
            {if $info.total_items > 0}
            {jrCore_module_url module="jrGroup" assign="gurl"}
            <div class="container">
                <div class="row">
                    {foreach $_items as $item}
                    {if $item@iteration == 4}
                    <div class="col3 last">
                        {else}
                        <div class="col3">
                            {/if}
                            <div class="item center">
                                <a href="{$jamroom_url}/{$item.profile_url}/{$gurl}/{$item._item_id}/{$item.group_title_url}">
                                    {jrCore_module_function function="jrImage_display" module="jrGroup" type="group_image" item_id=$item._item_id size="large" crop="auto" class="img_scale" alt=$item.group_title width=false height=false}
                                </a><a href="{$jamroom_url}/{$item.profile_url}/{$gurl}/{$item._item_id}/{$item.group_title_url}">{$item.group_title}</a>
                            </div>
                        </div>
                        {/foreach}
                    </div>
                </div>
                {/if}
                {/literal}
                {/capture}
                {jrCore_list module="jrGroup" profile_id=$_profile_id search="group_featured = on" order_by="_created desc" limit=4 template=$tpl assign="featured"}
                {if strlen($featured) > 10}

                    <div id="list">
                        {$featured}
                    </div>

                {/if}

                <div id="list">
                    {jrCore_list module="jrGroup" profile_id=$_profile_id order_by="_created desc" pagebreak="8" page=$_post.p pager=true}
                </div>
        </div>
    </div>
</div>
