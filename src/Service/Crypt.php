<?php

namespace App\Service;


class Crypt
{


    public function checkAuthEncodeData(string $name, string $result, string $hash) : string
    {
        $string = $name . substr($name . $result, 2, 5) . substr($hash, 3, 8);

        return substr(hash('sha512', $string), 15, 33);

    }

}