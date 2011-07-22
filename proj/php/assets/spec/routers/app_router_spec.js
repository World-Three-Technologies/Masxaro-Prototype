describe("AppRouter",function(){

  beforeEach(function(){
    readCookie = sinon.stub();
    readCookie.returns("test");
    this.router = new AppRouter();
  });

  it("should initialize user account and model from cookie",function(){

    expect(readCookie.calledWith("user_acc")).toBeTruthy();
    expect(this.router.user.get("account")).toEqual("test");

  });

  it("should initialize receiptsView and userView",function(){
    expect(appView.model).toEqual(this.router.receipts);
    expect(userView.model).toEqual(this.router.user);
  });

  describe("routes",function(){
    
    it("should match index and fetch receipts",function(){
      
      var index = sinon.spy();
      this.router.bind("route:index",index);

      this.router.navigate("index",true);

      expect(index.called).toBeTruthy();

      this.router.unbind("route:index");
      this.router.navigate("",false);
    });

  });

});
