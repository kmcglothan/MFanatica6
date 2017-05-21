{jrCore_module_url module="jrPoll" assign="murl"}

{if $smarty.now < $item.poll_end_date}
    <script type="text/javascript">
        $(document).ready(function () {
            $('span.countdown').each(function (i,e) {
                var n = Number($(this).text());
                var c = new Date(n);
                if (typeof c != "undefined") {
                    $(this).countdown({ until: c });
                }
            });
        });
    </script>
{/if}

{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrISkin_breadcrumbs module="jrPoll" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {if $_user.quota_jrPoll_allowed == 'on'}
            {jrCore_item_detail_buttons module="jrPoll" item=$item}
        {/if}
    </div>
</div>


<div class="col8">
    <div class="box">
        {jrISkin_sort template="icons.tpl" nav_mode="jrPoll" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap detail_section">
                <div class="media">
                    <div class="wrap">
                        {if $smarty.now > $item.poll_end_date}
                            <div class="poll_status">
                                <span class="poll_closed">{jrCore_lang module="jrPoll" id="49" default="Voting has Ended"}</span>
                            </div>
                        {/if}

                        <div class="row">
                            <div class="col12 last">
                                <div class="item">

                                    {if $smarty.now < $item.poll_start_date}

                                        <div class="poll_countdown">
                                            {jrCore_lang module="jrPoll" id="48" default="Voting begins"}:<br>
                                            <span class="countdown">{$item.poll_start_date}000</span>

                                            <div style="clear:both"></div>
                                        </div>
                                        <div class="p5">{$item.poll_description|jrCore_format_string:$item.profile_quota_id}</div>
                                        <div style="clear:both"></div>

                                    {elseif $smarty.now >= $item.poll_start_date && $smarty.now < $item.poll_end_date}

                                        <div class="poll_countdown">
                                            {jrCore_lang module="jrPoll" id="61" default="Voting closes in"}:<br>
                                            <span class="countdown">{$item.poll_end_date}000</span>
                                            <div style="clear:both"></div>
                                        </div>
                                        <div class="p5">{$item.poll_description|jrCore_format_string:$item.profile_quota_id}</div>
                                        <div style="clear:both"></div>

                                    {else}

                                        {$item.poll_description|jrCore_format_string:$item.profile_quota_id}

                                    {/if}

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col12 last">
                                <div class="poll_choices">

                                    {* Figure winner *}
                                    {assign var="winner" value="0"}
                                    {assign var="votes"  value="0"}
                                    {if $smarty.now > $item.poll_end_date}
                                        {foreach $item.poll_options as $k => $_v}
                                            {if isset($_v.votes) && $_v.votes > $votes}
                                                {assign var="votes"  value=$_v.votes}
                                                {assign var="winner" value=$k}
                                            {/if}
                                        {/foreach}
                                    {/if}

                                    {if $smarty.now >= $item.poll_start_date}
                                        <div style="display:table;width:95%">
                                            <div style="display:table-row;width:100%">
                                                <div class="p5" style="display:table-cell;width:8%;text-align:right">
                                                    <b>{jrCore_lang module="jrPoll" id="50" default="Select"}</b>
                                                </div>
                                                <div class="p5" style="display:table-cell;width:82%;text-align:center">
                                                    <b>{jrCore_lang module="jrPoll" id="52" default="Option"}</b>
                                                </div>
                                                <div class="p5" style="display:table-cell;width:10%;text-align:right">
                                                    <b>{jrCore_lang module="jrPoll" id="53" default="Votes"}</b>
                                                </div>
                                            </div>
                                        </div>
                                    {/if}

                                    {foreach $item.poll_options as $k => $_v}
                                        <div style="display:table;width:95%" class="item poll_choice {if $k == $winner}poll_winner{/if}">

                                            <div style="display:table-row;width:100%" class="poll_entry">

                                                {if $smarty.now >= $item.poll_start_date && $smarty.now < $item.poll_end_date}
                                                    <div style="display:table-cell;width:5%" class="p5">
                                                        {if jrUser_is_logged_in() || $_conf.jrPoll_require_login == 'off'}
                                                            <input type="radio" class="form_radio" name="jrPoll_option" value="{$k}">
                                                        {/if}
                                                    </div>
                                                    <div style="display:table-cell;width:85%;padding-left:12px" class="p5">
                                                        {$_v.text|jrCore_format_string:$item.profile_quota_id}
                                                    </div>
                                                    <div style="display:table-cell;width:10%;text-align:center" class="p5">
                                                        {if jrProfile_is_profile_owner($item._profile_id) || jrPoll_has_voted($item._item_id) || $_conf.jrPoll_results_before_voting == 'on'}
                                                            <span class="poll_vote_count">{$_v.votes|default:0|jrCore_number_format}</span>
                                                        {else}
                                                            <span class="poll_vote_count">?</span>
                                                        {/if}
                                                    </div>
                                                {elseif $smarty.now >= $item.poll_end_date}
                                                    <div style="display:table-cell;width:90%;padding-left:12px" class="p5">
                                                        {$_v.text|jrCore_format_string:$item.profile_quota_id}
                                                    </div>
                                                    <div style="display:table-cell;width:10%;text-align:center" class="p5">
                                                        <span class="poll_vote_count">{$_v.votes|default:0|jrCore_number_format}</span>
                                                    </div>
                                                {else}
                                                    <div style="display:table-cell;width:100%" class="p5">
                                                        {$_v.text|jrCore_format_string:$item.profile_quota_id}
                                                    </div>
                                                {/if}
                                            </div>
                                        </div>
                                    {/foreach}

                                    <div style="display:table;width:95%">
                                        <div style="display:table-row">
                                            <div style="display:table-cell;width:100%;padding-left:12px">
                                                {if jrPoll_is_allowed_to_vote()}
                                                    {if !jrPoll_has_voted($item._item_id)}
                                                        {if $smarty.now >= $item.poll_start_date && $smarty.now < $item.poll_end_date}
                                                            {jrCore_lang module="jrCore" id="73" default="working..." assign="working"}
                                                            {jrCore_image image="form_spinner.gif" id="poll_submit_fsi" width="24" height="24" alt=$working style="margin:8px 8px 0px 8px;display:none"}<input type="submit" id="poll_submit_button" class="form_button" style="margin-top:8px" value="{jrCore_lang module="jrPoll" id="62" default="Vote"}" onclick="jrPollVote('{$item._item_id}','{$murl}');">
                                                            <div id="poll_error"></div>
                                                        {/if}
                                                    {elseif $smarty.now < $item.poll_end_date}
                                                        <span class="poll_voted">{jrCore_lang module="jrPoll" id="35" default="You have already voted on this poll"}</span>
                                                    {/if}
                                                {/if}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {* bring in module features *}
                <div class="action_feedback">
                    {jrISkin_feedback_buttons module="jrPoll" item=$item}
                    {if jrCore_module_is_active('jrRating')}
                        <div class="rating" id="jrAudio_{$item._item_id}_rating">{jrCore_module_function
                            function="jrRating_form"
                            type="star"
                            module="jrAudio"
                            index="1"
                            item_id=$item._item_id
                            current=$item.audio_rating_1_average_count|default:0
                            votes=$item.audio_rating_1_number|default:0}</div>
                    {/if}
                    {jrCore_item_detail_features module="jrPoll" item=$item}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        {jrISkin_sort template="icons.tpl" nav_mode="jrPoll" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrISkin" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrPoll"
                    profile_id=$_profile_id
                    order_by='_created RANDOM'
                    pagebreak=8
                    template="chart_poll.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>

