<table>
{foreach from=$events key=eventId item=eventTitle}
  <tr>
    <td>{$eventTitle.title}</td>
    <td>{$eventTitle.links}<br/>{$eventTitle.participantLinks}<br/>{$eventTitle.eventLinks}</td>
  </tr>
{/foreach}
{* Make the overflow on this widget visible.  Otherwise, opening the links makes
the widget scroll within its original space. Script included in template because
widgets don't allow for adding things to the html-header region. *}
{literal}
<script type="text/javascript">
CRM.$( function($) {
  $(CRM.vars.eventPermissions.dashletId).children('.widget-wrapper').css("overflow-x", "visible");
});
</script>
{/literal}
</table>
