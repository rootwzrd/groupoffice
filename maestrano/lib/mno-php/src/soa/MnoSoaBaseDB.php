<?php

/**
 * Mno DB Map Interface
 */
class MnoSoaBaseDB {
    
    public static function initialize($db=null)
    {
        error_log("initialized");
    }

    public static function addIdMapEntry($local_id, $local_entity_name, $mno_id, $mno_entity_name) 
    {
        throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoDB class!');
    }
    
    public static function getMnoIdByLocalId($local_id, $local_entity_name, $mno_entity_name)
    {
        throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoDB class!');
    }
    
    public static function getLocalIdByMnoId($mno_id, $mno_entity_name, $local_entity_name)
    {
        throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoDB class!');
    }
    
    public static function deleteIdMapEntry($local_id, $local_entity_name)
    {
        throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoDB class!');
    }
}

?>