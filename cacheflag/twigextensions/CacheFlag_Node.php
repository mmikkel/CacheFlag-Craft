<?php
namespace Craft;

/**
 * Class CacheFlag_Node
 */
class CacheFlag_Node extends \Twig_Node
{
	// Properties
	// =========================================================================

	/**
	 * @var int
	 */
	private static $_cacheCount = 1;

	// Public Methods
	// =========================================================================

	/**
	 * @param \Twig_Compiler $compiler
	 *
	 * @return null
	 */
	public function compile(\Twig_Compiler $compiler)
	{
		$n = static::$_cacheCount++;

		$conditions = $this->getNode('conditions');
		$ignoreConditions = $this->getNode('ignoreConditions');
		$key = $this->getNode('key');
		$durationNum = $this->getAttribute('durationNum');
		$durationUnit = $this->getAttribute('durationUnit');
		$expiration = $this->getNode('expiration');
		$global = $this->getAttribute('global') ? 'true' : 'false';
		$flags = $this->getNode('flags');

		$compiler
			->addDebugInfo($this)
			->write("\$cacheService = \Craft\craft()->templateCache;\n")
			->write("\$cacheFlagService = \Craft\craft()->cacheFlag_cache;\n")
			->write("\$ignoreCacheFlag{$n} = (\Craft\craft()->request->isLivePreview() || \Craft\craft()->request->getToken()");

		if ($conditions)
		{
			$compiler
				->raw(' || !(')
				->subcompile($conditions)
				->raw(')');
		}
		else if ($ignoreConditions)
		{
			$compiler
				->raw(' || (')
				->subcompile($ignoreConditions)
				->raw(')');
		}

		$compiler
			->raw(");\n")
			->write("if (!\$ignoreCacheFlag{$n}) {\n")
			->indent()
				->write("\$cacheKey{$n} = ");

		if ($key)
		{
			$compiler->subcompile($key);
		}
		else
		{
			$compiler->raw('"'.StringHelper::randomString().'"');
		}

		$compiler
				->raw(";\n")
				->write("\$cacheBody{$n} = \$cacheService->getTemplateCache(\$cacheKey{$n}, {$global});\n")
			->outdent()
			->write("}\n")
			->write("if (empty(\$cacheBody{$n})) {\n")
			->indent()
				->write("ob_start();\n")
				->subcompile($this->getNode('body'))
				->write("\$cacheBody{$n} = ob_get_clean();\n")
				->write("if (!\$ignoreCacheFlag{$n}) {\n")
				->indent()
					->write("\$cacheService->startTemplateCache(\$cacheKey{$n});\n")
					->write("\$cacheService->endTemplateCache(\$cacheKey{$n}, {$global}, ");

		if ($durationNum)
		{
			// So silly that PHP doesn't support "+1 week" http://www.php.net/manual/en/datetime.formats.relative.php

			if ($durationUnit == 'week')
			{
				if ($durationNum == 1)
				{
					$durationNum = 7;
					$durationUnit = 'days';
				}
				else
				{
					$durationUnit = 'weeks';
				}
			}

			$compiler->raw("'+{$durationNum} {$durationUnit}'");
		}
		else
		{
			$compiler->raw('null');
		}

		$compiler->raw(', ');

		if ($expiration)
		{
			$compiler->subcompile($expiration);
		}
		else
		{
			$compiler->raw('null');
		}

		$compiler
					->raw(", \$cacheBody{$n});\n")
				->outdent()
				->write("}\n")
			->outdent();

		if ($flags)
		{
			$compiler->write("\$cacheFlagService->addCacheByKey(\$cacheKey{$n}, ");
			$compiler->subcompile($flags);
			$compiler->write(", {$global});\n");
		}

		$compiler
			->write("}\n")
			->write("echo \$cacheBody{$n};\n");

	}
}
