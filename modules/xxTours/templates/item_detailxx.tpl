<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="../../../modules/xxTours/js/tour_map.js"></script>
<script src="https://maps.google.com/maps/api/js?key=AIzaSyAInIeH3sX4HZSMHNfERyY5TOgyR_wh4-E&sensor=true"></script>

{jrCore_module_url module="xxTours" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_detail_buttons module="xxTours" item=$item}
        </div>
        <h1>{$item.tours_title}</h1>

        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo;
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="xxTours" id="10" default="Tours"}</a> &raquo; {$_post._2|default:"Tours"}
        </div>
    </div>

    <div class="block_content">

        <div class="item">
            <div class="container">
                <div class="row">
                    <div class="col3">
                        <div class="block_image center">

                            {foreach from=$item item="v" key="k"}
                                {if strpos($k, 'tours') === 0 && (substr($v,0,6)) == 'image/'}
                                    {assign var="type" value=$k|substr:0:-5}
                                    {jrCore_module_function function="jrImage_display" module="xxTours" type=$type item_id=$item._item_id size="large" alt=$item.tours_title width=false height=false class="iloutline img_scale"}
                                    <br>
                                {/if}
                            {/foreach}

                            {jrCore_module_function function="jrRating_form" type="star" module="xxTours" index="1" item_id=$item._item_id current=$item.tours_rating_1_average_count|default:0 votes=$item.tours_rating_1_count|default:0}

                        </div>
                    </div>
                    <div class="col9 last">
                        <div class="p5">
                            <h1>Calculate your route</h1>
                            <form id="calculate-route" name="calculate-route" action="#" method="get">
                                <label for="from">From:</label>
                                <input type="text" id="from" name="from" required="required" placeholder="An address" size="30" />
                                <a id="from-link" href="#">Get my position</a>
                                <br />

                                <label for="to">To:</label>
                                <input type="text" id="to" name="to" required="required" placeholder="Another address" size="30" />

                                <br />
                                {jrCore_list module="xxTours" template="null"  return_keys="_item_id" profile_id="_profile_id" assign="_xt"}
                                {foreach from=$_items item="item"}
                                    <tr>
                                        <td><img width="50" height="50" src="/tour_images/{$tour.tour_image_name}" /></td>
                                        <td><a style="cursor:pointer;" class="tour_title" id="{$item.tours_id}">{$item.tours_title}</a></td>
                                    </tr>
                                {/foreach}

                                <input type="submit" value="Get your Route"/>
                                <input type="reset" />
                            </form>
                            <div id="map"></div>
                            <p id="error"></p>

                            <h2>{$item.tours_title}</h2><br>
                                <table border='5' cellspacing='5' cellpadding='5'>
                            {foreach from=$item item="v" key="k"}
                                {assign var="m" value="Tours"}
                                {assign var="l" value=$m|strlen}
                                {if substr($k,0,$l) == "tours"}
                                    {if strpos($k, 'venue_address')}
                                     <tr>
                                         <th><span class="info">{$k}:</span></th><td><span class="info_c">{$v}</span>&nbsp;&nbsp;<span><a href="#" onclick="setInputValue('to', '{$v}')">Get This Venue's Directions!</a></span></td>
                                     </tr>
                                    {else}
                                    <tr>
                                    <th><span class="info">{$k}:</span></th><td><span class="info_c">{$v}</span></td>
                                    </tr>
                                    {/if}
                                {/if}
                            {/foreach}
                                </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {*jrCore_item_detail_features module="xxTours" item=$item*}
    </div>

</div>
