<div id="gallery-save-image" class="success">
    {jrCore_lang module="jrGallery" id=50 default="Press &quot;Save Changes&quot; to save your image modifications"}
</div>

<table width="100%">
    <tr>

        <td style="width:10%">
            <a href="{$jamroom_url}/{$_post.module_url}/image/gallery_image/{$_item_id}/xxxlarge/v={$_updated}" data-lightbox="images">
                {jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$_item_id size="xlarge" alt=$gallery_image_name id="gallery-edit-image" _v=$_updated}
            </a>
        </td>

        <td class="p20" style="width:90%;vertical-align:middle">

            {* only show this section if an aviary.com key has been entered for the site.*}
            {if isset($_conf['jrGallery_aviary_key'])  && strlen($_conf['jrGallery_aviary_key']) > 2}

                <input id="new_image" name="gallery_alt_img" type="hidden" value="">

                {if isset($_conf.jrGallery_aviary_key) && strlen($_conf.jrGallery_aviary_key) < 20}

                    {if jrCore_get_server_protocol() == 'https'}
                        <script type="text/javascript" src="https://dme0ih8comzn4.cloudfront.net/js/feather.js"></script>
                    {else}
                        <script type="text/javascript" src="http://feather.aviary.com/js/feather.js"></script>
                    {/if}

                    <script type="text/javascript">
                        var featherEditor = new Aviary.Feather({ apiKey: '{$_conf['jrGallery_aviary_key']}', apiVersion: 3, theme: '{$_conf.jrGallery_theme}', tools: 'all', appendTo: '',
                            onSave: function(imageID, newURL) {
                                $('#gallery-save-image').show();
                                var img = document.getElementById(imageID);
                                img.src = newURL;
                                $('#new_image').val(newURL);
                                featherEditor.close();
                            },
                            onError: function(code, msg) {
                                if (typeof console === "object") {
                                    console.log('error: [' + code +']: ' + msg);
                                }
                            }
                        });
                        function launchEditor(id, src) {
                            featherEditor.launch({ image: id, url: src });
                            return false;
                        }
                    </script>

                {else}

                    {if jrCore_get_server_protocol() == 'https'}
                        <script type="text/javascript" src="https://dme0ih8comzn4.cloudfront.net/imaging/v1/editor.js"></script>
                    {else}
                        <script type="text/javascript" src="http://feather.aviary.com/imaging/v1/editor.js"></script>
                    {/if}

                    <script type="text/javascript">

                        var featherEditor;
                        {if $_conf.jrGallery_original == 'on'}

                            {jrCore_module_url module="jrGallery" assign="murl"}
                            {jrGallery_get_image_edit_key assign="edit_key" item_id=$_item_id}

                            featherEditor = new Aviary.Feather({
                                apiKey: '{$_conf.jrGallery_api_key}',
                                timestamp: '{$timestamp}',
                                salt: '{$salt}',
                                encryptionMethod: 'sha1',
                                signature: '{$signature}',
                                hiresUrl: '{$jamroom_url}/{$_post.module_url}/original_image/{$_item_id}/edit_key={$edit_key}',
                                theme: '{$_conf.jrGallery_theme}',
                                tools: 'all',
                                appendTo: '',
                                onSaveButtonClicked: function() {
                                    $.ajax("{$jamroom_url}/{$murl}/get_signature", function(r)
                                    {
                                        featherEditor.updateConfig({
                                            apiKey: '{$_conf.jrGallery_api_key}',
                                            salt: r.salt,
                                            timestamp: r.timestamp,
                                            signature: r.signature
                                        });
                                        featherEditor.saveHiRes();
                                        featherEditor.close();
                                    });
                                    return false;
                                },
                                onSaveHiRes: function(imageID, newURL) {
                                    $('#gallery-save-image').show();
                                    var img = document.getElementById(imageID);
                                    img.src = newURL;
                                    $('#new_image').val(newURL);
                                    featherEditor.close();
                                },
                                onError: function(code, msg) {
                                    if (typeof console === "object") {
                                        console.log('HiRes error: [' + code +']: ' + msg);
                                    }
                                }
                            });

                        {else}

                            featherEditor = new Aviary.Feather({ apiKey: '{$_conf['jrGallery_aviary_key']}', theme: '{$_conf.jrGallery_theme}', tools: 'all', appendTo: '',
                                onSave: function(imageID, newURL) {
                                    $('#gallery-save-image').show();
                                    var img = document.getElementById(imageID);
                                    img.src = newURL;
                                    $('#new_image').val(newURL);
                                    featherEditor.close();
                                }
                            });

                        {/if}
                        function launchEditor(id, src) {
                            featherEditor.launch({ image: id, url: src });
                            return false;
                        }
                    </script>

                {/if}

                {if $_conf.jrGallery_original == 'on'}

                    {jrCore_lang module="jrGallery" id=49 default="Edit Image" assign="edit_tag"}
                    <span class="sprite_icon p5" style="cursor:pointer" onclick="launchEditor('gallery-edit-image', '{$jamroom_url}/{$_post.module_url}/original_image/{$_item_id}/edit_key={$edit_key}');">
                        {jrCore_image module="jrGallery" image="editor.png" width=32 alt="{$edit_tag|jrCore_entity_string}"}&nbsp;{$edit_tag}&nbsp;
                    </span>

                {else}

                    {jrCore_lang module="jrGallery" id=49 default="Edit Image" assign="edit_tag"}
                    <span class="sprite_icon p5" style="cursor:pointer" onclick="launchEditor('gallery-edit-image', '{$jamroom_url}/{$_post.module_url}/image/gallery_image/{$_item_id}/1280');">
                        {jrCore_image module="jrGallery" image="editor.png" width=32 alt="{$edit_tag|jrCore_entity_string}"}&nbsp;{$edit_tag}&nbsp;
                    </span>

                {/if}

            {/if}
        </td>
    </tr>
</table>