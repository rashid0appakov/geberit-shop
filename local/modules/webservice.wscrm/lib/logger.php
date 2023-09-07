<?php

namespace Webservice\Wscrm;

use Bitrix\Main\IO;

class Logger
{
    protected $file;
    protected $filesize = 10 * 1024 * 1024;

    public function __construct($filename)
    {
        $this->file = new IO\File($filename);

        if ($this->file->isExists()) {
            $this->checkSize();
        }
    }

    public function log($message = null)
    {
        if (! is_null($message) && ! empty($message)) {
            $this->file->putContents(
                $this->prepareMessage($message),
                IO\File::APPEND
            );
        }
    }

    protected function checkSize()
    {
        if ($this->file->getSize() > $this->filesize) {
            $this->file->delete();
        }
    }

    private function prepareMessage($message)
    {
        return '[--- ' . date('d.m.Y H:i:s') . ' ----] LOG: ' . $message . "\n"; // with double quotes =/
    }
}