describe("user model", function() {

  beforeEach(function() {
    this.user = new User({
      account:"test",
      flash : "flash message"
    });
  });
  
  it("should have an account name",function(){
    expect(this.user.get("account"))
      .toEqual("test");

  });

  it("should have a flash message",function(){
    expect(this.user.get("flash"))
      .toEqual("flash message");

  });
});
