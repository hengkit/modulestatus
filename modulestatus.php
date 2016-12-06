<?php
//Callback function to only add modules out of the project list
function moduleFilter($var){
  if($var['type']=='module'){
    return true;
  } else {
    return false;
  }
}
//TODO: make this betterer
// Eventually re-write this as a terminus plugin.
$sitename = $argv[1];
$siteenv = $argv[2];
# Pulled this list from Drupal 8 Contrib Tracker where Status = Closed (won't fix)
# https://www.drupal.org/project/issues/search/contrib_tracker?text=&assigned=&submitted=&project_issue_followers=&status%5B%5D=5&issue_tags_op=%3D&issue_tags=
# Had to make it an associative array so that array_diff_key would work. I hate it.
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
//Modules that we don't care about explicitly. Not really necessary using pmpi but keeping it around just in case
$excluded = array('pantheon_api' => 'pantheon_api');
$result = array();
//Login with terminus
shell_exec('terminus auth login --format=silent 2>&1 1> /dev/null');
$login = json_decode(shell_exec('terminus auth whoami --format=json'),true);
echo "Logged in as: " . $login['email']. "\n";
//Checking that it is a D7 site.
$framework=json_decode(shell_exec('terminus site info --site=' . $sitename . ' --field=framework --format=json'),true);
$drushversion = shell_exec('terminus drush "status --fields=drush-version" --env=' . $siteenv . ' --site=' .$sitename);
if(!preg_match('/8\.\d.\d/',$drushversion)){
  exit("Exiting. Please update Drush to version 8\n");
}
if ($framework != 'drupal'){
  exit("Exiting. This is not a Drupal 7 site.\n");
}
  echo "Finding all enabled modules\n";
  $projects = json_decode(shell_exec('terminus drush "pm-projectinfo --status=enabled --format=json" --format=silent --env=' . $siteenv . ' --site=' .$sitename),true);
  $modules = array_filter($projects,"moduleFilter");
  //exit();
  $placeholder = array('name'=> NULL, 'url'=>null, 'status'=> 'Not Available');
//create result array
  $result = array_fill_keys(array_keys($modules),$placeholder);
  //compare against modules moved to core
  $alreadyin = array_intersect_key($modules,$incore);
//populate result array with modules moved to core
  foreach($alreadyin as $module =>$value){
    $result[$module]['url'] = 'https://www.drupal.org/project/' . $module;
    $result[$module]['status'] = "Moved to Core";
    $result[$module]['name'] = $value['label'];
  }
  //filter out modules moved to core and excluded modules to minimize update API requests
  //TODO: need to filter out modules that don't exist on d.o. explicitly i.e. xmlsitemap_custom, xmlsitemap_engines
  $diff = array_diff_key($modules,$incore);
  $diff = array_diff_key($diff,$excluded);
//toss excluded modules out of the results
  $result = array_diff_key($result,$excluded);
  // for all remaining modules, check for D8.X releases
  foreach($diff as $module => $value){
    $result[$module]['url'] = 'https://www.drupal.org/project/' . $module;
    $result[$module]['name'] = $value['label'];
    //Check the update api.
    $releasexml = file_get_contents('https://updates.drupal.org/release-history/' . $module . '/8.x');
    if(strstr($releasexml, 'No release history')){
      $result[$module]['status'] = 'Not Available';
    } else {
      //TODO: Expand with notes about implementation?
      $result[$module]['status'] = 'Available';
    }
  }
  //Sort results by key because I'm that guy.
  asort($result);
  //Dump results in CSV format.
  echo  "Module, Status, URL\n";
  foreach($result as $module =>$value){
    echo $value['name'] . ", ". $value['status']. ", ". $value['url'] .  "\n";
  }
  echo  "\n";
