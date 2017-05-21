{jrCore_module_url module="jrImage" assign="iurl"}
<img src="{$jamroom_url}/{$iurl}/img/module/jrBirthday/cake.png" width="48" height="48" style="vertical-align:middle;margin-right:12px">
{jrCore_lang module="jrBirthday" id=5 default="Today is %1's Birthday" 1=$item.action_data.user_name}
{if jrUser_is_logged_in() && $_user._user_id != $item.action_data._user_id}
  - <span class="birthday_share">{jrCore_lang module="jrBirthday" id=6 default="share a birthday wish"}</span>
{/if}
