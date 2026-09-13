create table if not exists forget_password(
    user bigint not null,
    code text not null,
    created_at datetime not null default current_timestamp,
    expires_at TIMESTAMP,
    used boolean default 0,
    foreign key (user) references users(id)
);

CREATE TRIGGER set_expires_at
BEFORE INSERT ON forget_password
FOR EACH ROW
BEGIN
    IF NEW.expires_at IS NULL THEN
        SET NEW.expires_at = CURRENT_TIMESTAMP + INTERVAL 15 MINUTE;
    END IF;
END;