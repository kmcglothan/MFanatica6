{jrCore_module_url module="jrEvent" assign="murl"}

{jrProfile_disable_header}
{jrProfile_disable_sidebar}

{if isset($_profile._profile_id)}
    <div class="page_nav clearfix">
        <div class="breadcrumbs">
            {jrCore_include template="profile_header_minimal.tpl"}
            {jrBeatSlinger_breadcrumbs module="jrEvent" profile_url=$_profile.profile_url profile_name=$_profile.profile_name page="index"}

        </div>
        <div class="action_buttons">

            {if isset($_years) && is_array($_years)}
                {* on the profile, show the header. *}
                <select class="form_select" style="width: auto" name="calendar_month" id="calendar_month"
                        onchange="var m=this.options[this.selectedIndex].value; jrCore_window_location('{$browse_base_url}/month='+ m +'/year={$year}')">

                    <option value="1"
                            {if $month == 1}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="41" default="January"}</option>
                    <option value="2"
                            {if $month == 2}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="42" default="February"}</option>
                    <option value="3"
                            {if $month == 3}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="43" default="March"}</option>
                    <option value="4"
                            {if $month == 4}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="44" default="April"}</option>
                    <option value="5"
                            {if $month == 5}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="45" default="May"}</option>
                    <option value="6"
                            {if $month == 6}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="46" default="June"}</option>
                    <option value="7"
                            {if $month == 7}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="47" default="July"}</option>
                    <option value="8"
                            {if $month == 8}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="48" default="August"}</option>
                    <option value="9"
                            {if $month == 9}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="49" default="September"}</option>
                    <option value="10"
                            {if $month == 10}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="50" default="October"}</option>
                    <option value="11"
                            {if $month == 11}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="51" default="November"}</option>
                    <option value="12"
                            {if $month == 12}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="52" default="December"}</option>
                </select>
                <select class="form_select" style="width: auto" name="calendar_year" id="calendar_year"
                        onchange="var y=this.options[this.selectedIndex].value; jrCore_window_location('{$browse_base_url}/month={$month}/year='+ y)">>
                    {foreach $_years as $v}
                        {if $v == $year}
                            <option value="{$v}" selected="selected">{$v}</option>
                        {else}
                            <option value="{$v}">{$v}</option>
                        {/if}
                    {/foreach}
                </select>
            {elseif $_post.option != 'calendar'}
                {* the header on the widget, but dont show for the main /event/calendar page *}
                {if $month == 1}{jrCore_lang module="jrEvent" id="41" default="January" assign="month_lang"}{/if}
                {if $month == 2}{jrCore_lang module="jrEvent" id="42" default="February" assign="month_lang"}{/if}
                {if $month == 3}{jrCore_lang module="jrEvent" id="43" default="March" assign="month_lang"}{/if}
                {if $month == 4}{jrCore_lang module="jrEvent" id="44" default="April" assign="month_lang"}{/if}
                {if $month == 5}{jrCore_lang module="jrEvent" id="45" default="May" assign="month_lang"}{/if}
                {if $month == 6}{jrCore_lang module="jrEvent" id="46" default="June" assign="month_lang"}{/if}
                {if $month == 7}{jrCore_lang module="jrEvent" id="47" default="July" assign="month_lang"}{/if}
                {if $month == 8}{jrCore_lang module="jrEvent" id="48" default="August" assign="month_lang"}{/if}
                {if $month == 9}{jrCore_lang module="jrEvent" id="49" default="September" assign="month_lang"}{/if}
                {if $month == 10}{jrCore_lang module="jrEvent" id="50" default="October" assign="month_lang"}{/if}
                {if $month == 11}{jrCore_lang module="jrEvent" id="51" default="November" assign="month_lang"}{/if}
                {if $month == 12}{jrCore_lang module="jrEvent" id="52" default="December" assign="month_lang"}{/if}
            {/if}

            {jrCore_item_index_buttons module="jrEvent" profile_id=$_profile._profile_id}
        </div>
    </div>
{/if}

<div id="jrEvent_calendar">
    <div class="wrap">
        <table class="ecal-main ecal-calendar">
            <colgroup>
                {*<col class="ecal-week"/>*}
                <col class="ecal-day"/>
                <col class="ecal-day"/>
                <col class="ecal-day"/>
                <col class="ecal-day"/>
                <col class="ecal-day"/>
                <col class="ecal-day"/>
                <col class="ecal-day"/>
            </colgroup>
            <thead>
            <tr>
                {*<th>{jrCore_lang module="jrEvent" id="54" default="W"}</th>*}
                <th>{jrCore_lang module="jrEvent" id="55" default="Sunday"}</th>
                <th>{jrCore_lang module="jrEvent" id="56" default="Monday"}</th>
                <th>{jrCore_lang module="jrEvent" id="57" default="Tuesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="58" default="Wednesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="59" default="Thursday"}</th>
                <th>{jrCore_lang module="jrEvent" id="60" default="Friday"}</th>
                <th>{jrCore_lang module="jrEvent" id="61" default="Saturday"}</th>
            </tr>
            </thead>
            <tbody>
            {if isset($_calendar) && is_array($_calendar)}
                {foreach $_calendar as $_weeks}
                    {foreach $_weeks as $week => $_days}
                        <tr>
                            {*<th>{$week}</th>*}
                            {foreach $_days as $_d}
                                <td>
                                    <div class="{$_d.class}">{$_d.day}</div>
                                    {if isset($_events) && is_array($_events[$_d.day]) && $_d.rel == 'this_month'}
                                        <ul>
                                            {foreach $_events[$_d.day] as $_e}
                                                <li>
                                                    <a href="{$jamroom_url}/{$_e.profile_url}/{$murl}/{$_e._item_id}/{$_e.event_title_url}">
                                                        {jrCore_module_function
                                                        function="jrImage_display"
                                                        module="jrEvent"
                                                        type="event_image"
                                                        item_id=$_e._item_id
                                                        size="icon96"
                                                        crop="auto"
                                                        alt=$_e.event_title
                                                        title=$_e.event_title
                                                        width=false
                                                        height=false}</a>
                                                </li>
                                            {/foreach}
                                        </ul>
                                    {/if}
                                </td>
                            {/foreach}
                        </tr>
                    {/foreach}
                {/foreach}
            {/if}
            </tbody>
        </table>
    </div>
</div>