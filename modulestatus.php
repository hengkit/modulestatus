<?php
//input parameter: site name
$sitename = $argv[1];
$siteenv = $argv[2];
$incore = array('admin_language' => 'admin_language',
'admin_views' => 'admin_views',
'admin' => 'admin',
'autoload' => 'autoload',
'bean' => 'bean',
'breakpoints' => 'breakpoints',
'cachetags' => 'cachetags',
'ckeditor' => 'ckeditor',
'commerce_cardonfile' => 'commerce_cardonfile',
'commerce_checkout_login' => 'commerce_checkout_login',
'commerce_checkout_progress' => 'commerce_checkout_progress',
'commerce_checkout_redirect' => 'commerce_checkout_redirect',
'commerce_checkout_redirect' => 'commerce_checkout_redirect',
'commerce_coupon' => 'commerce_coupon',
'commerce_custom_product' => 'commerce_custom_product',
'commerce_discount' => 'commerce_discount',
'commerce_entitycache' => 'commerce_entitycache',
'commerce_fancy_attributes' => 'commerce_fancy_attributes',
'commerce_features' => 'commerce_features',
'commerce_login_step' => 'commerce_login_step',
'commerce_order_reference' => 'commerce_order_reference',
'commerce_order_types' => 'commerce_order_types',
'commerce_payment_fields' => 'commerce_payment_fields',
'commerce_payment_icons' => 'commerce_payment_icons',
'commerce_product_display_manager' => 'commerce_product_display_manager',
'commerce_product_urls' => 'commerce_product_urls',
'commerce_simple' => 'commerce_simple',
'commerce_tax_reference' => 'commerce_tax_reference',
'commerce_uuid' => 'commerce_uuid',
'commerce_vat' => 'commerce_vat',
'composer_autoloader' => 'composer_autoloader',
'date_popup_authored' => 'date_popup_authored',
'date' => 'date',
'defaultconfig' => 'defaultconfig',
'dialog' => 'dialog',
'ds_ytchannel' => 'ds_ytchannel',
'editor' => 'editor',
'elements' => 'elements',
'email' => 'email',
'entity_translation' => 'entity_translation',
'entity_view_mode' => 'entity_view_mode',
'entity' => 'entity',
'entitycache' => 'entitycache',
'entityreference' => 'entityreference',
'facetapi' => 'facetapi',
'features' => 'features',
'field_formatter_settings' => 'field_formatter_settings',
'fieldable_panels_panes' => 'fieldable_panels_panes',
'filefield' => 'filefield',
'filter_html_image_secure' => 'filter_html_image_secure',
'i18n' => 'i18n',
'i18nviews' => 'i18nviews',
'image' => 'image',
'imagecache' => 'imagecache',
'imagefield' => 'imagefield',
'jquery_ui' => 'jquery_ui',
'jquery_update' => 'jquery_update',
'l10n_update' => 'l10n_update',
'link' => 'link',
'migrate' => 'migrate',
'modal_forms' => 'modal_forms',
'modernizr' => 'modernizr',
'multiupload_filefield_widget' => 'multiupload_filefield_widget',
'multiupload_imagefield_widget' => 'multiupload_imagefield_widget',
'navbar' => 'navbar',
'phone' => 'phone',
'picture' => 'picture',
'poormanscron' => 'poormanscron',
'quickedit' => 'quickedit',
'references' => 'references',
'rss_permissions' => 'rss_permissions',
'save_draft' => 'save_draft',
'secure_permissions' => 'secure_permissions',
'sendgrid_integration' => 'sendgrid_integration',
'strongarm' => 'strongarm',
'telephone' => 'telephone',
'transliteration' => 'transliteration',
'typekit' => 'typekit',
'user_picture_field' => 'user_picture_field',
'uuid' => 'uuid',
'vertical_tabs' => 'vertical_tabs',
'views_cache_bully' => 'views_cache_bully',
'views_content_cache' => 'views_content_cache',
'views_responsive_grid' => 'views_responsive_grid',
'views' => 'views',
'wysiwyg_filter' => 'wysiwyg_filter',
'wysiwyg' => 'wysiwyg'
);
$excluded = array('pantheon_api' => 'pantheon_api');
$unsupported = array();
$available = array();
shell_exec('terminus auth login --format=silent 2>&1 1> /dev/null');
$login = json_decode(shell_exec('terminus auth whoami --format=json'),true);
echo "Logged in as: " . $login['email']. "\n";
$framework=json_decode(shell_exec('terminus site info --site=' . $sitename . ' --field=framework --format=json'),true);
if ($framework == 'drupal' && isset($login['email'])){
  //terminus drush "pm-list --type=module --no-core --status=enabled --format=json" --env=dev --site=panther
  $modules = json_decode(shell_exec('terminus drush "pm-list --type=module --no-core --status=enabled --format=json" --format=silent --env=dev --site=' .$sitename),true);
//maybe use array_diff_ukey to push values into another array for incore?
  $alreadyin = array_intersect_key($modules,$incore);
  $diff = array_diff_key($modules,$incore);
  $diff = array_diff_key($diff,$excluded);
  foreach($diff as $module => $value){

    $releasexml = file_get_contents('https://updates.drupal.org/release-history/' . $module . '/8.x');
    if(strstr($releasexml, 'No release history available for')){
      array_push($unsupported,$module);
    } else {
      array_push($available,$module);
    }
  }
  var_dump($available);
  var_dump($unsupported);
  var_dump($alreadyin);
} else {
  exit('This is not a Drupal 7 site.');
}
