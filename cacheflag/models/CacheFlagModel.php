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

class CacheFlagModel extends BaseModel
{

	/**
	 * @access protected
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
            'id' => AttributeType::Number,
            'flags' => AttributeType::String,
			'sectionId' => AttributeType::Number,
            'categoryGroupId' => AttributeType::Number,
            'tagGroupId' => AttributeType::Number,
            'userGroupId' => AttributeType::Number,
            'assetSourceId' => AttributeType::Number,
            'globalSetId' => AttributeType::Number,
            'elementType' => AttributeType::String,
		);
	}

}
