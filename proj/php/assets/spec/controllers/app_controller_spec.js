var controller = new AppController();

describe("AppController",function(){

  it("should initialize user account and model from cookie",function(){
    
    readCookie = sinon.stub();
    readCookie.returns("test");
    this.controller = new AppController();

    expect(readCookie.calledWith("user_acc")).toBeTruthy();

    expect(this.controller.user.get("account")).toEqual("test");

  });

  it("should initialize receiptsView and userView",function(){
    this.controller = new AppController();

    expect(appView.model).toEqual(this.controller.receipts);
    expect(userView.model).toEqual(this.controller.user);
  });

  describe("route",function(){

    beforeEach(function(){
      this.controller = new AppController();
    });
    
    it("should match index and fetch receipts",function(){
      
      var index = sinon.spy();
      controller.bind("route:index",index);

      window.location.hash = "";
      Backbone.history.start();

      expect(index.called).toBeTruthy();

      controller.unbind("route:index");
    });

  });

});
