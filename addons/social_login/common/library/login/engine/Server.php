<?php

namespace addons\social_login\common\library\login\engine;


abstract class Server
{
    protected $error;

    /**
     * 返回错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

}
