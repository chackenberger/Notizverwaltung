<?php

use Base\Projekt as BaseProjekt;

/**
 * Skeleton subclass for representing a row from the 'projekt' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Projekt extends BaseProjekt
{

    function __construct($teilnehmer) {
        parent::__construct();
        $isarray = is_array($teilnehmer);
        if($teilnehmer === null || (!$isarray && !($teilnehmer instanceof Person)))
            throw new InvalidArgumentException("teilnehmer must be a Person or an array of Person");
        if($isarray) {
            foreach ($teilnehmer as $t) {
                $this->addPerson($t);
            }
        }else
            $this->addPerson($teilnehmer);
    }
}
