<?php

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Pantheon\Terminus\Collections\Sites;
use Pantheon\Terminus\Exceptions\TerminusException;

class ModuleStatusCommand extends TerminusCommand {
  public function checkStatus($args, $assoc_args) {
    // get site name

    // check dependencies -- drush version, drupal 7 framework
    // execute drush pmpi
    // check drupal.org

  }
}
