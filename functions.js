function render_transactions(data) {
			// List transactions
		$.post('ajax_responders.php', { rid: "transactions" }, function(data) {
			if(data.error == 'empty') {
				transactions = "";				
			} else {
				// @TODO: format transactions
				transactions = "lista";
			}

			html = transaction_form() + transactions; //render_transactions(data);
			$("#content").html(html);
		}, 'json');
}

// Render html one-line form for account entry
function transaction_form($accounts) {
	
	date = new Date();
	date_suggestion = date.getFullYear() + "-" + date.getMonth() + "-"+ date.getDate();

	// form
	f = "<form id='transaction_form'>\n"
	f += "<input type='text' name='date' value='"+ date_suggestion +"'>\n"
	f += "<select name='account' size=''>\n";
	for(i = 0; i < 3; ++i) {
		f += "<option value = '";
		f += accounts[i]['no'] + "'";
		f += "style='background-color:";
		f += accounts[i]['color'] + "'>";
		f += accounts[i]['name'] + "</option>\n";
	} 
	f += "</select>"
	f += "<input type='text' name='amount'>\n";
	f += "</form>";

	// No submit button - delibirately!
	
	// table with previous entries
	return f;
}

// Store data from account entry - return data to prepend to accounts list
function send_account_form() {
	
}

// Render html for budget and settings form
function budget_form(accounts) {
	html = "<h1>Budget</h1>\n";
	html += "<form id ='budget_form'>\n";
	html += "<table>\n";

	subaccount_now = false;
	for(i in accounts) {
		subaccount_prev = subaccount_now;
		subaccount_now = accounts[i].id % 10 != 0;
		html += "<tr>\n";
		html += "<td>";
		if(subaccount_now) {
			// Subaccount identifier
			html += " - ";	
		}
		html += " " + accounts[i].id + "</td>\n";
		html += "<td><input type='text' id value='" + accounts[i].name + "' id='accounts["+i+"]['name']></td>\n";
		html += "<td><input type='text' id value='" + accounts[i].budget + "' id='accounts["+i+"]['budget']></td>\n"; 
		html += "</tr>\n";

		if(!subaccount_prev) {
			// Add row link
			html += "<tr><td colspan='3'>\n";
			html += "<a id='new_row' href=''>Lägg till underkonto</a>\n";
			html += "</td></tr>\n";
		}
		
	}
	
	html += "<td colspan='3'>\n";
	html += "<input type='submit' value='Spara'>";
	html += "</td>\n";	
	
	html += "</table></form>\n";

	return html;
}

function color_by_number(no, lightness) {
	/*colors['61'].text = '#999900';
	colors[61].normal = '#aaaa00';
	colors[61].bg = '#ffff99'; 
	// etc.
    

	index = floor(no / 10);
	return colors[no][lightness];
	*/
	return "#FFFFFF";
}

// Post budget from data
function send_budget_form() {
	
}
