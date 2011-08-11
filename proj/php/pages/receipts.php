<div id="receipts-view">
  <nav id="action-bar">
    <div id="action-bar-inner">
      <h4>tags</h4>
      <ul class="action"></ul>
    <div>
  </nav>
  <div id="content">
    <div id="receipts">
      <div id="search-bar" class="clearfix">
        <div>
          <input id="search-query" type="text" placeholder="search"/>
        </div>
        <div>
          <button id="search-button" type="submit" title="search"></button>
          <div id="search-type">
            <input type="radio" name="type" value="keys" checked/>Name
            <input type="radio" name="type" value="tags"/>Tags
          </div>
        </div>
      </div>
      <table id="receipts-table">
        <td id="ajax-loader" colspan="4" align="center"><img width="150" src="assets/img/ajax-loader.gif"/></td>
      </table>
      <div class="receipts-stat">
        <span class="stat"></span>
        <button class="more">more</button>
      </div>
    </div>
  </div>
</div>
