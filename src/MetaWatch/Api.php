<?php
/**
 * Copyright (c) 2019.
 */

namespace MetaWatch;


class Api
{
    public static function getNodes(){
        return json_decode(file_get_contents('https://api.metawat.ch/nodes.json'))->data;
    }
}