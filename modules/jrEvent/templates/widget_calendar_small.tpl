{jrCore_module_url module="jrEvent" assign="murl"}

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
    <h4><a href="{$jamroom_url}/{$murl}/calendar/{$month}/{$year}">{$month_lang} - {$year}</a></h4>
</div>

<div class="block" id="jrEvent_small_calendar">
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
                <th>S</th>
                <th>M</th>
                <th>T</th>
                <th>W</th>
                <th>T</th>
                <th>F</th>
                <th>S</th>
            {elseif $_conf.jrEvent_calendar_start_day == 1}
                <th>M</th>
                <th>T</th>
                <th>W</th>
                <th>T</th>
                <th>F</th>
                <th>S</th>
                <th>S</th>
            {elseif $_conf.jrEvent_calendar_start_day == 2}
                <th>T</th>
                <th>W</th>
                <th>T</th>
                <th>F</th>
                <th>S</th>
                <th>S</th>
                <th>M</th>
            {elseif $_conf.jrEvent_calendar_start_day == 3}
                <th>W</th>
                <th>T</th>
                <th>F</th>
                <th>S</th>
                <th>S</th>
                <th>M</th>
                <th>T</th>
            {elseif $_conf.jrEvent_calendar_start_day == 4}
                <th>T</th>
                <th>F</th>
                <th>S</th>
                <th>S</th>
                <th>M</th>
                <th>T</th>
                <th>W</th>
            {elseif $_conf.jrEvent_calendar_start_day == 5}
                <th>F</th>
                <th>S</th>
                <th>S</th>
                <th>M</th>
                <th>T</th>
                <th>W</th>
                <th>T</th>
            {else}
                <th>S</th>
                <th>S</th>
                <th>M</th>
                <th>T</th>
                <th>W</th>
                <th>T</th>
                <th>F</th>
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
                                {if isset($_events) && is_array($_events[$_d.day]) && $_d.rel == 'this_month'}
                                    <div class="{$_d.class} has_events" title="{count($_events[$_d.day])} events today"><a href="{$jamroom_url}/{$murl}/day={$_d.day}/month={$month}/year={$year}">{$_d.day}</a></div>
                                {else}
                                    <div class="{$_d.class}">{$_d.day}</div>
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