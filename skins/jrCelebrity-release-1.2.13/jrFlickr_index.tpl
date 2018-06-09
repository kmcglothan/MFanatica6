{jrCore_lang module="jrFlickr" id=2 default="Flickr Images" assign="page_title"}
{jrCore_module_url module="jrFlickr" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="fs">
    <div class="row">
        <div class="col12">
            <div>
                <div class="box">
                    {jrCelebrity_sort template="icons.tpl" nav_mode="jrFlickr" profile_url=$profile_url}
                    <span>{$page_title}</span>
                    {jrSearch_module_form module="jrFlickr" fields="flickr_title,flickr_caption,flickr_description"}
                    <div class="box_body">
                        <div class="wrap">
                            <div class="media">
                                <div class="wrap">
                                    <div id="list">
                                        {capture name="template" assign="flickr_tpl"}
                                        {literal}
                                            {jrCore_module_url module="jrFlickr" assign="murl"}
                                            {foreach from=$_items item="item"}
                                            {if $item@iteration === 1 || ($item@iteration % 4) === 1}
                                            <div class="row">
                                                {/if}
                                                <div class="col3{if ($item@iteration % 4) === 0} last{/if}">
                                                    <div class="p5 center">
                                                        {assign var="_data" value=$item.flickr_data|json_decode:true}
                                                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.flickr_title_url}" title="{$item.flickr_title|jrCore_entity_string}">
                                                            <img src="{jrCore_server_protocol}://farm{$_data.attributes.farm}.staticflickr.com/{$_data.attributes.server}/{$_data.attributes.id}_{$_data.attributes.secret}_n.jpg" class="img_scale" alt="{$item.flickr_title|jrCore_entity_string}">
                                                        </a>
                                                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.flickr_title_url}" title="{$item.flickr_title|jrCore_entity_string}">{$item.flickr_title|truncate:30}</a>
                                                        <br><a href="{$jamroom_url}/{$item.profile_url}" style="margin-bottom:10px">@{$item.profile_url}</a>
                                                    </div>
                                                </div>
                                                {if ($item@iteration % 4) === 0 || $item@last}
                                            </div>
                                            {/if}
                                            {/foreach}
                                        {/literal}
                                        {/capture}

                                        {jrCore_list module="jrFlickr" order_by="_updated desc" pagebreak=12 page=$_post.p pager=true template=$flickr_tpl}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{jrCore_include template="footer.tpl"}

