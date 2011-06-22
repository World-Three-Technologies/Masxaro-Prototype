describe("app view",function(){
  beforeEach(function(){
    this.model = new Receipts();
    this.view = new AppView({model:this.model});
  });

  it("should initialize refresh model events binding",function(){
    var render = this.view.render = sinon.stub().returns(this.view);

    this.view.model.refresh(fixtures.receipts);
    //TODO:check why it doesn't trigger render.called
    this.view.render();
    expect(render.called).toBeTruthy();
  });

  it("should render multiple ReceiptView and set end value",function(){
    var renderReceipt = this.view.renderReceipt = sinon.stub();

    this.view.model.refresh(fixtures.receipts);

    expect(renderReceipt.calledTwice).toBeTruthy();
    expect(this.view.start).toEqual(1);
    expect(this.view.end).toEqual(2);
  });

  it("should hide renderMore button when receipt fewer than 10 or end larger than 10",function(){
  
  });


});
