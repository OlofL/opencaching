<table class="input_coord">
  <tr>
    <td>
      <select name="lat_hem" class="input40">
        <option value="N" {if $lat_hem == 'N'} selected {/if}>{t}N{/t}</option>
        <option value="S" {if $lat_hem == 'S'} selected {/if}>{t}S{/t}</option>
      </select>
    </td>
    <td>
      <input type="text" name="lat_deg" maxlength="2" value="{$lat_deg}" class="input30" /> &deg;
    </td>
    <td>
      <input type="text" name="lat_min" maxlength="6" value="{$lat_min}" class="input50" /> '
    </td>
  </tr>
  <tr>
    <td>
      <select name="lon_hem" class="input40">
        <option value="E" {if $lat_hem == 'E'} selected {/if}>{t}E{/t}</option>
        <option value="W" {if $lat_hem == 'W'} selected {/if}>{t}W{/t}</option>
      </select>
    </td>
    <td>
      <input type="text" name="lon_deg" maxlength="3" value="{$lon_deg}" class="input30" /> &deg;
    </td>
    <td>
      <input type="text" name="lon_min" maxlength="6" value="{$lon_min}" class="input50" /> '
    </td>
  </tr>

  {if isset($coord_error)}
  <tr>
    <td  colspan="3">
      {$coord_error}
    </td>
  </tr>
  {/if}

</table>
