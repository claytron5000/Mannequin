<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Drupal;

use Drupal\Core\Extension\ExtensionDiscovery;

/**
 * Modifies ExtensionDiscovery to avoid DB-backed function calls.
 */
class MannequinExtensionDiscovery extends ExtensionDiscovery
{
    public function setProfileDirectoriesFromSettings()
    {
        return $this;
    }
}