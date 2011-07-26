var ReceiptView = Backbone.View.extend({

  tagName:"tr",
  className:"row",
  template:_.template($('#receipt-row-template').html() || "<div/>"),
  fullTemplate:_.template($('#receipt-full-template').html() || "<div/>"),
  itemTemplate:_.template($('#receipt-item-template').html() || "<div/>"),

  initialize:function(){
    _.bindAll(this,'render','showReceipt','getItemText','editTags','saveTags');
    this.model.bind('change',this.render);
  },

  events:{
    "click .receipt-row" : "showReceipt",
    "click .edit-button" : "editTags",
    "click .close" :"render",
    "click .add-button" : "newTag",
  },

  render:function(){
    var view = $(this.el);
    view.html(this.template(this.model.toJSON()));

    var text = this.getItemText(this.model.get("items"));
    view.find(".items").html(text);
    view.find(".date").html(new Date(this.model.get("receipt_time")).format());
    return this;
  },

  editTags:function(){
    var content = $(event.target).parent().parent();
    content.addClass("editing");
    this.$('.edit-button').text("[save]");
    $(event.target).unbind("click");
  },

  saveTags:function(){
    var content = $(event.target).parent().parent();
    console.log(content.hasClass("editing"));
    content.removeClass("editing");
    this.$('.edit-button').text("[edit]");
//      .unbind("click",this.saveTags)
//      .bind("click",this.editTags);
  },

  newTag:function(){
    var tag = $("<input type='text' size='10'/>");
    this.$('.edit-area').append(tag);     
  },

  deleteTag:function(){
            
  },

  showReceipt:function(){

    if(window.lastOpen){
      window.lastOpen.render();
    }

    $(this.el).html(this.fullTemplate(this.model.toJSON()));
    $(this.el).find(".date").html(new Date(this.model.get("receipt_time")).format());

    var items = $(this.el).find(".items"),
        self = this;

    _.each(self.model.get("items"),function(model){
      items.append(self.itemTemplate(model));
    });

    window.lastOpen = this;
  },

  getItemText:function(items){
    return _.reduce(items,function(memo,item){
      return memo + item.item_name + ", ";
    },"").slice(0,-2);
  },

  getLinkTags:function(tags){
    return _.reduce(tags,function(html,tag){
      return html + "<span class='tag'><a href='index.php#tag/"+tag+"'>"
                  + tag + "</a></span>";
    },"");        
  }
});
