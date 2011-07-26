<script type="text/tamplate" id="receipt-row-template">
  <div class="receipt-row">
    <div class="date"></div>
    <div class="content">
      <div class="items"></div>
      <div class="store">at <%= store_name %> 
        <span class="tags">
        <% _.each(tags,function(tag){ %>
        <span class='tag'>
          <a href='index.php#tag/<%= tag %>'>
            <%=tag%>
          </a>
        </span>
      <% }); %></span></div>
    </div>
    <div class="total-cost">$<%= total_cost %></div>
  </div>
</script>

<script type="text/template" id="receipt-full-template">
  <div class="receipt clearfix">
    <div class="toolbar close">close[x]</div>
    <div class="date"></div>
    <div class="content">
      <div class="store">
        <%= store_name %> 
        <span class="tags">
        <% _.each(tags,function(tag){ %>
          <span class='tag'>
            <a href='index.php#tag/<%= tag %>'>
              <%= tag %>
            </a>
          </span>
        <% }); %>
        </span> 
        <span class='edit edit-area'>
        <% _.each(tags,function(tag){ %>
          <span>
          <input type="text" class="edit-tag" value="<%= tag%>" size="10"></input>
          <span class="delete-btn" tag-data='<%= tag %>'>[X]</span>
          </span>
        <% }); %>
        </span>
        <span class="edit add-button">[add]</span>
        <span class="edit-button">[edit]</span>
      </div>
      <div class="items"></div> 
      <hr class="separator"/>
      <div class="total-cost">$<%= total_cost %></div>
    </div>
  </div>
</script>

<script type="text/template" id="receipt-item-template">
<div class="receipt-item">
  <div class="display">
    <span class="item_name">
      <%= item_name %> 
    </span>
    <span class="item_price">
      $<%= item_price %>
    </span>
    <span class="item_qty">
      X <%= item_qty %>
    </span>
  </div>
</div>
</script>
