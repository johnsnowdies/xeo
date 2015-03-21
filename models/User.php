<?php
namespace app\models;

//app\models\gii\Users is the model generated using Gii from users table
use Yii;
use app\models\objects\Users as DbUser;

class User extends \yii\base\Object implements \yii\web\IdentityInterface {

    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
    public $phone_number;
    public $user_type;
    public $active;
    public $firstname;
    public $lastname;
    public $role;
    public $avatar;

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        $dbUser = DbUser::find()
            ->where([
                "id" => $id
            ])
            ->one();
        if (!count($dbUser)) {
            return null;
        }
        return new static($dbUser);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $userType = null) {

        $dbUser = DbUser::find()
            ->where(["accessToken" => $token])
            ->one();
        if (!count($dbUser)) {
            return null;
        }
        return new static($dbUser);
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username) {
        $dbUser = DbUser::find()
            ->where([
                "username" => $username
            ])
            ->one();
        if (!count($dbUser)) {
            return null;
        }
        return new static($dbUser);
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {
        return $this->password === $password;
    }

    /**
     * Получить роль пользователя по id
     * @param $uid
     * @return string
     */
    public static function getUserRole($uid){
        $command = Yii::$app->db->createCommand("SELECT role FROM users WHERE id = :uid");
        $command->bindParam(":uid", $uid);
        $role = $command->queryOne();
        return $role['role'];
    }

    public static function getUsername($uid){
        $command = Yii::$app->db->createCommand("SELECT firstname, lastname FROM users WHERE id = :uid");
        $command->bindParam(":uid", $uid);

        $username = "";
        $dataReader = $command->query();
        while (($row = $dataReader->read()) !== false) {
            $username = $row['firstname'].' '.$row['lastname'];
        }

        return $username;
    }

    public static function getUserList(){
        $command = Yii::$app->db->createCommand("SELECT * FROM users");
        $dataReader = $command->query();
        $userList = [];
        while (($row = $dataReader->read()) !== false) {
            $userList[] = $row;
        }

        return $userList;
    }

    public function deleteUser($uid){
        $command = Yii::$app->db->createCommand("DELETE FROM USERS WHERE id = :uid");
        $command->bindParam(":uid",$uid);
        $command->execute();
        return true;
    }

    public function addUser($username,$password,$firstname,$lastname,$isadmin = false, $sendmail){
        //TODO sendmail
        $command = Yii::$app->db->createCommand("INSERT INTO users(active,username,password,accessToken,authKey,firstname,lastname,role)
        VALUES(1,:username,:password,:password,:password,:firstname,:lastname,:role);");
        $isadmin == 1 ? $role = 'A': $role='U';

        $command->bindParam(":username",$username);
        $command->bindParam(":password",$password);
        $command->bindParam(":firstname",$firstname);
        $command->bindParam(":lastname",$lastname);
        $command->bindParam(":role",$role);
        $command->execute();
        return true;
    }

}