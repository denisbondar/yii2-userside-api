<?php
/**
 * Created by PhpStorm.
 * User: Denis Bondar
 * Date: 17.09.2017
 * Time: 19:35
 */

namespace denisbondar\userside\api;


use yii\base\ErrorException;
use yii\base\InvalidParamException;
use yii\base\Model;

class UsTasks extends Model
{
    const CATEGORY = "task";

    /** @var int ID ТИПА задания (tbl_conf_journal.TYPER) */
    public $work_typer;
    /** @var int дата на которую назначено выполнение задания (в формате ГГГГ-ММ-ДД чч:мм:сс) */
    public $work_datedo;

    /** @var int */
    public $id;
    public $pid;
    public $usercode;
    public $uzelcode;
    public $housecode;
    public $citycode;
    public $apart;
    public $fio;
    public $opis;
    public $dopf_N;
    public $state;

    public function rules()
    {
        return [
            [['work_typer', 'work_datedo'], 'required'],
            ['work_typer', 'number'],
            ['work_datedo', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            [['id', 'pid', 'usercode', 'uzelcode', 'housecode', 'citycode'], 'number'],
            [['apart', 'fio', 'opis'], 'string'],

            ['dopf_N', 'number'],
        ];
    }

    /**
     * @param $id int
     * @return UsTasks
     * @throws ErrorException
     */
    public static function show($id)
    {
        if (!is_int($id)) {
            throw new InvalidParamException('ID must be integer');
        }

        $interaction = \Yii::createObject(UsApiInteraction::class);
        $interaction->prepare([
            'cat' => self::CATEGORY,
            'subcat' => 'show',
            'id' => $id,
        ]);
        $result = $interaction->get();

        $data = $result->Data;

        return new self([
            'id' => (int)$data->id,
            'pid' => (int)$data->parentTaskId,
            'work_typer' => (int)$data->type->id,
            'work_datedo' => $data->date->todo,
            'state' => (int)$data->state->id,
            'usercode' => (int)$data->customer->id,
            'fio' => $data->customer->fullName,
            'uzelcode' => (int)$data->node->id,
            'housecode' => (int)$data->address->houseId,
            'citycode' => (int)$data->address->cityId,
            'apart' => $data->address->apartament,
            'opis' => $data->description,
        ]);
    }
}
