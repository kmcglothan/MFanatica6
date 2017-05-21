<?xml version="1.0"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{$_conf.jrCore_system_name} - Video</title>
        <description>A feed of the combined video items</description>
        <link>{$jamroom_url}/{jrCore_module_url module="jrCombinedVideo"}</link>
        <lastBuildDate>{$rss_builddate}</lastBuildDate>
        <atom:link href="{$rss_feed_url}" rel="self" type="application/rss+xml" />
        {capture name="row_template" assign="template"}
        {literal}
        {foreach $_items as $item}
            {$title = "`$item.seamless_module_prefix`_title"}
            {$title_url = "`$item.seamless_module_prefix`_title_url"}
            {$description = "`$item.seamless_module_prefix`_description"}
            <item>
                <title>{$item.$title|jrCore_entity_string}</title>
                <link>{$jamroom_url}/{$item.profile_url}/{$_mods[$item.seamless_module_name]['module_url']}/{$item._item_id}/{$item.$title_url}</link>
                <guid>{$jamroom_url}/{$item.profile_url}/{$_mods[$item.seamless_module_name]['module_url']}/{$item._item_id}</guid>
                <description><![CDATA[{$item.$description}]]></description>
                <pubDate>{$item._created|jrCore_date_format:"%a, %d %b %Y %T %z"}</pubDate>
            </item>
        {/foreach}
        {/literal}
        {/capture}
        {jrCombinedVideo_get_active_modules assign="mods"}
        {if strlen($mods) > 0}
            {jrSeamless_list modules=$mods order_by="_created numerical_desc" limit=20 template=$template}
        {elseif jrUser_is_admin()}
            No active video modules found!
        {/if}
    </channel>
</rss>