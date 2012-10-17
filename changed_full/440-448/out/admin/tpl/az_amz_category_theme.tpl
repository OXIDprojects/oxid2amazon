[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $shop->selflink }]" method="post">
    [{ $shop->hiddensid }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="az_amz_category_theme">    
</form>
  <form name="myedit" id="myedit" action="[{ $shop->selflink }]" method="post" onSubmit="copyLongDesc( 'oxcategories__oxlongdesc' );" style="padding: 0px;margin: 0px;height:0px;">
    [{ $shop->hiddensid }]
    <input type="hidden" name="cl" value="az_amz_category_theme">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="voxid" value="[{ $oxid }]">
    <input type="hidden" name="editval[oxcategories__oxid]" value="[{ $oxid }]">    
    <table>
    <tr>
    	<td>[{ oxmultilang ident="AZ_AMZ_CATEGORY_THEME" }]:</td>
    	<td>
    		<select name="aAmazon[theme]" onChange="Javascript:document.myedit.submit()" style="width: 250px;">
    			<option value=""> - </option>
				[{foreach from=$aAmazonThemes item=sTheme}]
				<option value="[{$sTheme}]" [{if $sTheme == $aCatThemeData.theme}]selected[{/if}]>[{$sTheme}]</option>
				[{/foreach}]
    		</select>
    	</td>
    </tr>
    <tr>
    	<td>[{ oxmultilang ident="AZ_AMZ_CATEGORY_CATEGORY" }]:</td>
    	<td>
    		[{if $aAmazonThemeCategories}]
    		<select name="aAmazon[category]" style="width: 250px;">
    			<option value=""> - </option>
				[{foreach from=$aAmazonThemeCategories item=sThemeCategory}]
				<option value="[{$sThemeCategory}]" [{if $sThemeCategory == $aCatThemeData.category}]selected[{/if}]>[{$sThemeCategory}]</option>
				[{/foreach}]
    		</select>
    		[{else}]
    			N/A
    		[{/if}]
    	</td>
    </tr>    
    <tr>
   
    	<td>[{ oxmultilang ident="AZ_AMZ_CATEGORY_SUBCATEGORY" }]:</td>
    	<td>
    		[{if $aAmazonThemeSubCategories}]
    		<select name="aAmazon[subcategory]" style="width: 250px;">
    			<option value=""> - </option>
				[{foreach from=$aAmazonThemeSubCategories item=sThemeSubCategory}]
				<option value="[{$sThemeSubCategory}]" [{if $sThemeSubCategory == $aCatThemeData.subcategory}]selected[{/if}]>[{$sThemeSubCategory}]</option>
				[{/foreach}]
    		</select>
    		[{else}]
    			N/A
    		[{/if}]
    	</td>    
    </tr>
    
    <tr>
   
    	<td>[{ oxmultilang ident="AZ_AMZ_CATEGORY_VARIATION_THEME" }]:</td>
    	<td>
    		[{if $aAmazonVariationThemes}]
    		<select name="aAmazon[variation]" style="width: 250px;">
    			<option value=""> - </option>
				[{foreach from=$aAmazonVariationThemes item=sThemeVariation}]
				<option value="[{$sThemeVariation}]" [{if $sThemeVariation == $aCatThemeData.variation}]selected[{/if}]>[{$sThemeVariation}]</option>
				[{/foreach}]
    		</select>
    		[{else}]
    			N/A
    		[{/if}]
    	</td>    
    </tr>
    <tr>
      <td>
            <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="CATEGORY_TEXT_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'">
      </td>
    </tr>
    </form>
  </table>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
