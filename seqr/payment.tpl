{assign var='headName' value="Order summary"}

{include file="$breadcrumb"}

{if $nbProducts <= 0}
    <p class="warning">{l s='Your shopping cart is empty.' mod='seqr'}</p>
{else}
    <form action="{$moduleUrl}/paymentcode.php" method="post">

        <div class="box cheque-box">
            <h3 class="page-subheading">
                SEQR payment
            </h3>

            <p class="cheque-indent">
                <strong class="dark">
                    {l s='You have chosen to pay by SEQR.' mod='seqr'}
                    {l s='Here is a short summary of your order:' mod='seqr'}
                </strong>
            </p>

            <p>
                - {l s='The total amount of your order comes to:' mod='seqr'}
                <strong>
                    <span id="amount" class="price">{displayPrice price=$total}</span>
                </strong>
                {if $use_taxes == 1}
                    {l s='(tax incl.)' mod='seqr'}
                {/if}
            </p>

            <p>
                - {l s='We allow the following currency to be sent via SEQR:' mod='seqr'}&nbsp;
                <b>{$currency->iso_code}</b>
                <input type="hidden" name="currency_payement" value="{$currency->id_currency}"/>
            </p>

            <p>
                - {l s='The QR code for this payment will be displayed on the next page.' mod='seqr'}
                <br/><br/>
            </p>

            <p>
                <strong class="dark">
                    <b>{l s='Please confirm your order by clicking "I confirm my order".' mod='seqr'}</b>
                </strong>
            </p>
        </div>

        <p class="cart_navigation submit">
            <a href="{$base_dir_ssl}order.php?step=3" title="{l s='Other payment methods' mod='seqr'}"
               class="button">&laquo; {l s='Other payment methods' mod='seqr'}</a>
            <input class="exclusive button" type="submit" value="{l s='I confirm' mod='seqr'} &raquo;" width="100px"/>
        </p>
    </form>
{/if}
