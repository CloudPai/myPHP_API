<?php
/**
 * 连接数据库并返回数据库链接句柄
 * Created by PhpStorm.
 * User: Liupai
 * Date: 2017/7/30
 * Time: 下午12:43
 */
$pdo = new PDO('mysql:host=localhost;dbname=mydb','root','');
return $pdo;