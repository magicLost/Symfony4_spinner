<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * This context class contains the definitions of the steps used by the demo 
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 * 
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext extends RawMinkContext implements Context
{


    public function __construct()
    {

    }

    /**
     * @Given wait :seconds seconds
     */
    public function waitSeconds($seconds)
    {
        $this->getSession()->wait($seconds * 1000, false);
    }


}
