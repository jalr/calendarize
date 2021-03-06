<?php
/**
 * Translate helper
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Calendarize\Utility;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Translate helper
 */
class TranslateUtility
{

    /**
     * Set the right path and extension for translations in PHP
     *
     * @param string $key
     *
     * @return NULL|string
     */
    public static function get($key)
    {
        if (TYPO3_MODE === 'FE' && !is_object($GLOBALS['TSFE'])) {
            // check wrong eID context. Do not call "LocalizationUtility::translate" in eID context, if there is no
            // valid TypoScriptFrontendController. Skip this call by returning just the $key!
            return $key;
        }
        return LocalizationUtility::translate(self::getLll($key), 'calendarize');
    }

    /**
     * Get the LLL string
     *
     * @param string $key
     *
     * @return string
     */
    public static function getLll($key)
    {
        return \HDNET\Autoloader\Utility\TranslateUtility::getLllString($key, 'calendarize', 'locallang.xlf');
    }
}
