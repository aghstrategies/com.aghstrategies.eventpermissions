<table>
{foreach from=$events key=eventId item=eventTitle}
  <tr>
    <td>{$eventTitle.title}</td>
    <td>{$eventTitle.links}<br/>{$eventTitle.participantLinks}<br/>{$eventTitle.eventLinks}</td>
  </tr>
{/foreach}
</table>
<div class="copy_event-section">
  Copy an event: <input name="copy_event" placeholder="{ts}- select event to copy -{/ts}"/>
</div>

<div class="description">{ts}Recently created or copied events will not appear in this listing until you click "Refresh Dashboard Data"{/ts}</div>

{* Script included in template because widgets don't allow for adding things to
the html-header region. *}
{literal}
<script type="text/javascript">
CRM.$(function($) {
  $(CRM.vars.eventPermissions.dashletId).children('.widget-wrapper').css("overflow-x", "visible");

  $('[name="copy_event"]').crmEntityRef({
    entity: 'event',
    select: {minimumInputLength: 0}
  }).change(function() {
    window.location.href = CRM.url('civicrm/event/manage', 'reset=1&action=copy&id=' + $('[name="copy_event"]').val())
  });
});
</script>
{/literal}
