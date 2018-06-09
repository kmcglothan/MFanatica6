<a id="tag_section" name="tag_section"></a>
<div class="item_tags" id="{$jrTags.module}_{$jrTags.item_id}_tag">

<div id="existing_tags"><!-- existing tags for this item load here --></div>
{if jrUser_is_logged_in() && (jrUser_is_admin() || $_user.quota_jrTags_allowed == 'on')}
    <div id="tag_message" class="tags" style="display:none;"><!-- success message load here --></div>

        <form action="" id="tag_form">
            <input type="hidden" id="tag_module" name="tag_module" value="{$jrTags.module}">
            <input type="hidden" id="tag_item_id" name="tag_item_id" value="{$jrTags.item_id}">
            <input type="hidden" name="tag_profile_id" value="{$jrTags.profile_id}">

            <div style="display: table; width: 100%">
                <div style="display: table-row">
                    <div style="display: table-cell; width: 99%"><input type="text" id="tag_text" name="tag_text" class="tag_text" style="width:100%; margin: 0;" placeholder="Enter a new tag"></div>
                    <div style="display: table-cell; width: 5%" class="spinner">{jrCore_lang module="jrCore" id="73" default="working..." assign="working"}
                       <div class="loader"> {jrCore_image image="form_spinner.gif" id="tag_submit_indicator" width="24" height="24" style="display:none;" alt=$working}</div> </div>
                </div>
            </div>
        </form>

    <script type="text/javascript">
        //submit
        $("#tag_form").submit(function (e) {
            e.preventDefault();
            jrTagsAdd('#tag_form');
        });
    </script>
{/if}
</div>

<script type="text/javascript">
    //start initially
    $(document).ready(function () {
        jrLoadTags('{$jrTags.module}', '{$jrTags.item_id}', '{$jrTags.profile_id}');
    });
</script>

