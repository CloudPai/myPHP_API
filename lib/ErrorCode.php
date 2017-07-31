<?php
/**
 * Created by PhpStorm.
 * User: Liupai
 * Date: 2017/7/30
 * Time: 下午2:08
 */

class ErrorCode
{
    const USERNAME_EXITS = 1;//用户名已存在
    const PASSWORD_CANNOT_EMPTY = 2;//密码不能为空
    const USERNAME_CANNOT_EMPTY = 3;//用户名不能为空
    const REGISTER_FAIL = 4;//注册失败
    const USERNAME_OR_PASSWORD_INVALID = 5;//用户名或密码错误
    const ARTICLE_TITLE_CANNOT_EMPTY = 6;//文章标题不能为空
    const ARTICLE_CONTENT_CANNOT_EMPTY = 7;//文章内容不能为空
    const ARTICLE_CREATE_FAIL = 8;//发表文章失败
    const ARTICLE_ID_CANNOT_EMPTY = 9;//文章ID不能为空
    const ARTICLE_NOT_FOUND = 10;//文章不存在
    const PERMISSION_DENIED = 11;//您无权编辑该文章
    const ARTICLE_EDIT_FAIL = 12;//文章编辑失败
    const ARTICLE_DELETE_FAIL = 13;//文章删除失败
    const PAGE_SIZE_TOO_BIG = 14;//分页大小过大
    const SERVER_INTERNAL_ERROR = 15;//服务器内部错误

}