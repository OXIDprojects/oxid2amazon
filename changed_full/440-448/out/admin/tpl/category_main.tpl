[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function SchnellSortManager(oObj)
{   oRadio = document.getElementsByName("editval[oxcategories__oxdefsortmode]");
    if(oObj.value)
        for ( i=0; i<oRadio.length; i++)
            oRadio.item(i).disabled="";
    else
        for ( i=0; i<oRadio.length; i++)
            oRadio.item(i).disabled = true;
}

function DeletePic( sField )
{
    var oForm = document.getElementById("myedit");
    document.getElementById(sField).value="";
    oForm.fnc.value='save';
    oForm.submit();
}

function LockAssignment(obj)
{   var aButton = document.myedit.assignArticle;
    if ( aButton != null && obj != null )
    {
        if (obj.value > 0)
        {
            aButton.disabled = true;
        }
        else
        {
            aButton.disabled = false;
        }
    }
}
//-->
</script>
<!-- END add to *.css file -->
<form name="transfer" id="transfer" action="[{ $shop->selflink }]" method="post">
    [{ $shop->hiddensid }]
    <input type="hidden" name="oxid" id="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="category_main">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{ if $readonly_fields }]
    [{assign var="readonly_fields" value="readonly disabled"}]
[{else}]
    [{assign var="readonly_fields" value=""}]
[{/if}]

<form name="myedit" id="myedit" enctype="multipart/form-data" action="[{ $shop->selflink }]" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="[{$iMaxUploadFileSize}]">
[{ $shop->hiddensid }]
<input type="hidden" name="cl" value="category_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[oxcategories__oxid]" value="[{ $oxid }]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">

      <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td class="edittext" width="120">
            [{ oxmultilang ident="CATEGORY_MAIN_ACTIVE" }]
            </td>
            <td class="edittext" colspan="2">
            <input class="edittext" type="checkbox" name="editval[oxcategories__oxactive]" value='1' [{if $edit->oxcategories__oxactive->value == 1}]checked[{/if}] [{$readonly}]>&nbsp;&nbsp;&nbsp;
            [{ oxmultilang ident="CATEGORY_MAIN_HIDDEN" }]&nbsp;&nbsp;&nbsp;
            <input class="edittext" type="checkbox" name="editval[oxcategories__oxhidden]" value='1' [{if $edit->oxcategories__oxhidden->value == 1}]checked[{/if}] [{$readonly}]>
            [{ oxinputhelp ident="HELP_CATEGORY_MAIN_ACTIVE" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_TITLE" }]
            </td>
            <td class="edittext" colspan="2">
            <input type="text" class="editinput" size="25" maxlength="[{$edit->oxcategories__oxtitle->fldmax_length}]" name="editval[oxcategories__oxtitle]" value="[{$edit->oxcategories__oxtitle->value}]" [{$readonly}]>
            [{ oxinputhelp ident="HELP_CATEGORY_MAIN_TITLE" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_DESCRIPTION" }]
            </td>
            <td class="edittext" colspan="2">
            <input type="text" class="editinput" size="25" maxlength="[{$edit->oxcategories__oxdesc->fldmax_length}]" name="editval[oxcategories__oxdesc]" value="[{$edit->oxcategories__oxdesc->value}]" [{$readonly}]>
            [{ oxinputhelp ident="HELP_CATEGORY_MAIN_DESCRIPTION" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_PARENTID" }]
            </td>
            <td class="edittext" colspan="2">
                <select name="editval[oxcategories__oxparentid]" class="editinput" [{$readonly}]>
                [{foreach from=$cattree->aList item=pcat}]
                <option value="[{if $pcat->oxcategories__oxid->value}][{$pcat->oxcategories__oxid->value}][{else}]oxrootid[{/if}]" [{ if $pcat->selected}]SELECTED[{/if}]>[{ $pcat->oxcategories__oxtitle->value|oxtruncate:33:"..":true }]</option>
                [{/foreach}]
                </select>
                [{ oxinputhelp ident="HELP_CATEGORY_MAIN_PARENTID" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_SORT" }]
            </td>
            <td class="edittext" colspan="2">
            <input type="text" class="editinput" size="25" maxlength="[{$edit->oxcategories__oxorder->fldmax_length}]" name="editval[oxcategories__oxsort]" value="[{$edit->oxcategories__oxsort->value}]" [{$readonly}]>
            [{ oxinputhelp ident="HELP_CATEGORY_MAIN_SORT" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_THUMB" }]
            </td>
            <td class="edittext">
            <input id="oxthumb" type="text" class="editinput" size="42" maxlength="[{$edit->oxcategories__oxthumb->fldmax_length}]" name="editval[oxcategories__oxthumb]" value="[{$edit->oxcategories__oxthumb->value}]">
            [{ oxinputhelp ident="HELP_CATEGORY_MAIN_THUMB" }]
            [{ if (!($edit->oxcategories__oxthumb->value=="nopic.jpg" || $edit->oxcategories__oxthumb->value=="" || $edit->oxcategories__oxthumb->value=="nopic_ico.jpg")) }]
            </td>
            <td class="edittext">
            <a href="Javascript:DeletePic('oxthumb');" class="delete left" [{include file="help.tpl" helpid=item_delete}]></a>
            [{/if}]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_THUMBUPLOAD" }]
            </td>
            <td class="edittext" colspan="2">
            <input class="editinput" name="myfile[TC@oxcategories__oxthumb]" type="file"  size="26" [{$readonly}]>
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_ICON" }]
            </td>
            <td class="edittext">
            <input id="oxicon" type="text" class="editinput" size="42" maxlength="[{$edit->oxcategories__oxicon->fldmax_length}]" name="editval[oxcategories__oxicon]" value="[{$edit->oxcategories__oxicon->value}]">
            [{ oxinputhelp ident="HELP_CATEGORY_MAIN_ICON" }]
            </td>
            <td class="edittext">
            [{ if (!($edit->oxcategories__oxicon->value=="nopic.jpg" || $edit->oxcategories__oxicon->value=="" || $edit->oxcategories__oxicon->value=="nopic_ico.jpg")) }]
            <a href="Javascript:DeletePic('oxicon');" class="delete left" [{include file="help.tpl" helpid=item_delete}]></a>
            [{/if}]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_ICONUPLOAD" }]
            </td>
            <td class="edittext" colspan="2">
            <input class="editinput" name="myfile[CICO@oxcategories__oxicon]" type="file" size="26" >
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_EXTLINK" }]
            </td>
            <td class="edittext" colspan="2">
            <input type="text" class="editinput" size="42" maxlength="[{$edit->oxcategories__oxextlink->fldmax_length}]" name="editval[oxcategories__oxextlink]" value="[{$edit->oxcategories__oxextlink->value}]" [{$readonly}]>
            [{ oxinputhelp ident="HELP_CATEGORY_MAIN_EXTLINK" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_TEMPLATE" }]
            </td>
            <td class="edittext" colspan="2">
            <input type="text" class="editinput" size="42" maxlength="[{$edit->oxcategories__oxtemplate->fldmax_length}]" name="editval[oxcategories__oxtemplate]" value="[{$edit->oxcategories__oxtemplate->value}]" [{include file="help.tpl" helpid=article_template}] [{$readonly}]>
            [{ oxinputhelp ident="HELP_CATEGORY_MAIN_TEMPLATE" }]
            </td>
        </tr>

        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_DEFSORT" }]
            </td>
            <td class="edittext" colspan="2">
            <select name="editval[oxcategories__oxdefsort]" class="editinput" onChange="JavaScript:SchnellSortManager(this);">
            <option value="">[{ oxmultilang ident="CATEGORY_MAIN_NONE" }]</option>
            [{foreach from=$sortableFields key=field item=desc}]
            [{assign var="ident" value=GENERAL_ARTICLE_$desc}]
            [{assign var="ident" value=$ident|oxupper }]
            <option value="[{ $desc }]" [{ if $defsort == $desc }]SELECTED[{/if}]>[{ oxmultilang|oxtruncate:20:"..":true ident=$ident }]</option>
            [{/foreach}]
            </select>
            <input type="radio" class="editinput" name="editval[oxcategories__oxdefsortmode]" [{if !$defsort}]disabled[{/if}] value="0" [{if $edit->oxcategories__oxdefsortmode->value=="0"}]checked[{/if}]>[{ oxmultilang ident="CATEGORY_MAIN_ASC" }]
            <input type="radio" class="editinput" name="editval[oxcategories__oxdefsortmode]" [{if !$defsort}]disabled[{/if}] value="1" [{if $edit->oxcategories__oxdefsortmode->value=="1"}]checked[{/if}]>[{ oxmultilang ident="CATEGORY_MAIN_DESC" }]
            [{ oxinputhelp ident="HELP_CATEGORY_MAIN_DEFSORT" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_PRICEFROMTILL" }] ([{ $oActCur->sign }])
            </td>
            <td class="edittext" colspan="2">
            <input type="text" class="editinput" size="5" maxlength="[{$edit->oxcategories__oxpricefrom->fldmax_length}]" name="editval[oxcategories__oxpricefrom]" value="[{$edit->oxcategories__oxpricefrom->value}]" [{$readonly}]>&nbsp;
            <input type="text" class="editinput" size="5" maxlength="[{$edit->oxcategories__oxpriceto->fldmax_length}]" name="editval[oxcategories__oxpriceto]" value="[{$edit->oxcategories__oxpriceto->value}]" onchange="JavaScript:LockAssignment(this);" onkeyup="JavaScript:LockAssignment(this);" onmouseout="JavaScript:LockAssignment(this);" [{$readonly}]>
            [{ oxinputhelp ident="HELP_CATEGORY_MAIN_PRICEFROMTILL" }]
            </td>
        </tr>
        
[{* ### oxid2amazon BEGIN ### *}]
	      <td class="edittext">
	         [{ oxmultilang ident="AZ_AMZ_CATEGORY_STOCK_RESERVE" }]
	       </td>
	       <td class="edittext">
	         <input type="text" class="editinput" size="20" maxlength="[{$edit->oxcategories__az_amz_stock_reserve->fldmax_length}]" name="editval[oxcategories__az_amz_stock_reserve]" value="[{$edit->oxcategories__az_amz_stock_reserve->value}]" [{include file="help.tpl" helpid=article_stock}] [{ $readonly }]>
	       </td>
	     </tr>
[{* ### oxid2amazon END ### *}]         
        
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_VAT" }]
            </td>
            <td class="edittext" colspan="2">
            <input type="text" class="editinput" size="5" maxlength="[{$edit->oxcategories__oxvat->fldmax_length}]" name="editval[oxcategories__oxvat]" value="[{$edit->oxcategories__oxvat->value}]" [{$readonly}]>
            [{ oxinputhelp ident="HELP_CATEGORY_MAIN_VAT" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            [{ oxmultilang ident="CATEGORY_MAIN_SKIPDISCOUNTS" }]
            </td>
            <td class="edittext" colspan="2">
            <input type="hidden" name="editval[oxcategories__oxskipdiscounts]" value='0' [{$readonly_fields}]>
            <input class="edittext" type="checkbox" name="editval[oxcategories__oxskipdiscounts]" value='1' [{if $edit->oxcategories__oxskipdiscounts->value == 1}]checked[{/if}] [{$readonly_fields}]>
            [{ oxinputhelp ident="HELP_CATEGORY_MAIN_SKIPDISCOUNTS" }]
            </td>
        </tr>
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext" colspan="2"><br>
            <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="CATEGORY_MAIN_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'" [{$readonly}]><br>
            </td>
        </tr>
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext" colspan="2"><br>
                [{include file="language_edit.tpl"}]
            </td>
        </tr>


        </table>
    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext" align="left" width="50%">
    [{ if $oxid != "-1"}]

        <input [{ $readonly }] type="button" name="assignArticle" value="[{ oxmultilang ident="GENERAL_ASSIGNARTICLES" }]" class="edittext" onclick="JavaScript:showDialog('&cl=category_main&aoc=1&oxid=[{ $oxid }]');" [{if $edit->oxcategories__oxpriceto->value > 0 }] disabled [{/if}]>

    [{ /if}]
    </td>
    </tr>
</table>

</form>
[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
