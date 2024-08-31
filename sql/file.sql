begin;

create database if not exists lfinanca;

use lfinanca;

CREATE TABLE IF NOT EXISTS users (
    id bigint auto_increment primary key,
    fullname varchar(100) not null default '',
    email varchar(150) not null unique,
    password text not null,
    phone_number varchar(14) not null default '',
    actived boolean not null default false,
    email_verified_at datetime,
    created_at datetime not null default current_timestamp,
    update_at datetime on update current_timestamp
);

create table if not exists bank_accounts(
    id bigint auto_increment primary key,
    bankCode varchar(10) not null,
    user bigint not null,
    bankName varchar(100) not null, 
    agency varchar(8) not null,
    accountNumber varchar(10) not null,
    created_at datetime not null default current_timestamp,
    update_at datetime on update current_timestamp,
    foreign key(user) references users(id),
    unique (user, bankCode, agency, accountNumber)
);


create table if not exists transfer_bank(
    id bigint auto_increment primary key,
    type ENUM('PIX', 'TED', 'DOC', 'BOLETO', 'OTHERS') not null,
    description varchar(250) not null,
    bank_account bigint not null,
    created_at datetime not null default current_timestamp,
    update_at datetime on update current_timestamp,
    foreign key (bank_account) references bank_accounts(id)
);

create table if not exists categories(
    id bigint auto_increment primary key,
    description varchar(10) not null,
    user bigint not null,
    created_at datetime not null default current_timestamp,
    update_at datetime on update current_timestamp,
    foreign key (user) references users(id)
);

create table if not exists cards(
    id bigint auto_increment primary key,
    user bigint not null,
    type ENUM('Credit', 'Debit', 'VR', 'VA', 'Others') not null,
    brand varchar(50) not null, 
    flag enum('Visa', 'Mastercard', 'Elo', 'AEXP', 'Others') not null,
    expires_at char(4) not null default '',
    last_4_digits char(4) not null default '',
    invoice_day int not null,
    created_at datetime not null default current_timestamp,
    update_at datetime on update current_timestamp,
    check (invoice_day >= 1 and invoice_day <=31),
    foreign key (user) references users(id),
    unique(type, flag, last_4_digits)
);

create table if not exists items(
    id bigint auto_increment primary key,
    user bigint not null,
    description varchar(100) not null, 
    expense boolean not null default true,
    value float not null default 0.00,
    date datetime not null default now(),
    category bigint not null, 
    card bigint not null,
    transfer_bank bigint not null,
    created_at datetime not null default current_timestamp,
    update_at datetime on update current_timestamp,
    foreign key (user) references users(id),
    foreign key (category) references categories(id),
    foreign key (card) references cards(id),
    foreign key (transfer_bank) references transfer_bank(id)
);

create table email_verified(
    email varchar(150) not null,
    token text not null, 
    expires_at varchar(30) not null
);

show tables;

commit;