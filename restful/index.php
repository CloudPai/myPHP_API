<?php
/**
 * Created by PhpStorm.
 * User: Liupai
 * Date: 2017/7/30
 * Time: 下午8:05
 */

require __DIR__.'/../lib/User.php';
require __DIR__.'/../lib/Article.php';

$pdo = require __DIR__.'/../lib/db.php';


class Restful
{

    /**
     * @var User
     */
    private $_user;


    /**
     * @var Article
     */
    private $_article;


    /**
     * 请求方法
     * @var String
     *
     */
    private $_requestMethod;


    /**
     * 请求资源名称
     * @var String
     */
    private $_resourceName;


    /**
     * 请求的资源ID
     * @var String
     */
    private $_id;


    /**
     * x允许请求的资源列表
     * @var array
     */
    private  $_allowResource = ['users','articles'];


    /**
     * 允许请求的HTTP方法
     * @var array
     */
    private  $_allowRequestMethods = ['GET','POST','DELETE','OPTIONS'];


    /**
     * 常用状态码
     * @var array
     */
    private $_statusCodes = [
        200 => 'ok',
        204 => 'No Content',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Server Internal Error'



    ];




    /**
     * Restful constructor.
     * @param User $_user
     * @param Article $_article
     */
    public function __construct(User $_user, Article $_article)
    {
        $this->_user = $_user;
        $this->_article = $_article;
    }

    /**
     *
     */
    public function run()
    {
        try{
            $this -> setupRequestMethod();
            $this -> setupResource();
            if ($this ->_resourceName=='users'){
                return $this ->_json($this->handleUser());
            } else {

                return $this ->_json($this->_handleArticle());
            } 
        } catch (Exception $e){
            $this -> _json(['error'=>$e->getMessage()],$e->getCode());
        }


    }
    /**
     * 初始化请求方法
     */
    private function setupRequestMethod()
    {
        $this->_requestMethod = $_SERVER['REQUEST_METHOD'];
        if (!in_array($this->_requestMethod, $this->_allowRequestMethods)){
                throw new Exception('请求方法不被允许',405);

        }
    }

    /**
     * 初始化请求资源
     */
    private function setupResource()
    {
        $path = $_SERVER['PATH_INFO'];
        $params = explode('/',$path);
        $this ->_resourceName = $params[1];
        if(!in_array($this->_resourceName,$this->_allowResource)){
            throw new Exception('请求资源不被允许',400);
        }
        if(!empty($params[2])){
            $this  -> _id = $params[2];
        }

    }


    /**
     * 输出JSON
     * @param $array
     */
    private function _json($array, $code = 0)
    {

        if($code > 0 && $code != 200 && $code!=204){
            header("HTTP/1.1 " . $code . " " . $this->_statusCodes[$code]);
        }
        header('Content-Type:application/json;charset=utf-8');
        echo json_encode($array,JSON_UNESCAPED_UNICODE);
        exit();

    }

    /**
     * 请求文章资源
     */
    private function _handleArticle()
    {
        switch ($this->_requestMethod){
            case 'POST':
                return $this->_handleArticleCreate();
            case 'PUT':
                return $this->_handleArticleEdit();
            case 'DELETE':
                return $this->_handleArticleDelete();
            case 'GET':
                if(empty($this->_id)){
                    return $this->_handleArticleList();

                }else{
                    return $this->_handleArticleView();
                }

            default:
                throw new Exception('请求方法不被允许',405);
        }
    }

    /**
     * 请求用户
     * @return array
     * @throws Exception
     */
    private function handleUser()
    {
        if($this->_requestMethod !='POST'){
            throw new Exception('请求方法不被允许',405);
        }

        $body = $this ->_getBodyParams();
        if(empty($body['username'])){
            throw new Exception('用户名不能为空',400);
        }
        if(empty($body['password'])){
            throw new Exception('密码不能为空',400);
        }
        $data =  $this->_user->register($body['username'],$body['password']);
        var_dump($data);
        exit(0);

    }

    /**
     * 获取请求参数
     * @return mixed
     * @throws Exception
     */
    private function _getBodyParams()
    {
        $raw = file_get_contents('php://input');
        if(empty($raw)){
            throw new Exception('请求参数错误',400);

        }
        return json_decode($raw,true);



    }

    /**
     * 创建文章
     * @return array
     * @throws Exception
     */
    private function _handleArticleCreate()
    {
        $body = $this->_getBodyParams();
        if(empty($body['title'])){
            throw new Exception('文章标题不能为空',400);
        }
        if(empty($body['content'])){
            throw new Exception('文章内容不能为空',400);
        }
        $user = $this ->_userLogin($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
        try {
            $article = $this -> article->create($body['title'],$body['content']);
            return $article;
        } catch (Exception $e){
            if(!in_array($e->getCode(),
            [
                ErrorCode::ARTICLE_TITLE_CANNOT_EMPTY,
                ErrorCode::ARTICLE_CONTENT_CANNOT_EMPTY,
            ])
            ){

                throw new Exception($e->getMessage(),400);
            }
            throw new Exception($e->getMessage(),500);
        }
}

    }

    private function _handleArticleEdit()
    {
    }

    private function _handleArticleDelete()
    {
    }

    private function _handleArticleList()
    {
    }
    private function _handleArticleView()
    {
    }

    /**
     * 用户登录
     * @param $PHP_AUTH_USER
     * @param $PHP_AUTH_PW
     * @return
     * @throws Exception
     */
    private function _userLogin($PHP_AUTH_USER, $PHP_AUTH_PW)
    {

        try{
            return $this->user->login($PHP_AUTH_USER,$PHP_AUTH_PW);

        }catch(Exception $e){
            if(in_array($e->getCode(),
                [
                    ErrorCode::USERNAME_CANNOT_EMPTY,
                    ErrorCode::PASSWORD_CANNOT_EMPTY,
                    ErrorCode::USERNAME_OR_PASSWORD_INVALID
                 ])){
                throw new Exception($e->getMessage(),400);
            }
            throw new Exception($e->getMessage(),500);




    }


}

$article = new Article($pdo);
$user = new User($pdo);

$restful = new Restful($user,$article);
$restful ->run();