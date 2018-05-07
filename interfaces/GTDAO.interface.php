<?php

interface GTDAO{

    public static function __form($controller, $params = array());

    public static function createGT($array, $db);

    public static function updateGT($array, $db);

    public static function getAll($array, $db);

    public static function getOne($array, $db);

    public static function getByCode($array, $db);

    public static function getTableAttrs();

    public static function getStructuredInfoAttrs();

}