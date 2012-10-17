[{* Enter your custom HTML here *}]
[{* ### oxid2amazon BEGIN ### *}]
[{if $bottom_buttons->az_amz_destinations_new }]
<li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="AZ_AMZ_DESTINATION_BUTTON_NEW" }]</a> |</li>
[{/if}]
[{* ### oxid2amazon END ### *}]