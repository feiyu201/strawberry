<?php

namespace addons\qiniu_storage\common\library\storage\engine;

use think\Exception;
use think\Request;

/**
 * 存储引擎抽象类
 * Class server
 * @package addons\qiniu_storage\common\library\storage\engine
 */
abstract class Server
{
    protected $file;
    protected $error;
    protected $fileName;
    protected $fileInfo;
    protected $fileOriginalExtension;
    protected $fileOriginalName;
    protected $fileGetOriginalMime;
    protected $fileGetRealPath;

    /**
     * 构造函数
     * Server constructor.
     * @throws Exception
     */
    protected function __construct()
    {
        // 接收上传的文件
//        $this->file = Request::instance()->file('iFile'); // TP6以上废弃
        $this->file = request()->file('file');

        if (empty($this->file)) {
            throw new Exception('未找到上传文件的信息');
        }

        // 获取原始文件名称
        $this->fileOriginalName = $this->file->getOriginalName();

        // 扩展名
        $this->fileOriginalExtension = $this->file->getOriginalExtension();

        // 生成保存文件名
        $this->fileName = date('YmdHis') . substr(md5($this->fileOriginalName), 0, 5) . '.' . $this->fileOriginalExtension;

        //获取获取上传文件类型信息：image/jpeg
        $this->fileGetOriginalMime = $this->file->getOriginalMime();

        //获取文件的哈希散列值
        $this->fileGetHash = $this->file->hash();

        //获取文件的sha1值
        $this->fileGetSha = $this->file->sha1();

        //文件信息
        $this->fileInfo = $this->file;

        //文件临时路径
        $this->fileGetRealPath = $this->file->getRealPath();
    }

    /**
     * 文件上传
     * @return mixed
     */
    abstract protected function upload();

    /**
     * 返回上传后文件路径
     * @return mixed
     */
    abstract public function getFileName();

    /**
     * 返回文件信息
     * @return mixed
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * 返回文件组装文件信息
     * @return mixed
     */
    public function getFileInfoArray()
    {
        return [
            'fileOriginalName'      => $this->fileOriginalName,
            'fileName'              => $this->fileName,
            'fileOriginalExtension' => $this->fileOriginalExtension,
            'fileGetOriginalMime'   => $this->fileGetOriginalMime,
            'fileGetHash'           => $this->fileGetHash,
            'fileGetSha'            => $this->fileGetSha,
            'fileGetRealPath'       => $this->fileGetRealPath,
        ];
    }

    /**
     * 返回错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 生成保存文件名
     */
    private function buildSaveName()
    {
        // 要上传图片的本地路径
        $realPath = $this->file->getRealPath();
        // 扩展名
        $ext = pathinfo($this->fileOriginalExtension, PATHINFO_EXTENSION);
        // 自动生成文件名
        return date('YmdHis') . substr(md5($realPath), 0, 5)
            . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . '.' . $ext;
    }

}
