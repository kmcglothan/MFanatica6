        </ul>
    </section>
</div>

{* We want to allow the item owner to re-order *}
{if jrProfile_is_profile_owner($item._profile_id)}

    <style type="text/css">
    .sortable {
        margin: auto;
        padding: 0;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .sortable li {
        list-style: none;
        cursor: move;
    }
    li.sortable-placeholder {
        border: 1px dashed #BBB;
        background: none;
        height: 100px;
        margin: 12px;
    }
    </style>

    <script>
        $(function() {
            $('.sortable').sortable().bind('sortupdate', function(event,ui) {
                //Triggered when the user stopped sorting and the DOM position has changed.
                var o = $('ul.sortable li').map(function(){
                    return $(this).data("id");
                }).get();
                $.post(core_system_url + '/' + jrDocs_url + "/order_update/__ajax=1", {
                    doc_order: o
                });
            });
        });
    </script>

{/if}

{* page jumper *}
{if !empty($item._prev) || !empty($item._next)}
    {jrCore_module_url module="jrDocs" assign="murl"}
    <div class="doc_pager_box">
        <div class="doc_pager">
            <div style="display:table-row">
                <div class="doc_pager_cell" style="width:5%;">
                    {if !empty($item._prev)}
                        <input type="button" value="&lt;" class="form_button" onclick="window.location='{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.doc_category_url}/{$item._prev._item_id}/{$item._prev.doc_title_url}'">
                    {/if}
                </div>
                <div class="doc_pager_cell" style="width:37%;">
                    {if !empty($item._prev)}
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.doc_category_url}/{$item._prev._item_id}/{$item._prev.doc_title_url}">{$item._prev.doc_title}</a>
                    {/if}
                </div>
                <div class="doc_pager_cell center" style="width:16%">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.doc_category_url}">{$item.doc_category}</a>
                </div>
                <div class="doc_pager_cell right" style="width:37%">
                    {if !empty($item._next)}
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.doc_category_url}/{$item._next._item_id}/{$item._next.doc_title_url}">{$item._next.doc_title}</a>
                    {/if}
                </div>
                <div class="doc_pager_cell right" style="width:5%">
                    {if !empty($item._next)}
                        <input type="button" value="&gt;" class="form_button" onclick="window.location='{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.doc_category_url}/{$item._next._item_id}/{$item._next.doc_title_url}'">
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/if}

{* bring in module features *}
{jrCore_item_detail_features module="jrDocs" item=$item}

    </div>
</div>

{if isset($_conf.jrDocs_show_related) && $_conf.jrDocs_show_related == 'on' && (!isset($item.doc_show_related) || $item.doc_show_related == 'on')}
<script>
/**
 * Turn each tag of other documents into a link that shows links to related docs
 */
$(document).ready(function() {
    $('.docs_related').on('click', function(e) {
        $('.doc_tag_options').remove();
        var tag = $(this).attr("data-tag");
        var left = e.pageX + 5;
        var top = e.pageY + 5;
        $('body').append('<div class="doc_tag_options" style="left:' + left + 'px;top:' + top + 'px"><div class="doc_tag_inner"><img id="doc_tag_form_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="working..."></div></div>');
        var u = core_system_url + '/' + jrDocs_url + '/get_related/' + tag + '/profile_id={$item._profile_id}/__ajax=1';
        $.ajax({
            type: 'GET',
            url: u,
            cache: false,
            dataType: 'html',
            success: function (r)
            {
                $('.doc_tag_inner').html(r);
            }
        });
    });
});

/**
 * When the tags option panel is not clicked on, hide it.
 */
$(document).mouseup(function(e) {
    var c = $('.doc_tag_options');
    if (!c.is(e.target) && c.has(e.target).length === 0) {
        c.hide();
    }
});
</script>
{/if}