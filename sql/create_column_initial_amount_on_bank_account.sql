/* add column initial_balance on bank_accounts */
alter table bank_accounts add column initial_balance decimal(15,2) not null default 0.00;

/* modify datatype to decimal on value column */
alter table items modify column value decimal(15,2) not null default 0.00;