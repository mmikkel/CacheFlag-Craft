<?php namespace Craft;

/**
 * Cache Flag by Mats Mikkel Rummelhoff
 *
 * @author      Mats Mikkel Rummelhoff <http://mmikkel.no>
 * @package     Cache Tag
 * @since       Craft 2.4
 * @copyright   Copyright (c) 2015, Mats Mikkel Rummelhoff
 * @license     http://opensource.org/licenses/mit-license.php MIT License
 * @link        https://github.com/mmikkel/CacheFlag-Craft
 */

class CacheFlagService extends BaseApplicationComponent
{

    public function getPlugin()
	{
		return craft()->plugins->getPlugin('cacheFlag');
	}

	public function getCpTabs()
	{
		return array(
			'cacheFlagIndex' => array(
				'label' => '',
				'url' => UrlHelper::getUrl('cacheflag'),
			),
			'about' => array(
				'label' => Craft::t('About'),
				'url' => UrlHelper::getUrl('cacheflag/about'),
			),
		);
	}

    public function getFlags()
    {
        $query = craft()->db->createCommand();
        $query->from('templatecaches_flags');
        return $query->queryAll();
    }

    public function saveFlags(CacheFlagModel $model)
    {

        if (!$model->id || !$record = CacheFlag_FlagsRecord::model()->findById((int) $model->id))
        {
			$record = new CacheFlag_FlagsRecord();
		}

        $record->flags = $model->flags ? preg_replace('/\s+/', '', $model->flags) : null;
        $record->sectionId = $model->sectionId ? (int) $model->sectionId : null;
        $record->categoryGroupId = $model->categoryGroupId ? (int) $model->categoryGroupId : null;
        $record->tagGroupId = $model->tagGroupId ? (int) $model->tagGroupId : null;
        $record->userGroupId = $model->userGroupId ? (int) $model->userGroupId : null;
        $record->assetSourceId = $model->assetSourceId ? (int) $model->assetSourceId : null;
        $record->globalSetId = $model->globalSetId ? (int) $model->globalSetId : null;
        $record->elementType = $model->elementType ? trim(strip_tags($model->elementType)) : null;

        $record->validate();

        $model->addErrors($record->getErrors());

		if (!$model->hasErrors()) {
			$transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
			try	{
				if (!$model->id) {
					$record->save();
				} else {
					$record->update();
				}
				$model->id = $record->id;
				if ($transaction !== null) {
					$transaction->commit();
				}
			} catch (\Exception $e) {
				if ($transaction !== null) {
					$transaction->rollback();
				}
				throw $e;
			}

			return true;

		}

		return false;

    }

    public function deleteFlaggedCachesByElement($element)
    {

		$elementType = $element->elementType;

		// Get flagged caches for element
		$query = craft()->db->createCommand();
		$query->select('flags');
        $query->from('templatecaches_flags');

        switch ($elementType) {
			case 'Asset' :
				$query->orWhere(array(
					'assetSourceId' => $element->source->id,
				));
				break;
			case 'Category' :
				$query->orWhere(array(
					'categoryGroupId' => $element->group->id,
				));
				break;
			case 'Entry' :
				$query->orWhere(array(
					'sectionId' => $element->section->id,
				));
				break;
			case 'GlobalSet' :
				$query->orWhere(array(
					'globalSetId' => $element->id,
				));
				break;
			case 'Tag' :
				$query->orWhere(array(
					'tagGroupId' => $element->group->id,
				));
				break;
			case 'User' :
				foreach ($element->groups as $userGroup)
				{
					$query->orWhere(array(
						'userGroupId' => $userGroup->id,
					));
				}
				break;
		}

		$query->orWhere(array(
            'elementType' => $elementType,
    	));

    	$result = $query->queryAll();

		if (!$result || empty($result))
		{
			return false;
		}

		$flags = [];
		foreach ($result as $row)
    	{
    		$rowFlags = craft()->cacheFlag->sanitizeflags($row['flags']);
    		if (strlen($rowFlags) > 0)
    		{
    			$flags = array_merge($flags, explode(',', $rowFlags));
    		}
    	}

    	if (!empty($flags))
        {
        	// We can haz flags! Now go forth and smite all the caches
        	return $this->deleteFlaggedCachesByFlags($flags);
        }

        return false;

    }

    public function deleteFlaggedCachesByFlags($flags)
    {

    	$query = craft()->db->createCommand();
    	$query->select('cacheId');
    	$query->from('templatecaches_flagged');

    	if (!is_array($flags))
    	{
    		$flags = explode(',', $flags);
    	}

    	foreach ($flags as $flag)
    	{
    		$query->orWhere('FIND_IN_SET("' . $flag . '",flags)');
    	}

    	$result = $query->queryAll();

    	if (!$result || empty($result))
		{
			return false;
		}

		$cacheIds = [];

		foreach ($result as $row)
		{
			$cacheIds[] = (int) $row['cacheId'];
		}

		if (!empty($cacheIds))
		{
			return craft()->templateCache->deleteCacheById($cacheIds);
		}

		return false;

    }

    public function deleteAllFlaggedCaches()
    {

    	$query = craft()->db->createCommand();
    	$query->select('cacheId');
    	$query->from('templatecaches_flagged');

    	$result = $query->queryAll();

    	if (!$result || empty($result))
    	{
    		return false;
    	}

    	$cacheIds = [];
    	foreach ($result as $row)
		{
			$cacheIds[] = (int) $row['cacheId'];
		}

		if (!empty($cacheIds))
		{
			return craft()->templateCache->deleteCacheById($cacheIds);
		}

		return false;

    }

    /*
    *	Helper method that removes whitespace and pipes and replaces them with commas
    *	Also makes sure the string is alphanumeric only
    *
    */
    public function sanitizeFlags($flags)
    {
    	return preg_replace('/[ |]+/', ',', strip_tags(trim(preg_replace('@[^0-9a-z\,\|]+@i', '', $flags))));
    }

    /*
    *	Helper method that converts "camelCase" to "camel case"
    *	Stolen from http://leehblue.com/camelcase-to-snake_case/
    *
    */
    public function unCamelCase($value)
    {
		if (preg_match ('/[A-Z]/', $value) === 0)
		{
			return $value;
		}
		$pattern = '/([a-z])([A-Z])/';
		$r = strtolower (preg_replace_callback ($pattern, function ($a)
		{
			return $a[1] . ' ' . strtolower ($a[2]);
		}, $value));
		return $r;
    }

}
