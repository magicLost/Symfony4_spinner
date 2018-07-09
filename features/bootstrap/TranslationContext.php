<?php

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;

class TranslationContext extends  RawMinkContext implements Context
{
    /**
     * @When I click on :locale_name locale button
     */
    public function iClickOnLocaleButton($locale_name)
    {
        $locale = ($locale_name === 'ru') ? 'en' : 'ru';

        $page = $this->getSession()->getPage();

        $locale_link = $page->findLink($locale);

        $locale_link_for_click = $page->findLink($locale_name);

        if($locale_link === null || $locale_link_for_click === null)
            throw new PendingException("No locale link");

        $locale_link->click();

        $locale_link_for_click->click();
    }
}