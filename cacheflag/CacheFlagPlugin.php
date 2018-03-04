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

    /**
     * @var string
     */
    protected $_version = '1.1.11';
    /**
     * @var string
     */
    protected $_schemaVersion = '1.0';
    /**
     * @var string
     */
    protected $_name = 'Cache Flag';
    /**
     * @var string
     */
    protected $_url = 'https://github.com/mmikkel/CacheFlag-Craft';
    /**
     * @var string
     */
    protected $_releaseFeedUrl = 'https://raw.githubusercontent.com/mmikkel/CacheFlag-Craft/master/releases.json';
    /**
     * @var string
     */
    protected $_documentationUrl = 'https://github.com/mmikkel/CacheFlag-Craft/blob/master/README.md';
    /**
     * @var string
     */
    protected $_description = 'Flag and clear template caches without element criteria.';
    /**
     * @var string
     */
    protected $_developer = 'Mats Mikkel Rummelhoff';
    /**
     * @var string
     */
    protected $_developerUrl = 'http://mmikkel.no';
    /**
     * @var string
     */
    protected $_minVersion = '2.4';

    /**
     * @return mixed
     */
    public function getName()
    {
        return Craft::t($this->_name);
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * @return string
     */
    public function getSchemaVersion()
    {
        return $this->_schemaVersion;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return $this->_releaseFeedUrl;
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return $this->_documentationUrl;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @return string
     */
    public function getDeveloper()
    {
        return $this->_developer;
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return $this->_developerUrl;
    }

    /**
     * @return bool
     */
    public function hasCpSection()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getCraftRequiredVersion()
    {
        return $this->_minVersion;
    }

    /**
     * @return mixed
     */
    public function isCraftRequiredVersion()
    {
        return version_compare(craft()->getVersion(), $this->getCraftRequiredVersion(), '>=');
    }

    /**
     * @return CacheFlagTwigExtension
     */
    public function addTwigExtension()
    {
        Craft::import('plugins.cacheflag.twigextensions.*');
        return new CacheFlagTwigExtension();
    }

    /**
     * @return array
     */
    public function registerCpRoutes()
    {
        return array(
            'cacheflag' => array('action' => 'cacheFlag/getIndex'),
        );
    }

    /**
     * @return bool
     */
    public function init()
    {
        parent::init();

        if (!$this->isCraftRequiredVersion()) {
            return false;
        }

        $this->addEventListeners();

    }

    /**
     *
     */
    protected function addEventListeners()
    {
        craft()->on('elements.saveElement', array($this, 'onSaveElement'));
        craft()->on('elements.beforeDeleteElements', array($this, 'onBeforeDeleteElements'));
        craft()->on('elements.performAction', array($this, 'onPerformAction'));
        craft()->on('structures.onMoveElement', array($this, 'onMoveElement'));
    }

    /*
    *   Event handlers
    *
    */
    /**
     * @param Event $event
     */
    public function onMoveElement(Event $event)
    {
        craft()->cacheFlag->deleteFlaggedCachesByElement($event->params['element']);
    }

    /**
     * @param Event $event
     */
    public function onSaveElement(Event $event)
    {
        craft()->cacheFlag->deleteFlaggedCachesByElement($event->params['element']);
    }

    /**
     * @param Event $event
     */
    public function onBeforeDeleteElements(Event $event)
    {
        $elementIds = $event->params['elementIds'];
        foreach ($elementIds as $elementId) {
            if ($element = craft()->elements->getElementById($elementId)) {
                craft()->cacheFlag->deleteFlaggedCachesByElement($element);
            }
        }
    }

    /**
     * @param Event $event
     */
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
