<?php
/* Smarty version 3.1.31, created on 2018-05-22 04:54:08
  from "/webserver/mf6/data/cache/jrCore/f32f8ea9c96e57b46bd13af1d29e047a^jrCore^form_editor.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b039460bd1050_66995857',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd30ccac254e4cb788ccdfbf8b48503b8670e9974' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/f32f8ea9c96e57b46bd13af1d29e047a^jrCore^form_editor.tpl',
      1 => 1526961248,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b039460bd1050_66995857 (Smarty_Internal_Template $_smarty_tpl) {
echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrCore",'assign'=>"murl"),$_smarty_tpl) : '';?>

<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrImage",'assign'=>"imurl"),$_smarty_tpl) : '';?>

tinymce.init({
    setup: function(ed) {
        var mce_body_fs = $('body').width();
        ed.on('FullscreenStateChanged', function(e) {
            if (e.state === true) { $('.form_editor_holder .mce-fullscreen').width(mce_body_fs); }
            else {
                $('.form_editor_holder .mce-panel').css('width','');
            }
        });
    },
    <?php if (jrCore_is_mobile_device() && !jrCore_is_tablet_device()) {?>
    mobile: { theme: 'mobile' },
    <?php }?>
    body_id: "<?php echo $_smarty_tpl->tpl_vars['form_editor_id']->value;?>
",
    branding: false,
    content_css: "<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/css/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/jrCore_tinymce.css?v=<?php echo $_smarty_tpl->tpl_vars['_mods']->value['jrCore']['module_updated'];?>
",
    images_upload_url: "<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['imurl']->value;?>
/tinymce_imagetools",
    valid_elements : '*[*]',
    toolbar_items_size : "small",
    element_format: "html",
    autoresize_bottom_margin: "3",
    keep_styles: false,
    theme: "<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
",
    selector: "textarea#<?php echo $_smarty_tpl->tpl_vars['form_editor_id']->value;?>
",
    relative_urls: false,
    remove_script_host: false,
    convert_fonts_to_spans: true,
    menubar: false,
    statusbar: false,
    paste_auto_cleanup_on_paste : true,
    paste_remove_styles: true,
    paste_remove_styles_if_webkit: true,
    paste_strip_class_attributes: true,
    entity_encoding: "raw",
    height: "100%",
    image_advtab: true,
    browser_spellcheck: true,
    <?php if ($_smarty_tpl->tpl_vars['table']->value) {?> table_toolbar: "tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | tablecellprops tablesplitcells tablemergecells",<?php }?>
    <?php if ($_smarty_tpl->tpl_vars['script']->value) {?>
    extended_valid_elements: "script[type|defer|src|language]",
    <?php }?>
    plugins: "lists,imagetools,pagebreak,<?php if ($_smarty_tpl->tpl_vars['jrsmiley']->value) {?>jrsmiley,<?php }
if ($_smarty_tpl->tpl_vars['jrembed']->value) {?>jrembed,media<?php }?>,image,autoresize,<?php if ($_smarty_tpl->tpl_vars['table']->value) {?>table,<?php }?>link,code,fullscreen,textcolor,colorpicker,preview<?php if ($_smarty_tpl->tpl_vars['hr']->value) {?>,hr<?php }?>,tabindex,paste,anchor",
    toolbar1: "formatselect | fontselect fontsizeselect forecolor <?php if ($_smarty_tpl->tpl_vars['strong']->value) {?> bold<?php }
if ($_smarty_tpl->tpl_vars['em']->value) {?> italic<?php }
if ($_smarty_tpl->tpl_vars['span']->value) {?> underline<?php }?> removeformat | <?php if ($_smarty_tpl->tpl_vars['span']->value || $_smarty_tpl->tpl_vars['div']->value) {?> alignleft<?php }
if ($_smarty_tpl->tpl_vars['span']->value || $_smarty_tpl->tpl_vars['div']->value) {?> aligncenter<?php }
if ($_smarty_tpl->tpl_vars['span']->value || $_smarty_tpl->tpl_vars['div']->value) {?> alignright<?php }
if ($_smarty_tpl->tpl_vars['span']->value || $_smarty_tpl->tpl_vars['div']->value) {?> alignjustify |<?php }
if ($_smarty_tpl->tpl_vars['ul']->value || $_smarty_tpl->tpl_vars['ol']->value || $_smarty_tpl->tpl_vars['li']->value) {
if ($_smarty_tpl->tpl_vars['ul']->value || $_smarty_tpl->tpl_vars['li']->value) {?> bullist<?php }
if ($_smarty_tpl->tpl_vars['ol']->value) {?> numlist<?php }?> |<?php }
if ($_smarty_tpl->tpl_vars['div']->value) {?> outdent indent |<?php }?> undo redo | link unlink anchor pagebreak<?php if ($_smarty_tpl->tpl_vars['table']->value) {?> table<?php }
if ($_smarty_tpl->tpl_vars['hr']->value) {?> hr<?php }?> | code preview fullscreen<?php if ($_smarty_tpl->tpl_vars['jrembed']->value || $_smarty_tpl->tpl_vars['jrsmiley']->value) {?> |<?php }
if ($_smarty_tpl->tpl_vars['jrembed']->value) {?> jrembed<?php }
if ($_smarty_tpl->tpl_vars['jrsmiley']->value) {?> jrsmiley<?php }?>"
});
<?php }
}
