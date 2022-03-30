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

class Lr_proinfo extends Module
{
    private $html; //Affiche message
    private $postErrors = array(); //Enregistre toutes les erreurs
    private $postSuccess = array(); //Enregistre toutes les succès

    public function __construct()
    {
        $this->name = 'lr_proinfo';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'Lise ';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('pro info by lise');
        $this->description = $this->l('This module give information for customers');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include(dirname(_FILE_) . '/sql/install.php');
        Configuration::updateValue('LR_PROINFO_ID_PAGE', 1);
        Configuration::updateValue('LR_PROINFO_ADMIN_EMAIL', 'liserochat@live.fr');
        Configuration::updateValue('LR_PROINFO_SEND_MAIL', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionObjectCustomerDeleteAfter');
    }

    public function uninstall()
    {
        include(dirname(_FILE_) . '/sql/uninstall.php');
        Configuration::deleteByName('LR_PROINFO_ID_PAGE');
        Configuration::deleteByName('LR_PROINFO_ADMIN_EMAIL');
        Configuration::deleteByName('LR_PROINFO_SEND_MAIL');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitLr_proinfoModule')) == true) {
            $this->postValidation();

            if (!count($this->postErrors)) {
                $this->postProcess();
            }
        }

        if (count($this->postErrors)) {
            foreach ($this->postErrors as $error) {
                $this->html .= $this->displayError($error);
            }
        }

        if (count($this->postSuccess)) {
            foreach ($this->postSuccess as $success) {
                $this->html .= $this->displayConfirmation($success);
            }
        }

        $this->context->smarty->assign(array(
            'module_dir' => $this->_path,
            'version' => $this->version,
        ));
        $this->html .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/header.tpl');
        $this->html .= $this->renderForm();
        $this->html .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/footer.tpl');
        return $this->html;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLr_proinfoModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $cms = CMS::getCMSPages(
            (int)Context::getContext()->language->id,
            null,
            true,
            (int)Context::getContext()->shop->id
        );

        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-smile',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',

                        'label' => $this->l('send mail to administrator'),
                        'name' => 'LR_PROINFO_SEND_MAIL',
                        'is_bool' => true,
                        'desc' => $this->l('Use this to send an email'),
                        'hint' => $this->l('Set not to disable mail sending'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'required' => true,
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'hint' => $this->l('Enter a valid email address'),
                        'name' => 'LR_PROINFO_ADMIN_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => false,
                        'label' => $this->l('Select Explination Pages'),
                        'desc' => $this->l('Choose one page'),
                        'hint' => $this->l('Select the page'),
                        'name' => 'LR_PROINFO_ID_PAGE',
                        'class' => 'chosen',
                        'options' => array(
                            'query' => $cms,
                            'id' => 'id_cms',
                            'name' => 'meta_title'
                        )

                    )
                ),

                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'LR_PROINFO_SEND_MAIL' => Configuration::get('LR_PROINFO_SEND_MAIL'),
            'LR_PROINFO_ADMIN_EMAIL' => Configuration::get('LR_PROINFO_ADMIN_EMAIL'),
            'LR_PROINFO_ID_PAGE' => Configuration::get('LR_PROINFO_ID_PAGE'),
        );
    }

    protected function postValidation()
    {
        if (
            !Tools::getValue('LR_PROINFO_ADMIN_EMAIL')
            || !Validate::isEmail(Tools::getValue('LR_PROINFO_ADMIN_EMAIL'))
        ) {
            $this->postErrors[] = $this->l(
                'Error : The field "MAIL" is not valid'
            );
        }

        if (
            Tools::getValue('LR_PROINFO_SEND_MAIL')
            && !Validate::isBool(Tools::getValue('LR_PROINFO_SEND_MAIL'))
        ) {
            $this->postErrors[] = $this->l(
                'Error : The field enable mail is not valid'
            );
        }

        if (
            Tools::getValue('LR_PROINFO_ID_PAGE')
            && !Validate::isInt(Tools::getValue('LR_PROINFO_ID_PAGE'))
        ) {
            $this->postErrors[] = $this->l(
                'Error : The field cms is not valid'
            );
        }
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    public function hookActionObjectCustomerDeleteAfter($params)
    {
        //Db::getInstance()->delete('lr_proinfo', 'id_customer', $params['object']->id);
        $obj = ProInfo::getByIdCustomer((int)$params['object']->id);
        if (Validate::isLoadedObject($obj)) {
            $obj->delete();
        }
    }
}
