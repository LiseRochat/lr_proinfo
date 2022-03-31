<?php

/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2022 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'lr_proinfo/models/ProInfo.php';

class Lr_proinfoproinfoModuleFrontController extends ModuleFrontController
{
    private $html; //Affiche message
    private $postErrors = array(); //Enregistre toutes les erreurs
    private $postSuccess = array(); //Enregistre toutes les succès

    public function init()
    {
        if ((bool)Context::getContext()->customer->isLogged() === false) {
            $link = new Link();
            $authLink = $link->getPageLink('authentication');
            Tools::redirect($authLink);
        }

        $obj = ProInfo::getByIdCustomer((int)Context::getContext()->customer->id);
        if (Validate::isLoadedObject($obj) && count($this->postSuccess) <= 0) {
            Tools::redirect('index.php');
        }

        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        // $vars = Tools::getAllValues();
        // die(var_dump($vars));

        if (((bool)Tools::isSubmit('lr_validate')) == true) {
            $this->postProcess();
        }

        if (count($this->postErrors)) {
            $this->context->smarty->assign(array(
                'errors' => $this->postErrors
            ));
        }

        if (count($this->postSuccess)) {
            $this->context->smarty->assign(array(
                'successes' => $this->postSuccess
            ));
        }

        $address = Address::getFirstCustomerAddressId((int)$this->context->customer->id);
        //die(var_dump($address));
        if ($address <= 0) {
            $this->context->smarty->assign(array(
                'noaddress' => true
            ));
        }

        $this->context->smarty->assign(array(
            'addresses' => $this->context->customer->getAddresses($this->context->language->id),
            'lr_company' => Tools::getValue('lr_company'),
            'lr_manager' => Tools::getValue('lr_manager'),
            'lr_siret' => Tools::getValue('lr_siret'),
            'lr_vat_number' => Tools::getValue('lr_vat_number'),
            'lr_bank' => Tools::getValue('lr_bank'),
            'lr_bic' => Tools::getValue('lr_bic'),
            'lr_website' => Tools::getValue('lr_website'),
            'lr_comment' => Tools::getValue('lr_comment')
        ));

        $this->setTemplate('module:lr_proinfo/views/templates/front/proinfo.tpl');
    }

    public function postProcess()
    {
        if (((bool)Tools::isSubmit('lr_validate')) == true) {
            //die(var_dump(Tools::getAllValues()));
            if (
                !Tools::getValue('lr_idaddress')
                || !Validate::isInt(Tools::getValue('lr_idaddress'))
            ) {
                $this->postErrors[] = $this->l(
                    'Error : The field address is not valid'
                );
            }

            if (
                !Tools::getValue('lr_company')
                || !Validate::isGenericName(Tools::getValue('lr_company'))
            ) {
                $this->postErrors[] = $this->l(
                    'Error : The field company is not valid'
                );
            }

            if (
                !Tools::getValue('lr_manager')
                || !Validate::isCustomerName(Tools::getValue('lr_manager'))
            ) {
                $this->postErrors[] = $this->l(
                    'Error : The field manager is not valid'
                );
            }

            if (
                !Tools::getValue('lr_siret')
                || !Validate::isGenericName(Tools::getValue('lr_siret'))
            ) {
                $this->postErrors[] = $this->l(
                    'Error : The field siret is not valid'
                );
            }

            if (
                !Tools::getValue('lr_vat_number')
                || !Validate::isGenericName(Tools::getValue('lr_vat_number'))
            ) {
                $this->postErrors[] = $this->l(
                    'Error : The field vat number is not valid'
                );
            }

            if (
                Tools::getValue('lr_bank')
                && !Validate::isGenericName(Tools::getValue('lr_bank'))
            ) {
                $this->postErrors[] = $this->l(
                    'Error : The field bank is not valid'
                );
            }

            if (
                Tools::getValue('lr_iban')
                && !Validate::isGenericName(Tools::getValue('lr_iban'))
            ) {
                $this->postErrors[] = $this->l(
                    'Error : The field iban is not valid'
                );
            }

            if (
                Tools::getValue('lr_bic')
                && !Validate::isGenericName(Tools::getValue('lr_bic'))
            ) {
                $this->postErrors[] = $this->l(
                    'Error : The field bic is not valid'
                );
            }

            if (
                Tools::getValue('lr_website')
                && !Validate::isUrl(Tools::getValue('lr_website'))
            ) {
                $this->postErrors[] = $this->l(
                    'Error : The field website is not valid'
                );
            }

            if (
                Tools::getValue('lr_comment')
                && !Validate::isCleanHtml(Tools::getValue('lr_comment'))
            ) {
                $this->postErrors[] = $this->l(
                    'Error : The field website is not valid'
                );
            }

            if (!count($this->postErrors)) {

                $proInfo = new ProInfo();
                $proInfo->id_customer = (int)$this->context->customer->id;
                $proInfo->id_address = (int)Tools::getValue('lr_idaddress');
                $proInfo->company = (string)Tools::getValue('lr_company');
                $proInfo->manager = (string)Tools::getValue('lr_manager');
                $proInfo->siret = (string)Tools::getValue('lr_siret');
                $proInfo->vat_number = (string)Tools::getValue('lr_vat_number');
                $proInfo->bank = (string)Tools::getValue('lr_bank');
                $proInfo->iban = (string)Tools::getValue('lr_iban');
                $proInfo->bic = (string)Tools::getValue('lr_bic');
                $proInfo->website = (string)Tools::getValue('lr_website');
                $proInfo->comment = (string)Tools::getValue('lr_comment');

                if ($proInfo->save()) {
                    $this->postSuccess[] = $this->l('Success !!!');
                } else {
                    $this->postErrors[] = $this->l('An error has occured');
                }
            }
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = array(
            'title' => $this->l('Pro Account Creation'),
            'url' => $this->context->link->getModuleLink(
                'lr_proinfo',
                'Lr_proinfoproinfoModuleFrontController'
            ),
        );
        return $breadcrumb;
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        if ((bool)Context::getContext()->customer->isLogged() === true) {
            $page['body_classes']['is-logged'] = true;
        }

        $page['body_classes']['page-pro-info'] = true;

        return $page;
    }

    public function getCanonicalUrl()
    {
        return $this->context->link->getModuleLink('lr_proinfo', 'proinfo');
    }
}
