{assign var='headName' value="SEQR Payment code"}

{include file="$breadcrumb"}

<link rel="stylesheet" type="text/css" href="https://cdn.seqr.com/webshop-plugin/css/seqrShop.css">
<script type="text/javascript" src="js/seqr.js"></script>
<script type="text/javascript">
    {literal}
        (function () {
    {/literal}
            window.seqr.backUrl = "{$backUrl}";
    {literal}
        }());
    {/literal}
</script>

<div class="seqr-box" style="text-align: center">
    <h1>{l s="Total amount: "} {$total} {$currency->iso_code}</h1>
</div>
<div class="seqr-box">
    <script id="seqrShop" src="{$webPluginUrl}" type="text/javascript"></script>
</div>

