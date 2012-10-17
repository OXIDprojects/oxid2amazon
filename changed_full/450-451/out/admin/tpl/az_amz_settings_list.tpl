[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list"}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
    <!--
    window.onload = function ()
    {
        top.reloadEditFrame();
        [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
        [{ /if}]
    }
    //-->
</script>

<script type="text/javascript">
<!--
window.onload = function ()
{
    [{ if $updatenav }]
    var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
    oTransfer.updatenav.value = 1;
    oTransfer.cl.value = '[{ $default_edit }]';
    [{ /if}]
    top.reloadEditFrame();
}
//-->
</script>

<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="az_amz_settings_list">
<input type="hidden" name="lstrt" value="[{ $lstrt }]">
<input type="hidden" name="sort" value="[{ $sort }]">
<input type="hidden" name="actedit" value="[{ $actedit }]">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="delshopid" value="">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="updatenav" value="">





[{include file="pagetabsnippet.tpl"}]

<script type="text/javascript">
if (parent.parent != null && parent.parent.setTitle )
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="SHOP_LIST_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="SHOP_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>
