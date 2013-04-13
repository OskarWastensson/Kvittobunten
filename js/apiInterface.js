// Adds an interface for interaction with the api to a jquery element

$.fn.extend({
  setCursorPosition : function (pos) {
    if ($(this).get(0).setSelectionRange) {
      $(this).get(0).setSelectionRange(pos, pos);
    } else if ($(this).get(0).createTextRange) {
      var range = $(this).get(0).createTextRange();
      range.collapse(true);
      range.moveEnd('character', pos);
      range.moveStart('character', pos);
      range.select();
    }
  },
  clearForm : function () {
    $(this).find('input, option:selected').each(function (index, element) {
      var input = $(element);
      input.val('');
    });
  }, 
  addInterface : function(funcName, url, type) {
    var func, self = this;

    function getDataFromInputs() {
      self.find('input, select').each(function (index, element) {
        var input = $(element),
          id = input.attr('id');
        if (id) {
          if (input.hasClass('currency')) {
            self.parameters[id] = input.asNumber({parseType:'int'});
          } else {
            self.parameters[id] = input.val();
          }
        }
      });
    }

    if (!self.parameters) {
      self.parameters = {};
    }

    url = 'index.php?q=' + url;

    switch (type) {
    case 'page':
      self[funcName] = function () {
        $.mobile.changePage(url, { transition: "fade" });
        return self;
      };
      break;
    case 'element':
      self[funcName] = function (success) {
        $.get(url, self.parameters, function (reply) {
          if (!reply.error) {
            self.html(reply);
            if (success) {
              success();
            }
          }
          // error handling
        }, 'html');
        return self;
      };
      break;
    case 'package':
      self[funcName] = function (success) {
        $.get(url, self.parameters, function (reply) {
          if (!reply.error) {
            self.parameters = reply.data;
            self.prepend(reply.html);
            self.template = twig({data: reply.template});
            if (success) {
              success(reply);
            }
          }
          // error handling
        }, 'json');
        return self;
      };
      break;
    case 'refresh':
      self[funcName] = function (success) {
        $.get(url, function (reply) {
          if (!reply.error) {
            self.parameters = reply.data;
            self.prepend(self.template(reply.data));
            if (success) {
              success();
            }
          }
          // error handling
        });
        return self;
      };
      break;
    case 'template':
      self[funcName] = function (success) {
        $.get(url, function(reply) {
          if (!reply.error) {
            self.template = twig({data: reply.template});
            self.prepend(self.template(self.parameters));
            if (success) {
              success();
            }
          }
          // error handling
        });
        return self;
      };
      break;
    case 'create':
      self[funcName] = function (success) {
        getDataFromInputs();
        $.post(url, self.parameters, function(reply) {
          if (!reply.error) {
            if (success) {
              success(reply);
            }
          }
        }, 'json');
        return self;
      };
      break;
    case 'update':
      self[funcName] = function (success) {
        getDataFromInputs();
        $.ajax(url, {
          data: self.parameters,
          type: "put",
          dataType: "json",
          success: function(reply) {
            if (!reply.error) {
              if (success) {
                success(reply);
              }
            }
          }
        });
        return self;
      };
      break;
    case 'destroy':
      self[funcName] = function (success) {
        $.ajax(url, {
          type: "delete",
          success: function(reply) {
            if (!reply.error) {
              if (success) {
                success();
              }
            }
          }
        });
        return self;
      };
      break;
    default:
    }
    return self;
  }
});