[{include file="popups/headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
	
    <table width="100%">
        <tr>
        	<td class="edittext" style="font-weight:bold" width="20%">[{ oxmultilang ident="GENERAL_ITEMNR" }]</td>
        	<td class="edittext" style="font-weight:bold" width="50%">[{ oxmultilang ident="GENERAL_ARTICLE_OXTITLE" }]</td>
        	[{if $blExportVariants}]        	
        	<td class="edittext" style="font-weight:bold" width="30%">[{ oxmultilang ident="GENERAL_ARTICLE_OXVARCOUNT" }]</td>
        	[{/if}]
       	</tr>
       	[{foreach from=$aArticles item=oArticle}]
       	<tr>
       		<td class="edittext">[{$oArticle->oxarticles__oxartnum->value}]</td>
       		<td class="edittext">[{$oArticle->oxarticles__oxtitle->value}]</td>
       		[{if $blExportVariants}]
       		<td class="edittext">[{if $oArticle->oxarticles__oxvarcount->value > 0}][{$oArticle->oxarticles__oxvarcount->value}][{else}]-[{/if}]</td>
       		[{/if}]       		
       	</tr>
       	[{/foreach}]
       	       
    </table>

</body>
</html>