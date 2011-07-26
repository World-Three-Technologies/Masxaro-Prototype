var ReceiptView = Backbone.View.extend({

  tagName:"tr",
  className:"row",
  template:_.template($('#receipt-row-template').html() || "<div/>"),
  fullTemplate:_.template($('#receipt-full-template').html() || "<div/>"),
  itemTemplate:_.template($('#receipt-item-template').html() || "<div/>"),
  isEditing:false,

  initialize:function(){
    _.bindAll(this,'render','showReceipt','getItemText',
              'editTags','getTags');
    this.model.bind('change',this.showReceipt);
  },

  events:{
    "click .receipt-row" : "showReceipt",
    "click .close" :"render",
    "click .add-button" : "newTag",
    "click .edit-button" : "editTags"
  },


  render:function(){
    console.log("rendering...");
    var view = $(this.el);
    view.html(this.template(this.model.toJSON()));

    var text = this.getItemText(this.model.get("items"));
    view.find(".items").html(text);
    view.find(".date").html(new Date(this.model.get("receipt_time")).format());
    return this;
  },

  editTags:function(){
    var content = $(event.target).parent().parent();
    if(!this.isEditing){
      content.addClass("editing");
      this.$('.edit-button').text("[save]");
      this.isEditing = true;
    }else{
      content.removeClass("editing");
      this.model.set({tags: this.getTags() });
      this.model.updateTags();
      this.$('.edit-button').text("[edit]");
      this.isEditing = false;
    }
  },

  getTags:function(){
    var tags = [];
    _.each(this.$(".edit-tag"),function(tag){
      tags.push($(tag).val());
    });     
    return tags;
  },

  newTag:function(){
    var tag = $("<input type='text' size='10' class='edit-tag'/><span class='delete-btn'>[X]</span>");
    this.$('.edit-area').append(tag);     
  },

  deleteTag:function(){
            
  },

  showReceipt:function(){

    if(window.lastOpen && window.lastOpen != this){
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
