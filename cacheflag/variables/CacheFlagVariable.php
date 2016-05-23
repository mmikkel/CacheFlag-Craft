<?php namespace Craft;

/**
 * Cache Flag by Mats Mikkel Rummelhoff
 *
 * @author      Mats Mikkel Rummelhoff <http://mmikkel.no>
 * @package     Cache Flag
 * @since       Craft 2.4
 * @copyright   Copyright (c) 2015, Mats Mikkel Rummelhoff
 * @license     http://opensource.org/licenses/mit-license.php MIT License
 * @link        https://github.com/mmikkel/CacheFlag-Craft
 */

/**
 * Class CacheFlagVariable
 * @package Craft
 */
class CacheFlagVariable
{

    /**
     * @return mixed
     */
    public function getPlugin()
    {
        return craft()->cacheFlag->getPlugin();
    }

    /**
     * @return mixed
     */
    public function getCpTabs()
    {
        return craft()->cacheFlag->getCpTabs();
    }

    /**
     * @return mixed
     */
    public function getPluginUrl()
    {
        return $this->getPlugin()->getUrl();
    }

    /**
     * @return mixed
     */
    public function getPluginName()
    {
        return $this->getPlugin()->getName();
    }

    /**
     * @return mixed
     */
    public function version()
    {
        return $this->getPlugin()->getVersion();
    }

    /**
     * @return mixed
     */
    public function requiredCraftVersion()
    {
        return $this->getPlugin()->getCraftRequiredVersion();
    }

    /**
     * @return mixed
     */
    public function isCraftRequiredVersion()
    {
        return $this->getPlugin()->isCraftRequiredVersion();
    }

    /**
     * @param string $flags
     * @return mixed
     */
    public function flagsHasCaches($flags = '')
    {
        return craft()->cacheFlag->flagsHasCaches($flags);
    }

}
