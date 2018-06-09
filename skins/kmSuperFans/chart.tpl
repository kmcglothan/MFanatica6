{jrCore_include template="header.tpl"}
<section class="featured">
    <div class="row">
        <div class="col12">
            <div class="head">
                {jrCore_icon icon="stats" size="20" color="ff5500"} <span> {jrCore_lang skin="kmSuperFans" id=69 default="Top 200"}</span>
            </div>
            <div class="index_chart">
                <div class="list_item">
                    <div class="table center">
                        <div class="table-row">
                            <div class="table-cell" style="width: 50px;">
                                #
                            </div>
                            <div class="table-cell" style="width: 50px;">
                                dir
                            </div>
                            <div class="table-cell desk" style="width: 50px;">
                                last
                            </div>
                            <div class="table-cell desk" style="width: 50px">
                                &nbsp;
                            </div>
                            <div class="table-cell desk" style="width: 42px;">
                                &nbsp;
                            </div>
                            <div class="table-cell">
                                {jrCore_lang skin="kmSuperFans" id="30" default="Title"}
                            </div>
                            <div class="table-cell desk" style="width: 220px">
                                {jrCore_lang skin="kmSuperFans" id="31" default="Artist"}
                            </div>

                            <div class="table-cell desk" style="width: 120px;">
                                Genre
                            </div>
                            <div class="table-cell desk" style="width: 80px;">
                                {jrCore_lang skin="kmSuperFans" id="58" default="Plays"}
                            </div>
                            <div class="table-cell chart_buttons" style="width:150px; text-align: right; position: relative;">
                                {jrCore_module_url module="jrImage" assign="iurl"}
                                {if $_conf.kmSuperFans_require_price_3 == 'on'}
                                    {$s2 = "audio_file_item_price > 0"}
                                {/if}
                                {$days = $_conf.kmSuperFans_chart_days}
                                {if isset($_post.days) && strlen($_post.days) > 0}
                                    {$days = $_post.days}
                                {/if}
                                <div id="chartLoader" class="p10" style="display:none"><img src="{$jamroom_url}/skins/kmSuperFans/img/ajax-loader.gif" alt="{$working|jrCore_entity_string}"></div>
                                <select class="form_select" id="chart_days" onchange="jrCore_window_location('{$jamroom_url}/chart/days=' + this.value)">
                                    <option {if $days == '1'}selected="selected"{/if} value="1">1 {jrCore_lang skin="kmSuperFans" id=61 default="Days"}</option>
                                    <option {if $days == '7'}selected="selected"{/if} value="7">7 {jrCore_lang skin="kmSuperFans" id=61 default="Days"}</option>
                                    <option {if $days == '14'}selected="selected"{/if} value="14">14 {jrCore_lang skin="kmSuperFans" id=61 default="Days"}</option>
                                    <option {if $days == '30'}selected="selected"{/if} value="30">30 {jrCore_lang skin="kmSuperFans" id=61 default="Days"}</option>
                                    <option {if $days == '90'}selected="selected"{/if} value="90">90 {jrCore_lang skin="kmSuperFans" id=61 default="Days"}</option>
                                    <option {if $days == '365'}selected="selected"{/if} value="365">365 {jrCore_lang skin="kmSuperFans" id=61 default="Days"}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="list" id="chart">
                    {jrCore_list module="jrAudio" chart_field="audio_file_stream_count" search=$s2 chart_days=$days pagebreak=25 page=$_post.p limit="200" pager="true" template="index_chart_item.tpl"}
                </div>
            </div>
        </div>
    </div>
</section>
{jrCore_include template="footer.tpl"}

