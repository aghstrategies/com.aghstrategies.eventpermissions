<table>
{foreach from=$events key=eventId item=eventTitle}
  <tr>
    <td>{$eventTitle.title}</td>
    <td>{$eventTitle.links}<br/>{$eventTitle.participantLinks}<br/>{$eventTitle.eventLinks}</td>
  </tr>
{/foreach}
</table>
