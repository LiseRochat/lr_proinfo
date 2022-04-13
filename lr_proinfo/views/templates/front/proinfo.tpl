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

{extends file='page.tpl'}
{block name='page_content'}
<div class="container">

    {if isset($lr_siret)}
        <h1>{l s='Edit your Pro Account' mod='lr_proinfo'}</h1>
    {else}
        <h1>{l s='Creation Pro Account' mod='lr_proinfo'}</h1>
    {/if}
    {if isset($successes) && $successes}
        {foreach from=$successes item=success}
            <div class="alert alert-success">
                {$success|escape:'htmlall':'UTF-8'}
            </div>
        {/foreach}
    {/if}
    {if isset($errors) && $errors}
        {foreach from=$errors item=error}
            <div class="alert alert-warning">
                {$error|escape:'htmlall':'UTF-8'}
            </div>
        {/foreach}
    {/if}
    {if isset($noaddress) && $noaddress}
        <div class="alert alert-warning">
            <p>{l s='Please create an address to create a pro account' mod='lr_proinfo'}</p>
            <a href="{$link->getPageLink('address', true)|escape:'htmlall':'UTF-8'}" title="{l s='Create my first address' mod='lr_proinfo'}">{l s='Create my first address' mod='lr_proinfo'}</a>
        </div>
    {else}
        {* {foreach from=$addresses item=address}
             {$address|var_dump}       
        {/foreach} *}
        <a href="{$cmsLink|escape:'htmlall':'UTF-8'}" title="{l s='See explination page' mod='lr_proinfo'}" class="btn btn-primary btn-see-explination-page" target="_blank">{l s='See explination page' mod='lr_proinfo'}</a>
        {if !isset($successes) }
            <form method="post" name="proInfoForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="lr_idaddress"><span class="required">* </span>{l s='Address of Your Company' mod='lr_proinfo'}<span class="required">*</span></label>
                    <select class="form-control" name="lr_idaddress" id="lr_idaddress">
                        {foreach from=$addresses item=address}
                            <option value="{$address.id_address|escape:'htmlall':'UTF-8'}">
                                {$address.address1|escape:'htmlall':'UTF-8'}
                                {$address.address2|escape:'htmlall':'UTF-8'}
                                {$address.postcode|escape:'htmlall':'UTF-8'}
                                {$address.city|escape:'htmlall':'UTF-8'}
                            </option>
                        {/foreach}
                    </select>
                </div>
                <div class="form-group">
                    <label for="lr_company"><span class="required">* </span>{l s='Name of Your Company' mod='lr_proinfo'}</label>
                    <input type="text" class="form-control" id="lr_company" name="lr_company" placeholder="{l s='Name of Your Company' mod='lr_proinfo'}" {if isset($lr_company) && $lr_company} value="{$lr_company|escape:'htmlall':'UTF-8'}"{/if} required>
                </div>
                <div class="form-group">
                    <label for="lr_manager"><span class="required">* </span>{l s='Name of Manager' mod='lr_proinfo'}</label>
                    <input type="text" class="form-control" id="lr_manager" name="lr_manager" placeholder="{l s='Name of Your Manager' mod='lr_proinfo'}" {if isset($lr_manager) && $lr_manager} value="{$lr_manager|escape:'htmlall':'UTF-8'}"{/if} required> 
                </div>
                {if $LR_PROINFO_SIRET == true}
                    <div class="form-group">
                        <label for="lr_siret">{l s='Your SIRET' mod='lr_proinfo'}</label>
                        <input type="text" class="form-control" id="lr_siret" name="lr_siret" placeholder="{l s='Your Siret' mod='lr_proinfo'}" {if isset($lr_siret) && $lr_siret} value="{$lr_siret|escape:'htmlall':'UTF-8'}"{/if}>
                    </div>
                {/if}
                {if $LR_PROINFO_VAT_NUMBER == true}
                    <div class="form-group">
                        <label for="lr_vat_number">{l s='Your VAT Number' mod='lr_proinfo'}</label>
                        <input type="text" class="form-control" id="lr_vat_number" name="lr_vat_number" placeholder="{l s='Your Vat Number' mod='lr_proinfo'}" {if isset($lr_vat_number) && $lr_vat_number} value="{$lr_vat_number|escape:'htmlall':'UTF-8'}"{/if}>
                    </div>
                {/if}
                {if $LR_PROINFO_BANK_NAME }
                    <div class="form-group">
                        <label for="lr_bank">{l s='Your Bank' mod='lr_proinfo'}</label>
                        <input type="text" class="form-control" id="lr_bank" name="lr_bank" placeholder="{l s='Your Bank' mod='lr_proinfo'}" {if isset($lr_bank) && $lr_bank} value="{$lr_bank|escape:'htmlall':'UTF-8'}"{/if}>
                    </div>
                {/if}
                {if $LR_PROINFO_IBAN == true}
                    <div class="form-group">
                        <label for="lr_iban">{l s='Your Iban' mod='lr_proinfo'}</label>
                        <input type="text" class="form-control" id="lr_iban" name="lr_iban" placeholder="{l s='Your Iban' mod='lr_proinfo'}" {if isset($lr_iban) && $lr_iban} value="{$lr_iban|escape:'htmlall':'UTF-8'}"{/if}>
                    </div>
                {/if}
                {if $LR_PROINFO_BIC == true}
                    <div class="form-group">
                        <label for="lr_bic">{l s='Your BIC' mod='lr_proinfo'}</label>
                        <input type="text" class="form-control" id="lr_bic" name="lr_bic" placeholder="{l s='Your BIC' mod='lr_proinfo'}" {if isset($lr_bic) && $lr_bic} value="{$lr_bic|escape:'htmlall':'UTF-8'}"{/if}>
                    </div>
                {/if}
                {if $LR_PROINFO_WEBSITE == true}
                    <div class="form-group">
                        <label for="lr_website">{l s='Your WebSite' mod='lr_proinfo'}</label>
                        <input type="text" class="form-control" id="lr_website" name="lr_website" placeholder="{l s='Your WebSite' mod='lr_proinfo'}" {if isset($lr_website) && $lr_website} value="{$lr_website|escape:'htmlall':'UTF-8'}"{/if}>
                    </div>
                {/if}
                {if $LR_PROINFO_COMMENT == true}
                    <div class="form-group">
                        <label for="lr_comment">{l s='Comment' mod='lr_proinfo'}</label>
                        <textarea class="form-control proInfo-textarea" id="lr_comment" name="lr_comment"  rows="3">{if isset($lr_comment) && $lr_comment}{$lr_comment|escape:'htmlall':'UTF-8'}{/if}</textarea>
                    </div>
                {/if}
                <button type="submit" name="lr_validate" id="lr_validate" class="btn btn-primary">{l s='Submit' mod='lr_proinfo'}</button>
            </form>
        {/if}
    {/if}
</div>
{/block}
