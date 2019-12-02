<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="tasks_weeks")})
 * @ORM\Entity(repositoryClass="App\Repository\TasksRepository")
 */
class Tasks
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="week", type="integer", nullable=false)
     */
    protected $week;

    /**
     * @ORM\Column(name="year", type="integer", nullable=false)
     */
    protected $year;

    /**
     * @ORM\Column(name="day", type="string", length=255, nullable=true)
     */
    protected $day;

    /**
     * @ORM\Column(name="gestic", type="string", length=255, nullable=true)
     */
    protected $gestic;

    /**
     * @ORM\Column(name="gestic_description", type="string", length=255, nullable=true)
     */
    protected $gesticDescription;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(name="task", type="string", length=255, nullable=true)
     */
    protected $task;

    /**
     * @ORM\Column(name="hour_type", type="string", length=255, nullable=true)
     */
    protected $hourType;

    /**
     * @ORM\Column(name="hours", type="float", nullable=true)
     */
    protected $hours;

    /**
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(name="percent", type="integer", nullable=false)
     */
    protected $percent;

    /**
     * Tasks constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $week
     * @return Tasks
     */
    public function setWeek($week)
    {
        $this->week = $week;

        return $this;
    }

    /**
     * @return string
     */
    public function getWeek()
    {
        return $this->week;
    }

    /**
     * @param integer $year
     * @return Tasks
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param string $day
     * @return Tasks
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param string $gestic
     * @return Tasks
     */
    public function setGestic($gestic)
    {
        $this->gestic = $gestic;

        return $this;
    }

    /**
     * @return string
     */
    public function getGestic()
    {
        return $this->gestic;
    }

    /**
     * @param string $gesticDescription
     * @return Tasks
     */
    public function setGesticDescription($gesticDescription)
    {
        $this->gesticDescription = $gesticDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getGesticDescription()
    {
        return $this->gesticDescription;
    }

    /**
     * @param string $description
     * @return Tasks
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $task
     * @return Tasks
     */
    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * @return string
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param string $hourType
     * @return Tasks
     */
    public function setHourType($hourType)
    {
        $this->hourType = $hourType;

        return $this;
    }

    /**
     * @return string
     */
    public function getHourType()
    {
        return $this->hourType;
    }

    /**
     * @param float $hours
     * @return Tasks
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * @return float
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @param string $status
     * @return Tasks
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param integer $percent
     * @return Tasks
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * @return string
     */
    public function getPercent()
    {
        return $this->percent;
    }
}
