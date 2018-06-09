{* prev/next pager for the index.tpl timeline ajax *}
{if $info.next_page > 0}
    {jrCore_module_url module="jrImage" assign="murl"}

    <div id="moreHolder{$info.this_page}" class="action">
        <div class="wrap">
            <div class="item center" style="margin:0" onclick="jrISkin_load_more_timeline('{$info.next_page}','{$info.this_page}');">
                <div class="wrap">
                    {jrCore_lang module="jrCore" id="73" default="working..." assign="working"}
                    <div id="moreLoader" class="p10" style="display:none"><img src="{$jamroom_url}/{$murl}/img/module/jrCore/loading.gif" alt="{$working|jrCore_entity_string}"></div>
                    <a class="moreButton">{jrCore_lang skin="jrISkin" id=118 default="Load More"}</a>
                </div>
            </div>
        </div>
    </div>


{elseif $info.total_items > 0}
    <div class="action">
        <div class="wrap">
            <div class="item center" style="margin: 0">
                <div class="wrap">
                    <a id="back_to_top">{jrCore_lang skin="jrISkin" id=119 default="Back To Top"}</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#back_to_top').click(function()
        {
            $.scrollTo(0, 1500);
        });
    </script>
{/if}
