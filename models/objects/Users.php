<?php

namespace app\models\objects;

use Yii;

/**
 * This is the model class for table "Users".
 *
 * @property integer $id
 * @property integer $active
 * @property string $username
 * @property string $password
 * @property string $accessToken
 * @property string $authKey
 * @property string $firstname
 * @property string $lastname
 * @property string $role
 * @property resource $avatar
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['active'], 'integer'],
            [['password'], 'required'],
            [['role', 'avatar'], 'string'],
            [['username', 'accessToken', 'authKey', 'firstname', 'lastname'], 'string', 'max' => 255],
            [['password'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active' => 'Active',
            'username' => 'Username',
            'password' => 'Password',
            'accessToken' => 'Access Token',
            'authKey' => 'Auth Key',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'role' => 'Role',
            'avatar' => 'Avatar',
        ];
    }
}
