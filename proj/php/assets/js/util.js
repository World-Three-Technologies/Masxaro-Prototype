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
//require jquery validate
//the form receive result 1 for success, and redirect to dest attribute, 
//or alert message attribute
//ex: <form action="/signin" message="success" dest="/portal"></form>
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
        var el = $(v);
        var name = el.attr("name");
        if(name.indexOf("-") > -1){
          var field = name.split("-");
          if(typeof data[field[0]] == "undefined" || data[field[0]] == null){
            data[field[0]] = {};
          }
          data[field[0]][field[1]] = el.val();
        }else{
          data[name] = el.val();
        }
      });
      $.post(action,data).success(function(data){
        if(data != "1" && data != ""){
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
