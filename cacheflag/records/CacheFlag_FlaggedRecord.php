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

class CacheFlag_FlaggedRecord extends BaseRecord
{
	public function getTableName()
	{
		return 'templatecaches_flagged';
	}

	/**
	 * @access protected
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
			'cacheId' => AttributeType::Number,
			'flags' => AttributeType::String,
		);
	}

    /**
	 * Creates the model's table.
	 *
	 * @return null
	 */
    public function createTable()
    {
        parent::createTable();

        // There's no TemplateCachesRecord – need to set the FK manually
        craft()->db->createCommand()->addForeignKey($this->getTableName(), 'cacheId', 'templatecaches', 'id', static::CASCADE);

    }

}
