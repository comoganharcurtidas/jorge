<?php

namespace App\Helpers;

class EnvWriter
{

    protected $environmentFilePath;

    public function __construct()
    {
        $this->environmentFilePath = \App::environmentFilePath();
    }

    public function writeNewEnvironmentFileWith($key, $value)
    {
        file_put_contents($this->environmentFilePath, preg_replace(
            "/.*" . $key . ".*/",
            $key . '=' . $value,
            file_get_contents($this->environmentFilePath)
        ));
    }

}
