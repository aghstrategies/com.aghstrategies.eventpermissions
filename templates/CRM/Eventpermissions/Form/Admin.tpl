<p>{ts domain="com.aghstrategies.eventpermissions"}Event Permissions restricts event management to the following users (who must also have "Access CiviCRM" and "Access CiviEvent" permissions):{/ts}</p>
<ul>
  <li>{ts domain="com.aghstrategies.eventpermissions"}users with the "Administer CiviCRM" permission,{/ts}</li>
  <li>{ts domain="com.aghstrategies.eventpermissions"}the user who created an event, or{/ts}</li>
  <li>{ts domain="com.aghstrategies.eventpermissions"}users registered for the event with one of the roles selected here.{/ts}</li>
</ul>
<p>{ts domain="com.aghstrategies.eventpermissions"}For example, if you want event hosts to be able to edit their events, you can select "Host" as the Permissioned Role.{/ts}</p>

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
