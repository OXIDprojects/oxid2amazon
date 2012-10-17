[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function removeAll()
{
	return confirm("are you sure");
}

//-->
</script>

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]


<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="az_amz_destinations_main">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="az_amz_destinations_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[az_amz_destinations__oxid]" value="[{ $oxid }]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>

    <td valign="top" class="edittext">
        <table cellspacing="0" cellpadding="0" border="0">       
        <tr>
            <td class="edittext" width="100">
            [{ oxmultilang ident="GENERAL_TITLE" }]
            </td>
            <td class="edittext">
            <input type="text" class="editinput" size="50" maxlength="[{$edit->az_amz_destinations__az_title->fldmax_length}]" name="editval[az_amz_destinations__az_title]" value="[{$edit->az_amz_destinations__az_title->value}]" [{ $readonly }]>
            </td>
        </tr>        
        <tr>
        	<td class="edittext">&nbsp;</td>
        </tr>
        <tr>
        	<td class="edittext" width="100%" style="background-color:#f0f0f0;padding-left:20px;" colspan="2"><b>[{ oxmultilang ident="AZ_AMZ_AMAZON_LOGIN_TITLE" }]</b></td>
        </tr>
        <tr>
        	<td class="edittext">&nbsp;</td>
        </tr>
        <tr>
        	<td class="edittext" colspan="2">
        		<table cellspacing="0" cellpadding="0" border="0">       
			        <tr>
			            <td class="edittext" width="150">
			            [{ oxmultilang ident="AZ_AMZ_AMAZON_MERCHANT_ID" }]
			            </td>
			            <td class="edittext">
			            <input type="text" class="editinput" size="40" maxlength="[{$edit->az_amz_destinations__az_amz_merchantid->fldmax_length}]" name="editval[az_amz_destinations__az_amz_merchantid]" value="[{$edit->az_amz_destinations__az_amz_merchantid->value}]" [{ $readonly }]>
			            </td>
			        </tr>
			        <tr>
			            <td class="edittext" width="150">
			            [{ oxmultilang ident="AZ_AMZ_AMAZON_SHOP_NAME" }]
			            </td>
			            <td class="edittext">
			            <input type="text" class="editinput" size="40" maxlength="[{$edit->az_amz_destinations__az_amz_shopname->fldmax_length}]" name="editval[az_amz_destinations__az_amz_shopname]" value="[{$edit->az_amz_destinations__az_amz_shopname->value}]" [{ $readonly }]>
			            </td>
			        </tr>
			        <tr>
			            <td class="edittext" width="150">
			            [{ oxmultilang ident="AZ_AMZ_AMAZON_LOGIN" }]
			            </td>
			            <td class="edittext">
			            <input type="text" class="editinput" size="40" maxlength="[{$edit->az_amz_destinations__az_amz_user->fldmax_length}]" name="editval[az_amz_destinations__az_amz_user]" value="[{$edit->az_amz_destinations__az_amz_user->value}]" [{ $readonly }]>
			            </td>
			        </tr>
			        <tr>
			            <td class="edittext" width="150">
			            [{ oxmultilang ident="AZ_AMZ_AMAZON_PASSWORD" }]
			            </td>
			            <td class="edittext">
			            <input type="password" class="editinput" size="40" maxlength="[{$edit->az_amz_destinations__az_amz_password->fldmax_length}]" name="editval[az_amz_destinations__az_amz_password]" value="[{$edit->az_amz_destinations__az_amz_password->value}]" [{ $readonly }]>
			            </td>
			        </tr>
		        </table>
        	</td>
        </tr>
        <tr>
        	<td class="edittext">&nbsp;</td>
        </tr>
        <tr>
        	<td class="edittext" width="100%" style="background-color:#f0f0f0;padding-left:20px;" colspan="2"><b>[{ oxmultilang ident="AZ_AMZ_AMTU_SERVER_TITLE" }]</b></td>
        </tr>
        <tr>
        	<td class="edittext">&nbsp;</td>
        </tr>
        <tr>
        	<td class="edittext" colspan="2">
        		<table cellspacing="0" cellpadding="0" border="0">
        			<tr>
			            <td class="edittext" width="150">
			            [{ oxmultilang ident="AZ_AMZ_FTP_SERVER_PASSIVE" }]
			            </td>
			            <td class="edittext">
			            	<input type="hidden" name="editval[az_amz_destinations__az_ftppassivemode]" value="0" />			            
			            	<input class="edittext" type="checkbox" name="editval[az_amz_destinations__az_ftppassivemode]" value='1' [{if $edit->az_amz_destinations__az_ftppassivemode->value == 1}]checked[{/if}] [{ $readonly }] [{ $disableSharedEdit }]>			            
			            </td>
			        </tr>
			        <tr>
			            <td class="edittext" width="150">
			            [{ oxmultilang ident="AZ_AMZ_FTP_SERVER_ADDR" }]
			            </td>
			            <td class="edittext">
			            <input type="text" class="editinput" size="40" maxlength="[{$edit->az_amz_destinations__az_server->fldmax_length}]" name="editval[az_amz_destinations__az_server]" value="[{$edit->az_amz_destinations__az_server->value}]" [{ $readonly }]>
			            </td>
			        </tr>
			        <tr>
			            <td class="edittext" width="150">
			            [{ oxmultilang ident="AZ_AMZ_FTP_SERVER_USERNAME" }]
			            </td>
			            <td class="edittext">
			            <input type="text" class="editinput" size="40" maxlength="[{$edit->az_amz_destinations__az_ftpuser->fldmax_length}]" name="editval[az_amz_destinations__az_ftpuser]" value="[{$edit->az_amz_destinations__az_ftpuser->value}]" [{ $readonly }]>
			            </td>
			        </tr>
			        <tr>
			            <td class="edittext" width="150">
			            [{ oxmultilang ident="AZ_AMZ_FTP_SERVER_PASSWORD" }]
			            </td>
			            <td class="edittext">
			            <input type="password" class="editinput" size="40" maxlength="[{$edit->az_amz_destinations__az_ftppassword->fldmax_length}]" name="editval[az_amz_destinations__az_ftppassword]" value="[{$edit->az_amz_destinations__az_ftppassword->value}]" [{ $readonly }]>
			            </td>
			        </tr>
			        <tr>
			            <td class="edittext" width="150">
			            [{ oxmultilang ident="AZ_AMZ_FTP_SERVER_DIR_PATH" }]
			            </td>
			            <td class="edittext">
			            <input type="text" class="editinput" size="40" maxlength="[{$edit->az_amz_destinations__az_ftpdirectory->fldmax_length}]" name="editval[az_amz_destinations__az_ftpdirectory]" value="[{$edit->az_amz_destinations__az_ftpdirectory->value}]" [{ $readonly }]>
			            </td>
			        </tr>
			        <tr>
			            <td class="edittext" width="150">
			            [{ oxmultilang ident="AZ_AMZ_FTP_REPORTS_DIR_PATH" }]
			            </td>
			            <td class="edittext">
			            <input type="text" class="editinput" size="40" maxlength="[{$edit->az_amz_destinations__az_reportsdirectory->fldmax_length}]" name="editval[az_amz_destinations__az_reportsdirectory]" value="[{$edit->az_amz_destinations__az_reportsdirectory->value}]" [{ $readonly }]>
			            </td>
			        </tr>
       			</table>
        	</td>
        </tr>        
        </table>
    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="50%">
    	<table cellspacing="0" cellpadding="0" border="0">
    		<tr>
	        	<td class="edittext">&nbsp;</td>
	        </tr>
	        <tr>
	        	<td class="edittext" width="100%" style="background-color:#f0f0f0;padding-left:20px;" colspan="2"><b>[{ oxmultilang ident="AZ_AMZ_SETTINGS_TITLE" }]</b></td>
	        </tr>
	        <tr>
	        	<td class="edittext">&nbsp;</td>
	        </tr>
	        <tr>
	        	<td class="edittext" colspan="2">
	        		<table cellspacing="0" cellpadding="0" border="0">
	        			<tr>
				            <td class="edittext" width="150">
				            [{ oxmultilang ident="AZ_AMZ_SETTINGS_LANGUAGE" }]
				            </td>
				            <td class="edittext">
				            	<select class="editinput" name="editval[az_amz_destinations__az_language]" [{ $readonly }]>			                    
			                    [{foreach from=$aLanguages item=oLang key=sLangId}]
			                    	<option value="[{$sLangId}]"[{if $edit->az_amz_destinations__az_language->value == $sLangId}] selected[{/if}]>[{ $oLang }]</option>
			                    [{/foreach}]
			                    </select>				            	
				            </td>
				        </tr>
	        			<tr>
				            <td class="edittext" width="150">
				            [{ oxmultilang ident="AZ_AMZ_SETTINGS_CURRENCY" }]
				            </td>
				            <td class="edittext">
				            	<select class="editinput" name="editval[az_amz_destinations__az_currency]" [{ $readonly }]>			                    
			                    [{foreach from=$aCurrencies item=oCur}]
			                    	<option value="[{$oCur->id}]"[{if $edit->az_amz_destinations__az_currency->value == $oCur->id}] selected[{/if}]>[{ $oCur->name }]</option>
			                    [{/foreach}]
			                    </select>				            	
				            </td>
				        </tr>
				        <tr>
				            <td class="edittext" width="150">
				            [{ oxmultilang ident="AZ_AMZ_SETTINGS_PARCEL" }]
				            </td>
				            <td class="edittext">
				            	<select class="editinput" name="editval[az_amz_destinations__az_parcel]" [{ $readonly }]>			                    
			                    [{foreach from=$aParcels item=oParcel}]
			                    	<option value="[{$oParcel->id}]"[{if $edit->az_amz_destinations__az_parcel->value == $oParcel->id}] selected[{/if}]>[{ $oParcel->name }]</option>
			                    [{/foreach}]
			                    </select>				            	
				            </td>
				        </tr>				        
	       			</table>
	        	</td>
	        </tr>
	        <tr>
	        	<td class="edittext">&nbsp;</td>
	        </tr>
	        <tr>
	        	<td class="edittext" width="100%" style="background-color:#f0f0f0;padding-left:20px;" colspan="2"><b>[{ oxmultilang ident="AZ_AMZ_CONTROL_TITLE" }]</b></td>
	        </tr>
	        <tr>
	        	<td class="edittext">&nbsp;</td>
	        </tr>
	        <tr>
	        	<td class="edittext" width="400px">
	        		[{if $oxid != '-1'}]
		        	<a href="[{$sRemoveAllUrl}]" target="_blank" onclick="return removeAll()">[{ oxmultilang ident="AZ_AMZ_BTN_REMOVEALL" }]</a>
		        	[{/if}]
	        	</td>
	        </tr>	        
	        <tr>
	        	<td class="edittext" width="400px">&nbsp;</td>
	        </tr>
	        <tr>
	        	<td class="edittext" width="100%" style="background-color:#f0f0f0;padding-left:20px;" colspan="2"><b>[{ oxmultilang ident="AZ_AMZ_CRONJOB_TITLE" }]</b></td>
	        </tr>
	        <tr>
	        	<td class="edittext">&nbsp;</td>
	        </tr>
	        <tr>
	        	<td class="edittext">
	        	[{foreach from=$aCrons item=cron_url key=cron_title}]
	        		<a href="[{$cron_url}]" target="_blank">[{ oxmultilang ident=$cron_title }]</a><br/>
				[{/foreach}]
	        	</td>
	        </tr>
	    </table>
    </td>

    </tr>
    <tr>            
        <td class="edittext" colspan="2" style="text-align:center;"><br>        
        	<input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly }] [{ $disableSharedEdit }]/>        
        </td>
    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]