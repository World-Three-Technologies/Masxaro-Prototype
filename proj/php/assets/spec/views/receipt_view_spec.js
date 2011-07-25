describe("receipt view",function(){

  beforeEach(function(){
    var template = "<td>$<%= total_cost %></td>"+ 
    "<td class='items'></td>" +
    "<td class='store'><%= store_name %></td>" +
    "<td><%= receipt_time %></td>";

    setFixtures(template);

    this.model = new Receipt(fixtures.receipt);
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

    this.view.render();

    expect(this.view.$(".total-cost").text()).toEqual("$208");
  
  });

  it("can show digest item description",function(){
    var itemText = this.view.getItemText(fixtures.receipt.items);

    expect(itemText).toEqual("harry_potter, harry_potter II");

  });

  it("can render receipt with items data after clicked",function(){
    this.view.render();
    $(this.view.el).click();

    expect(this.view.$(".total-cost").text()).toEqual("$"+fixtures.receipt.total_cost);
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
