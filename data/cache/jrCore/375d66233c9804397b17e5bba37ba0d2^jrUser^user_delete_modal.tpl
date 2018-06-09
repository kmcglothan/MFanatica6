<div id="modal_window" class="user-delete-modal" style="display:none">
    <input type="button" id="d-user" class="form_button" name="d-user" value="delete user account only" onclick="jrUser_delete_user_from_modal()">
    <input type="button" id="d-profile" class="form_button" name="d-profile" value="delete user account and profile" onclick="jrUser_delete_profile_from_modal()">
    <input type="button" id="d-cancel" class="form_button" name="d-cancel" value="cancel" onclick="$.modal.close()">
    <div id="user-delete-active-user-id" style="display:none">0</div>
    <div id="user-delete-active-profile-id" style="display:none">0</div>
</div>
