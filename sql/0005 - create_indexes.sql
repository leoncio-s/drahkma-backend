/*
    @author: Leoncio
    description: create index to tables
*/

drop procedure if exists create_index;

create procedure create_index(in p_sql_index text, in p_table_name varchar(100), in p_idx_name varchar(100))
begin
	declare continue handler for sqlexception begin end;

	if exists(select 1 
    from information_schema.statistics s 
    where s.table_schema = database()
    and s.table_name = p_table_name
    and s.index_name = p_idx_name)
  	then
    	set @drp_sql = concat(
            'drop index `',
            replace(p_idx_name, '`', '``'),
            '` on `',
            replace(p_table_name, '`', '``'),
            '`'
        );
		select concat('index ', p_idx_name, ' has exists. Drop it!') as message;
        prepare stmt from @drp_sql;
        execute stmt;
        deallocate prepare stmt;
	end if;
	
	select concat('crete index ', p_idx_name, ' to ', p_table_name, '. Drop it!') as message;
	
	set @sql = p_sql_index;
	prepare stmt from @sql;
	execute stmt;
	deallocate prepare stmt;
end;

/* ------ users ------------ */
call create_index("create unique index idx_users_email on users(email)", "users", "idx_users_email");

call create_index("create index idx_users_id on users(id) using btree", "users", "idx_users_id");

call create_index("create index idx_users_id on users(id) using btree", "users", "idx_users_id");

/* ------ bank accounts ------------ */
call create_index("create unique index idx_bank_accounts_uniques on bank_accounts(user, bankCode, agency, accountNumber)", "bank_accounts", "idx_bank_accounts_uniques");

call create_index("create unique index idx_bank_accounts_id on bank_accounts(id)", "bank_accounts", "idx_bank_accounts_id");

call create_index("create index idx_bank_accounts_user_x_id on bank_accounts(user, id)", "bank_accounts", "idx_bank_accounts_user_x_id");

/* ------ transfer bank ------------ */
call create_index("create index idx_transfer_bank_user_x_id on transfer_bank(user, id)", "transfer_bank", "idx_transfer_bank_user_x_id");

call create_index("create index idx_transfer_bank_user_x_bank_account_x_id on transfer_bank(user, id, bank_account)", "transfer_bank", "idx_transfer_bank_user_x_bank_account_x_id");

call create_index("create index idx_transfer_bank_user_x_bank_account on transfer_bank(user, bank_account)", "transfer_bank", "idx_transfer_bank_user_x_bank_account");

call create_index("create index idx_transfer_bank_id_x_bank_account on transfer_bank(id, bank_account)", "transfer_bank", "idx_transfer_bank_id_x_bank_account");

call create_index("create unique index idx_transfer_bank_id on transfer_bank(id)", "transfer_bank", "idx_transfer_bank_id");

call create_index("create unique index idx_transfer_bank_id on transfer_bank(id)", "transfer_bank", "idx_transfer_bank_id");

/* ------ categories ------------ */
call create_index("create unique index idx_categories_id on categories(id)", "categories", "idx_categories_id");

call create_index("create index idx_categories_user on categories(user)", "categories", "idx_categories_user");

call create_index("create unique index idx_categories_id_x_user on categories(id, user)", "categories", "idx_categories_id_x_user");

call create_index("create unique index idx_categories_user_x_description on categories(user, description)", "categories", "idx_categories_user_x_description");

call create_index("create index idx_categories_description on categories(description)", "categories", "idx_categories_description");

/* ------ cards ------------ */
call create_index("create unique index idx_cards_uniques on cards(user,type, flag, last_4_digits)", "cards", "idx_cards_uniques");

call create_index("create unique index idx_cards_id on cards(id)", "cards", "idx_cards_id");

call create_index("create unique index idx_cards_id_x_user on cards(id, user)", "cards", "idx_cards_id_x_user");

call create_index("create index idx_cards_user on cards(user)", "cards", "idx_cards_user");

/* ------ items ------------ */
call create_index("create index idx_items_user on items(user)", "items", "idx_items_user");

call create_index("create unique index idx_items_id on items(id)", "items", "idx_items_id");

call create_index("create index idx_items_category on items(category)", "items", "idx_items_category");

call create_index("create index idx_items_card on items(card)", "items", "idx_items_card");

call create_index("create index idx_items_transfer_bank on items(transfer_bank)", "items", "idx_items_transfer_bank");

call create_index("create index idx_items_expense on items(expense)", "items", "idx_items_expense");

call create_index("create index idx_items_user_x_expense on items(user, expense)", "items", "idx_items_user_x_expense");

call create_index("create unique index idx_items_user_x_id on items(user, id)", "items", "idx_items_user_x_id");

call create_index("create index idx_items_user_x_date_x_expense on items(user, date, expense)", "items", "idx_items_user_x_date_x_expense");

call create_index("create index idx_items_user_x_date_x_expense_x_category on items(user, date, expense, category)", "items", "idx_items_user_x_date_x_expense_x_category");

call create_index("create index idx_items_user_x_date_x_expense_x_category on items(user, date, expense, category)", "items", "idx_items_user_x_date_x_expense_x_category");

call create_index("create index idx_items_user_x_date_x_expense_x_card on items(user, date, expense, card)", "items", "idx_items_user_x_date_x_expense_x_card");

call create_index("create index idx_items_user_x_date_x_expense_x_transfer_bank on items(user, date, expense, transfer_bank)", "items", "idx_items_user_x_date_x_expense_x_transfer_bank");

call create_index("create index idx_items_user_x_description on items(user, description)", "items", "idx_items_user_x_description");

/* -------- email_verified ------------ */
call create_index("create unique index idx_email_verified_email_x_token on email_verified(email, token)", "email_verified", "idx_email_verified_email_x_token");

call create_index("create index idx_email_verified_email on email_verified(email)", "email_verified", "idx_email_verified_email");

call create_index("create index idx_email_verified_email on email_verified(token(300))", "email_verified", "idx_email_verified_email");

drop procedure create_index;