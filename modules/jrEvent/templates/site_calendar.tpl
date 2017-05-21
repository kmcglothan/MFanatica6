{jrCore_module_url module="jrEvent" assign="murl"}
{jrCore_include template="header.tpl"}

<div class="block">
    <div class="title">
        <div class="block_config">
            <select class="form_select" style="width: auto" name="calendar_month" id="calendar_month" onchange="var m=this.options[this.selectedIndex].value; jrCore_window_location('{$jamroom_url}/{$murl}/calendar/'+ m +'/{$year}')">

                <option value="1" {if $month == "1"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="41" default="January"}</option>
                <option value="2" {if $month == "2"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="42" default="February"}</option>
                <option value="3" {if $month == "3"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="43" default="March"}</option>
                <option value="4" {if $month == "4"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="44" default="April"}</option>
                <option value="5" {if $month == "5"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="45" default="May"}</option>
                <option value="6" {if $month == "6"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="46" default="June"}</option>
                <option value="7" {if $month == "7"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="47" default="July"}</option>
                <option value="8" {if $month == "8"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="48" default="August"}</option>
                <option value="9" {if $month == "9"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="49" default="September"}</option>
                <option value="10" {if $month == "10"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="50" default="October"}</option>
                <option value="11" {if $month == "11"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="51" default="November"}</option>
                <option value="12" {if $month == "12"}selected="selected"{/if}>{jrCore_lang module="jrEvent" id="52" default="December"}</option>
            </select>

            <select class="form_select" style="width: auto" name="calendar_year" id="calendar_year" onchange="var y=this.options[this.selectedIndex].value; jrCore_window_location('{$jamroom_url}/{$murl}/calendar/{$month}/'+ y)">>
                {foreach $_years as $v}
                    {if $v == $year}
                        <option value="{$v}" selected="selected">{$v}</option>
                    {else}
                        <option value="{$v}">{$v}</option>
                    {/if}
                {/foreach}
            </select>
        </div>
        <h1>{jrCore_lang module="jrEvent" id="53" default="Event Calendar"}</h1>
    </div>
</div>

{jrEvent_calendar month=$month year=$year}

{jrCore_include template="footer.tpl"}