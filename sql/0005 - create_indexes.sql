/*
    @author: Leoncio
    description: create index to tables
*/

/* ------ users ------------ */
drop index idx_users_email on users;
create unique index idx_users_email
on users(email);

drop index idx_users_id on users;
create index idx_users_id
on users(id) using btree;

drop index idx_users_phone_number on users;
create index idx_users_id
on users(id) using btree;

/* ------ bank accounts ------------ */
drop index idx_bank_accounts_uniques on bank_accounts;
create unique index idx_bank_accounts_uniques
on bank_accounts(user, bankCode, agency, accountNumber);

drop index idx_bank_accounts_id on bank_accounts;
create unique index idx_bank_accounts_id
on bank_accounts(id);

drop index idx_bank_accounts_user_x_id on bank_accounts;
create index idx_bank_accounts_user_x_id
on bank_accounts(user, id);

/* ------ transfer bank ------------ */
drop index idx_transfer_bank_user_x_id on transfer_bank;
create index idx_transfer_bank_user_x_id
on transfer_bank(user, id);

drop index idx_transfer_bank_user_x_bank_account_x_id on transfer_bank;
create index idx_transfer_bank_user_x_bank_account_x_id
on transfer_bank(user, id, bank_account);

drop index idx_transfer_bank_user_x_bank_account on transfer_bank;
create index idx_transfer_bank_user_x_bank_account
on transfer_bank(user, bank_account);

drop index idx_transfer_bank_id_x_bank_account on transfer_bank;
create index idx_transfer_bank_id_x_bank_account
on transfer_bank(id, bank_account);

drop index idx_transfer_bank_id on transfer_bank;
create unique index idx_transfer_bank_id
on transfer_bank(id);

drop index idx_transfer_bank_id on transfer_bank;
create unique index idx_transfer_bank_id
on transfer_bank(id);

/* ------ categories ------------ */
drop index idx_categories_id on categories;
create unique index idx_categories_id
on categories(id);

drop index idx_categories_user on categories;
create index idx_categories_user
on categories(user);

drop index idx_categories_id_x_user on categories;
create unique index idx_categories_id_x_user
on categories(id, user);

drop index idx_categories_user_x_description on categories;
create unique index idx_categories_user_x_description
on categories(user, description);

drop index idx_categories_description on categories;
create index idx_categories_description
on categories(description);

/* ------ cards ------------ */
drop index idx_cards_uniques on cards;
create unique index idx_cards_uniques
on cards(user,type, flag, last_4_digits);

drop index idx_cards_id on cards;
create unique index idx_cards_id
on cards(id);

drop index idx_cards_id_x_user on cards;
create unique index idx_cards_id_x_user
on cards(id, user);

drop index idx_cards_user on cards;
create index idx_cards_user
on cards(user);

/* ------ items ------------ */
drop index idx_items_user on items;
create index idx_items_user
on items(user);

drop index idx_items_id on items;
create unique index idx_items_id
on items(id);

drop index idx_items_category on items;
create index idx_items_category
on items(category);

drop index idx_items_card on items;
create index idx_items_card
on items(card);

drop index idx_items_transfer_bank on items;
create index idx_items_transfer_bank
on items(transfer_bank);

drop index idx_items_expense on items;
create index idx_items_expense
on items(expense);

drop index idx_items_user_x_expense on items;
create index idx_items_user_x_expense
on items(user, expense);

drop index idx_items_user_x_id on items;
create unique index idx_items_user_x_id
on items(user, id);

drop index idx_items_user_x_date_x_expense on items;
create index idx_items_user_x_date_x_expense
on items(user, date, expense);

drop index idx_items_user_x_date_x_expense_x_category on items;
create index idx_items_user_x_date_x_expense_x_category
on items(user, date, expense, category);

drop index idx_items_user_x_date_x_expense_x_category on items;
create index idx_items_user_x_date_x_expense_x_category
on items(user, date, expense, category);

drop index idx_items_user_x_date_x_expense_x_card on items;
create index idx_items_user_x_date_x_expense_x_card
on items(user, date, expense, card);

drop index idx_items_user_x_date_x_expense_x_transfer_bank on items;
create index idx_items_user_x_date_x_expense_x_transfer_bank
on items(user, date, expense, transfer_bank);

drop index idx_items_user_x_description on items;
create index idx_items_user_x_description
on items(user, description);

/* -------- email_verified ------------ */
drop index idx_email_verified_email_x_token on email_verified;
create unique index idx_email_verified_email_x_token
on email_verified(email, token);

drop index idx_email_verified_email on email_verified;
create index idx_email_verified_email
on email_verified(email);

drop index idx_email_verified_token on email_verified;
create index idx_email_verified_email
on email_verified(token);
