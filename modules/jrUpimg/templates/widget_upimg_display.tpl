{* format the uploaded images *}
{if $info.total_items == 1}

    {* single image, show as a single image. *}
    {if isset($_items)}
        <div class="item" style="padding:0">
            {foreach $_items as $item}
                {jrCore_module_function function="jrImage_display" module="jrUpimg" type="upimg_file" crop=$upimg_crop item_id=$item._item_id size=$upimg_size style="width:100%;margin:0" alt=$item.upimg_file_name width=false height=false}
            {/foreach}
        </div>
    {/if}

{else}

    {* multiple images, show as a slider. *}
    {if isset($_items)}
        <script type="text/javascript">
            $(function() {
                $("#s{$unique_id}").responsiveSlides({
                    auto: true, {* Boolean: Animate automatically, true or false *}
                    speed: 400, {* Integer: Speed of the transition, in milliseconds *}
                    timeout: 4000, {* Integer: Time between slide transitions, in milliseconds *}
                    pager: true, {* Boolean: Show pager, true or false *}
                    random: false, {* Boolean: Randomize the order of the slides, true or false *}
                    pause: true, {* Boolean: Pause on hover, true or false *}
                    maxwidth: 0, {* Integer: Max-width of the slideshow, in pixels *}
                    namespace: "upimg_rslides" {* String: change the default namespace used *}
                });
            });
            {if isset($aspect_w) && isset($aspect_h)}
            $(document).ready(function() {
                var fh = $("#s{$unique_id} li").width();
                $("#s{$unique_id}").height((fh / {$aspect_w}) * {$aspect_h});
            });
            {else}
            $(document).ready(function() {
                var fh = $("#s{$unique_id} li").width();
                $("#s{$unique_id}").height((fh / 3) * 2);
            });
            {/if}
        </script>
        <ul id="s{$unique_id}" class="upimg_rslides callbacks">
            {foreach $_items as $item}
                <li>
                    {jrCore_module_function function="jrImage_display" module="jrUpimg" type="upimg_file" crop=$upimg_crop item_id=$item._item_id size=$upimg_size style="width:100%;margin:0" alt=$item.upimg_file_name width=false height=false}
                </li>
            {/foreach}
        </ul>
    {/if}
{/if}