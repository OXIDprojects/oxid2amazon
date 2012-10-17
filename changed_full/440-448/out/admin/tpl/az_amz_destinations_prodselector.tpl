[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
[{ if $updatelist == 1}]
    UpdateList('[{ $oxid }]');
[{ /if}]

function UpdateList( sID)
{
    var oSearch = parent.list.document.getElementById("search");
    oSearch.oxid.value=sID;
    oSearch.submit();
}

function DeleteField(key)
{
	blCheck = confirm("[{ oxmultilang ident="GENERAL_YOUWANTTODELETE" }]");
	if (blCheck)
	{
		var oDeleteFlag = document.getElementById('delete_flag_'+key);
		
		oDeleteFlag.value = '1';
		document.myedit.fnc.value='save';
		document.myedit.submit();
	}	
}

function openPreview(sParams)
{
	window.open('[{ $shop->selflink }]'+sParams, 'ProductsPreview', 'width=600,height=680,scrollbars=yes,resizable=yes');
}
//-->
</script>

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]
<form name="transfer" id="transfer" action="[{ $shop->selflink }]" method="post">
    [{ $shop->hiddensid }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="az_amz_destinations_prodselector">
</form>
<form name="myedit" id="myedit" action="[{ $shop->selflink }]" method="post">
[{ $shop->hiddensid }]
<input type="hidden" name="cl" value="az_amz_destinations_prodselector">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[az_amz_destinations__oxid]" value="[{ $oxid }]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
	<td valign="top" class="edittext" width="25%">
		[{ oxmultilang ident="AZ_AMZ_PS_CATEGORIES_TREE" }] <br/>
		<select name="aFilter[categories][]" size="15" multiple class="editinput" style="width: 210px;" [{ $readonly }]>
        [{foreach from=$cattree item=oCat key=sCatId}]        	
        	<option value="[{ $oCat->getId() }]" [{if $sCatId|in_array:$aFilter.categories}]selected[{/if}]>[{ $oCat->oxcategories__oxtitle->value }]</option>
        [{/foreach}]        
        </select>
        <br/><br/>
       	<div style="width:100%;text-align:center"><input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly }] [{ $disableSharedEdit }]></div>
        
	</td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="55%">
    	<table>
    		[{if $aFilter.fields}]
    		[{foreach from=$aFilter.fields item=oField name=flds}]
    		[{assign var="fld_count" value=$smarty.foreach.flds.index}]
    		<tr>
    			<td>[{$fld_count+1}]</td>    			
    			<td>
    				<select class="select" name="aFilter[fields][[{$fld_count}]][field]" [{ $readonly}]>
			          [{ foreach from=$aArticleFields item=field}]
			            <option value="[{$field}]" [{if $field == $oField.field}]selected[{/if}]>[{ oxmultilang ident="AZ_AMZ_FILTER_FIELD_"|cat:$field }]</option>
			          [{/foreach}]
			        </select>
    			</td>
    			<td>
    				[{*
    				<select class="select" name="aFilter[fields][[{$fld_count}]][operator]" [{ $readonly}]>
			          [{ foreach from=$aOperators item=oper}]
			          	[{if $oper.enabled}]
			            	<option value="[{$oper.operator}]" [{if $oper.operator == $oField.operator}]selected[{/if}]>[{$oper.operator}]</option>
			            [{/if}]
			          [{/foreach}]			          
			        </select>*}]
			        <input type="text" class="edittext" size="15" name="aFilter[fields][[{$fld_count}]][operator]" value="[{$oField.operator}]" readonly=true />
    			</td>
    			<td>
    				[{if $oField.req }]
    					<input type="text" class="edittext" size="40" name="aFilter[fields][[{$fld_count}]][value]" value="[{$oField.value}]"/>
    				[{else}]
						<input type="hidden" name="aFilter[fields][[{$fld_count}]][value]">
					[{/if}]  					
    			</td>    			
    			<td>
    				<input type="hidden" class="edittext" size="40" name="aFilter[fields][[{$fld_count}]][delete]" value="" id="delete_flag_[{$fld_count}]"/>
    				<a href="Javascript:DeleteField('[{$fld_count}]');" class="delete"></a>
    			</td>
    		</tr>    	    			
    		[{/foreach}]
    		[{else}]
			<tr>
				<td colspan="4">[{ oxmultilang ident="AZ_AMZ_PS_NO_FIELDS_MESSAGE" }]</td>
    		</tr>
    		[{/if}]
    		[{assign var="fld_count" value=$fld_count+1}]
    		    		
    		<tr>
    			<td colspan="4"><hr /></td>
    		</tr>
    		[{if $iMaxFields > $fld_count}]
    		<tr>
    			<td>=></td>
    			<td>
    				<select class="select" name="aFilter[fields][[{$fld_count}]][field]" [{ $readonly}]>
			          [{ foreach from=$aArticleFields item=field}]
			            <option value="[{$field}]">[{ oxmultilang ident="AZ_AMZ_FILTER_FIELD_"|cat:$field }]</option>
			          [{/foreach}]
			        </select>
    			</td>
    			<td>
    				<select class="select" name="aFilter[fields][[{$fld_count}]][operator]" [{ $readonly}]>
			          [{ foreach from=$aOperators item=oper}]
			          	[{if $oper.enabled}]
			            	<option value="[{$oper.operator}]">[{$oper.operator}]</option>
			            [{/if}]
			          [{/foreach}]
			        </select>
    			</td>
    			<td><input type="text" class="edittext" size="40" name="aFilter[fields][[{$fld_count}]][value]" /></td>
    			<td><input type="submit" class="edittext" value="[{ oxmultilang ident="AZ_AMZ_PS_ADD_FIELD" }]" onClick="Javascript:document.myedit.fnc.value='save'"></td>    			
    		</tr>
    		[{else}]
    		<tr>
    			<td colspan="3">[{ oxmultilang ident="AZ_AMZ_PS_MAX_FIELDS_MESSAGE" }]</td>
    		</tr>
    		[{/if}]
    	</table>
    </td>
    <td valign="top" class="edittext" width="20%">
    	<fieldset>
    		<table width="98%">
    			<tr>
    				<td width="80%">100 products from sample</td>
    				<td width="20%"><input type="button" value="[{oxmultilang ident="AZ_AMZ_SAMPLE_PRODUCTS_BTN"}]" onclick="JavaScript:showDialog('?cl=az_amz_destinations_prodselector&preview=1&oxid=[{ $oxid }]');" /></td>
    			</tr>
    			<tr>
    				<td width="80%">DEV! do snapshot!</td>
    				<td width="20%"><input type="button" value="do snapshot" onClick="Javascript:document.myedit.fnc.value='doSnapshot';document.myedit.submit();" /></td>
    			</tr>
    		</table>
    	</fieldset>
    </td>
    </tr>       
    <tr>            
        
    </tr>
</table>

</form>
[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]