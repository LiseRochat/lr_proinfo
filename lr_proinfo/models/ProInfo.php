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

class ProInfo extends ObjectModel
{
    public $id_customer;
    public $id_address;
    public $company;
    public $manager;
    public $siret;
    public $vat_number;
    public $bank;
    public $iban;
    public $bic;
    public $website;
    public $comment;

    public static $definition = [
        'table' => 'lr_proinfo',
        'primary' => 'id_lr_proinfo',
        'fields' => [
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'id_address' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'company' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'manager' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'siret' => ['type' => self::TYPE_STRING, 'validate' => 'isSiret', 'required' => true],
            'vat_number' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'bank' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'iban' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'bic' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'website' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'comment' => ['type' => self::TYPE_HTML, 'validate' => 'isCleanHTML']
        ],
    ];

    public static function getByIdCustomer($id_customer)
    {
        $cacheId = 'ProInfo::getByIdCustomer' . $id_customer . date('Ymd');
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }
        $sql = new DbQuery();

        $sql->select('id_lr_proinfo');
        $sql->from('lr_proinfo');
        $sql->where('id_customer = ' . (int)$id_customer);

        $idObj = Db::getInstance()->getValue($sql);

        if (Validate::isInt($idObj)) {
            $obj = new self($idObj);
            Cache::store($cacheId, $obj);
            return $obj;
        }

        Cache::store($cacheId, false);

        return false;
    }

    public static function getByManager($manager)
    {
        $sql = new DbQuery();

        $sql->select('id_lr_proinfo');
        $sql->from('lr_proinfo');
        $sql->where('manager = "' . pSQL($manager) . '"');

        $idObj = Db::getInstance()->getValue($sql);

        if (Validate::isInt($idObj)) {
            return new self($idObj);
        }

        return false;
    }
}
