{if isset($_items)}
  {jrCore_module_url module="jrBlog" assign="murl"}
  {foreach from=$_items item="item"}
      {if $item.list_rank == 1}
          <div class="block_content">

              <div class="item blogpost">
                  <h1><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h1>
                  <div class="p10">
                      {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                          <div class="blog_image">
                              {jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="medium" alt=$item.blog_title width=false height=false class="img_scale"}
                          </div>
                      {/if}

                      {$item.blog_text|jrCore_strip_html|truncate:500}

                  </div>

                  <div class="table" style="border-top:1px solid #DDD;width:auto">
                      <div class="table-row">
                          <div class="table-cell" style="padding:10px;width:50%">
                              <span class="info">{jrCore_lang module="jrBlog" id="26" default="Posted in"}: <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.blog_category_url|default:"default"}">{$item.blog_category|default:"default"}</a></span>
                              {if jrCore_module_is_active('jrComment')}
                                  <span class="info"> | <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comment_section"> {$item.blog_comment_count|default:0} {jrCore_lang module="jrBlog" id="27" default="comments"}</a></span>
                              {/if}
                          </div>
                          <div class="table-cell" style="padding:10px;width:50%;text-align:right">
                              {* check to see if the blog has a pagebreak in it *}
                              {if strpos($item.blog_text,'<!-- pagebreak -->')}
                                  <span class="info"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_lang module="jrBlog" id="25" default="Read more"} &raquo;</a></span>
                              {/if}
                          </div>
                      </div>
                  </div>
                  <div class="clear"></div>

              </div>

          </div>
      {/if}
  {/foreach}
{else}
    <div class="no-items">
        <h1>{jrCore_lang skin="jrElastic2" id="62" default="No items found"}</h1>
    </div>
{/if}