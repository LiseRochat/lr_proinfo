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

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lr_proinfo` (
    `id_lr_proinfo` int(11) NOT NULL AUTO_INCREMENT,
    `id_customer` int(11) NOT NULL,
    `id_address` int(11) NOT NULL,
    `company` varchar(255) DEFAULT NULL,
    `manager` varchar(255) DEFAULT NULL,
    `siret` varchar(255) DEFAULT NULL,
    `vat_number` varchar(255) DEFAULT NULL,
    `bank` varchar(255) DEFAULT NULL,
    `iban` varchar(255) DEFAULT NULL,
    `bic` varchar(255) DEFAULT NULL,
    `website` varchar(255) DEFAULT NULL,
    `comment` text DEFAULT NULL,
    PRIMARY KEY  (`id_lr_proinfo`,`id_customer`)

) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
