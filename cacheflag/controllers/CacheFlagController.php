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

class CacheFlagController extends BaseController
{

	/**
	 * @access public
	 * @return mixed
	 */
	public function actionGetIndex(array $variables = array())
	{

        $variables['tabs'] = craft()->cacheFlag->getCpTabs();
		$variables['selectedTab'] = 'cacheFlagIndex';

        $variables['targets'] = array(
            'section' => craft()->sections->allSections,
            'categoryGroup' => craft()->categories->allGroups,
            'tagGroup' => craft()->tags->allTagGroups,
            'userGroup' => craft()->userGroups->allGroups,
            'assetSource' => craft()->assetSources->allSources,
            'globalSet' => craft()->globals->allSets,
            'elementType' => craft()->elements->allElementTypes,
        );

        $variables['cacheFlags'] = [];
        $cacheFlags = craft()->cacheFlag->getFlags();

        foreach ($variables['targets'] as $target => $data)
        {
            foreach ($cacheFlags as $cacheFlag)
            {
                if (!isset($variables['cacheFlags'][$target]))
                {
                    $variables['cacheFlags'][$target] = [];
                }
                $targetProperty = $target !== 'elementType' ? $target . 'Id' : $target;
                if ($cacheFlag[$targetProperty])
                {
                    $variables['cacheFlags'][$target][$cacheFlag[$targetProperty]] = $cacheFlag;
                }
            }
        }

		return $this->renderTemplate('cacheFlag', $variables);

	}

    public function actionSaveFlags()
	{

		$this->requirePostRequest();
        $request = craft()->request;

        $model = new CacheFlagModel();
        $model->id = $request->getPost('flagId');
        $model->flags = craft()->cacheFlag->sanitizeFlags($request->getPost('flags'));

        $target = $request->getPost('target');

        if ($target !== 'elementType')
        {
            $targetIdProperty = $target . 'Id';
            $model->$targetIdProperty = (int) $request->getPost('id');
        }
        else
        {
            $model->elementType = $request->getPost('elementType');
        }

        if (craft()->cacheFlag->saveFlags($model)) {
            craft()->userSession->setNotice(Craft::t('Flags saved for {target} "{targetName}"', array(
                'target' => craft()->cacheFlag->unCamelCase($target),
                'targetName' => $request->getPost('targetName'),
            )));
			$this->redirectToPostedUrl($model);
        } else {
            craft()->userSession->setError(Craft::t('Mayday! Flags not saved for {targetName}!', array(
                'targetName' => $request->getPost('targetName'),
            )));
        }

	}

    public function actionClearAllCaches()
    {
        $this->requirePostRequest();
        craft()->cacheFlag->deleteAllFlaggedCaches();
        craft()->userSession->setNotice(Craft::t('All flagged caches cleared'));
        $this->redirectToPostedUrl();
    }

    public function actionClearCachesByFlags()
    {
        $this->requirePostRequest();
        $request = craft()->request;

        $flags = craft()->cacheFlag->sanitizeFlags($request->getPost('flags'));

        craft()->cacheFlag->deleteFlaggedCachesByFlags($tags);

        craft()->userSession->setNotice(Craft::t('Cached cleared for {flags}', array(
            'flags' => str_replace(',', ', ', $flags),
        )));

        $this->redirectToPostedUrl();

    }

}
