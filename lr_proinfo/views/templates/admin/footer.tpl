{*
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
*}

<div class="panel row">
	<h3><i class="icon icon-smile"></i>{l s='Pro Info Module' mod='lr_proinfo'}</h3>
	<div class="col-md-6">

		<p>{l s='Thanks to set this modul' mod='lr_proinfo'}</p>
		<img src='{$module_dir|escape:'htmlall':'UTF-8'}/logo.png'>

	</div>
	<div class="col-md-6">
		<p>
			{l s='Version of module :' mod='lr_proinfo'}
			{$version|escape:'htmlall':'UTF-8'}
		</p>	
	</div>
</div>