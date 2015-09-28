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

class CacheFlag_FlagsRecord extends BaseRecord
{
	public function getTableName()
	{
		return 'templatecaches_flags';
	}

	/**
	 * @access protected
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
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

    /**
	 * @access public
	 * @return array
	 */
	public function defineRelations()
	{
		return array(
			'section' => array(
				static::BELONGS_TO,
				'SectionRecord',
				'sectionId',
				'onDelete' => static::CASCADE,
			),
			'categoryGroup' => array(
				static::BELONGS_TO,
				'CategoryGroupRecord',
				'categoryGroupId',
				'onDelete' => static::CASCADE,
			),
            'tagGroup' => array(
				static::BELONGS_TO,
				'TagGroupRecord',
				'tagGroupId',
				'onDelete' => static::CASCADE,
			),
            'userGroup' => array(
				static::BELONGS_TO,
				'UserGroupRecord',
				'userGroupId',
				'onDelete' => static::CASCADE,
			),
            'assetSource' => array(
				static::BELONGS_TO,
				'AssetSourceRecord',
				'assetSourceId',
				'onDelete' => static::CASCADE,
			),
            'globalSet' => array(
				static::BELONGS_TO,
				'GlobalSetRecord',
				'globalSetId',
				'onDelete' => static::CASCADE,
			),
		);
	}

}
