<?php
//TODO: make this betterer
$sitename = $argv[1];
$siteenv = $argv[2];
# Pulled this list from Drupal 8 Contrib Tracker where Status = Closed (won't fix)
# https://www.drupal.org/project/issues/search/contrib_tracker?text=&assigned=&submitted=&project_issue_followers=&status%5B%5D=5&issue_tags_op=%3D&issue_tags=
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
$result = array();
shell_exec('terminus auth login --format=silent 2>&1 1> /dev/null');
$login = json_decode(shell_exec('terminus auth whoami --format=json'),true);
echo "Logged in as: " . $login['email']. "\n";
$framework=json_decode(shell_exec('terminus site info --site=' . $sitename . ' --field=framework --format=json'),true);
if ($framework == 'drupal' && isset($login['email'])){
  $modules = json_decode(shell_exec('terminus drush "pm-list --type=module --no-core --status=enabled --format=json" --format=silent --env=' . $siteenv . ' --site=' .$sitename),true);
  $placeholder = array('name'=> NULL, 'url'=>null, 'status'=> 'Not Available');
  $result = array_fill_keys(array_keys($modules),$placeholder);
  $alreadyin = array_intersect_key($modules,$incore);
  foreach($alreadyin as $module =>$value){
    $result[$module]['url'] = 'https://www.drupal.org/project/' . $module;
    $result[$module]['status'] = "Moved to Core";
    $result[$module]['name'] = $value['name'];
  }
  $diff = array_diff_key($modules,$incore);
  $diff = array_diff_key($diff,$excluded);
  $result = array_diff_key($result,$excluded);
  foreach($diff as $module => $value){
    $result[$module]['url'] = 'https://www.drupal.org/project/' . $module;
    $result[$module]['name'] = $value['name'];
    $releasexml = file_get_contents('https://updates.drupal.org/release-history/' . $module . '/8.x');
    if(strstr($releasexml, 'No release history')){
      $result[$module]['status'] = 'Not Available';
    } else {
      $result[$module]['status'] = 'Available';
    }
  }
  asort($result);
  var_dump($result);
  echo  "Module,Status\n";
  foreach($result as $module =>$value){
    echo $value['name'] . ", ". $value['status'] .  "\n";
  }
  echo  "\n";
} else {
  exit("Exiting. This is not a Drupal 7 site.\n");
}
