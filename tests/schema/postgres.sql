drop table if exists chk_constraints;

drop table if exists serusers;

drop sequence if exists serusers_id_seq;

drop table if exists users;

drop sequence if exists seq_users;

create sequence seq_users;

create table users(
    id integer primary key default nextval('seq_users'),
    name varchar(256) unique not null
);

create table serusers(
    id serial primary key,
    name varchar(256)
);

insert into users (name) values ('user1'), ('user2');

insert into serusers (name) values ('user1'), ('user2');

create table chk_constraints(
    id int primary key,
    notnull_unnamed int not null,
    uniq_unnamed int unique,
    fk int references users(id),
    uniq_named int constraint uniq_constr unique,
    chk_unnamed int check (chk_unnamed > 0),
    chk_named int constraint chk_constr check (chk_named > 0)
);

create or replace function test_raise() returns boolean as $$
    begin
        raise 'error message' using
            detail = 'my detail'
            , hint = 'my hint'
            , errcode = '11111';
    end;
$$ language plpgsql;