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
    <input type="hidden" name="cl" value="az_amz_destinations_history">
</form>

<form name="myedit" id="myedit" action="[{ $shop->selflink }]" method="post">
[{ $shop->hiddensid }]
<input type="hidden" name="cl" value="az_amz_destinations_history">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[az_amz_destinations__oxid]" value="[{ $oxid }]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">
    	<table>
    		<tr>
    			<td width="120px"><b>Timestamp</b></td>
    			<td width="100px"><b>Action</b></td>
    			<td width="600px"><b>Message</b></td>
    			<td width="120px"><b>User</b></td>
    		</tr>
    		[{foreach from=$aHistory item=oRecord}]
    		<tr onmouseover="style.backgroundColor='#f0f0f0';" onmouseout="style.backgroundColor='#FFFFFF';">
    			<td>[{$oRecord->az_amz_history__az_timestamp->value}]</td>
    			<td>[{$oRecord->az_amz_history__az_action->value}]</td>
    			<td>[{$oRecord->az_amz_history__az_statusmsg->value}]</td>
    			<td>[{$oRecord->oxuser__oxusername->value|default:'-'}]</td>
    		</tr>
    		[{/foreach}]
    		<tr>	
    			<td colspan="4"><hr/></td>
    		</tr>
    		<tr>
    			<td colspan="4">
    				[{section name=az_page loop=$iTotalPages start=1 step=1}]
    					[{if $iPage == $smarty.section.az_page.index}]
    						[{assign var=sPageIndex value="<span style=\"text-decoration:underline;font-weight:bold;\">"|cat:$smarty.section.az_page.index|cat:"</span>"}]
    					[{else}]
    						[{assign var=sPageIndex value=$smarty.section.az_page.index}]
    					[{/if}]
    					<a href="[{ $shop->selflink }]?cl=az_amz_destinations_history&oxid=[{$oxid}]&page=[{$smarty.section.az_page.index}]">[{$sPageIndex}]</a>&nbsp
    				[{/section}]
    			</td>
    		</tr>
    	</table>      
    </td>    
</table>

</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]