describe("receipts model", function(){

  beforeEach(function(){

    this.receipts = new Receipts(fixtures.receipts);
    this.receipts.account = "test";

  });

  it("contains multiple receipt model",function(){
    expect(this.receipts.model)
      .toEqual(Receipt);
    expect(this.receipts.size())
      .toEqual(2);
    expect(this.receipts.at(1).get("receipt_id"))
      .toEqual(3);
    expect(this.receipts.at(0).get("store_name"))
      .toEqual("Mac Store");
  
  });

  describe("fetch",function(){

    beforeEach(function(){
      this.xhr = sinon.useFakeXMLHttpRequest();
      var requests = this.requests = [];

      this.xhr.onCreate = function(xhr){
        requests.push(xhr);
      }

      this.receipts = new Receipts();
      this.receipts.account = "test";
    });

    afterEach(function(){

      this.xhr.restore();

    });

    it("should set the url to receiptOperation.php",function(){
      expect(this.receipts.url).toEqual("receiptOperation.php");
    });

    it("should have the user account",function(){
      expect(this.receipts.account).toEqual("test");
    });

    it("should send post request for fetch data",function(){
      this.receipts.fetch();

      expect(this.requests[0].method).toEqual("POST");
      expect(this.requests[0].url).toEqual(this.receipts.url);
      expect(this.requests[0].requestBody).toEqual("opcode=user_get_all_receipt&acc=test");
    
    });

    it("should receive JSON data and refresh data models",function(){
      var reset = sinon.spy();
      this.receipts.bind("reset",reset);

      this.receipts.fetch();

      this.requests[0].respond(200, 
        { "Content-Type": "application/json"},
        JSON.stringify(fixtures.receipts)
      );

      expect(this.receipts.size()).toEqual(2);
      expect(this.receipts.at(0).get("receipt_id")).toEqual(2);
      expect(reset.calledOnce).toBeTruthy();
    });

  });

  describe("search",function(){

    beforeEach(function(){
      this.xhr = sinon.useFakeXMLHttpRequest();
      var requests = this.requests = [];

      this.xhr.onCreate = function(xhr){
        requests.push(xhr);
      }

      this.receipts = new Receipts();
      this.receipts.account = "test";
    });

    afterEach(function(){
      this.xhr.restore();
    });

    it("should send post request with keys",function(){
    
      this.receipts.searchByKeys(["test","test2"]);

      expect(this.requests[0].method).toEqual("POST");
      expect(this.requests[0].url).toEqual(this.receipts.url);
      expect(this.requests[0].requestBody).toEqual(
        "opcode=search&keys%5B%5D=test&keys%5B%5D=test2");

    });

    it("should send post request with tags",function(){
    
      this.receipts.searchByTags(["test","test2"]);

      expect(this.requests[0].method).toEqual("POST");
      expect(this.requests[0].url).toEqual(this.receipts.url);
      expect(this.requests[0].requestBody).toEqual(
        "opcode=search&tags%5B%5D=test&tags%5B%5D=test2");
    });
  });
});
