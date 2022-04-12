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
	<h3><i class="material-icons">library_books</i>&nbsp;{l s='What should I do' mod='lr_proinfo'}</h3>
    <div>
        <h4 class="bg-warning text-center">{l s='How can I configure my Pro Info module ?' mod='lr_proinfo'}</h4>
        <ol>
            <li>
                <h5>{l s='Admin information' mod='lr_proinfo'}</h5>
                <p>{l s='Inform admin information. Select If you want receive mail when customer make a request to change his account in pro account. Inform your email.' mod='lr_proinfo'}</p>
            </li>
            <li>
                <h5>{l s='CMS Page' mod='lr_proinfo'}</h5>
                <p>{l s='Create a CMS Page where you explain the procedure and conditon to yours customers to create a pro account. Then select this page in scrolling menu dedicated.' mod='lr_proinfo'}</p>
            </li>
            <li>
                <h5>{l s='Customize Form' mod='lr_proinfo'}</h5>
                <p>{l s='After install this module you just need to select which information you need to ask to customer to validate his pro account.' mod='lr_proinfo'}</p>
            </li>
        </ol>
        <p> {l s='Congratulation !! Pro Info module is configured.' mod='lr_proinfo'}</p>
	</div>
    <div>
        <h4 class="bg-warning text-center">{l s='Where I can see all Pro Account ?' mod='lr_proinfo'}</h4>
        <p> {l s='On left menu on your prestashop backoffice in Clients category you can find sub menu : Pro Infos. Click on it !' mod='lr_proinfo'}</p>
        <p>{l s='Now you can :' mod='lr_proinfo'}</p>
        <ul> 
            <li>{l s='Validate Pro Account' mod='lr_proinfo'}</li>
            <li>{l s='Delete Pro Account' mod='lr_proinfo'}</li>
            <li>{l s='Edit Pro Account' mod='lr_proinfo'}</li>
            <li>{l s='Search Pro Account' mod='lr_proinfo'}</li>
            <li>{l s='Show and Edit Customer Profile' mod='lr_proinfo'}</li>
        </ul>
    </div>
    <div>
        <h4 class="bg-warning text-center">{l s='Customer Profile' mod='lr_proinfo'}</h4>
        <p> {l s='On each profile customer you can show pro information if customer have a validate pro account' mod='lr_proinfo'}</p>
    </div>
</div>
