<?php

/**
 * TCA Structure for Configurations
 */

use HDNET\Autoloader\Utility\ArrayUtility;
use HDNET\Autoloader\Utility\ModelUtility;
use HDNET\Calendarize\Domain\Model\Configuration;
use HDNET\Calendarize\Service\TcaService;
use HDNET\Calendarize\Service\TimeSelectionWizard;
use HDNET\Calendarize\Utility\TranslateUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$base = ModelUtility::getTcaInformation(Configuration::class);

$timeType = str_replace(
    '--palette--;LLL:EXT:hdnet/Resources/Private/Language/locallang.xlf:language;language',
    '',
    $base['types']['1']['showitem']
);
$timeType = str_replace(
    ',frequency',
    ',--div--;LLL:EXT:calendarize/Resources/Private/Language/locallang.xlf:tx_calendarize_domain_model_configuration.frequency,frequency',
    $timeType
);
$timeType = str_replace(',external_ics_url', '', $timeType);
$timeType = str_replace(',groups', '', $timeType);
$timeType = str_replace(
    ',start_date,end_date',
    ',--palette--;LLL:EXT:calendarize/Resources/Private/Language/locallang.xlf:date;date',
    $timeType
);
$timeType = str_replace(
    ',start_time,end_time,all_day',
    ',--palette--;LLL:EXT:calendarize/Resources/Private/Language/locallang.xlf:time;time',
    $timeType
);
$timeType = str_replace(
    ',counter_interval,recurrence,day',
    ',--palette--;LLL:EXT:calendarize/Resources/Private/Language/locallang.xlf:frequency_configuration;frequency_configuration',
    $timeType
);

$baseConfiguration = '--palette--;LLL:EXT:calendarize/Resources/Private/Language/locallang.xlf:base_configuration;base';
$timeType = str_replace(
    'type,handling',
    $baseConfiguration,
    $timeType
);

$extendTab = ',--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.extended';
$iconPath = ExtensionManagementUtility::extRelPath('calendarize') . 'Resources/Public/Icons/';

$custom = [
    'ctrl' => [
        'type' => 'type',
        'hideTable' => true,
        'typeicons' => [
            Configuration::TYPE_TIME => $iconPath . 'Configuration.png',
            Configuration::TYPE_GROUP => $iconPath . 'ConfigurationGroupType.png',
            Configuration::TYPE_EXTERNAL => $iconPath . 'ConfigurationExternal.png',
        ],
        'typeicon_column' => 'type',
        'requestUpdate' => 'all_day,frequency,handling',
        'formattedLabel_userFunc' => TcaService::class . '->configurationTitle'
    ],
    'columns' => [
        'type' => [
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        TranslateUtility::getLll('configuration.type.' . Configuration::TYPE_TIME),
                        Configuration::TYPE_TIME
                    ],
                    [
                        TranslateUtility::getLll('configuration.type.' . Configuration::TYPE_GROUP),
                        Configuration::TYPE_GROUP
                    ],
                    [
                        TranslateUtility::getLll('configuration.type.' . Configuration::TYPE_EXTERNAL),
                        Configuration::TYPE_EXTERNAL
                    ],
                ],
                'default' => Configuration::TYPE_TIME
            ]
        ],
        'handling' => [
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        TranslateUtility::getLll('configuration.handling.' . Configuration::HANDLING_INCLUDE),
                        Configuration::HANDLING_INCLUDE
                    ],
                    [
                        TranslateUtility::getLll('configuration.handling.' . Configuration::HANDLING_EXCLUDE),
                        Configuration::HANDLING_EXCLUDE
                    ],
                    [
                        TranslateUtility::getLll('configuration.handling.' . Configuration::HANDLING_OVERRIDE),
                        Configuration::HANDLING_OVERRIDE
                    ],
                ],
                'default' => Configuration::HANDLING_INCLUDE
            ]
        ],
        'start_date' => [
            'config' => [
                'eval' => 'required,date',
                'size' => 6,
            ],
            'displayCond' => [
                'FIELD:type:=:' . Configuration::TYPE_TIME,
            ],
        ],
        'end_date' => [
            'config' => [
                'eval' => 'date',
                'size' => 6,
            ],
            'displayCond' => [
                'FIELD:type:=:' . Configuration::TYPE_TIME,
            ],
        ],
        'start_time' => [
            'config' => [
                'eval' => 'time,required',
                'size' => 4,
                'wizards' => [
                    'time_selection' => [
                        'type' => 'userFunc',
                        'userFunc' => TimeSelectionWizard::class . '->renderWizard',
                    ],
                ],
            ],
            'displayCond' => [
                'AND' => [
                    'FIELD:all_day:!=:1',
                    'FIELD:type:=:' . Configuration::TYPE_TIME,
                ],
            ],
        ],
        'end_time' => [
            'config' => [
                'eval' => 'time',
                'size' => 4,
                'wizards' => [
                    'time_selection' => [
                        'type' => 'userFunc',
                        'userFunc' => TimeSelectionWizard::class . '->renderWizard',
                    ],
                ],
            ],
            'displayCond' => [
                'AND' => [
                    'FIELD:all_day:!=:1',
                    'FIELD:type:=:' . Configuration::TYPE_TIME,
                ],
            ],
        ],
        'all_day' => [
            'displayCond' => [
                'FIELD:type:=:' . Configuration::TYPE_TIME,
            ],
            'config' => [
                'default' => '0',
            ],
        ],
        'groups' => [
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_calendarize_domain_model_configurationgroup',
                'minitems' => '1',
                'size' => 5,
                'maxitems' => '99',
            ],
            'displayCond' => [
                'FIELD:type:=:' . Configuration::TYPE_TIME,
            ],
        ],
        'frequency' => [
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        TranslateUtility::getLll('configuration.frequency.' . Configuration::FREQUENCY_NONE),
                        Configuration::FREQUENCY_NONE
                    ],
                    [
                        TranslateUtility::getLll('configuration.frequency.' . Configuration::FREQUENCY_DAILY),
                        Configuration::FREQUENCY_DAILY
                    ],
                    [
                        TranslateUtility::getLll('configuration.frequency.' . Configuration::FREQUENCY_WEEKLY),
                        Configuration::FREQUENCY_WEEKLY
                    ],
                    [
                        TranslateUtility::getLll('configuration.frequency.' . Configuration::FREQUENCY_MONTHLY),
                        Configuration::FREQUENCY_MONTHLY
                    ],
                    [
                        TranslateUtility::getLll('configuration.frequency.' . Configuration::FREQUENCY_YEARLY),
                        Configuration::FREQUENCY_YEARLY
                    ],
                ],
                'default' => Configuration::FREQUENCY_NONE
            ],
            'displayCond' => [
                'FIELD:type:=:' . Configuration::TYPE_TIME,
            ],
        ],
        'till_date' => [
            'config' => [
                'eval' => 'date',
                'size' => 8,
            ],
            'displayCond' => [
                'AND' => [
                    'FIELD:frequency:!=:' . Configuration::FREQUENCY_NONE,
                    'FIELD:type:=:' . Configuration::TYPE_TIME,
                ],
            ],
        ],
        'counter_amount' => [
            'config' => [
                'eval' => 'int',
                'size' => 5,
                'default' => 0,
            ],
            'displayCond' => [
                'AND' => [
                    'FIELD:frequency:!=:' . Configuration::FREQUENCY_NONE,
                    'FIELD:type:=:' . Configuration::TYPE_TIME,
                ],
            ],
        ],
        'counter_interval' => [
            'config' => [
                'eval' => 'int,required',
                'size' => 5,
                'default' => '1',
            ],
            'displayCond' => [
                'AND' => [
                    'FIELD:frequency:!=:' . Configuration::FREQUENCY_NONE,
                    'FIELD:type:=:' . Configuration::TYPE_TIME,
                ],
            ],
        ],
        'external_ics_url' => [
            'displayCond' => [
                'FIELD:type:=:' . Configuration::TYPE_EXTERNAL,
            ],
            'config' => [
                'eval' => 'trim,required',
            ],
        ],
        'day' => [
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        TranslateUtility::getLll('configuration.day.' . Configuration::DAY_SPECIAL_WEEKDAY),
                        Configuration::DAY_SPECIAL_WEEKDAY
                    ],
                    [
                        TranslateUtility::getLll('configuration.day.' . Configuration::DAY_SPECIAL_BUSINESS),
                        Configuration::DAY_SPECIAL_BUSINESS
                    ],
                    [
                        TranslateUtility::getLll('configuration.day.' . Configuration::DAY_SPECIAL_WORKDAY),
                        Configuration::DAY_SPECIAL_WORKDAY
                    ],
                    [
                        TranslateUtility::getLll('configuration.day.' . Configuration::DAY_SPECIAL_WEEKEND),
                        Configuration::DAY_SPECIAL_WEEKEND
                    ],
                    [
                        TranslateUtility::getLll('configuration.day.' . Configuration::DAY_MONDAY),
                        Configuration::DAY_MONDAY
                    ],
                    [
                        TranslateUtility::getLll('configuration.day.' . Configuration::DAY_TUESDAY),
                        Configuration::DAY_TUESDAY
                    ],
                    [
                        TranslateUtility::getLll('configuration.day.' . Configuration::DAY_WEDNESDAY),
                        Configuration::DAY_WEDNESDAY
                    ],
                    [
                        TranslateUtility::getLll('configuration.day.' . Configuration::DAY_THURSDAY),
                        Configuration::DAY_THURSDAY
                    ],
                    [
                        TranslateUtility::getLll('configuration.day.' . Configuration::DAY_FRIDAY),
                        Configuration::DAY_FRIDAY
                    ],
                    [
                        TranslateUtility::getLll('configuration.day.' . Configuration::DAY_SATURDAY),
                        Configuration::DAY_SATURDAY
                    ],
                    [
                        TranslateUtility::getLll('configuration.day.' . Configuration::DAY_SUNDAY),
                        Configuration::DAY_SUNDAY
                    ],
                ],
                'default' => Configuration::DAY_SPECIAL_WEEKDAY
            ],
            'displayCond' => [
                'OR' => [
                    'FIELD:frequency:=:' . Configuration::FREQUENCY_MONTHLY,
                    'FIELD:frequency:=:' . Configuration::FREQUENCY_YEARLY,
                ],
            ],
        ],
        'recurrence' => [
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        TranslateUtility::getLll('configuration.recurrence.' . Configuration::RECURRENCE_NONE),
                        Configuration::RECURRENCE_NONE
                    ],
                    [
                        TranslateUtility::getLll('configuration.recurrence.' . Configuration::RECURRENCE_FIRST),
                        Configuration::RECURRENCE_FIRST
                    ],
                    [
                        TranslateUtility::getLll('configuration.recurrence.' . Configuration::RECURRENCE_SECOND),
                        Configuration::RECURRENCE_SECOND
                    ],
                    [
                        TranslateUtility::getLll('configuration.recurrence.' . Configuration::RECURRENCE_THIRD),
                        Configuration::RECURRENCE_THIRD
                    ],
                    [
                        TranslateUtility::getLll('configuration.recurrence.' . Configuration::RECURRENCE_FOURTH),
                        Configuration::RECURRENCE_FOURTH
                    ],
                    [
                        TranslateUtility::getLll('configuration.recurrence.' . Configuration::RECURRENCE_FIFTH),
                        Configuration::RECURRENCE_FIFTH
                    ],
                    [
                        TranslateUtility::getLll('configuration.recurrence.' . Configuration::RECURRENCE_LAST),
                        Configuration::RECURRENCE_LAST
                    ],
                    [
                        TranslateUtility::getLll('configuration.recurrence.' . Configuration::RECURRENCE_NEXT_TO_LAST),
                        Configuration::RECURRENCE_NEXT_TO_LAST
                    ],
                    [
                        TranslateUtility::getLll('configuration.recurrence.' . Configuration::RECURRENCE_THIRD_LAST),
                        Configuration::RECURRENCE_THIRD_LAST
                    ],
                ],
                'default' => Configuration::RECURRENCE_NONE
            ],
            'displayCond' => [
                'OR' => [
                    'FIELD:frequency:=:' . Configuration::FREQUENCY_MONTHLY,
                    'FIELD:frequency:=:' . Configuration::FREQUENCY_YEARLY,
                ],
            ],
        ],
        'import_id' => [
            'config' => [
                'readOnly' => true,
            ],
        ]
    ],
    'palettes' => [
        'base' => [
            'canNotCollapse' => 1,
            'showitem' => 'type,handling',
        ],
        'date' => [
            'canNotCollapse' => 1,
            'showitem' => 'start_date,end_date',
        ],
        'time' => [
            'canNotCollapse' => 1,
            'showitem' => 'start_time,end_time,--linebreak--,all_day',
        ],
        'termination_condition' => [
            'canNotCollapse' => 1,
            'showitem' => 'till_date,counter_amount',
        ],
        'frequency_configuration' => [
            'canNotCollapse' => 1,
            'showitem' => 'counter_interval,recurrence,day',
        ],
    ],
    'types' => [
        Configuration::TYPE_TIME => [
            'showitem' => $timeType,
        ],
        Configuration::TYPE_GROUP => [
            'showitem' => $baseConfiguration . ',groups' . $extendTab,
        ],
        Configuration::TYPE_EXTERNAL => [
            'showitem' => $baseConfiguration . ',external_ics_url' . $extendTab,
        ],
    ]
];

$tca = ArrayUtility::mergeRecursiveDistinct($base, $custom);
unset($tca['types']['1']);

return $tca;
