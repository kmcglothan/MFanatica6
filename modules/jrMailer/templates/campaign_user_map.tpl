<script type="text/javascript">
    google.charts.load('current', {ldelim} 'packages': ['geochart'] {rdelim});
    google.charts.setOnLoadCallback(drawRegionsMap);
    function drawRegionsMap()
    {
        var data = google.visualization.arrayToDataTable([
            ['Country', 'Users'],
            {foreach $countries as $cny => $cnt}
            ['{$cny}', {$cnt}],
            {/foreach}
        ]);

        var options = {ldelim}{rdelim};

        var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

        chart.draw(data, options);
    }
</script>

<div class="container">

    <div class="row">
        <div class="col9">
            <div class="p20">
                <div id="regions_div" style="width:100%;height:400px;"></div>
            </div>
        </div>
        <div class="col3 last">
            <div style="max-height:400px;overflow:auto">
                {if isset($countries)}
                    <table class="page_table">
                        <tr>
                            <th class="page_table_header" style="width:75%">Country</th>
                            <th class="page_table_header" style="width:25%">User Count</th>
                        </tr>
                        {foreach $countries as $cny => $cnt}
                            {if ($cnt@index % 2) === 0}
                                <tr class="page_table_row_alt">
                                    {else}
                                <tr class="page_table_row">
                            {/if}
                            <td class="page_table_cell center">{$cny}</td>
                            <td class="page_table_cell center">{$cnt|jrCore_format_number}</td>
                            </tr>
                        {/foreach}
                    </table>
                {else}
                    <div class="p10 center">No Countries</div>
                {/if}
            </div>
        </div>
    </div>

</div>
