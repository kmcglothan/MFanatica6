{jrCore_module_url module="jrEvent" assign="murl"}
{if isset($_profile) && is_array($_profile)}
    {* on the profile, show the header. *}
    <div class="block">
        <div class="title">
            <div class="block_config">
                <select class="form_select" style="width: auto" name="calendar_month" id="calendar_month" onchange="var m=this.options[this.selectedIndex].value; jrCore_window_location('{$browse_base_url}/month='+ m +'/year={$year}')">

                    <option value="1" {if $month == 1}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="41" default="January"}</option>
                    <option value="2" {if $month == 2}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="42" default="February"}</option>
                    <option value="3" {if $month == 3}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="43" default="March"}</option>
                    <option value="4" {if $month == 4}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="44" default="April"}</option>
                    <option value="5" {if $month == 5}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="45" default="May"}</option>
                    <option value="6" {if $month == 6}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="46" default="June"}</option>
                    <option value="7" {if $month == 7}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="47" default="July"}</option>
                    <option value="8" {if $month == 8}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="48" default="August"}</option>
                    <option value="9" {if $month == 9}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="49" default="September"}</option>
                    <option value="10" {if $month == 10}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="50" default="October"}</option>
                    <option value="11" {if $month == 11}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="51" default="November"}</option>
                    <option value="12" {if $month == 12}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="52" default="December"}</option>
                </select>

                <select class="form_select" style="width: auto" name="calendar_year" id="calendar_year" onchange="var y=this.options[this.selectedIndex].value; jrCore_window_location('{$browse_base_url}/month={$month}/year='+ y)">>
                    {foreach $_years as $v}
                        {if $v == $year}
                            <option value="{$v}" selected="selected">{$v}</option>
                        {else}
                            <option value="{$v}">{$v}</option>
                        {/if}
                    {/foreach}
                </select>
                {jrCore_item_create_button module="jrEvent" profile_id=$_profile._profile_id action="`$murl`/create"}
            </div>
            <h1>{jrCore_lang module="jrEvent" id="53" default="Event Calendar"}</h1>

            <div class="breadcrumbs">
                <a href="{$jamroom_url}/{$_profile.profile_url}/">{$_profile.profile_name}</a> &raquo;
                <a href="{$jamroom_url}/{$_profile.profile_url}/{$murl}/calendar">{jrCore_lang module="jrEvent" id="53" default="Event Calendar"}</a>
            </div>
        </div>
    </div>
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

    <div class="center">
        <h4><a href="{$jamroom_url}/{$murl}/calendar">{$month_lang} - {$year}</a></h4>
    </div>
{/if}

<div class="block" id="jrEvent_calendar">
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
            {if !isset($_conf.jrEvent_calendar_start_day) || $_conf.jrEvent_calendar_start_day == 0}
                <th>{jrCore_lang module="jrEvent" id="55" default="Sunday"}</th>
                <th>{jrCore_lang module="jrEvent" id="56" default="Monday"}</th>
                <th>{jrCore_lang module="jrEvent" id="57" default="Tuesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="58" default="Wednesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="59" default="Thursday"}</th>
                <th>{jrCore_lang module="jrEvent" id="60" default="Friday"}</th>
                <th>{jrCore_lang module="jrEvent" id="61" default="Saturday"}</th>
            {elseif $_conf.jrEvent_calendar_start_day == 1}
                <th>{jrCore_lang module="jrEvent" id="56" default="Monday"}</th>
                <th>{jrCore_lang module="jrEvent" id="57" default="Tuesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="58" default="Wednesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="59" default="Thursday"}</th>
                <th>{jrCore_lang module="jrEvent" id="60" default="Friday"}</th>
                <th>{jrCore_lang module="jrEvent" id="61" default="Saturday"}</th>
                <th>{jrCore_lang module="jrEvent" id="55" default="Sunday"}</th>
            {elseif $_conf.jrEvent_calendar_start_day == 2}
                <th>{jrCore_lang module="jrEvent" id="57" default="Tuesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="58" default="Wednesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="59" default="Thursday"}</th>
                <th>{jrCore_lang module="jrEvent" id="60" default="Friday"}</th>
                <th>{jrCore_lang module="jrEvent" id="61" default="Saturday"}</th>
                <th>{jrCore_lang module="jrEvent" id="55" default="Sunday"}</th>
                <th>{jrCore_lang module="jrEvent" id="56" default="Monday"}</th>
            {elseif $_conf.jrEvent_calendar_start_day == 3}
                <th>{jrCore_lang module="jrEvent" id="58" default="Wednesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="59" default="Thursday"}</th>
                <th>{jrCore_lang module="jrEvent" id="60" default="Friday"}</th>
                <th>{jrCore_lang module="jrEvent" id="61" default="Saturday"}</th>
                <th>{jrCore_lang module="jrEvent" id="55" default="Sunday"}</th>
                <th>{jrCore_lang module="jrEvent" id="56" default="Monday"}</th>
                <th>{jrCore_lang module="jrEvent" id="57" default="Tuesday"}</th>
            {elseif $_conf.jrEvent_calendar_start_day == 4}
                <th>{jrCore_lang module="jrEvent" id="59" default="Thursday"}</th>
                <th>{jrCore_lang module="jrEvent" id="60" default="Friday"}</th>
                <th>{jrCore_lang module="jrEvent" id="61" default="Saturday"}</th>
                <th>{jrCore_lang module="jrEvent" id="55" default="Sunday"}</th>
                <th>{jrCore_lang module="jrEvent" id="56" default="Monday"}</th>
                <th>{jrCore_lang module="jrEvent" id="57" default="Tuesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="58" default="Wednesday"}</th>
            {elseif $_conf.jrEvent_calendar_start_day == 5}
                <th>{jrCore_lang module="jrEvent" id="60" default="Friday"}</th>
                <th>{jrCore_lang module="jrEvent" id="61" default="Saturday"}</th>
                <th>{jrCore_lang module="jrEvent" id="55" default="Sunday"}</th>
                <th>{jrCore_lang module="jrEvent" id="56" default="Monday"}</th>
                <th>{jrCore_lang module="jrEvent" id="57" default="Tuesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="58" default="Wednesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="59" default="Thursday"}</th>
            {else}
                <th>{jrCore_lang module="jrEvent" id="61" default="Saturday"}</th>
                <th>{jrCore_lang module="jrEvent" id="55" default="Sunday"}</th>
                <th>{jrCore_lang module="jrEvent" id="56" default="Monday"}</th>
                <th>{jrCore_lang module="jrEvent" id="57" default="Tuesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="58" default="Wednesday"}</th>
                <th>{jrCore_lang module="jrEvent" id="59" default="Thursday"}</th>
                <th>{jrCore_lang module="jrEvent" id="60" default="Friday"}</th>
            {/if}
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
                                    <ul class="ecal-event-list">
                                        {foreach $_events[$_d.day] as $_e}
                                            <li>
                                                <a href="{$jamroom_url}/{$_e.profile_url}/{$murl}/{$_e._item_id}/{$_e.event_title_url}">{$_e.event_title}</a>
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