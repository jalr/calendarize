<?php
/**
 * Index information
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Calendarize\Domain\Model;

use HDNET\Calendarize\Exception;
use HDNET\Calendarize\Register;
use HDNET\Calendarize\Utility\DateTimeUtility;
use HDNET\Calendarize\Utility\HelperUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Index information
 *
 * @db
 * @smartExclude Workspaces
 */
class Index extends AbstractModel
{

    /**
     * The unique register key of the used table/model configuration
     *
     * @var string
     * @db varchar(150) DEFAULT '' NOT NULL
     */
    protected $uniqueRegisterKey;

    /**
     * TableName
     *
     * @var string
     * @db varchar(150) DEFAULT '' NOT NULL
     */
    protected $foreignTable;

    /**
     * The Id of the foreign element
     *
     * @var int
     * @db
     */
    protected $foreignUid;

    /**
     * Start date
     *
     * @var \DateTime
     * @db
     */
    protected $startDate;

    /**
     * End date
     *
     * @var \DateTime
     * @db
     */
    protected $endDate;

    /**
     * Start time
     *
     * @var int
     * @db
     */
    protected $startTime;

    /**
     * End time
     *
     * @var int
     * @db
     */
    protected $endTime;

    /**
     * AllDay
     *
     * @var boolean
     * @db
     */
    protected $allDay;

    /**
     * The original object
     *
     * @var AbstractEntity
     */
    protected $originalObject;

    /**
     * Get the original record for the current index
     *
     * @return AbstractEntity
     * @throws Exception
     */
    public function getOriginalObject()
    {
        if ($this->originalObject === null) {
            $configuration = $this->getConfiguration();
            if ($configuration === null) {
                throw new Exception('No valid configuration for the current index: ' . $this->getUniqueRegisterKey(), 123678123);
            }
            $this->originalObject = $this->getOriginalRecordByConfiguration($configuration, $this->getForeignUid());
        }
        return $this->originalObject;
    }

    /**
     * Get the original record by configuration
     *
     * @param $configuration
     * @param $uid
     *
     * @return object
     */
    protected function getOriginalRecordByConfiguration($configuration, $uid)
    {
        $query = HelperUtility::getQuery($configuration['modelName']);
        $query->getQuerySettings()
            ->setRespectStoragePage(false);
        $query->matching($query->equals('uid', $uid));
        return $query->execute()
            ->getFirst();
    }

    /**
     * Get the current configuration
     *
     * @return null|array
     */
    public function getConfiguration()
    {
        foreach (Register::getRegister() as $key => $configuration) {
            if ($this->getUniqueRegisterKey() == $key) {
                return $configuration;
            }
        }
        return null;
    }

    /**
     * Get the complete start date
     *
     * @return \DateTime
     */
    public function getStartDateComplete()
    {
        $date = $this->getStartDate();
        if (!$this->isAllDay()) {
            $time = DateTimeUtility::normalizeDateTimeSingle($this->getStartTime());
            $date->setTime($time->format('H'), $time->format('i'), 0);
        }
        return $date;
    }

    /**
     * Get the complete end date
     *
     * @return \DateTime
     */
    public function getEndDateComplete()
    {
        $date = $this->getEndDate();
        if (!$this->isAllDay()) {
            $time = DateTimeUtility::normalizeDateTimeSingle($this->getEndTime());
            $date->setTime($time->format('H'), $time->format('i'), 0);
        }
        return $date;
    }

    /**
     * Get the complete end date
     *
     * @return \DateTime
     */
    public function getEndDateCompleteFullcalendar()
    {
        $date = $this->getEndDateComplete();
        if ($this->isAllDay()) {
            $date->modify('+1 day');
        }
        return $date;
    }

    /**
     * Set foreign uid
     *
     * @param int $foreignUid
     */
    public function setForeignUid($foreignUid)
    {
        $this->foreignUid = $foreignUid;
    }

    /**
     * Get foreign uid
     *
     * @return int
     */
    public function getForeignUid()
    {
        return $this->foreignUid;
    }

    /**
     * Set unique register key
     *
     * @param string $uniqueRegisterKey
     */
    public function setUniqueRegisterKey($uniqueRegisterKey)
    {
        $this->uniqueRegisterKey = $uniqueRegisterKey;
    }

    /**
     * Get unique register key
     *
     * @return string
     */
    public function getUniqueRegisterKey()
    {
        return $this->uniqueRegisterKey;
    }

    /**
     * Set foreign table
     *
     * @param string $foreignTable
     */
    public function setForeignTable($foreignTable)
    {
        $this->foreignTable = $foreignTable;
    }

    /**
     * Get foreign table
     *
     * @return string
     */
    public function getForeignTable()
    {
        return $this->foreignTable;
    }

    /**
     * Set all day
     *
     * @param boolean $allDay
     */
    public function setAllDay($allDay)
    {
        $this->allDay = $allDay;
    }

    /**
     * Is all day
     *
     * @return boolean
     */
    public function isAllDay()
    {
        return (bool)$this->allDay;
    }

    /**
     * Set end date
     *
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * Get end date
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set end time
     *
     * @param int $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * Get end time
     *
     * @return int
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set start date
     *
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * Get start date
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set start time
     *
     * @param int $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * Get start time
     *
     * @return int
     */
    public function getStartTime()
    {
        return $this->startTime;
    }
}
