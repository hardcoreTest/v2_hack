<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.06.2016
 * Time: 12:25
 */

namespace schedule\Model;


class Schedule extends AbstractDbModel
{
    const FIELD_ID = "id";
    const FIELD_DATE = "date";
    const FIELD_LESSON = "lesson";

    const TABLE_NAME = "schedule";
    /**
     * @var string $id ;
     */
    protected $id;

    /**
     * @var string $date ;
     */
    protected $date;
    /**
     * @var string $lesson ;
     */
    protected $lesson;

    protected $tableName = self::TABLE_NAME;
    protected $keyField = 'id';
    protected $fieldMap = array(
        'id' => 'id',
        'date' => 'date',
        'lesson' => 'lesson',
    );


    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
        $this->dirtyField('date');
    }

    /**
     * @return string
     */
    public function getLesson()
    {
        return $this->lesson;
    }

    /**
     * @param string $lesson
     */
    public function setLesson($lesson)
    {
        $this->lesson = $lesson;
        $this->dirtyField('lesson');
    }



}