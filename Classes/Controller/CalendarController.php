<?php
/**
 * Calendar
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Calendarize\Controller;

use HDNET\Calendarize\Domain\Model\Index;
use HDNET\Calendarize\Register;
use HDNET\Calendarize\Utility\DateTimeUtility;
use HDNET\Calendarize\Utility\TranslateUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Calendar
 */
class CalendarController extends AbstractController
{

    /**
     * Init all actions
     */
    public function initializeAction()
    {
        parent::initializeAction();
        $this->indexRepository->setIndexTypes(GeneralUtility::trimExplode(',', $this->settings['configuration']));
        $additionalSlotArguments = [
            'contentRecord' => $this->configurationManager->getContentObject()->data,
            'settings' => $this->settings
        ];
        $this->indexRepository->setAdditionalSlotArguments($additionalSlotArguments);

        if (isset($this->settings['sorting'])) {
            $this->indexRepository->setDefaultSortingDirection($this->settings['sorting']);
        }

        if (isset($this->arguments['startDate'])) {
            $this->arguments['startDate']->getPropertyMappingConfiguration()
                ->setTypeConverterOption(
                    DateTimeConverter::class,
                    DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                    $this->settings['dateFormat']
                );
        }
        if (isset($this->arguments['endDate'])) {
            $this->arguments['endDate']->getPropertyMappingConfiguration()
                ->setTypeConverterOption(
                    DateTimeConverter::class,
                    DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                    $this->settings['dateFormat']
                );
        }
    }
    /**
     * Latest action
     *
     * @param \HDNET\Calendarize\Domain\Model\Index $index
     * @param \DateTime                             $startDate
     * @param \DateTime                             $endDate
     * @param array                                 $customSearch *
     * @param int                                   $year
     * @param int                                   $month
     * @param int                                   $week
     *
     * @ignorevalidation $startDate
     * @ignorevalidation $endDate
     * @ignorevalidation $customSearch
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function latestAction(
        Index $index = null,
        \DateTime $startDate = null,
        \DateTime $endDate = null,
        array $customSearch = [],
        $year = null,
        $month = null,
        $week = null
    ) {
        $this->checkStaticTemplateIsIncluded();
        if (($index instanceof Index) && in_array('detail', $this->getAllowedActions())) {
            $this->forward('detail');
        }

        $search = $this->determineSearch($startDate, $endDate, $customSearch, $year, $month, null, $week);

        $this->slotExtendedAssignMultiple([
            'indices'         => $search['indices'],
            'searchMode'      => $search['searchMode'],
            'searchParameter' => [
                'startDate'    => $startDate,
                'endDate'      => $endDate,
                'customSearch' => $customSearch,
                'year'         => $year,
                'month'        => $month,
                'week'         => $week
            ]
        ], __CLASS__, __FUNCTION__);
    }

    /**
     * Result action
     *
     * @param \HDNET\Calendarize\Domain\Model\Index $index
     * @param \DateTime                             $startDate
     * @param \DateTime                             $endDate
     * @param array                                 $customSearch
     * @param int                                   $year
     * @param int                                   $month
     * @param int                                   $week
     *
     * @ignorevalidation $startDate
     * @ignorevalidation $endDate
     * @ignorevalidation $customSearch
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function resultAction(
        Index $index = null,
        \DateTime $startDate = null,
        \DateTime $endDate = null,
        array $customSearch = [],
        $year = null,
        $month = null,
        $week = null
    ) {
        $this->checkStaticTemplateIsIncluded();
        if (($index instanceof Index) && in_array('detail', $this->getAllowedActions())) {
            $this->forward('detail');
        }

        $search = $this->determineSearch($startDate, $endDate, $customSearch, $year, $month, null, $week);

        $this->slotExtendedAssignMultiple([
            'indices'         => $search['indices'],
            'searchMode'      => $search['searchMode'],
            'searchParameter' => [
                'startDate'    => $startDate,
                'endDate'      => $endDate,
                'customSearch' => $customSearch,
                'year'         => $year,
                'month'        => $month,
                'week'         => $week
            ]
        ], __CLASS__, __FUNCTION__);
    }

    /**
     * List action
     *
     * @param \HDNET\Calendarize\Domain\Model\Index $index
     * @param \DateTime                             $startDate
     * @param \DateTime                             $endDate
     * @param array                                 $customSearch *
     * @param int                                   $year
     * @param int                                   $month
     * @param int                                   $day
     * @param int                                   $week
     *
     * @ignorevalidation $startDate
     * @ignorevalidation $endDate
     * @ignorevalidation $customSearch
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function listAction(
        Index $index = null,
        \DateTime $startDate = null,
        \DateTime $endDate = null,
        array $customSearch = [],
        $year = null,
        $month = null,
        $day = null,
        $week = null
    ) {
        $this->checkStaticTemplateIsIncluded();
        if (($index instanceof Index) && in_array('detail', $this->getAllowedActions())) {
            $this->forward('detail');
        }

        $search = $this->determineSearch($startDate, $endDate, $customSearch, $year, $month, $day, $week);

        $this->slotExtendedAssignMultiple([
            'indices'         => $search['indices'],
            'searchMode'      => $search['searchMode'],
            'searchParameter' => [
                'startDate'    => $startDate,
                'endDate'      => $endDate,
                'customSearch' => $customSearch,
                'year'         => $year,
                'month'        => $month,
                'day'          => $day,
                'week'         => $week
            ]
        ], __CLASS__, __FUNCTION__);
    }

    /**
     * @param \DateTime|null $startDate
     * @param \DateTime|null $endDate
     * @param array          $customSearch
     * @param int            $year
     * @param int            $month
     * @param int            $day
     * @param int            $week
     *
     * @return array
     */
    protected function determineSearch(
        \DateTime $startDate = null,
        \DateTime $endDate = null,
        array $customSearch = [],
        $year = null,
        $month = null,
        $day = null,
        $week = null
    ) {
        $searchMode = false;
        if ($startDate || $endDate || !empty($customSearch)) {
            $searchMode = true;
            $indices = $this->indexRepository->findBySearch($startDate, $endDate, $customSearch);
        } elseif (MathUtility::canBeInterpretedAsInteger($year) && MathUtility::canBeInterpretedAsInteger($month) && MathUtility::canBeInterpretedAsInteger($day)) {
            $indices = $this->indexRepository->findDay($year, $month, $day);
        } elseif (MathUtility::canBeInterpretedAsInteger($year) && MathUtility::canBeInterpretedAsInteger($month)) {
            $indices = $this->indexRepository->findMonth($year, $month);
        } elseif (MathUtility::canBeInterpretedAsInteger($year) && MathUtility::canBeInterpretedAsInteger($week)) {
            $indices = $this->indexRepository->findWeek($year, $week, $this->settings['weekStart']);
        } elseif (MathUtility::canBeInterpretedAsInteger($year)) {
            $indices = $this->indexRepository->findYear($year);
        } else {
            $overrideStartDate = (int)$this->settings['overrideStartdate'];
            $overrideEndDate = (int)$this->settings['overrideEnddate'];
            $indices = $this->indexRepository->findList(
                (int)$this->settings['limit'],
                $this->settings['listStartTime'],
                (int)$this->settings['listStartTimeOffsetHours'],
                $overrideStartDate,
                $overrideEndDate
            );
        }

        // use this variable in your extension to add more custom variables
        $variables['extended'] = [
            'indices'    => $indices,
            'searchMode' => $searchMode,
            'parameters' => [
                'startDate'    => $startDate,
                'endDate'      => $endDate,
                'customSearch' => $customSearch,
                'year'         => $year,
                'month'        => $month,
                'day'          => $day,
                'week'         => $week
            ]
        ];
        $variables['settings'] = $this->settings;

        $dispatcher = $this->objectManager->get(Dispatcher::class);
        $variables = $dispatcher->dispatch(__CLASS__, __FUNCTION__, $variables);

        return $variables['extended'];
    }

    /**
     * Year action
     *
     * @param int $year
     *
     * @return void
     */
    public function yearAction($year = null)
    {
        $date = DateTimeUtility::normalizeDateTime(1, 1, $year);

        $this->slotExtendedAssignMultiple([
            'indices' => $this->indexRepository->findYear($date->format('Y')),
            'date' => $date
        ], __CLASS__, __FUNCTION__);
    }

    /**
     * Month action
     *
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return void
     */
    public function monthAction($year = null, $month = null, $day = null)
    {
        $date = DateTimeUtility::normalizeDateTime($day, $month, $year);

        $this->slotExtendedAssignMultiple([
            'date' => $date,
            'indices' => $this->indexRepository->findMonth($date->format('Y'), $date->format('n')),
        ], __CLASS__, __FUNCTION__);
    }

    /**
     * Week action
     *
     * @param int $year
     * @param int $week
     *
     * @return void
     */
    public function weekAction($year = null, $week = null)
    {
        $now = DateTimeUtility::getNow();
        if ($year === null) {
            $year = $now->format('o'); // 'o' instead of 'Y': http://php.net/manual/en/function.date.php#106974
        }
        if ($week === null) {
            $week = $now->format('W');
        }
        $firstDay = DateTimeUtility::convertWeekYear2DayMonthYear($week, $year);
        $firstDay->setTime(0, 0, 0);

        // respect Week start
        $weekStart = (int)$this->settings['weekStart'];
        $firstDay->modify('+ ' . ($weekStart - 1) . 'days');

        $weekConfiguration = [
            '+0 day' => 2,
            '+1 days' => 2,
            '+2 days' => 2,
            '+3 days' => 2,
            '+4 days' => 2,
            '+5 days' => 1,
            '+6 days' => 1
        ];

        $this->slotExtendedAssignMultiple([
            'firstDay' => $firstDay,
            'indices' => $this->indexRepository->findWeek($year, $week, $this->settings['weekStart']),
            'weekConfiguration' => $weekConfiguration,
        ], __CLASS__, __FUNCTION__);
    }

    /**
     * Day action
     *
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return void
     */
    public function dayAction($year = null, $month = null, $day = null)
    {
        $date = DateTimeUtility::normalizeDateTime($day, $month, $year);
        $date->modify('+12 hours');

        $previous = clone $date;
        $previous->modify('-1 day');

        $next = clone $date;
        $next->modify('+1 day');

        $this->slotExtendedAssignMultiple([
            'indices' => $this->indexRepository->findDay($date->format('Y'), $date->format('n'), $date->format('j')),
            'today' => $date,
            'previous' => $previous,
            'next' => $next,
        ], __CLASS__, __FUNCTION__);
    }

    /**
     * Detail action
     *
     * @param \HDNET\Calendarize\Domain\Model\Index $index
     *
     * @return string
     */
    public function detailAction(Index $index = null)
    {
        if ($index === null) {
            // handle fallback for "strange language settings"
            if ($this->request->hasArgument('index')) {
                $indexId = (int)$this->request->getArgument('index');
                if ($indexId > 0) {
                    $index = $this->indexRepository->findByUid($indexId);
                }
            }

            if ($index === null) {
                if (!MathUtility::canBeInterpretedAsInteger($this->settings['listPid'])) {
                    return TranslateUtility::get('noEventDetailView');
                }
                $this->redirect('list', null, null, [], $this->settings['listPid'], 0, 301);
            }
        }

        $this->slotExtendedAssignMultiple([
            'index' => $index,
            'domain' => GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY')
        ], __CLASS__, __FUNCTION__);

        return $this->view->render();
    }

    /**
     * Render the search view
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param array $customSearch
     *
     * @ignorevalidation $startDate
     * @ignorevalidation $endDate
     * @ignorevalidation $customSearch
     */
    public function searchAction(\DateTime $startDate = null, \DateTime $endDate = null, array $customSearch = [])
    {
        $baseDate = DateTimeUtility::getNow();
        if (!($startDate instanceof \DateTimeInterface)) {
            $startDate = clone $baseDate;
        }
        if (!($endDate instanceof \DateTimeInterface)) {
            $baseDate->modify('+1 month');
            $endDate = $baseDate;
        }

        $this->slotExtendedAssignMultiple([
            'startDate' => $startDate,
            'endDate' => $endDate,
            'customSearch' => $customSearch,
            'configurations' => $this->getCurrentConfigurations()
        ], __CLASS__, __FUNCTION__);
    }

    /**
     * Get the allowed actions
     *
     * @return array
     */
    protected function getAllowedActions()
    {
        $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $allowedActions = [];
        foreach ($configuration['controllerConfiguration'] as $controllerName => $controllerActions) {
            $allowedActions[$controllerName] = $controllerActions['actions'];
        }
        return isset($allowedActions['Calendar']) ? $allowedActions['Calendar'] : [];
    }

    /**
     * Get the current configurations
     *
     * @return array
     */
    protected function getCurrentConfigurations()
    {
        $configurations = GeneralUtility::trimExplode(',', $this->settings['configuration'], true);
        $return = [];
        foreach (Register::getRegister() as $key => $configuration) {
            if (in_array($key, $configurations)) {
                $return[] = $configuration;
            }
        }
        return $return;
    }
}
