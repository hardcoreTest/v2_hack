<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.06.2016
 * Time: 12:25
 */

namespace schedule\Model;


class Users extends AbstractDbModel
{
    const FIELD_ID = "id";
    const FIELD_NAME = "name";

    const TYPE_USERS = ['user', 'collector', 'utilizer'];
    // user, collector, utilizer
    const FIELD_TYPE_USER = "type_user";
    const TYPE_WASTE = ['paper', 'glass', 'plastic'];
//    paper, хуибала, дрыгалы
    const FIELD_TYPE_WASTE = "type_waste";
    const FIELD_COORDINATE = "coordinate";
    const FIELD_CONTACT = "contact";
    const FIELD_EMAIL = "email";
    const FIELD_DATE_CREATE = "date_create";
    const FIELD_DATE_UPDATE = "date_update";

    const TABLE_NAME = "user";

    const  USER_TRANSLATE = [
        'user' => 'Пользователь',
        'collector' => 'Сборщик',
        'utilizer' => 'Утилизатор',
    ];
    const  WASTE_TRANSLATE = [
        'paper' => 'Бумага',
        'glass' => 'Стекло',
        'plastic' => 'Пластик',
    ];

    const WASTE_COLOR = [
        'paper' => '#fff',
        'glass' => '#f0f',
        'plastic' => '#ff0',
    ];
    /**
     * @var string $id ;
     */
    protected $id;

    /** @var string */
    protected $name;
    /** @var string */
    protected $type_user;
    /** @var string */
    protected $type_waste;
    /** @var string */
    protected $coordinate;
    /** string */
    protected $contact;
    /** string */
    protected $email;
    /** @var string */
    protected $date_create;
    /** @var string */
    protected $date_update;

    protected $tableName = self::TABLE_NAME;
    protected $keyField = 'id';
    protected $fieldMap = [
        'id' => 'id',
        'name' => 'name',
        'type_user' => 'type_user',
        'type_waste' => 'type_waste',
        'coordinate' => 'coordinate',
        'contact' => 'contact',
        'date_create' => 'date_create',
        'date_update' => 'date_update',
    ];

    /**
     * @param self[] $models
     * @return self[]
     */
    public function getGroupByType($models)
    {
        $result = [];
        foreach ($models as $model) {
            $types = $model->getTypeWaste();
            foreach ($types as $type) {
                if (empty($result[$type])) {
                    $result[$type] = [];
                }
                $result[$type][] = $model->toArray();
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
        $this->dirtyField('id');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
        $this->dirtyField('name');
    }

    /**
     * @return string
     */
    public function getTypeUser(): string
    {
        return $this->type_user;
    }

    /**
     * @param string $type_user
     */
    public function setTypeUser(string $type_user)
    {
        $this->type_user = $type_user;
        $this->dirtyField('type_user');
    }

    /**
     * @return array
     */
    public function getTypeWaste(): array
    {
        return json_decode($this->type_waste);
    }

    /**
     * @param string $type_waste
     */
    public function setTypeWaste(string $type_waste)
    {
        $this->type_waste = json_encode($type_waste);
        $this->dirtyField('type_waste');
    }

    /**
     * @return string
     */
    public function getCoordinate(): string
    {
        return json_decode($this->coordinate);
    }

    /**
     * @param string $coordinate
     */
    public function setCoordinate(string $coordinate)
    {
        $this->coordinate = json_encode($coordinate);
        $this->dirtyField('coordinate');
    }

    /**
     * @return mixed
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param mixed $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
        $this->dirtyField('contact');
    }

    /**
     * @return string
     */
    public function getDateCreate(): string
    {
        return $this->date_create;
    }

    /**
     * @param string $date_create
     */
    public function setDateCreate(string $date_create)
    {
        $this->date_create = $date_create;
        $this->dirtyField('date_create');
    }

    /**
     * @return string
     */
    public function getDateUpdate(): string
    {
        return $this->date_update;
    }

    /**
     * @param string $date_update
     */
    public function setDateUpdate(string $date_update)
    {
        $this->date_update = $date_update;
        $this->dirtyField('date_update');
    }


}