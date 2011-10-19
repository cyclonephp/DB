CREATE TABLE "select" (
    delete character varying(2)
);

CREATE TABLE users_all (
    name character varying(32),
    deleted smallint DEFAULT 0,
    owner smallint
);
