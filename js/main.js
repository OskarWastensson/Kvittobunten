// @FILE
//   - Events and behaviours for Kvittobunten
//
// Author: oskar@wastensson.se

$(document).bind("pageinit", function () {

  // Define page loads
  var doc = $(document)
    .addInterface('pageLogin', 'pageLogin', 'page')
    .addInterface('pageBudget', 'pageBudget', 'page')
    .addInterface('pageAddTransaction', 'pageAddTransaction', 'page')
    .addInterface('pageOverview', 'pageOverview', 'page')
    .addInterface('pageBrowse', 'pageBrowse', 'page');
  
  // Links
  $('.linkOverview').bind('click', function (event, ui) {
    event.preventDefault();
    doc.pageOverview();
  });
  $('.linkAddTransaction').bind('click', function (event, ui) {
    event.preventDefault();
    doc.pageAddTransaction();
  });
  $('.linkBudget').bind('click', function (event, ui) {
    event.preventDefault();
    doc.pageBudget();
  });
  
  // Currency input
  $('.currency')
    .bind('blur', function (event, ui) {
      // @TODO: These should be options
      $(this).formatCurrency({
        region: 'sv-SE',
        roundToDecimalPlace: 0
      });
    });
  
  // Add Transaction form
  var TransactionForm = $('.transactionForm')
      .addInterface('postTransaction', 'transaction', 'create')
      .addInterface('getViewTemplate', 'transactionTemplate', 'template')
      .submit(function(event) {
        TransactionForm.postTransaction(function (reply) {
          if(reply.success) {
            var newRow = $('<tr />');
            newRow.parameters = {"id": reply.id};
            newRow.addInterface('get', 'transaction', 'element');
            newRow.get(function() {
              $('.transactionTable').prepend(newRow);      
            });
            TransactionForm.clearForm();
          }
        });
      });
      
  // Login form
  var LoginForm = $('.loginForm')
    .addInterface('login', 'login', 'update')
    .submit(function(event) {
      LoginForm.login(function(reply) {
        if(reply.login == 1) {
          doc.pageAddTransaction();
        }
      });
      return false;
    });
});





