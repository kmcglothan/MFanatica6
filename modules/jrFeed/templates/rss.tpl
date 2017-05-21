<?xml version="1.0"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><![CDATA[{$rss_title}]]></title>
        <description><![CDATA[{$rss_desc}]]></description>
        <link>{$rss_url}</link>
        <lastBuildDate>{$rss_builddate}</lastBuildDate>
        <atom:link href="{$rss_feed_url}" rel="self" type="application/rss+xml" />
        {foreach $_items as $item}
            <item>
                <title><![CDATA[{$item.title}]]></title>
                <link>{$item.link}</link>
                <guid>{$item.guid}</guid>
                <description><![CDATA[{$item.description}]]></description>
                <pubDate>{$item.pubdate}</pubDate>
            </item>
        {/foreach}
    </channel>
</rss>