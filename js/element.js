/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


var element = function () {
  var view = function() {
    $.get(this.resourceName, function(reply) {
      if(reply.error) {
        // handle errors
      } else {
        
      }
    })
  }
}