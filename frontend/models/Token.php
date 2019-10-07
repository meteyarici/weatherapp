<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "token".
 *
 * @property int $id
 * @property string $token
 * @property int $user_id
 * @property int $status
 * @property int $expires_at
 * @property int $created_at
 * @property int $updated_at
 */
class Token extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token', 'user_id', 'expires_at', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'status', 'expires_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['user_id', 'status', 'expires_at', 'created_at', 'updated_at'], 'integer'],
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
            'user_id' => 'User ID',
            'status' => 'Status',
            'expires_at' => 'Expires At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
