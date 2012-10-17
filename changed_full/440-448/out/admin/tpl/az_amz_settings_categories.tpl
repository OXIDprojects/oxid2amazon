[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{ $shop->selflink }]" method="post">
    [{ $shop->hiddensid }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="az_amz_settings_categories">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{ $shop->id }]">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

<form name="myedit" id="myedit" action="[{ $shop->selflink }]" method="post">
[{ $shop->hiddensid }]
<input type="hidden" name="cl" value="az_amz_settings_categories">
<input type="hidden" name="fnc" value="save">
<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
	<td valign="top" class="edittext" width="40%">
		<table cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td class="edittext" style="width:300px;background-color:#f0f0f0;"><b>Category</b></td>
				<td class="edittext" style="width:200px;background-color:#f0f0f0;"><b>[{ oxmultilang ident="AZ_AMZ_CATEGORY_THEME" }]</b></td>
				<td class="edittext" style="width:250px;background-color:#f0f0f0;"><b>[{ oxmultilang ident="AZ_AMZ_CATEGORY_CATEGORY" }]</b></td>
				<td class="edittext" style="width:250px;background-color:#f0f0f0;"><b>[{ oxmultilang ident="AZ_AMZ_CATEGORY_SUBCATEGORY" }]</b></td>
			</tr>
			[{foreach from=$aCategoryMappings item=aCatMap key=sCatId}]
			<tr onmouseover="style.backgroundColor='#f0f0f0';" onmouseout="style.backgroundColor='#FFFFFF';">
				<td class="edittext" >[{$aCatMap.path}]</td>
				<td class="edittext" >[{$aCatMap.map.theme}]</td>
				<td class="edittext" >[{$aCatMap.map.category}]</td>
				<td class="edittext" >[{$aCatMap.map.subcategory}]</td>				
			</tr>
			[{/foreach}]
		</table>
	</td>
</tr>
</table>
</form>


[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]