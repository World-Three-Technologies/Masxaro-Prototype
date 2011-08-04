describe("receipt model",function(){
  
  beforeEach(function(){
    
    this.receipt = new Receipt(fixtures.receipt);
  });
  
  describe("should have attributes",function(){
  
    it("should contain id, cost, store_name ,tax and time",function(){
      expect(this.receipt.get("receipt_id")).toEqual(fixtures.receipt.receipt_id);
      expect(this.receipt.get("total_cost")).toEqual(208.00);
      expect(this.receipt.get("receipt_time")).toEqual("2011-06-24 16:13:28");
      expect(this.receipt.get("store_name")).toEqual("McDonalds(NYU)");
      expect(this.receipt.get("tax")).toEqual("0.0875");
    });

    it("should have items and attributes of item", function(){
      
      expect(this.receipt.get("items").length).toEqual(2);
      expect(this.receipt.get("items")[0].item_id).toEqual(3);
      expect(this.receipt.get("items")[1].item_name).toEqual("harry_potter II");
      expect(this.receipt.get("items")[1].item_qty).toEqual(1);
      expect(this.receipt.get("items")[1].item_price).toEqual("19.99");
    });
  });

  describe("api",function(){
    it("should be able to update receipt",function(){
      this.receipt.save();
    });

  })
});
