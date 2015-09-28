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

class CacheFlag_CacheService extends BaseApplicationComponent
{

    public function addCacheByKey($key, $flags)
    {

        if (!$caches = $this->getTemplateCachesByKey($key))
        {
            return false;
        }

        foreach ($caches as $cache)
        {
            $cacheId = (int) $cache['id'];
            $this->addCacheById($cacheId, $flags);
        }

    }

    public function addCacheById($cacheId, $flags)
    {

        $flags = craft()->cacheFlag->sanitizeFlags($flags);

        $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
        try
        {
            craft()->db->createCommand()->insert('templatecaches_flagged', array(
                'cacheId'   => $cacheId,
                'flags'      => $flags,
        ), false);
            if ($transaction !== null)
            {
                $transaction->commit();
            }
        }
        catch (\Exception $e)
        {
             if ($transaction !== null)
             {
                  $transaction->rollback();
             }
             throw $e;
        }

    }

    public function getTemplateCachesByKey($key)
    {
        $query = craft()->db->createCommand();
        $query->from('templatecaches');
        $query->where(array(
            'cacheKey' => $key,
    ));
        return $query->queryAll();
    }

}
