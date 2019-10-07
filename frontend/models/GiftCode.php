<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gift_code".
 *
 * @property int $id
 * @property string $token
 * @property int $status
 * @property int $expires_at
 * @property int $created_at
 * @property int $updated_at
 */
class GiftCode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gift_code';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token', 'expires_at', 'created_at', 'updated_at'], 'required'],
            [['status', 'expires_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['status', 'expires_at', 'created_at', 'updated_at'], 'integer'],
            [['token'], 'string', 'max' => 255],
            [['token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'token' => 'Token',
            'status' => 'Status',
            'expires_at' => 'Expires At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
