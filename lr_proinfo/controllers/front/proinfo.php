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

        $this->obj = ProInfo::getByIdCustomer((int)Context::getContext()->customer->id);

      
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        // $vars = Tools::getAllValues();
        // die(var_dump($vars));

        // if (((bool)Tools::isSubmit('lr_validate')) == true) {
        //     $this->postProcess();
        // }

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

        $cms = new CMS(
            (int)Configuration::get('LR_PROINFO_ID_PAGE'),
            (int)Context::getContext()->language->id,
            (int)Context::getContext()->shop->id
        );

        $link = new Link();

        $cmsLink = $link->getCMSLink(
            $cms,
            null,
            true,
            (int)Context::getContext()->language->id,
            (int)Context::getContext()->shop->id
        );
        
        if (Validate::isLoadedObject($this->obj)) {

            $company = $this->obj->company;
            $manager = $this->obj->manager;
            $siret = $this->obj->siret;
            $vat_number = $this->obj->vat_number;
            $bank = $this->obj->bank;
            $website = $this->obj->website;
            $comment = $this->obj->comment;
            $id_address = $this->obj->id_address;
            $bic = $this->obj->bic;

        } else {

            $company = Tools::getValue('lr_company');
            $manager = Tools::getValue('lr_manager');
            $siret = Tools::getValue('lr_siret');
            $vat_number = Tools::getValue('lr_vat_number');
            $bank = Tools::getValue('lr_bank');
            $website = Tools::getValue('lr_website');
            $comment = Tools::getValue('lr_comment');
            $id_address = Tools::getValue('id_address');
            $bic = Tools::getValue('lr_bic');

        }

        $this->context->smarty->assign(array(
            'addresses' => $this->context->customer->getAddresses($this->context->language->id),
            'lr_company' => $company,
            'lr_manager' => $manager,
            'lr_siret' => $siret,
            'lr_vat_number' => $vat_number,
            'lr_bank' => $bank,
            'lr_bic' => $bic,
            'lr_website' => $website,
            'lr_comment' => $comment,
            'lr_id_address' => $id_address,
            'cmsLink' => $cmsLink,
            'LR_PROINFO_SIRET' => (bool)Configuration::get('LR_PROINFO_SIRET'),
            'LR_PROINFO_VAT_NUMBER' => (bool)Configuration::get('LR_PROINFO_VAT_NUMBER'), 
            'LR_PROINFO_BANK_NAME' => (bool)Configuration::get('LR_PROINFO_BANK_NAME'),
            'LR_PROINFO_IBAN' => (bool)Configuration::get('LR_PROINFO_IBAN'),
            'LR_PROINFO_IBAN' => (bool)Configuration::get('LR_PROINFO_IBAN'),
            'LR_PROINFO_BIC' => (bool)Configuration::get('LR_PROINFO_BIC'),
            'LR_PROINFO_WEBSITE' => (bool)Configuration::get('LR_PROINFO_WEBSITE'),
            'LR_PROINFO_COMMENT' => (bool)Configuration::get('LR_PROINFO_COMMENT'),
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

                // Si l'objet proInfo du client est bien instancé dans init
                if (Validate::isLoadedObject($this->obj)) {
                    // donc on a juste à préciser que $proInfo c'est $this->obj
                    $proInfo = $this->obj;
                } else {
                    // Sinon on crée une nouvelle entrée
                    $proInfo = new ProInfo();
                }
                
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
