var readCookie =function(name){
  var nameEQ = name + "=";
  var ca = document.cookie.split(";");
  for(var i = 0; i<ca.length;i++){
    var c = ca[i];
    while (c.charAt(0)== ' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
}


//date format

Date.prototype.format = function(){
  var monthName = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Dec","Nov"];

  return monthName[this.getMonth()] + " " + this.getDate();
}

//change post form to ajax form
var bindAjax = function(el){
  var target = $(el),
      action = target.attr("action"),
      data = {},
      attrs = target.find("input"),
      dest = target.attr("dest"),
      message = target.attr("message");

  target.validate({
    submitHandler:function(form){

      $.each(attrs,function(i,v){
        var el = $(v)
        data[el.attr("name")] = el.val();
      });

      $.post(action,data).success(function(data){
        if(data != "u1"){
          alert(data);
          return;
        }
        if(message){
          alert(message);
        }
        if(dest){
          location.href = dest;
        }
      });
    }
  });
}
