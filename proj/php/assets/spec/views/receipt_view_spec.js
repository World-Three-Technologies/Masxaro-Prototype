describe("receipt view",function(){

  beforeEach(function(){
    this.fixtures = {
      model:{
        "receipt_id":1,
        "store_name":"Mac store",
        "total_cost":100,
        "receipt_time":"2011-6-17 09-45-32",
        "tax": 0.875,
        "items":[
          {
            "item_name":"mac",
            "item_qty": 1,
            "item_price": 50.00
          },{
            "item_name":"mac",
            "item_qty": 1,
            "item_price": 50.00
          }
        ] 
      },
      template:"<td>$<%= total_cost %></td>"+ 
    "<td class='items'></td>" +
    "<td><%= store_name %></td>" +
    "<td><%= receipt_time %></td>",

    }
    setFixtures(this.fixtures.template);

    this.model = new Receipt(this.fixtures.model);
    this.view = new ReceiptView({model:this.model});

  });
  
  it("can initialize with receipt model and bind with model's change event",function(){

    this.view.render = sinon.spy();
    //TODO: find why the change event wont trigger render
    this.view.model.set({"total_cost":99});
    this.view.model.change();
    this.view.render();

    expect(this.view.render.called).toBeTruthy();
  });
  
  it("have tr tag and .row class as root element",function(){
    expect($(this.view.el)).toHaveClass("row");
    expect($(this.view.el)).toBe("tr");
  });

  it("can render model data with template",function(){
    //TODO: remove the template from html fixture or inject it

    this.view.render();

    expect(this.view.$("td").first().text()).toEqual("$100");
  
  });

  it("can show digest item description",function(){
    var itemText = this.view.getItemText(this.fixtures.model.items);

    expect(itemText).toEqual("mac,mac");

  });

  it("can render receipt with items data after clicked",function(){
    this.view.render();
    $(this.view.el).click();

    expect(this.view.$(".store").text()).toEqual("at "+ this.fixtures.model.store_name);
  });

  it("can hide other opened receipt after clicked",function(){

    this.view.render();
    this.view.showReceipt();

    var render = window.lastOpen.render = sinon.spy();

    this.view2 = new ReceiptView({model:this.model});
    this.view2.showReceipt();

    expect(render.called).toBeTruthy();
  });
});
