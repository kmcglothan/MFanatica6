{if isset($_items)}
    {jrCore_module_url module="jrPage" assign="murl"}
    {foreach from=$_items item="item"}

        <div class="block">

            <div class="title">

                <div class="block_config">
                    {if jrUser_is_admin()}
                        <div class="block_config">
                            <a onclick="jrCore_window_location('{$jamroom_url}/{$murl}/browse');" title="update" href="{$jamroom_url}/{$murl}/browse">{jrCore_icon icon="gear" size="16"}</a>
                        </div>
                    {/if}
                </div>

                <h1>{$item.page_title}</h1>
                <div class="breadcrumbs">
                    <a href="{$jamroom_url}/">{jrCore_lang module="jrPage" id="20" default="home"}</a> &raquo; {$item.page_title}
                </div>
            </div>

            <div class="block_content">

                <div class="item">
                    <div id="jrpage_body">
                        <div class="normal">
                            {$item.page_body|jrCore_format_string:$item.profile_quota_id}
                        </div>
                    </div>
                </div>

                {* bring in module features if enabled *}
                {if !isset($item.page_features) || $item.page_features == 'on'}
                    {jrCore_item_detail_features module="jrPage" item=$item}
                {/if}

            </div>

        </div>

    {/foreach}
{/if}
