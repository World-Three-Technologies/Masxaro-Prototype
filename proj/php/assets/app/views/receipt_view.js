//render receipt in row and full mode
//handle tag updates
var ReceiptView = Backbone.View.extend({

  tagName:"tr",
  className:"row",
  template:_.template($('#receipt-row-template').html() || "<div/>"),
  fullTemplate:_.template($('#receipt-full-template').html() || "<div/>"),
  itemTemplate:_.template($('#receipt-item-template').html() || "<div/>"),
  editTagArea :"<span><input type='text' size='10' class='edit-tag'/><span class='delete-button'/></span>",
  isEditing:false,
  tagState:[],

  initialize:function(){
    _.bindAll(this,'render','showReceipt','getItemText',
              'editTags','getTags','setDate','animateReceipt');
    this.model.bind('change',this.showReceipt);
  },

  events:{
    "click .receipt-row" : "animateReceipt",
    "click .close" :"render",
    "click .add-button" : "newTag",
    "click .edit-button" : "editTags",
    "click .delete-button" : "deleteTag"
  },

  render:function(){
    var view = $(this.el);
    //set height for animation effect
    view.css({height:70})
        .html(this.template(this.model.toJSON()));

    this.setDate(this.model.get("receipt_time"));

    view.find(".items").html(
      this.getItemText(this.model.get("items"))
    );

    return this;
  },

  //edit or save tags
  editTags:function(){
    //sequence is important here...
    var content = this.$(".content");

    if(this.isEditing){
      //save tags
      content.removeClass("editing");
      this.isEditing = false;

      this.model.set({tags: this.getTags() });
      this.model.updateTags(this.tagState);

      this.$('.edit-button').text("[edit]");
    }else{
      //edit tags
      content.addClass("editing");
      this.isEditing = true;

      this.$('.edit-button').text("[save]");
      this.tagState = this.model.get("tags");
    }
  },

  //collect tags data from input
  getTags:function(){
    return _.map(this.$(".edit-tag"),function(tag){
      return $(tag).val();
    });     
  },

  newTag:function(){
    this.$('.edit-area').append($(this.editTagArea));     
  },

  deleteTag:function(){
    var tag = $(event.target).prev().val();
    $(event.target).parent().remove();
    var tags = this.model.get("tags");
    this.model.set({tags: _.without(tags,tag)});
  },

  animateReceipt:function(){

    //hard coded animation height by item height
    var itemLength = this.model.get("items").length * 26 + 106;
          
    $(this.el).css({height:itemLength,opacity:0});

    //close last opened view
    if(ReceiptView.lastOpen && ReceiptView.lastOpen != this){
      ReceiptView.lastOpen.render();
    }
    ReceiptView.lastOpen = this;
    setTimeout(this.showReceipt,300);
  },

  //render full receipt
  showReceipt:function(){

    $(this.el).html(this.fullTemplate(this.model.toJSON())).css({opacity:1});
    this.setDate(this.model.get("receipt_time"));

    var items = $(this.el).find(".items"),
        view = this;

    _.each(this.model.get("items"),function(model){
      items.append(view.itemTemplate(model));
    });

    if(this.isEditing){
      this.$(".content").addClass("editing");
      this.$('.edit-button').text("[save]");
    }
  },

  getItemText:function(items){
    return _.map(items,function(item){
      return item.item_name;
    }).join(", ");
  },

  setDate:function(date){
    var receipt_time = date.replace(/-/g,"/"); 
    $(this.el).find(".date").html(new Date(receipt_time).format());
  }
});
