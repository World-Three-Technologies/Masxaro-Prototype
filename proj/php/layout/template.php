<script type="text/tamplate" id="receipt-row-template">
  <div class="receipt-row">
    <div class="date"></div>
    <div class="content">
      <div class="items"></div>
      <div class="store">at <%= store_name %> <span class="tag">
        <a href="index.php#category/<%= receipt_category %>">
          <%= receipt_category %>
        </a>
      </span></div>
    </div>
    <div class="total-cost">$<%= total_cost %></div>
  </div>
</script>

<script type="text/template" id="receipt-full-template">
  <div class="receipt clearfix">
    <div class="toolbar close">close[x]</div>
    <div class="date"></div>
    <div class="content">
    <div class="store"><%= store_name%> <span class="tag">
    <a href="index.php#category/<%= receipt_category %>">
      <%= receipt_category %>
    </a>
    </span></div>
      <div class="items"></div> 
      <hr style="border-top:1px black solid;margin:0;"/>
      <div class="total-cost">$<%= total_cost %></div>
    </div>
  </div>
</script>

<script type="text/template" id="receipt-item-template">
<div class="receipt-item" id-data="<%= item_id %>">
  <div class="display">
    <span class="item_name">
    <%= item_name %> 
    </span>
      <span class="tag">
        <a href="index.php#category/<%= item_category %>" class="item_category">
          <%= item_category %>
        </a>
      </span>
    <span class="item_price">
      $<%= item_price %>
    </span>
    <span class="item_qty">
      X <%= item_qty %>
    </span>
  </div>
  <div class="edit">
    <input type="text" class="item_name" value="<%= item_name %>" />
    <input type="text" class="item_category" value="<%= item_category %>"/>
  </div>
</div>
</script>
