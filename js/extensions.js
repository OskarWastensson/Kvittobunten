//
// @FILE
// - Jquery extensions specific to Kvittobunten
// 
// Author: oskar@wastensson.se
//

$.fn.extend({
  // Marks content in an input field (not currently used)
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
  // Clears the input fields of a form
  clearForm : function () {
    $(this).find('input, option:selected').each(function (index, element) {
      var input = $(element);
      input.val('');
    });
    return this;
  },
  // Adds a function that provides (ajax) interaction with 
  // the api to a jquery element.
  addInterface : function(funcName, url, type) {
    var func, self = this;

    // Helper (private scope) function that converts 
    // form input values to parameters
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
    
    // Define paramaters as object if not already set
    if (!self.parameters) {
      self.parameters = {};
    }
    
    // Convert to non-clean url
    url = 'index.php?q=' + url;

    switch (type) {
    case 'page':
      // Page load function
      self[funcName] = function () {
        $.mobile.changePage(url, { transition: "fade" });
        return self;
      };
      break;
    case 'element':
      // Fetch html element
      self[funcName] = function (success) {
        $.get(url, self.parameters, function (reply) {
          if (!reply.error) {
            self.html(reply);
            if (success) {
              success();
            }
          }
          // @TODO: error handling
        }, 'html');
        return self;
      };
      break;
    case 'package':
      // Fetch data, twig template and rendered element
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
          // @TODO: error handling
        }, 'json');
        return self;
      };
      break;
    case 'refresh':
      // Fetch data and re-render template
      self[funcName] = function (success) {
        $.get(url, function (reply) {
          if (!reply.error) {
            self.parameters = reply.data;
            self.prepend(self.template(reply.data));
            if (success) {
              success();
            }
          }
          // @TODO: error handling
        });
        return self;
      };
      break;
    case 'template':
      // Fetch template
      self[funcName] = function (success) {
        $.get(url, function(reply) {
          if (!reply.error) {
            self.template = twig({data: reply.template});
            self.prepend(self.template(self.parameters));
            if (success) {
              success();
            }
          }
          // @TOOD: error handling
        });
        return self;
      };
      break;
    case 'create':
      // Create a new record from form data 
      self[funcName] = function (success) {
        getDataFromInputs();
        $.post(url, self.parameters, function(reply) {
          if (!reply.error) {
            if (success) {
              success(reply);
            }
            // @TODO: error handling
          }
        }, 'json');
        return self;
      };
      break;
    case 'update':
      // Update an existing record from form data
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
        // @TODO: error handling
        return self;
      };
      break;
    case 'destroy':
      // Delete a record
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
        // @TODO: error handling
        return self;
      };
      break;
    default:
      // @TODO: error handling
    }
    return self;
  }
});