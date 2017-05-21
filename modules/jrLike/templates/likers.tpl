{if isset($_items)}
    <div class="item">
        <div class="container">
            <h3>{$_params.action}:</h3><br>
            {foreach from=$_items item="item"}
                {if $item@total >= 4}
                    {if $item@first || ($item@iteration % 4) == 1}
                        <div class="row">
                    {/if}
                    <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
                        <div class="p5 center">
                            {if jrCore_checktype($item._user_id, 'number_nz')}
                                <a href="{$jamroom_url}/{$item.profile_url}">@{$item.user_name}</a>
                            {/if}
                        </div>
                    </div>
                    {if $item@last || ($item@iteration % 4) == 0}
                        </div>
                    {/if}
                {else}
                    <div class="row">
                        <div class="col12 last">
                            <div class="p5 center">
                                {if jrCore_checktype($item._user_id, 'number_nz')}
                                    <a href="{$jamroom_url}/{$item.profile_url}">@{$item.user_name}</a>
                                {/if}
                            </div>
                        </div>
                    </div>
                {/if}
            {/foreach}
        </div>
    </div>
{/if}
