<!doctype html>
<html>
<head>
	<title>{jrCore_lang module="jrPlaylist" id="37" default="Standalone Playlist"}</title>
    <script type="text/javascript" src="{jrCore_javascript_src}"></script>
    <link rel="stylesheet" href="{jrCore_css_src}" media="screen" type="text/css">
    <style type="text/css">
        #payment-view-cart-button,
        #jrchat-tabs{
            display: none;
        }

    </style>
</head>
<body style="border:0;margin:0;padding:0;overflow:hidden">

{jrCore_media_player module="jrPlaylist" items=$item.playlist_items}

</body>
</html>