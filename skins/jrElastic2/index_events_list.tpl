{if isset($_items)}
    {jrCore_module_url module="jrEvent" assign="murl"}
  {foreach from=$_items item="item"}
  <div class="table event">
      <div class="table-row">
          <div class="table-cell" style="width:50px;">
              {jrCore_module_function function="jrImage_display" module='jrEvent' type='event_image' item_id=$item._item_id size="large" crop="auto" class="img_scale" alt=$item.event_title width=false height=false}
          </div>
          <div class="table-cell">
              <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{$item.event_title}</a> <br>
              {$item.event_location}
          </div>
          {if jrCore_is_mobile_device()}
      </div>
      <div class="table-row">
          {/if}
          <div class="table-cell event-attending">
              {if jrUser_is_logged_in()}
                  {jrEvent_attending_button item=$item}
              {else}
                  {jrCore_module_url module="jrUser" assign="uurl"}
                  <input class="form_button event_attend_button nonattendee" value="{jrCore_lang module="jrEvent" id=34 default="Attending?"}" title="{jrCore_lang module="jrEvent" id=34 default="Attending?"}" onclick="jrCore_window_location('{$jamroom_url}/{$uurl}/login')" type="button">
              {/if}
          </div>
          <div class="table-cell event-date">
              {$item.event_date|jrCore_date_format:"%a, %b %d %r"}
          </div>
      </div>
  </div>
  {/foreach}
{/if}