describe("user view",function(){

  beforeEach(function(){

    setFixtures('<div id="username"/><div id="user"><div id="user-flash"/></div>');

    this.userFixture = {
      account:"test",
      flash:"flash message"
    };
    this.user = new User(this.userFixture);
    this.view = new UserView({model:this.user});

  });

  it("have user model and binding to #user",function(){
    expect(this.view.model.get("account")).toEqual(this.userFixture.account);
    expect(this.view.model.get("flash")).toEqual(this.userFixture.flash);
    expect(this.view.el).toHaveId('user');

  })
  
  it("can set user name to #username",function(){
    expect($("#username").text()).toEqual(this.userFixture.account);
  });

  it("can set notice message to flash",function(){
    expect(this.view.$("#user-flash").text()).toEqual(this.userFixture.flash);
  });
});
