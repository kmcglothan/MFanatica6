{if isset($_items)}
  {foreach from=$_items item="item"}
  <div class="col3">
      <div class="p10">
          <div class="image">
              <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="xxlarge" crop="4:3" alt=$item.profile_name title=$item.profile_name class="img_scale"}</a>
              <div class="hover">
                  <div class="table">
                      <div class="table-row">
                          <div class="table-cell">
                              <a href="{$jamroom_url}/{$item.profile_url}" title="{jrCore_lang skin="jrElastic2" id=70 default="View Profile"}">{jrCore_icon icon="profile" size="30" color="ffffff"}</a>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="center">
          <span><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name|truncate:24}</a></span>
      </div>
  </div>
  {/foreach}
{/if}
