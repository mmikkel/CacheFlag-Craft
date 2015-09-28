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

class CacheFlagVariable
{

    public function getPlugin()
	{
		return craft()->cacheFlag->getPlugin();
	}

	public function getCpTabs()
	{
		return craft()->cacheFlag->getCpTabs();
	}

	public function getPluginUrl()
	{
		return $this->getPlugin()->getUrl();
	}

	public function getPluginName()
	{
		return $this->getPlugin()->getName();
	}

	public function version()
	{
		return $this->getPlugin()->getVersion();
	}

	public function requiredCraftVersion()
	{
		return $this->getPlugin()->getCraftRequiredVersion();
	}

	public function isCraftRequiredVersion()
	{
		return $this->getPlugin()->isCraftRequiredVersion();
	}

}
