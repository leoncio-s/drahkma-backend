create table if not exists forget_password(
    user bigint not null,
    code text not null,
    created_at datetime not null default current_timestamp,
    expires_at TIMESTAMP DEFAULT (current_timestamp + INTERVAL 15 MINUTE),
    used boolean default 0,
    foreign key (user) references users(id)
);