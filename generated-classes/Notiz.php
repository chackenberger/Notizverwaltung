<?php

use Base\Notiz as BaseNotiz;

/**
 * Skeleton subclass for representing a row from the 'notiz' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Notiz extends BaseNotiz
{

    /**
     * @param Person $besitzer Besitzer der Notiz
     * @param string $betreff Betreff der Notiz
     * @param string $text Text der Notiz
     */
    public function __construct(Person $besitzer, $betreff, $text) {
        parent::__construct();
        if(is_string($betreff) && is_string($text)) {
            $this->setBesitzer($besitzer);
            $this->setBetreff($betreff);
            $this->setText($text);
        }else {
            throw new InvalidArgumentException("betreff und text muessen strings sein");
        }
    }

    /**
     * @see parent::setBetreff()
     */
    public function setBetreff($betreff) {
        if(is_string($betreff)) {
            if(strlen($betreff) <= 100) {
                parent::setBetreff($betreff);
            }else {
                throw new InvalidArgumentException("betreff darf max. 100 Zeichen lang sein");
            }
        }else {
            throw new InvalidArgumentException("betreff muss ein string sein");
        }
    }
}
