<table>
{foreach from=$events key=eventId item=eventTitle}
  <tr>
    <td>{$eventTitle.title}</td>
    <td>{$eventTitle.links}</td>
  </tr>
{/foreach}
</table>
