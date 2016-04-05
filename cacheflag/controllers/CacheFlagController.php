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

        $flags = $request->getPost('cacheflags');

        $success = false;

        $savedFlags = [];

        foreach ($flags as $key => $flag) {

            $target = @$flag['target'] ?: null;
            $pointId = @$flag['pointId'] ?: null;

            if (!$target || !$pointId) {
                continue;
            }

            $model = new CacheFlagModel();
            $model->id = @$flag['flagId'] ?: null;
            $model->flags = isset($flag['flags']) ? craft()->cacheFlag->sanitizeFlags($flag['flags']) : null;

            $savedFlags[$key]['flags'] = $model->flags;

            if (!$model->flags) {
                if ($model->id) {
                    craft()->cacheFlag->deleteFlagsById($model->id);
                }
                continue;
            }

            if ($target !== 'elementType') {
                $targetIdProperty = $target . 'Id';
                $model->$targetIdProperty = (int) $pointId;
            } else {
                $elementType = @$flag['elementType'] ?: null;
                if (!$elementType) {
                    continue;
                }
                $model->elementType = $elementType;
            }

            $success = craft()->cacheFlag->saveFlags($model);

            if ($success && $success->id) {

                $savedFlags[$key]['id'] = $success->id;
                $savedFlags[$key]['flags'] = $success->flags;

            } else {

                CacheFlagPlugin::log(Craft::t('CacheFlag unable to save tags for {target} "{targetName}"', array(
                    'target' => craft()->cacheFlag->unCamelCase($target),
                    'targetName' => Craft::t($request->getPost('targetName')),
                )));

            }

        }

        $iCanHazAjax = craft()->request->isAjaxRequest();
        $message = $success ? Craft::t($this->_getRandomSuccessMessage()) : Craft::t('Sorry, Cache Flag encountered some issues saving your flags. Please check your logs.');

        if ($iCanHazAjax) {
            $this->returnJson(array(
                'success' => $success,
                'message' => $message,
                'flags' => $savedFlags,
            ));
        } else {
            if ($success) {
                craft()->userSession->setNotice($message);
                $this->redirectToPostedUrl();
            } else {
                craft()->userSession->setError($message);
            }
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

    private function _getRandomSuccessMessage()
    {
        $messages = array(
            "Flags saved! Good day to you.",
            "Ahoy! All flags saved.",
            "Flags. Saved. High five.",
            "Flags! We haz them.",
            "Cache flags saved, friend.",
            "Yarr! Yer flags be safe.",
            "Dun dun dun. All flags saved.",
            "You are awesome. And so are your flags.",
            "Flags, so many flags. All mine!",
            "Flags flags flags flags",
            "You say save, I say sure.",
            "Before, your flags were lost. But now they are saved.",
            "My, what beautiful flags you have.",
            "Your flags are safe with me.",
            "I'll just hang on to these flags for you.",
        );
        return $messages[array_rand($messages)];
    }

}
