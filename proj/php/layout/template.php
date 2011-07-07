<script type="text/tamplate" id="receipt-row-template">
  <div>
    <div class="date"></div>
    <div class="content">
      <div class="items"></div>
      <div class="store">at <%= store_name %></div>
    </div>
    <div class="total-cost">$<%= total_cost %></div>
  </div>
</script>

<script type="text/template" id="receipt-full-template">
  <div class="receipt">
    <div class="date"></div>
    <div class="toolbar">
      show receipt, edit, delete, add tags
    </div>
  </div>
  <div class="clearfix"></div>
  <div class="items"></div> 
  <div class="total-cost" style="float:left;padding-left:60px;">$<%= total_cost %></div>
  <div class="store">at <%= store_name%></div>
</script>

<script type="text/template" id="receipt-item-template">
<div class="receipt-item">
  <span class="item_name">
    <%= item_name %>
  </span>
  <span class="item_qty">
    <%= item_qty %>
  </span>
  <span class="item_price">
    <%= item_price %>
  </span>
</div>
</script>
