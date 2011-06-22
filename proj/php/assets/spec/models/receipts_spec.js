describe("receipts model", function(){

  beforeEach(function(){

    this.fixture = [{
        "receipt_id":2, 
        "store_name":"Mac Store",
        "receipt_time":"2011-6-17 09:12:32"
      }, {
        "receipt_id":3, 
        "store_name":"Mac Store",
        "receipt_time":"2011-6-17 09:12:32"
      }];

    this.receipts = new Receipts(this.fixture);
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

  describe("send request to fetch data from backend",function(){

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
      expect(this.receipts.url).toEqual("/receiptOperation.php");
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
      var refresh = sinon.spy();
      this.receipts.bind("refresh",refresh);

      this.receipts.fetch();

      this.requests[0].respond(200, 
        { "Content-Type": "application/json"},
        JSON.stringify(this.fixture)
      );

      expect(this.receipts.size()).toEqual(2);
      expect(this.receipts.at(0).get("receipt_id")).toEqual(2);
      expect(refresh.calledOnce).toBeTruthy();
    });
  });
});
