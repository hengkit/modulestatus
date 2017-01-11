<?php

use Pantheon\Terminus\Commands\Remote\DrushCommand;
use Pantheon\Terminus\Commands\Remote\SSHBaseCommand as SSH;
use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Pantheon\Terminus\Exceptions\TerminusException;

class ModuleStatusCommand extends DrushCommand implements SiteAwareInterface {
  use SiteAwareTrait;
  //protected $command = 'site:modulestatus';
  protected $valid_frameworks = ['drupal'];
  /**
  * Get the project status for modules installed on a D7 site
  * @command site:modulestatus
  * @param string $site_env Site & environment in the format `site-name.env`
  * @usage terminus site:modulestatus <site>.<env>
  */
  public function checkStatus($site_env) {
    list($site, $env) = $this->getSiteEnv($site_env);

    if ( !in_array(($site->serialize()['framework']),$this->valid_frameworks)){
      throw new TerminusException('This command can only be run on Drupal 7 sites');
    }
    $env->wake();
    $drush_version_command = array('status', '--format=var_export');
    $drush_status = $this->drushCommand($site_env,$drush_version_command);
    //print_r($drush_status);

    // execute drush pmpi
    //drush pm-projectinfo --status=enabled
    // check drupal.org

  }
}
