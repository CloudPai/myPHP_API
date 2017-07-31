<?php
/**
 * Created by PhpStorm.
 * User: Liupai
 * Date: 2017/7/30
 * Time: 下午12:43
 */
require_once __DIR__.'/ErrorCode.php';

class User
{
    /**
     * 数据库连接句柄
     * @var
     */


    private $_db;

    /**
     * 构造方法
     * User constructor
     * @param PDO $_db PDO连接句柄
     */
    public function __construct($_db)
    {

        $this ->_db = $_db;

    }

    /**
     *用户登录
     * @param $username
     * @param $password
     */

    public function login($username, $password)
    {
        if(empty($username)){
            throw new Exception('用户名不能为空',ErrorCode::USERNAME_CANNOT_EMPTY);

        }
        if(empty($password)){
            throw new Exception('密码不能为空',ErrorCode::PASSWORD_CANNOT_EMPTY);
        }
        $sql = 'SELECT * FROM `user` WHERE `username` = :username AND `password`=:password';
        $password = $this ->_md5($password);
//        print_r([
//            'username' =>$username,
//            'password'=>$password,
//        ]);
//        exit(0);
        $stmt = $this->_db->prepare($sql);
        $stmt ->bindParam(':username',$username);
        $stmt ->bindParam(':password',$password);
        if(!$stmt ->execute()){
            throw new Exception('服务器内部错误',ErrorCode::SERVER_INTERNAL_ERROR);


        }
        $user = $stmt ->fetch(PDO::FETCH_ASSOC);
//        var_dump($user);exit();
        if(empty($user)){
            throw new Exception('用户名或密码错误',ErrorCode::USERNAME_OR_PASSWORD_INVALID);
        }
        unset($user['password']);
        return $user;


    }

    /**
     *用户注册
     * @param $username
     * @param $password
     */

    public function register($username,$password)
    {
        if(empty($username)){
            throw new Exception('用户名不能为空',ErrorCode::USERNAME_CANNOT_EMPTY);

        }
        if(empty($password)){
            throw new Exception('密码不能为空',ErrorCode::PASSWORD_CANNOT_EMPTY);
        }
        if($this -> _isUernameExists($username)){
            throw new Exception('用户名已存在',ErrorCode::USERNAME_EXITS);
        }
        //写入数据库
        $sql = 'INSERT INTO `user`(`username`,`password`,`createdAt`)VALUES (:username,:password,:createdAt)';
        $createdAt = time();
        $password = $this->_md5($password);
        $stmt = $this ->_db->prepare($sql);
        $stmt ->bindParam(':username',$username);
        $stmt ->bindParam(':password',$password);
        $stmt ->bindParam(':createdAt',$createdAt);
        if(!$stmt->execute()){
            throw new Exception('注册失败',ErrorCode::REGISTER_FAIL);
        }
        return [
            'userId' => $this -> _db ->lastInsertId(),
            'username' =>$username,
            'createdAt'=>$createdAt,

        ];

    }

    /**
     * MD5加密
     * @param $string
     * @param string $key
     * @return string
     */
    private function _md5($string,$key = 'imooc')
    {
        return md5($string,$key);
    }

    /**
     * 检测用户名是否存在
     * @param $username
     * @return bool
     */

    private function _isUernameExists($username)
    {
        $exists = false;
        $sql = 'SELECT * FROM `user` WHERE `username` =:username';/*用``括起user,应为user在mysql中是关键字*/
        $stmt = $this ->_db->prepare($sql);
        $stmt ->bindParam(':username',$username);
        $stmt ->execute();
        $result = $stmt ->fetch(PDO::FETCH_ASSOC);
        return !empty($result);

    }


}