<?php
namespace Craft;

use Twig_Extension;
use Twig_Filter_Method;

/**
 * Class CacheFlagTwigExtension
 */
class CacheFlagTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'cacheflag';
    }

    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return array(
            new CacheFlag_TokenParser(),
        );
    }

    public function getFilters()
    {
        return array(
            'cacheFlagUnCamelCase' => new Twig_Filter_Method($this, 'unCamelCase'),
        );
    }

    public function unCamelCase($input)
    {
        return craft()->cacheFlag->unCamelCase($input);
    }

}
