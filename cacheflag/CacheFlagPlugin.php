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

class CacheFlagPlugin extends BasePlugin
{

    protected $_version = '1.1.2',
        $_schemaVersion = '1.0',
        $_name = 'Cache Flag',
        $_url = 'https://github.com/mmikkel/CacheFlag-Craft',
        $_releaseFeedUrl = 'https://raw.githubusercontent.com/mmikkel/CacheFlag-Craft/master/releases.json',
        $_documentationUrl = 'https://github.com/mmikkel/CacheFlag-Craft/blob/master/README.md',
        $_description = 'Flag and clear template caches without element criteria.',
        $_developer = 'Mats Mikkel Rummelhoff',
        $_developerUrl = 'http://mmikkel.no',
        $_minVersion = '2.4';

    public function getName()
    {
        return $this->_name;
    }

    public function getVersion()
    {
        return $this->_version;
    }

    public function getSchemaVersion()
    {
        return $this->_schemaVersion;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function getReleaseFeedUrl()
    {
        return $this->_releaseFeedUrl;
    }

    public function getDocumentationUrl()
    {
        return $this->_documentationUrl;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function getDeveloper()
    {
        return $this->_developer;
    }

    public function getDeveloperUrl()
    {
        return $this->_developerUrl;
    }

    public function hasCpSection()
    {
        return true;
    }

    public function getCraftRequiredVersion()
    {
        return $this->_minVersion;
    }

    public function isCraftRequiredVersion()
    {
        return version_compare(craft()->getVersion(), $this->getCraftRequiredVersion(), '>=');
    }

    public function addTwigExtension()
    {
        Craft::import('plugins.cacheflag.twigextensions.*');
        return new CacheFlagTwigExtension();
    }

    public function registerCpRoutes()
    {
        return array(
            'cacheflag' => array('action' => 'cacheFlag/getIndex'),
        );
    }

    public function init()
    {
        parent::init();

        if (!craft()->request->isCpRequest() || !$this->isCraftRequiredVersion()) {
            return false;
        }

        $this->addEventListeners();

    }

    protected function addEventListeners()
    {
        craft()->on('elements.saveElement', array($this, 'onSaveElement'));
        craft()->on('elements.beforeDeleteElements', array($this, 'onBeforeDeleteElements'));
        craft()->on('elements.performAction', array($this, 'onPerformAction'));
    }

    /*
    *   Event handlers
    *
    */
    public function onSaveElement(Event $event)
    {
        craft()->cacheFlag->deleteFlaggedCachesByElement($event->params['element']);
    }

    public function onBeforeDeleteElements(Event $event)
    {
        $elementIds = $event->params['elementIds'];
        foreach ($elementIds as $elementId) {
            if ($element = craft()->elements->getElementById($elementId)) {
                craft()->cacheFlag->deleteFlaggedCachesByElement($element);
            }
        }
    }

    public function onPerformAction(Event $event)
    {

        $action = $event->params['action'];
        $criteria = $event->params['criteria'];

        // The actions we want to... act on.
        $actions = array('SetStatus');

        if (in_array($action->name, $actions)) {
            $elements = $criteria->find();
            foreach ($elements as $element) {
                craft()->cacheFlag->deleteFlaggedCachesByElement($element);
            }
        }

    }

}
