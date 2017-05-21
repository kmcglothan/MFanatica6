<li id="w{$_widget.widget_id}" class="sb-drag-handle" data-id="{$_widget.widget_id}">
    <div id="c{$_widget.widget_id}" class="sb-widget-block sb-widget-block-edit">
            <div class="sb-widget-controls">
                <a title="modify this widget" onclick="jrSiteBuilder_modify_widget('widget_id-{$_widget.widget_id}')">{jrCore_icon icon="gear" size=20}</a>
                <a title="clone this widget" onclick="jrSiteBuilder_clone_widget('{$_widget.widget_id}')">{jrCore_icon icon="sb_clone" size=20}</a>
                <a title="delete this widget" onclick="jrSiteBuilder_delete_widget('widget_id-{$_widget.widget_id}')">{jrCore_icon icon="trash" size=20}</a>
            </div>
            <div class="title sb-widget-title">
                {if $_widget.no_title == 1}
                <h2 class="sb-widget-type-info">{$_widget.widget_title}</h2>
                {else}
                <h2>{$_widget.widget_title}</h2>
                {/if}
            </div>
        <div id="widget_id-{$_widget.widget_id}" data-id="{$_widget.widget_id}" class="sb-widget-content block_content">
        </div>
    </div>
</li>