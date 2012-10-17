[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
[{ if $updatelist == 1}]
    UpdateList('[{ $oxid }]');
[{ /if}]

function _groupExp(el) {
    var _cur = el.parentNode;

    if (_cur.className == "exp") _cur.className = "";
      else _cur.className = "exp";
}


-->
</script>

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="az_amz_settings_main">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{ $shop->id }]">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="az_amz_settings_main">
<input type="hidden" name="fnc" value="save">

<div class="groupExp">
  <div>
    <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{ oxmultilang ident="SHOP_OPTIONS_GROUP_GLOBAL" }]</b></a>
    <dl>
      <dt>
        <select class="select" name="editval[sSkuField]" [{ $readonly}]>
          [{ foreach from=$aArticleFields item=field}]
            <option value="[{$field}]"[{if $field eq $oAzConfig->sSkuField}] selected="selected"[{elseif !$oAzConfig->sSkuField && $field eq "oxartnum"}] selected="selected"[{/if}]>[{ oxmultilang ident="AZ_AMZ_FILTER_FIELD_oxarticles."|cat:$field noerror=1}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_SKU" }]
      </dd>
      <div class="spacer"></div>
    </dl>
    
    
    <dl>
      <dt>
        <select class="select" name="editval[sEanField]" [{ $readonly}]>
          [{ foreach from=$aArticleFields item=field}]
            <option value="[{$field}]"[{if $field eq $oAzConfig->sEanField}] selected="selected"[{/if}]>[{$field}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_EAN" }]
      </dd>
      <div class="spacer"></div>
    </dl>
    
    <dl>
      <dt>
        <select class="select" name="editval[sConditionTypeField]" [{ $readonly}]>
          <option value="">[{oxmultilang ident="AZ_AMZ_SETTINGS_CONDITION_TYPE_NEW"}]</option>
          [{ foreach from=$aArticleFields item=field}]
            <option value="[{$field}]"[{if $field eq $oAzConfig->sConditionTypeField}] selected="selected"[{/if}]>[{$field}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_CONDITION_TYPE" }]
      </dd>
      <div class="spacer"></div>
    </dl>
    
    <dl>
      <dt>
        <select class="select" name="editval[sManufacturerField]" [{ $readonly}]>
            <option value=''>--------------</option>
            <option value="oxvendorid"[{if $oAzConfig->sManufacturerField eq 'oxvendorid'}] selected="selected"[{/if}]>[{ oxmultilang ident="ARTICLE_MAIN_VENDORID" }]</option>
            <option value="oxmanufacturerid"[{if $oAzConfig->sManufacturerField eq 'oxmanufacturerid' }] selected="selected"[{/if}]>[{ oxmultilang ident="ARTICLE_MAIN_MANUFACTURERID" }]</option>
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_MANUFACTURER_FIELD" }]
      </dd>
      <div class="spacer"></div>
    </dl>
    
    <dl>
      <dt>
        <select class="select" name="editval[sBrandField]" [{ $readonly}]>
            <option value="">----</option>
            <option value="oxvendorid"[{if $oAzConfig->sBrandField eq 'oxvendorid'}] selected="selected"[{/if}]>[{ oxmultilang ident="ARTICLE_MAIN_VENDORID" }]</option>
            <option value="oxmanufacturerid"[{if $oAzConfig->sBrandField eq 'oxmanufacturerid' }] selected="selected"[{/if}]>[{ oxmultilang ident="ARTICLE_MAIN_MANUFACTURERID" }]</option>
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_BRAND_FIELD" }]
      </dd>
      <div class="spacer"></div>
    </dl>    
    <dl>
      <dt>
        <select class="select" name="editval[sDefaultTheme]" [{ $readonly}]>            
            [{ foreach from=$aThemes item=sTheme}]
            	<option value="[{$sTheme}]"[{if $sTheme eq $oAzConfig->sDefaultTheme}] selected="selected"[{/if}]>[{$sTheme}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_DEFAULT_THEME_FIELD" }]
      </dd>
      <div class="spacer"></div>
    </dl>
    <dl>
      <dt>
        <select class="select" name="editval[sAmazonPayment]" [{ $readonly}]>            
            [{ foreach from=$aPayments item=oPayment}]
            	<option value="[{$oPayment->oxid}]"[{if $oPayment->oxid eq $oAzConfig->sAmazonPayment}] selected="selected"[{/if}]>[{$oPayment->title}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_AMAZONPAYMENT" }]
      </dd>
      <div class="spacer"></div>
    </dl>
    <dl>
      <dt>
        <select class="select" name="editval[sAmazonShippingStandard]" [{ $readonly}]>            
            [{ foreach from=$aShippings item=oShipping}]
            	<option value="[{$oShipping->oxid}]"[{if $oShipping->oxid eq $oAzConfig->sAmazonShippingStandard}] selected="selected"[{/if}]>[{$oShipping->title}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_AMAZONSHIPPINGSTANDARD" }]
      </dd>
      <div class="spacer"></div>
    </dl>
    <dl>
      <dt>
        <select class="select" name="editval[sAmazonShippingExpress]" [{ $readonly}]>            
            [{ foreach from=$aShippings item=oShipping}]
            	<option value="[{$oShipping->oxid}]"[{if $oShipping->oxid eq $oAzConfig->sAmazonShippingExpress}] selected="selected"[{/if}]>[{$oShipping->title}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_AMAZONSHIPPINGEXPRESS" }]
      </dd>
      <div class="spacer"></div>
    </dl>
    <dl>
      <dt>
        <input  type=text class="txt" name="editval[iDefaultStockReserve]" [{ $readonly}] value="[{$oAzConfig->iDefaultStockReserve}]"/>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_DEF_STOCK_RESERVE" }]
      </dd>
      <div class="spacer"></div>
    </dl>
   <dl>
        <dt>        	
            <input type=hidden name="editval[blAmazonExportVariants]" value=0>
            <input type=checkbox name="editval[blAmazonExportVariants]" value=1  [{if ($oAzConfig->blAmazonExportVariants == '1')}]checked[{/if}] [{ $readonly}]>            
            
        </dt>
        <dd>
            [{ oxmultilang ident="AZ_AMZ_SETTINGS_EXPORT_VARIANTS" }]
        </dd>
        <div class="spacer"></div>
    </dl>
    
  </div>
</div>
<div class="groupExp">
  <div>
     <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{ oxmultilang ident="AZ_AMZ_SETTINGS_GROUP_IMAGES" }]</b></a>
         
    
    <dl>
      <dt>
        <select class="select" name="editval[sPicField1]" [{ $readonly}]>
          [{foreach from=$aPictureFields key=key item=field}]
            <option value="[{$key}]"[{if $key eq $oAzConfig->sPicField1 }] selected="selected"[{/if}]">[{$field}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_MAIN_PIC" }]
      </dd>
      <div class="spacer"></div>
    </dl>
    
    <dl>
      <dt>
        <select class="select" name="editval[sPicField2]" [{ $readonly}]>
          <option value="">[{oxmultilang ident="AZ_AMZ_SETTINGS_NOT_USED"}]</option>
          [{foreach from=$aPictureFields key=key item=field}]
            <option value="[{$key}]"[{if $key eq $oAzConfig->sPicField2 }] selected="selected"[{/if}]>[{$field}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_PIC" }] 2
      </dd>
      <div class="spacer"></div>
    </dl>
    
    <dl>
      <dt>
        <select class="select" name="editval[sPicField3]" [{ $readonly}]>
          <option value="">[{oxmultilang ident="AZ_AMZ_SETTINGS_NOT_USED"}]</option>
          [{foreach from=$aPictureFields key=key item=field}]
            <option value="[{$key}]"[{if $key eq $oAzConfig->sPicField3 }] selected="selected"[{/if}]>[{$field}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_PIC" }] 3
      </dd>
      <div class="spacer"></div>
    </dl>
    
    <dl>
      <dt>
        <select class="select" name="editval[sPicField4]" [{ $readonly}]>
          <option value="">[{oxmultilang ident="AZ_AMZ_SETTINGS_NOT_USED"}]</option>
          [{foreach from=$aPictureFields key=key item=field}]
            <option value="[{$key}]"[{if $key eq $oAzConfig->sPicField4 }] selected="selected"[{/if}]>[{$field}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_PIC" }] 4
      </dd>
      <div class="spacer"></div>
    </dl>
    <dl>
      <dt>
        <select class="select" name="editval[sPicField5]" [{ $readonly}]>
          <option value="">[{oxmultilang ident="AZ_AMZ_SETTINGS_NOT_USED"}]</option>
          [{foreach from=$aPictureFields key=key item=field}]
            <option value="[{$key}]"[{if $key eq $oAzConfig->sPicField2 }] selected="selected"[{/if}]>[{$field}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_PIC" }] 5
      </dd>
      <div class="spacer"></div>
    </dl>
    
    <dl>
      <dt>
        <select class="select" name="editval[sPicField6]" [{ $readonly}]>
          <option value="">[{oxmultilang ident="AZ_AMZ_SETTINGS_NOT_USED"}]</option>
          [{foreach from=$aPictureFields key=key item=field}]
            <option value="[{$key}]"[{if $key eq $oAzConfig->sPicField6 }] selected="selected"[{/if}]>[{$field}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_PIC" }] 6
      </dd>
      <div class="spacer"></div>
    </dl>
    
    <dl>
      <dt>
        <select class="select" name="editval[sPicField7]" [{ $readonly}]>
          <option value="">[{oxmultilang ident="AZ_AMZ_SETTINGS_NOT_USED"}]</option>
          [{foreach from=$aPictureFields key=key item=field}]
            <option value="[{$key}]"[{if $key eq $oAzConfig->sPicField7 }] selected="selected"[{/if}]>[{$field}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_PIC" }] 7
      </dd>
      <div class="spacer"></div>
    </dl>
    
    <dl>
      <dt>
        <select class="select" name="editval[sPicField8]" [{ $readonly}]>
          <option value="">[{oxmultilang ident="AZ_AMZ_SETTINGS_NOT_USED"}]</option>
          [{foreach from=$aPictureFields key=key item=field}]
            <option value="[{$key}]"[{if $key eq $oAzConfig->sPicField8 }] selected="selected"[{/if}]>[{$field}]</option>
          [{/foreach}]
        </select>
      </dt>
      <dd>
        [{ oxmultilang ident="AZ_AMZ_SETTINGS_PIC" }] 8
      </dd>
      <div class="spacer"></div>
    </dl>
    

   </div>
</div>
    <br>

    <input type="submit" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'" [{ $readonly}]>

</form>


[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]