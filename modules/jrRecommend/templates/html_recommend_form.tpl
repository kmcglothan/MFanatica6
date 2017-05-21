{* Standard HTML recommend form *}
{assign var="form_name" value="jrRecommend"}
<div style="white-space:nowrap">
    <form name="{$form_name}" action="{$jamroom_url}/recommend/results/{$jrRecommend.page}/{$jrRecommend.pagebreak}" method="get" style="margin-bottom:0">
        <input type="text" id="recommend_string" name="recommend_string" value="" style="{$jrRecommend.style}" class="{$jrRecommend.class}" placeholder="{$jrRecommend.value}">&nbsp;<input type="submit" class="form_button" value="{$jrRecommend.submit_value|default:"find"}">
    </form>
</div>
