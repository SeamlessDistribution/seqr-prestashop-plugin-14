{capture name=path}{l s='SEQR Payment'}{/capture}
{include file=$tpl_dir./breadcrumb.tpl}

<h2>{l s=$headName mod="seqr"}</h2>

{assign var='current_step' value='payment'}
{include file=$tpl_dir./order-steps.tpl}

<link rel="stylesheet" type="text/css" href="css/seqr.css" />
