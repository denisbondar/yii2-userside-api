<?php
/**
 * @link      https://github.com/denisbondar/yii2-userside-api
 * @package   yii2-userside-api
 * @author    Denis Bondar <bondar.den@gmail.com>
 * @license   MIT License - view the LICENSE file that was distributed with this source code.
 * @date      17.09.2017
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
     * Метод детальной информации о задании: show
     *
     * @param $id int
     * @return UsTasks
     * @throws ErrorException
     */
    public static function show($id)
    {
        if (!is_numeric($id)) {
            throw new InvalidParamException('ID must be numeric.');
        }

        $interaction = \Yii::createObject(UsApiInteraction::class);
        $result = $interaction->get([
            'cat' => self::CATEGORY,
            'subcat' => 'show',
            'id' => (int)$id,
        ]);

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

    /**
     * Метод получения списка идентификаторов заданий: get_list
     * Метод является поисковым. Возвращает массив ID заданий,
     * удовлетворяющих заданным критериям поиска.
     *
     * @param $conditions array
     * @return array
     */
    public static function find($conditions)
    {
        if (!empty($conditions['state_id']) && is_array($conditions['state_id'])) {
            $conditions['state_id'] = implode(',', $conditions['state_id']);
        }
        if (!empty($conditions['type_id']) && is_array($conditions['type_id'])) {
            $conditions['type_id'] = implode(',', $conditions['type_id']);
        }
        if (!empty($conditions['staff_id']) && is_array($conditions['staff_id'])) {
            $conditions['staff_id'] = implode(',', $conditions['staff_id']);
        }
        if (!empty($conditions['division_id']) && is_array($conditions['division_id'])) {
            $conditions['division_id'] = implode(',', $conditions['division_id']);
        }

        $interaction = \Yii::createObject(UsApiInteraction::class);
        $result = $interaction->get(array_merge([
            'cat' => self::CATEGORY,
            'subcat' => 'get_list'
        ], $conditions));

        return [
            'ids' => explode(',', $result->list),
            'count' => $result->count,
        ];
    }

    /**
     * Метод получения списика связанных заданий: get_related_task_id
     * Метод возвращает массив ID заданий, которые связаны с
     * указанным ID задания.
     *
     * @param $id
     * @return array
     */
    public static function relatedTasks($id)
    {
        if (!is_numeric($id)) {
            throw new InvalidParamException('ID must be numeric.');
        }

        $interaction = \Yii::createObject(UsApiInteraction::class);
        $result = $interaction->get([
            'cat' => self::CATEGORY,
            'subcat' => 'get_related_task_id',
            'id' => (int)$id,
        ]);

        return explode(',', $result->Data);
    }

    /**
     * Добавление комментария к заданию по его ID.
     * Комментарий добавляется анонимно, так как API не предусматривает аутентификации.
     *
     * @param $id
     * @param $comment
     * @return int
     */
    public static function addComment($id, $comment)
    {
        if (!is_numeric($id)) {
            throw new InvalidParamException('ID must be numeric.');
        }

        $interaction = \Yii::createObject(UsApiInteraction::class);
        $result = $interaction->post([
            'cat' => self::CATEGORY,
            'subcat' => 'comment_add',
            'id' => (int)$id,
            'comment' => trim($comment),
        ]);

        return $result->Id;
    }

    /**
     * Проверка кода подтверждения
     *
     * @internal Метод не работает
     * @param $id
     * @param $code
     * @return mixed
     */
    public static function checkVerifyCode($id, $code)
    {
        if (!is_numeric($id)) {
            throw new InvalidParamException('ID must be numeric.');
        }

        $interaction = \Yii::createObject(UsApiInteraction::class);
        $result = $interaction->get([
            'cat' => self::CATEGORY,
            'subcat' => 'check_verify_code',
            'id' => (int)$id,
            'verify_code' => trim($code),
        ]);

        return $result;
    }
}
