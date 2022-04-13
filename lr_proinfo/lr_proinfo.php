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

class Lr_proinfo extends Module
{
    private $html; //Affiche message
    private $postErrors = array(); //Enregistre toutes les erreurs
    private $postSuccess = array(); //Enregistre toutes les succès


    public function __construct()
    {
        $this->name = 'lr_proinfo';
        $this->tab = 'front_office_features';
        $this->version = '3.0.1';
        $this->author = 'Lise ';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Pro Info by lise');
        $this->description = $this->l('This module allows customers to create a pro account');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');
        Configuration::updateValue('LR_PROINFO_ID_PAGE', 1);
        Configuration::updateValue('LR_PROINFO_ADMIN_EMAIL', 'liserochat@live.fr');
        Configuration::updateValue('LR_PROINFO_SEND_MAIL', false);
        Configuration::updateValue('LR_PROINFO_SIRET', false);
        Configuration::updateValue('LR_PROINFO_VAT_NUMBER', false);
        Configuration::updateValue('LR_PROINFO_BANK_NAME', false);
        Configuration::updateValue('LR_PROINFO_IBAN', false);
        Configuration::updateValue('LR_PROINFO_BIC', false);
        Configuration::updateValue('LR_PROINFO_WEBSITE', false);
        Configuration::updateValue('LR_PROINFO_COMMENT', false);

        $this->installModuleTab(
            'AdminLr_Proinfo',
            'AdminParentCustomer',
            $this->l('Pro Infos')
        );

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionObjectCustomerDeleteAfter') &&
            $this->registerHook('displayTop') &&
            $this->registerHook('actionObjectProinfoAddAfter') &&
            $this->registerHook('displayAdminCustomers') &&
            $this->registerHook('displayCustomerAccount') &&
            $this->registerHook('actionObjectProinfoUpdateAfter');
    }

    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');
        Configuration::deleteByName('LR_PROINFO_ID_PAGE');
        Configuration::deleteByName('LR_PROINFO_ADMIN_EMAIL');
        Configuration::deleteByName('LR_PROINFO_SEND_MAIL');
        Configuration::deleteByName('LR_PROINFO_SIRET');
        Configuration::deleteByName('LR_PROINFO_VAT_NUMBER');
        Configuration::deleteByName('LR_PROINFO_BANK_NAME');
        Configuration::deleteByName('LR_PROINFO_IBAN');
        Configuration::deleteByName('LR_PROINFO_BIC');
        Configuration::deleteByName('LR_PROINFO_WEBSITE');
        Configuration::deleteByName('LR_PROINFO_COMMENT');

        $this->uninstallModuleTab('AdminLr_Proinfo');

        return parent::uninstall();
    }

    private function installModuleTab($tabClass, $parent, $tabName)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $tabClass;
        $tab->id_parent = (int)Tab::getIdFromClassName($parent);
        $tab->position = Tab::getNewLastPosition($tab->id_parent);
        $tab->module = $this->name;
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[(int)$lang['id_lang']] = $tabName;
        }
        return $tab->add();
    }

    private function uninstallModuleTab($tabClass)
    {
        $tab = new Tab((int)Tab::getIdFromClassName($tabClass));
        return $tab->delete();
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
            'lr_version' => $this->version,
        ));
        $this->html .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/header.tpl');
        $this->html .= $this->renderForm();
        $this->html .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/documentation.tpl');
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
                        'options' =>
                        array(
                            'query' => $cms,
                            'id' => 'id_cms',
                            'name' => 'meta_title'
                        )

                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('You need SIRET of company'),
                        'name' => 'LR_PROINFO_SIRET',
                        'is_bool' => true,
                        'desc' => $this->l('Use this if you want that customer pro inform his SIRET'),
                        'hint' => $this->l('Set not if you dont need SIRET of pro customer'),
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
                        'type' => 'switch',
                        'label' => $this->l('You need VAT Number of company'),
                        'name' => 'LR_PROINFO_VAT_NUMBER',
                        'is_bool' => true,
                        'desc' => $this->l('Use this if you want that customer pro inform his VAT number'),
                        'hint' => $this->l('Set not if you dont need VAT number of pro customer'),
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
                        'type' => 'switch',
                        'label' => $this->l('You need bank name of company'),
                        'name' => 'LR_PROINFO_BANK_NAME',
                        'is_bool' => true,
                        'desc' => $this->l('Use this if you want that customer pro inform bank name'),
                        'hint' => $this->l('Set not if you dont need bank name of pro customer'),
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
                        'type' => 'switch',
                        'label' => $this->l('You need iban of company'),
                        'name' => 'LR_PROINFO_IBAN',
                        'is_bool' => true,
                        'desc' => $this->l('Use this if you want that customer pro inform his iban'),
                        'hint' => $this->l('Set not if you dont need iban of pro customer'),
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
                        'type' => 'switch',
                        'label' => $this->l('You need bic number of company'),
                        'name' => 'LR_PROINFO_BIC',
                        'is_bool' => true,
                        'desc' => $this->l('Use this if you want that customer pro inform his bic'),
                        'hint' => $this->l('Set not if you dont need bic of pro customer'),
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
                        'type' => 'switch',
                        'label' => $this->l('You need website of company'),
                        'name' => 'LR_PROINFO_WEBSITE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this if you want that customer pro inform his website'),
                        'hint' => $this->l('Set not if you dont need website of pro customer'),
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
                        'type' => 'switch',
                        'label' => $this->l('You authorize pro customer to write additional information'),
                        'name' => 'LR_PROINFO_COMMENT',
                        'is_bool' => true,
                        'desc' => $this->l('Use this if you authorize pro customer write additional information'),
                        'hint' => $this->l('Set not if you dont authorize pro customer write additionnal information'),
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
            'LR_PROINFO_SIRET' => Configuration::get('LR_PROINFO_SIRET'),
            'LR_PROINFO_VAT_NUMBER' => Configuration::get('LR_PROINFO_VAT_NUMBER'), 
            'LR_PROINFO_BANK_NAME' => Configuration::get('LR_PROINFO_BANK_NAME'),
            'LR_PROINFO_IBAN' => Configuration::get('LR_PROINFO_IBAN'),
            'LR_PROINFO_IBAN' => Configuration::get('LR_PROINFO_IBAN'),
            'LR_PROINFO_BIC' => Configuration::get('LR_PROINFO_BIC'),
            'LR_PROINFO_WEBSITE' => Configuration::get('LR_PROINFO_WEBSITE'),
            'LR_PROINFO_COMMENT' => Configuration::get('LR_PROINFO_COMMENT'),
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
        if (
            Tools::getValue('LR_PROINFO_SIRET')
            && !Validate::isBool(Tools::getValue('LR_PROINFO_SIRET'))
        ) {
            $this->postErrors[] = $this->l(
                'Error : The field enable siret is not valid'
            );
        }
        if (
            Tools::getValue('LR_PROINFO_VAT_NUMBER')
            && !Validate::isBool(Tools::getValue('LR_PROINFO_VAT_NUMBER'))
        ) {
            $this->postErrors[] = $this->l(
                'Error : The field enable vat number is not valid'
            );
        }
        if (
            Tools::getValue('LR_PROINFO_BANK_NAME')
            && !Validate::isBool(Tools::getValue('LR_PROINFO_BANK_NAME'))
        ) {
            $this->postErrors[] = $this->l(
                'Error : The field enable bank name is not valid'
            );
        }
        if (
            Tools::getValue('LR_PROINFO_IBAN')
            && !Validate::isBool(Tools::getValue('LR_PROINFO_IBAN'))
        ) {
            $this->postErrors[] = $this->l(
                'Error : The field enable iban is not valid'
            );
        }
        if (
            Tools::getValue('LR_PROINFO_BIC')
            && !Validate::isBool(Tools::getValue('LR_PROINFO_BIC'))
        ) {
            $this->postErrors[] = $this->l(
                'Error : The field enable bic is not valid'
            );
        }
        if (
            Tools::getValue('LR_PROINFO_WEBSITE')
            && !Validate::isBool(Tools::getValue('LR_PROINFO_WEBSITE'))
        ) {
            $this->postErrors[] = $this->l(
                'Error : The field enable website is not valid'
            );
        }
        if (
            Tools::getValue('LR_PROINFO_COMMENT')
            && !Validate::isBool(Tools::getValue('LR_PROINFO_COMMENT'))
        ) {
            $this->postErrors[] = $this->l(
                'Error : The field enable comment is not valid'
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

        $this->postSuccess[] = $this->l('Success !!!');
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

    public function hookActionObjectProinfoAddAfter($params)
    {
        if ((bool)Configuration::get('LR_PROINFO_SEND_MAIL') === false) {
            return;
        }

        $proInfo = $params['object'];
        $customer = new Customer(
            (int)$proInfo->id_customer
        );

        $address = new Address(
            (int)$proInfo->id_address
        );

        Mail::send(
            Context::getContext()->language->id,
            'proinfo',
            $this->l('New creation pro account'),
            array(
                '{firstname}' => (string)$customer->firstname,
                '{lastname}' => (string)$customer->lastname,
                '{email}' => (string)$customer->email,
                '{address}' => (string)$address->address1 . ' ' . $address->address2 . ' ' . $address->postcode . ' ' . $address->city,
                '{company}' => (string)$proInfo->company,
                '{manager}' => (string)$proInfo->manager,
                '{siret}' => (string)$proInfo->siret,
                '{vat_number}' => (string)$proInfo->vat_number,
                '{bic}' => (string)$proInfo->bic,
                '{website}' => (string)$proInfo->website,
                '{comment}' => (string)$proInfo->comment,
                '{iban}' => (string)$proInfo->iban,
            ),
            Configuration::get('LR_PROINFO_ADMIN_EMAIL'),
            Configuration::get('PS_SHOP_NAME'),
            Configuration::get('PS_SHOP_EMAIL'),
            Configuration::get('PS_SHOP_NAME'),
            null,
            null,
            dirname(__FILE__) . '/mails/'
        );
    }

    public function hookActionObjectProinfoUpdateAfter($params)
    {
        if ((bool)Configuration::get('LR_PROINFO_SEND_MAIL') === false) {
            return;
        }

        $proInfo = $params['object'];
        $customer = new Customer(
            (int)$proInfo->id_customer
        );

        if ((bool)$proInfo->active === true && (bool)$proInfo->mailsent === false) {

            $sent = Mail::send(
                Context::getContext()->language->id,
                'customer',
                $this->l('Your account has been validated'),
                array(
                    '{firstname}' => (string)$customer->firstname,
                    '{lastname}' => (string)$customer->lastname,
                    '{email}' => (string)$customer->email,
                    '{message}' => $this->l('Your account has been validated')
                ),
                $customer->email,
                $customer->firstname . ' ' . $customer->lastname,
                Configuration::get('PS_SHOP_EMAIL'),
                Configuration::get('PS_SHOP_NAME'),
                null,
                null,
                dirname(__FILE__) . '/mails/'

            );


            Db::getInstance()->update(
                'lr_proinfo',
                array(
                    'mailsent' => $sent,
                ),
                'id_lr_proinfo = ' . (int)$proInfo->id
            );
        }
    }

    public function hookDisplayTop()
    {
        if ((bool)Context::getContext()->customer->isLogged() === false) {
            return;
        }

        $obj = ProInfo::getByIdCustomer((int)Context::getContext()->customer->id);
        if (Validate::isLoadedObject($obj)) {
            return;
        }

        $link = new Link();

        $proInfoLink = $link->getModuleLink('lr_proinfo', 'proinfo');
        $this->context->smarty->assign(array(
            'proInfoLink' => $proInfoLink
        ));

        return $this->display(__FILE__, 'views/templates/hook/top.tpl');
    }

    public function hookDisplayReassurance()
    {
        return $this->hookDisplayTop();
    }

    public function hookDisplayAdminCustomers($params)
    {
        //die(var_dump($params['id_customer']));
        $obj = ProInfo::getByIdCustomer((int)$params['id_customer']);

        if (!Validate::isLoadedObject($obj)) {
            return;
        }

        $editUrl = 'index.php?controller=AdminLr_Proinfo&id_lr_proinfo=' . (int)$obj->id . '&updatelr_proinfo&token=' . Tools::getAdminTokenLite('AdminLr_Proinfo');

        $this->context->smarty->assign(array(
            'proInfo' => $obj,
            'urlProInfo' => $editUrl,
        ));

        return $this->display(__FILE__, 'views/templates/admin/customer.tpl');
    }

    public function hookDisplayCustomerAccount()
    {
        $obj = ProInfo::getByIdCustomer(Context::getContext()->customer->id);

        if (Validate::isLoadedObject($obj)) {
            $this->context->smarty->assign(array(
                'proInfo' => $obj,
            ));
        }
        return $this->display(__FILE__, '/views/templates/hook/myaccount.tpl');
    }
}
