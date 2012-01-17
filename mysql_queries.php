


// UPDATE BUDGET STENCILS

BEGIN;
	DELETE FROM budget_stencil WHERE user = @user;
	INSERT INTO budget_stencil (user, account, amount) VALUES(@user, @account{n}, @amount{n})...;
	//test//
COMMIT; (or ROLLBACK)

// USE STENCILS TO FILL BUDGET FOR NEXT MONTH

INSERT INTO transaction (user, date, amount, account) VALUES (@user, //now//, )

SELECT amount, account FROM budget_stencil WHERE user = @user

<?php


