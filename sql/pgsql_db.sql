create table if not exists migration
(
	version varchar(180) not null
		constraint migration_pkey
			primary key,
	apply_time integer
)
;

alter table migration owner to postgres
;

create table if not exists "user"
(
	id serial not null
		constraint user_pkey
			primary key,
	username varchar(255) not null
		constraint user_username_key
			unique,
	auth_key varchar(32) not null,
	password_hash varchar(255) not null,
	password_reset_token varchar(255)
		constraint user_password_reset_token_key
			unique,
	email varchar(255) not null
		constraint user_email_key
			unique,
	city varchar(255),
	timezone varchar(255),
	language varchar(255),
	os varchar(255),
	info_token varchar(32) not null,
	status smallint default 10 not null,
	created_at integer not null,
	updated_at integer not null
)
;

alter table "user" owner to postgres
;

create table if not exists token
(
	id serial not null
		constraint token_pkey
			primary key,
	token varchar(255) not null
		constraint token_token_key
			unique,
	user_id integer not null,
	status smallint default 10 not null,
	expires_at integer not null,
	created_at integer not null,
	updated_at integer not null
)
;

alter table token owner to postgres
;

create table if not exists gift_code
(
	id serial not null
		constraint gift_code_pkey
			primary key,
	token varchar(255) not null
		constraint gift_code_token_key
			unique,
	status smallint default 10 not null,
	expires_at integer not null,
	created_at integer not null,
	updated_at integer not null
)
;

alter table gift_code owner to postgres
;
