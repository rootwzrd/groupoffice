<?php

/**
 * Maestrano map table functions
 *
 * @author root
 */

class MnoSoaEntity extends MnoSoaBaseEntity {    
    public function getUpdates($timestamp)
    {
        MnoSoaLogger::info(__FUNCTION__ .  " start getUpdates (timestamp=" . $timestamp . ")");
        $msg = $this->callMaestrano("GET", "updates" . '/' . $timestamp);
        if (empty($msg)) { return false; }
        MnoSoaLogger::debug(__FUNCTION__ .  " after maestrano call");
        if (!empty($msg->organizations) && class_exists('MnoSoaOrganization')) {
            MnoSoaLogger::debug(__FUNCTION__ .  " has organizations");
            foreach ($msg->organizations as $organization) {
                MnoSoaLogger::debug(__FUNCTION__ .  " org id = " . $organization->id);
                try {
                    $mno_org = new MnoSoaOrganization();
                    $mno_org->receive($organization);
                } catch (Exception $e) {
                }
            }
        }
        if (!empty($msg->persons) && class_exists('MnoSoaPerson')) {
            MnoSoaLogger::debug(__FUNCTION__ . " has persons");
            foreach ($msg->persons as $person) {
                MnoSoaLogger::debug(__FUNCTION__ .  " person id = " . $person->id);
                try {
                    $mno_person = new MnoSoaPerson();
                    $mno_person->receive($person);
                } catch (Exception $e) {
                }
            }
        }
        
        return true;
        MnoSoaLogger::info(__FUNCTION__ .  " getUpdates successful (timestamp=" . $timestamp . ")");
    }
    
    public function process_notification($notification)
    {
        $notification_entity = strtoupper(trim($notification->entity));

        MnoSoaLogger::debug("Notification = ". json_encode($notification));

        switch ($notification_entity) {
            case "ORGANIZATIONS":
                if (class_exists('MnoSoaOrganization')) {
                    $mno_org = new MnoSoaOrganization();		
                    $mno_org->receiveNotification($notification);
                }
                break;
            case "PERSONS":
                if (class_exists('MnoSoaPerson')) {
                    $mno_person = new MnoSoaPerson();		
                    $mno_person->receiveNotification($notification);
                }
                break;
        }
    }
}
