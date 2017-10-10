<?php
namespace Craft;

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
 * Class CacheFlag_CacheService
 * @package Craft
 */
class CacheFlag_CacheService extends BaseApplicationComponent
{

    /**
     * The current request's path, as it will be stored in the templatecaches table.
     *
     * @var string
     */
    private $_path;

    /**
     * @param $key
     * @param $flags
     * @param bool|false $global
     * @return bool
     * @throws \Exception
     */
    public function addCacheByKey($key, $flags, $global = false)
    {

        if (!$caches = $this->getTemplateCachesByKey($key, $global)) {
            return false;
        }

        foreach ($caches as $cache) {
            $cacheId = (int)$cache['id'];
            $this->addCacheById($cacheId, $flags);
        }

    }

    /**
     * @param $cacheId
     * @param $flags
     * @throws \Exception
     */
    public function addCacheById($cacheId, $flags)
    {

        $flags = craft()->cacheFlag->sanitizeFlags($flags);

        $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;

        try {
            craft()->db->createCommand()->insert('templatecaches_flagged', array(
                'cacheId' => $cacheId,
                'flags' => $flags,
                'uid' => StringHelper::UUID(),
                'dateCreated' => date('Y-m-d H:i:s'),
                'dateUpdated' => date('Y-m-d H:i:s'),
            ), false);
            if ($transaction !== null) {
                $transaction->commit();
            }
        } catch (\Exception $e) {
            if ($transaction !== null) {
                $transaction->rollback();
            }
            throw $e;
        }

    }

    /**
     * @param $key
     * @param bool|false $global
     * @return mixed
     */
    public function getTemplateCachesByKey($key, $global = false)
    {
        $args = array(
            'cacheKey' => $key,
        );
        if ($global) {
            $args[] = 'path is null';
        } else {
            $args['path'] = $this->_getPath();
        }
        $query = craft()->db->createCommand();
        $query->from('templatecaches');
        $query->where($args);
        return $query->queryAll();
    }

    /**
     * Returns the current request path, including a "site:" or "cp:" prefix.
     *
     * @return string
     */
    private function _getPath()
    {
        if (!isset($this->_path)) {
            if (craft()->request->isCpRequest()) {
                $this->_path = 'cp:';
            } else {
                $this->_path = 'site:';
            }

            $this->_path .= craft()->request->getPath();

            if (($pageNum = craft()->request->getPageNum()) != 1) {
                $this->_path .= '/' . craft()->config->get('pageTrigger') . $pageNum;
            }

            // Get the querystring without the path param.
            if ($queryString = craft()->request->getQueryStringWithoutPath()) {
                $queryString = trim($queryString, '&');

                if ($queryString) {
                    $this->_path .= '?' . $queryString;
                }
            }
        }

        return $this->_path;
    }

}
