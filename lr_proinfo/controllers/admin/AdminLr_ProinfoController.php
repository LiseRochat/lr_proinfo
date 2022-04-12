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

class AdminLr_ProinfoController extends ModuleAdminController
{
    private $html;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;
        $this->table = 'lr_proinfo';
        $this->className = 'ProInfo';
        $this->context = Context::getContext();
        $this->identifier = 'id_lr_proinfo';
        $this->_select = 'l.email AS lemail,l.firstname AS lfirstname,l.lastname AS llastname';
        $this->_join ='LEFT JOIN `'._DB_PREFIX_.'customer` l
                            ON (
                                l.`id_customer` = a.`id_customer`
                            )';
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items ?')
            ),
        );  
        $this->fields_list = array(
            'id_lr_proinfo' => array(
                'title' => $this->l('id'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'lemail' => array(
                'title' => $this->l('Email'),
                'align' => 'left',
                'width' => 'auto',
                'havingFilter' => true,
                'filter_key' => 'l!email'
            ),
            'lfirstname' => array(
                'title' => $this->l('Firstname'),
                'align' => 'left',
                'width' => 'auto',
                'havingFilter' => true,
                'filter_key' => 'l!firstname'
            ),
            'llastname' => array(
                'title' => $this->l('Lastname'),
                'align' => 'left',
                'width' => 'auto',
                'havingFilter' => true,
                'filter_key' => 'l!lastname'
            ),
            'company' => array(
                'title' => $this->l('Company'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'siret' => array(
                'title' => $this->l('Siret'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'manager' => array(
                'title' => $this->l('Manager'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'active' => array(
                'title' => $this->l('active'),
                'type' => 'bool',
                'active' => 'statusactive'
            ),
        );

        $module = Module::getInstanceByName('lr_proinfo');

        $this->context->smarty->assign(array(
            'module_dir' => _PS_BASE_URL_ . __PS_BASE_URI__ .'/modules/lr_proinfo',
            'lr_version' => $module->version,
            'lr_employee' => Context::getContext()->employee->firstname,
        ));

        parent::__construct();
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Context::getContext()->getTranslator()->trans(
            $string,
            [],
            'Modules.Lr_proinfo.Adminlr_proinfocontroller'
        );
    }

    public function renderList()
    {
        
        $this->html = '';
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->toolbar_title = $this->l('Pro Infos');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected items'),
                'confirm' => $this->l('Delete selected items ?')
            ),
        );
        $this->addRowAction('EditCustomer');
        $this->addRowAction('EditAddress');

        //die(var_dump(Tools::getValue('statusactive'.$this->table)));

        if (Tools::getIsset('statusactive'.$this->table) && Tools::getValue($this->identifier)) {

            $proInfo = new ProInfo(Tools::getValue($this->identifier));

            $proInfo->active = !$proInfo->active;

            $proInfo->save();
        }

        $lists = parent::renderList();
        $this->html .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'/lr_proinfo/views/templates/admin/header.tpl'
        );
        if (count($this->errors)) {
            foreach ($this->errors as $error) {
                $this->html .= Tools::displayError($error);
            }
        }
        $this->html .= $lists;
        $this->html .= $this->context->smarty->fetch(_PS_MODULE_DIR_ .'/lr_proinfo/views/templates/admin/documentation.tpl');
        $this->html .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'/lr_proinfo/views/templates/admin/footer.tpl'
        );

        return $this->html;
        return parent::renderList();
    }

    public function initToolBar()
    {

    }
    // public function initPageHeaderToolbar()
    // {
    //     $this->page_header_toolbar_btn['new'] = array(
    //         'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
    //         'desc' => $this->l('Add new element'),
    //         'icon' => 'process-icon-new'
    //     );

    //     parent::initPageHeaderToolbar();
    // }

    public function renderForm()
    {
        $this->fields_form = array(
            'submit' => array(
                'name' => 'save',
                'title' => $this->l('save'),
                'class' => 'btn btn-success pull-right'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Manager'),
                    'desc' => $this->l('Manager'),
                    'hint' => $this->l('Manager'),
                    'required' => true,
                    'name' => 'manager'
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_customer'
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_address'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Company'),
                    'desc' => $this->l('Company'),
                    'hint' => $this->l('Company'),
                    'required' => true,
                    'name' => 'company'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Siret'),
                    'desc' => $this->l('Siret'),
                    'hint' => $this->l('Siret'),
                    'name' => 'siret'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('iban'),
                    'desc' => $this->l('iban'),
                    'hint' => $this->l('iban'),
                    'name' => 'iban'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('vat_number'),
                    'desc' => $this->l('vat_number'),
                    'hint' => $this->l('vat_number'),
                    'name' => 'vat_number'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('website'),
                    'desc' => $this->l('website'),
                    'hint' => $this->l('website'),
                    'name' => 'website'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('bank'),
                    'desc' => $this->l('bank'),
                    'hint' => $this->l('bank'),
                    'name' => 'bank'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('comment'),
                    'desc' => $this->l('comment'),
                    'hint' => $this->l('comment'),
                    'name' => 'comment',
                    //'autoload_rte' => true,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('active'),
                    'desc' => $this->l('active'),
                    'hint' => $this->l('active'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enable'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disable'),
                        ),

                    )

                )
        
            ),
        );
        
        return parent::renderForm();
    }

    public function postProcess()
    {
        
        parent::postProcess();

        
        if (Tools::isSubmit('save')) {
            if (!Tools::getValue('id_customer') || !Tools::getValue('id_address')) {
                $this->errors[] = $this->l('Missing customer or address');
            }

            if (!Tools::getValue('manager') || !Validate::isString(Tools::getValue('manager') )) {
                $this->errors[] = $this->l('Manager is not valid');
            }
    
            if (!Tools::getValue('company') || !Validate::isString(Tools::getValue('company'))) {
                $this->errors[] = $this->l('company is not valid');
    
            }
    
            if (Tools::getValue('siret') && !Validate::isSiret(Tools::getValue('siret'))) {
                $this->errors[] = $this->l('siret is not valid');
    
            }
            if (Tools::getValue('vat_number') && !Validate::isString(Tools::getValue('vat_number'))) {
                $this->errors[] = $this->l('vat_number is not valid');
    
            }
            if (Tools::getValue('bank') && !Validate::isString(Tools::getValue('bank'))) {
                $this->errors[] = $this->l('bank is not valid');
    
            }
            if (Tools::getValue('iban') && !Validate::isString(Tools::getValue('iban'))) {
                $this->errors[] = $this->l('iban is not valid');
    
            }
            if (Tools::getValue('bic') && !Validate::isString(Tools::getValue('bic'))) {
                $this->errors[] = $this->l('bic is not valid');
    
            }
            if (Tools::getValue('website') && !Validate::isString(Tools::getValue('website'))) {
                $this->errors[] = $this->l('website is not valid');
    
            }
            if (Tools::getValue('comment') && !Validate::isCleanHtml(Tools::getValue('comment'))) {
                $this->errors[] = $this->l('comment is not valid');
    
            }
            if (Tools::getValue('active') && !Validate::isBool(Tools::getValue('active'))) {
                $this->errors[] = $this->l('Active is not valid');
            }
            if (count($this->errors) <= 0) {
                $proInfo = new ProInfo(Tools::getValue($this->identifier));
    
                $proInfo->manager = Tools::getValue('manager');
                $proInfo->company = Tools::getValue('manager');
                $proInfo->siret = Tools::getValue('siret');
                $proInfo->vat_number = Tools::getValue('vat_number');
                $proInfo->bank = Tools::getValue('bank');
                $proInfo->iban = Tools::getValue('iban');
                $proInfo->bic = Tools::getValue('bic');
                $proInfo->website = Tools::getValue('website');
                $proInfo->comment = Tools::getValue('comment');
                $proInfo->active = Tools::getValue('active');
    
                $proInfo->save();
            }

        }
    }

    protected function displayError($message, $description = false)
    {
        /**
         * Set error message and description for the template.
         */
        array_push($this->errors, $this->module->l($message), $description);

        return $this->setTemplate('error.tpl');
    }

    public function displayEditCustomerLink($token, $id)
    {
        $proInfo = new ProInfo($id);

        $editUrl = 'index.php?controller=AdminCustomers&id_customer='. (int)$proInfo->id_customer. '&updatecustomer&token='. Tools::getAdminTokenLite('AdminCustomers');

        $this->context->smarty->assign(array(
            'href' => $editUrl,
            'confirm' => null,
            'action' => $this->l('Edit Customer'),
            'icon' => 'icon-file'
        ));
       
        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'lr_proinfo/views/templates/admin/helpers/lists/list_action_edit_customer.tpl'
          
        );
    }

    public function displayEditAddressLink($token, $id)
    {
        $proInfo = new ProInfo($id);

        $editUrl = 'index.php?controller=AdminAddresses&id_address='. (int)$proInfo->id_address. '&updateaddress&token='. Tools::getAdminTokenLite('AdminAddresses');

        $this->context->smarty->assign(array(
            'href' => $editUrl,
            'confirm' => null,
            'action' => $this->l('Edit Address'),
            'icon' => 'icon-smile'
        ));
       
        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'lr_proinfo/views/templates/admin/helpers/lists/list_action_edit_customer.tpl'
          
        );
    }

    // $link = new Link();
    // $edit_url = $link->getAdminLink(
    //     'AdminCustomers',
    //     true,
    //     ['id_customer' => $pro_customer->id_customer]
    // );

    // $this->context->smarty->assign(array(
    //     'seo_product' => $seo_product,
    //     'everlink' => $link->getAdminLink(
    //         'AdminEverPsSeoProduct',
    //         true,
    //         [],
    //         ['updateever_seo_product' => true, 'id_ever_seo_product' => $seo_product->id]
    //     ),
    // ))
}
